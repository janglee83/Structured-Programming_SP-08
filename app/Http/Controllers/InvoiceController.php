<?php

namespace App\Http\Controllers;

use App\Repositories\InvoiceRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InvoiceController extends ApiController
{
    private $invoiceRepository;

    public function __construct(InvoiceRepository $invoiceRepository)
    {
        $this->invoiceRepository = $invoiceRepository;
    }


    public function getInvoices(Request $request)
    {
//        $invoiceDataRequest = $request->only('date');

        try {
            $invoiceData = $this->invoiceRepository->getInvoices();

            return $this->successResponse($invoiceData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function status(Request $request, $invoice_id)
    {
        try {
            // TODO:
            $invoiceData = Http::get('SP_01:orderManagement/' . $invoice_id);

            return $this->successResponse($invoiceData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
