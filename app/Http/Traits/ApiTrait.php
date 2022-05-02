<?php
namespace App\Http\Traits;

trait ApiTrait{

    protected function onSuccess($data, string $message = '', int $code = 200){
        return response()->json([
            'status' => $code,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function onError(int $code, string $message = ''){        
        return response()->json([
            'status' => $code,
            'message' => $message,
        ], $code);
    }




}
