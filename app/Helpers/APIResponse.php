<?php

namespace App\Helpers;

class APIResponse
{
    public static function success($message = 'Success', $data = [], $status = 200)
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
            'error' => null
        ], $status);
    }

    public static function error($message = 'Error', $error = [], $status = 400)
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
            'error' => $error
        ], $status);
    }
}
