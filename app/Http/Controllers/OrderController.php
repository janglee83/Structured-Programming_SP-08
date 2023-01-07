<?php

namespace App\Http\Controllers;

use App\Repositories\OrderRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class OrderController extends ApiController
{
    private $orderRepository;

    public function __construct(OrderRepository $orderRepository)
    {
        $this->orderRepository = $orderRepository;
    }

    public function getOrders(Request $request)
    {
        $orderDataRequest = $request->only('date');

        try {
            $orderData = $this->orderRepository->getOrderData($orderDataRequest);

            return $this->successResponse($orderData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }
}
