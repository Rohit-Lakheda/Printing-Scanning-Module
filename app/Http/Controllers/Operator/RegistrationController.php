<?php

namespace App\Http\Controllers\Operator;

use App\Http\Controllers\Controller;
use App\Models\UserDetail;
use App\Models\Category;
use App\Models\BadgeDisplaySetting;
use App\Services\RegIdGenerator;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    public function create()
    {
        $categories = Category::all();
        return view('operator.registration.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'Category' => 'required|string|exists:categories,Category',
            'Name' => 'required|string|max:255',
            'Designation' => 'nullable|string|max:255',
            'Company' => 'nullable|string|max:255',
            'Country' => 'nullable|string|max:255',
            'State' => 'nullable|string|max:255',
            'City' => 'nullable|string|max:255',
            'Email' => 'nullable|email|max:255',
            'Mobile' => 'nullable|string|max:20',
            'Additional1' => 'nullable|string|max:255',
            'Additional2' => 'nullable|string|max:255',
            'Additional3' => 'nullable|string|max:255',
            'Additional4' => 'nullable|string|max:255',
            'Additional5' => 'nullable|string|max:255',
            'ReceiptNumber' => 'nullable|string|max:255',
        ]);

        // Get category for prefix
        $category = Category::where('Category', $validated['Category'])->first();
        
        $validated['RegID'] = RegIdGenerator::generateForCategory($validated['Category']);
        $validated['DataFrom'] = 'Onsite Registration';
        
        // Check if receipt number is required
        if ($category->receipt_number_required) {
            // Store registration data in session (don't create user yet)
            session(['pending_registration' => $validated]);
            return redirect()->route('operator.registration.receipt')
                ->with('info', 'Please enter receipt number to complete registration.');
        }

        // If receipt number not required, create user immediately
        $validated['Data_Received_At'] = now();
        $userDetail = UserDetail::create($validated);

        return redirect()->route('operator.badge.print', ['regid' => $userDetail->RegID])
            ->with('success', 'Registration successful! Printing badge...');
    }

    public function receipt()
    {
        $pendingRegistration = session('pending_registration');
        if (!$pendingRegistration) {
            return redirect()->route('operator.registration.create')
                ->with('error', 'No pending registration found.');
        }

        return view('operator.registration.receipt', [
            'registrationData' => $pendingRegistration
        ]);
    }

    public function cancel()
    {
        session()->forget('pending_registration');
        return redirect()->route('operator.registration.create')
            ->with('info', 'Registration cancelled.');
    }

    public function storeReceipt(Request $request)
    {
        $validated = $request->validate([
            'ReceiptNumber' => 'required|string|max:255',
        ]);

        $pendingRegistration = session('pending_registration');
        if (!$pendingRegistration) {
            return redirect()->route('operator.registration.create')
                ->with('error', 'No pending registration found.');
        }

        // Add receipt number and create the user
        $pendingRegistration['ReceiptNumber'] = $validated['ReceiptNumber'];
        $pendingRegistration['Data_Received_At'] = now();
        
        $userDetail = UserDetail::create($pendingRegistration);

        // Clear session
        session()->forget('pending_registration');

        return redirect()->route('operator.badge.print', ['regid' => $userDetail->RegID])
            ->with('success', 'Registration completed! Printing badge...');
    }
}
