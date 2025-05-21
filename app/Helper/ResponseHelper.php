<?php

namespace App\Helper;

use Illuminate\Http\JsonResponse;

class ResponseHelper
{
    public static function Out(bool $status, $data, int $code, $token = null): JsonResponse
    {

        return response()->json(['status' => $status, 'data' => $data, 'token' => $token], $code);

    }
}
