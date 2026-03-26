<?php

namespace App\Traits;

trait ApiResponse
{
    protected function success($data = null, $message = 'Success', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
        ], $status);
    }

    protected function error($message = 'Error', $status = 500, $details = null)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'details' => $details,
        ], $status);
    }
}
