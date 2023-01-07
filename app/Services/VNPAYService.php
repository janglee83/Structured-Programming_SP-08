<?php

namespace App\Services;

use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use Exception;

class VNPAYService
{

    private static function hasKey()
    {
        if (empty(config("vnpay.tmn_code"))) {
            return false;
        }

        if (empty(config("vnpay.hash_secret"))) {
            return false;
        }

        if (empty(config("vnpay.url"))) {
            return false;
        }

        if (empty(config("vnpay.return_url"))) {
            return false;
        }

        return true;
    }

    public static function create_payment($code, $money, $bank_code)
    {
        if (!self::hasKey()) return null;

        $vnp_Url = config("vnpay.url");
        $vnp_ReturnUrl = config("vnpay.return_url");
        $vnp_TmnCode = config("vnpay.tmn_code");//Mã website tại VNPAY
        $vnp_HashSecret = config("vnpay.hash_secret"); //Chuỗi bí mật

        // transaction_code
        $vnp_TxnRef = $code; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
        $vnp_OrderInfo = "Thanh toán hóa đơn mua hàng.";
        $vnp_OrderType = '200000';
        $vnp_Amount = $money * 100;
        $vnp_BankCode = $bank_code;
        $vnp_Locale = 'vn';
        $vnp_IpAddr = request()->ip();

        $inputData = array(
            "vnp_Version" => "2.0.0",
            "vnp_TmnCode" => $vnp_TmnCode,
            "vnp_Amount" => $vnp_Amount,
            "vnp_Command" => "pay",
            "vnp_CreateDate" => date('YmdHis'),
            "vnp_CurrCode" => "VND",
            "vnp_IpAddr" => $vnp_IpAddr,
            "vnp_Locale" => $vnp_Locale,
            "vnp_OrderInfo" => $vnp_OrderInfo,
            "vnp_OrderType" => $vnp_OrderType,
            "vnp_ReturnUrl" => $vnp_ReturnUrl,
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (isset($vnp_BankCode) && $vnp_BankCode != "") {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }
        ksort($inputData);
        $query = "";
        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . $key . "=" . $value;
            } else {
                $hashData .= $key . "=" . $value;
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnp_Url = $vnp_Url . "?" . $query;

        if (isset($vnp_HashSecret)) {
            // $vnpSecureHash = md5($vnp_HashSecret . $hashdata);
            $vnpSecureHash = hash('sha256', $vnp_HashSecret . $hashData);
//            $vnpSecureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $vnp_Url .= 'vnp_SecureHashType=SHA512&vnp_SecureHash=' . $vnpSecureHash;
        }

        return $vnp_Url;
    }

    public static function ipn($request, TransactionRepository $transactionRepository)
    {
        /* Payment Notify
         * IPN URL: Ghi nhận kết quả thanh toán từ VNPAY
         * Các bước thực hiện:
         * Kiểm tra checksum
         * Tìm giao dịch trong database
         * Kiểm tra số tiền giữa hai hệ thống
         * Kiểm tra tình trạng của giao dịch trước khi cập nhật
         * Cập nhật kết quả vào Database
         * Trả kết quả ghi nhận lại cho VNPAY
         */

        $vnp_HashSecret = config("vnpay.hash_secret");

        $inputData = array();
        $returnData = array();

        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        try {
            $vnp_SecureHash = $inputData['vnp_SecureHash'];
            unset($inputData['vnp_SecureHashType']);
            unset($inputData['vnp_SecureHash']);
            ksort($inputData);
            $i = 0;
            $hashData = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
            }

//            $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
            $secureHash = hash('sha256', $vnp_HashSecret . $hashData);
            $vnpTranId = $inputData['vnp_TransactionNo']; // Mã giao dịch tại VNPAY
            $vnp_BankCode = $inputData['vnp_BankCode']; // Ngân hàng thanh toán
            $vnp_CardType = $inputData['vnp_CardType']; //Hình thức thanh toán
            $money = $inputData['vnp_Amount'] / 100; // Số tiền thanh toán VNPAY phản hồi

            $Status = 0; // Là trạng thái thanh toán của giao dịch chưa có IPN lưu tại hệ thống của merchant chiều khởi tạo URL thanh toán.
            $transactionCode = $inputData['vnp_TxnRef'];

            //Check Orderid
            //Kiểm tra checksum của dữ liệu
            if ($secureHash == $vnp_SecureHash) {
                //Lấy thông tin đơn hàng lưu trong Database và kiểm tra trạng thái của đơn hàng, mã đơn hàng là: $orderId
                //Việc kiểm tra trạng thái của đơn hàng giúp hệ thống không xử lý trùng lặp, xử lý nhiều lần một giao dịch

                $transaction = $transactionRepository->findByPaymentCode($transactionCode);
                if (!empty($transaction)) {
                    if (!empty($transaction["status"]) && ($transaction["status"]) == "pending") {
                        if ($transaction["money"] == $money) //Kiểm tra số tiền thanh toán của giao dịch: giả sử số tiền kiểm tra là đúng. //$order["Amount"] == $vnp_Amount
                        {
                            if ($inputData['vnp_ResponseCode'] == '00' || $inputData['vnp_TransactionStatus'] == '00') {
                                $Status = 1; // Trạng thái thanh toán thành công
                            } else {
                                $Status = 2; // Trạng thái thanh toán thất bại / lỗi
                            }
                            //Cài đặt Code cập nhật kết quả thanh toán, tình trạng đơn hàng vào DB
                            //
                            //
                            //
                            //Trả kết quả về cho VNPAY: Website/APP TMĐT ghi nhận yêu cầu thành công
                            $returnData['RspCode'] = '00';
                            $returnData['Message'] = 'Confirm Success';
                        } else {
                            $returnData['RspCode'] = '04';
                            $returnData['Message'] = 'Invalid amount';
                        }
                    } else {
                        $returnData['RspCode'] = '02';
                        $returnData['Message'] = 'Order already confirmed';
                    }
                } else {
                    $returnData['RspCode'] = '01';
                    $returnData['Message'] = 'Order not found';
                }
            } else {
                $returnData['RspCode'] = '97';
                $returnData['Message'] = 'Invalid signature';
            }
        } catch (Exception $e) {
            $returnData['RspCode'] = '99';
            $returnData['Message'] = 'Unknow error';
        }

        return $returnData;
    }

//    public static function refund($code, $money, $bankcode) {
//
//    }
}
