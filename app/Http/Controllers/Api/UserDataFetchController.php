<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\GetDataApiConfiguration;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserDataFetchController extends Controller
{
    public function fetch(Request $request, string $apiKey)
    {
        $config = GetDataApiConfiguration::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$config) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API key',
            ], 401);
        }

        $inputFields = $config->input_fields ?? [];
        $responseFields = $config->response_fields ?? [];

        if (empty($inputFields) || empty($responseFields)) {
            return response()->json([
                'success' => false,
                'message' => 'API configuration is incomplete',
            ], 422);
        }

        $rules = [];
        foreach ($inputFields as $field) {
            $rules[$field] = 'required';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors(),
            ], 422);
        }

        $query = UserDetail::query();
        foreach ($inputFields as $field) {
            $query->where($field, $request->input($field));
        }

        $rows = $query->limit(50)->get($responseFields);

        return response()->json([
            'success' => true,
            'message' => $rows->isEmpty() ? 'No data found' : 'Data fetched successfully',
            'count' => $rows->count(),
            'data' => $rows,
        ]);
    }
}

