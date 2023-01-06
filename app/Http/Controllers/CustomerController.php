<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Repositories\CustomerRepository;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CustomerController extends ApiController
{
    private $customerRepository;

    public function __construct(CustomerRepository $customerRepository)
    {
        $this->customerRepository = $customerRepository;
    }

    public function getCustomers(Request $request)
    {
        $customerDataRequest = $request->only('user_id', 'name', 'email', 'city', 'district', 'town', 'address', 'phone');

        try {
            $customerData = $this->customerRepository->getUserData($customerDataRequest);

            return $this->successResponse($customerData, "Successfully!");
        } catch (\Exception $exception) {
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }
    }

    public function store(Customer $customer, Request $request)
    {
        $customerDataRequest = $request->only('user_id', 'name', 'email', 'city', 'district', 'town', 'address', 'phone');

        $customerData = $this->customerRepository->getUserData(['user_id' => $customer->user_id]);

        DB::beginTransaction();
        try {
            if (!is_null($customerData)) {
                $customerData = $this->customerRepository->update([
                    'name' => $customerDataRequest['name'],
                    'email' => $customerDataRequest['email'],
                    'city' => $customerDataRequest['city'],
                    'district' => $customerDataRequest['district'],
                    'town' => $customerDataRequest['town'],
                    'address' => $customerDataRequest['address'],
                    'phone' => $customerDataRequest['phone']
                ], $customer->id);
            } else {
                $customerData = $this->customerRepository->create([
                    'user_id' => $customerDataRequest['user_id'],
                    'name' => $customerDataRequest['name'],
                    'email' => $customerDataRequest['email'],
                    'city' => $customerDataRequest['city'],
                    'district' => $customerDataRequest['district'],
                    'town' => $customerDataRequest['town'],
                    'address' => $customerDataRequest['address'],
                    'phone' => $customerDataRequest['phone']
                ]);
            }
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            Log::error("[ERROR]" . $exception->getMessage());
            return $this->errorResponse([], 'Server error', 500);
        }

        return $this->successResponse($customerData, "Successfully!");
    }
}
