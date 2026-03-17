<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Models\Category;
use App\Models\ApiConfiguration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserRegistrationController extends Controller
{
    /**
     * Register user via API
     */
    public function register(Request $request, $apiKey)
    {
        // Find API configuration
        $apiConfig = ApiConfiguration::where('api_key', $apiKey)
            ->where('is_active', true)
            ->first();

        if (!$apiConfig) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid or inactive API key'
            ], 401);
        }

        // Get field mappings or use default
        $fieldMappings = $apiConfig->field_mappings;
        if (empty($fieldMappings) || !is_array($fieldMappings)) {
            $fieldMappings = $this->getDefaultFieldMappings();
        }
        
        // Map incoming data to database columns
        $mappedData = [];
        foreach ($fieldMappings as $apiField => $dbColumn) {
            if ($request->has($apiField)) {
                $value = $request->input($apiField);
                
                // Convert IsLunchAllowed to boolean if present
                if ($dbColumn === 'IsLunchAllowed') {
                    $value = filter_var($value, FILTER_VALIDATE_BOOLEAN);
                }
                
                $mappedData[$dbColumn] = $value;
            }
        }

        // Validate required fields
        $validator = Validator::make($mappedData, [
            'Category' => 'required|string|exists:categories,Category',
            'Name' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        // Handle RegID - generate if not provided
        if (empty($mappedData['RegID'])) {
            $mappedData['RegID'] = $this->generateRegID($mappedData['Category']);
        } else {
            // Check if RegID already exists
            if (UserDetail::where('RegID', $mappedData['RegID'])->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'RegID already exists',
                    'regid' => $mappedData['RegID']
                ], 409);
            }
        }

        // Set DataFrom
        $mappedData['DataFrom'] = 'Through API';
        $mappedData['Data_Received_At'] = now();

        // Create user
        try {
            $user = UserDetail::create($mappedData);

            return response()->json([
                'success' => true,
                'message' => 'User registered successfully',
                'data' => [
                    'id' => $user->id,
                    'RegID' => $user->RegID,
                    'Name' => $user->Name,
                    'Category' => $user->Category,
                ]
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register user',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate RegID for a category
     */
    private function generateRegID($category)
    {
        $categoryModel = Category::where('Category', $category)->first();
        
        if (!$categoryModel) {
            throw new \Exception('Category not found');
        }

        $prefix = $categoryModel->Prefix ?? '';
        
        // Find the highest RegID number for this category
        $maxRegNumber = 0;
        $users = UserDetail::where('Category', $category)->get();
        
        foreach ($users as $user) {
            $regID = $user->RegID;
            
            // If RegID starts with prefix, extract the numeric part
            if ($prefix && strpos($regID, $prefix) === 0) {
                $numericPart = substr($regID, strlen($prefix));
                if (preg_match('/^(\d+)/', $numericPart, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxRegNumber) {
                        $maxRegNumber = $num;
                    }
                }
            } else {
                // Fallback: extract last numeric sequence
                if (preg_match('/(\d+)$/', $regID, $matches)) {
                    $num = (int)$matches[1];
                    if ($num > $maxRegNumber) {
                        $maxRegNumber = $num;
                    }
                }
            }
        }
        
        // Generate new RegID
        $regNumber = $maxRegNumber + 1;
        $newRegID = $prefix . str_pad($regNumber, 4, '0', STR_PAD_LEFT);
        
        // Ensure uniqueness
        $attempts = 0;
        while (UserDetail::where('RegID', $newRegID)->exists() && $attempts < 100) {
            $regNumber++;
            $newRegID = $prefix . str_pad($regNumber, 4, '0', STR_PAD_LEFT);
            $attempts++;
        }
        
        return $newRegID;
    }

    /**
     * Get default field mappings
     */
    private function getDefaultFieldMappings()
    {
        return [
            'regid' => 'RegID',
            'category' => 'Category',
            'name' => 'Name',
            'designation' => 'Designation',
            'company' => 'Company',
            'country' => 'Country',
            'state' => 'State',
            'city' => 'City',
            'email' => 'Email',
            'mobile' => 'Mobile',
            'additional1' => 'Additional1',
            'additional2' => 'Additional2',
            'additional3' => 'Additional3',
            'additional4' => 'Additional4',
            'additional5' => 'Additional5',
            'receipt_number' => 'ReceiptNumber',
            'is_lunch_allowed' => 'IsLunchAllowed',
        ];
    }
}
