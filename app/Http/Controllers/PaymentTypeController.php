<?php

namespace App\Http\Controllers;

use App\Repositories\PaymentTypeRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PaymentTypeController extends ApiController
{
    private $paymentTypeRepository;

    public function __construct(PaymentTypeRepository $paymentTypeRepository)
    {
        $this->paymentTypeRepository = $paymentTypeRepository;
    }

    public function getPaymentTypeList(Request $request) {
        try {
            $paymentTypeData = $this->paymentTypeRepository->getPaymentTypes();

            return $this->successResponse($paymentTypeData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    /**
     *
     */
    public function setPaymentStatus(Request $request) {
        try {
            $listPayment = $request->only('payment_method');

            foreach ($listPayment as $method) {
                $data = $this->paymentTypeRepository->updateStatus($method);
            }

            return $this->successResponse($data, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
