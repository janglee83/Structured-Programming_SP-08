<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class ApiController extends Controller
{
    /**
     * Defind Resfull API code stauts
     * @var integer
     */
    const SUCCESS_CODE = 200;

    /**
     * Send to client a successfull response
     */
    public function successResponse($data, $message = '')
    {
        return $this->apiResponse(ApiController::SUCCESS_CODE, $data, $message);
    }

    /**
     * Send to clent a error response
     */
    public function errorResponse($data, $message, $code = 0, $error = [])
    {
        return $this->apiResponse($code, $data, $message, $error);
    }

    /**
     * Handle api response
     */
    private function apiResponse($code, $data, $message, $error = [])
    {
        return \response()->json([
            'result'        => $code,
            'current_time'  => time(),
            'message'       => $message,
            'data'          =>  !empty($data) ? $data : new \stdClass(),
            'error'         => !empty($error) ? $error : new \stdClass()
        ]);
    }
}
