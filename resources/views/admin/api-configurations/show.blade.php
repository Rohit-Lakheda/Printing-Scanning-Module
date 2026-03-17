@extends('layouts.app')

@section('title', 'Post Data API Configuration Details')

@section('content')
<div class="card">
    <div class="card-header" style="display: flex; justify-content: space-between; align-items: center;">
        <h1 class="card-title">Post Data API Configuration: {{ $apiConfiguration->name }}</h1>
        <div>
            <a href="{{ route('admin.api-configurations.edit', $apiConfiguration) }}" class="btn btn-secondary">Edit</a>
            <a href="{{ route('admin.api-configurations.index') }}" class="btn btn-secondary" style="margin-left: 10px;">Back to List</a>
        </div>
    </div>

    <div style="padding: 20px;">
        <!-- Status -->
        <div style="margin-bottom: 20px;">
            <strong>Status:</strong>
            @if($apiConfiguration->is_active)
                <span style="color: #10b981; font-weight: bold;">Active</span>
            @else
                <span style="color: #6b7280;">Inactive</span>
            @endif
        </div>

        @if($apiConfiguration->description)
        <div style="margin-bottom: 20px;">
            <strong>Description:</strong>
            <p style="margin-top: 5px;">{{ $apiConfiguration->description }}</p>
        </div>
        @endif

        <!-- API Details -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">API Endpoint</h3>
            <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <code style="font-size: 14px; word-break: break-all;">{{ $endpointUrl }}</code>
                <button onclick="copyToClipboard('{{ $endpointUrl }}')" class="btn btn-secondary" style="margin-left: 10px; padding: 5px 15px; font-size: 12px;">Copy URL</button>
            </div>
        </div>

        <!-- API Key -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">API Key</h3>
            <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <code style="font-size: 14px; word-break: break-all;">{{ $apiConfiguration->api_key }}</code>
                <button onclick="copyToClipboard('{{ $apiConfiguration->api_key }}')" class="btn btn-secondary" style="margin-left: 10px; padding: 5px 15px; font-size: 12px;">Copy Key</button>
            </div>
        </div>

        <!-- Field Mappings -->
        <div style="background: #f9fafb; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">Field Mappings</h3>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                These are the field mappings configured for this API. Send data using these API field names.
            </p>
            <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #e5e7eb;">
                <table style="width: 100%; font-size: 13px;">
                    <thead>
                        <tr style="border-bottom: 1px solid #e5e7eb;">
                            <th style="padding: 8px; text-align: left;">API Field Name</th>
                            <th style="padding: 8px; text-align: left;">Database Column</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($fieldMappings as $apiField => $dbColumn)
                            <tr style="border-bottom: 1px solid #f3f4f6;">
                                <td style="padding: 8px;"><code>{{ $apiField }}</code></td>
                                <td style="padding: 8px;"><code>{{ $dbColumn }}</code></td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- API Documentation -->
        <div style="background: #eff6ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #3b82f6;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">API Usage Instructions</h3>
            
            <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="font-size: 14px; margin-bottom: 10px;">Endpoint:</h4>
                <code style="display: block; padding: 10px; background: #f9fafb; border-radius: 4px; margin-bottom: 10px;">POST {{ $endpointUrl }}</code>
            </div>

            <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="font-size: 14px; margin-bottom: 10px;">Headers:</h4>
                <code style="display: block; padding: 10px; background: #f9fafb; border-radius: 4px;">
Content-Type: application/json<br>
Accept: application/json
                </code>
            </div>

            <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="font-size: 14px; margin-bottom: 10px;">Request Body Example:</h4>
                <pre style="background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;">{
  "category": "DELEGATE",
  "name": "John Doe",
  "email": "john@example.com",
  "mobile": "1234567890",
  "designation": "Manager",
  "company": "ABC Corp"
}</pre>
                <p style="font-size: 12px; color: #6b7280; margin-top: 10px;">
                    <strong>Note:</strong> If <code>regid</code> is not provided, it will be auto-generated based on the category prefix.
                </p>
            </div>

            <div style="background: white; padding: 15px; border-radius: 6px; margin-bottom: 15px;">
                <h4 style="font-size: 14px; margin-bottom: 10px;">Success Response (201):</h4>
                <pre style="background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;">{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "id": 1,
    "RegID": "DEL0001",
    "Name": "John Doe",
    "Category": "DELEGATE"
  }
}</pre>
            </div>

            <div style="background: white; padding: 15px; border-radius: 6px;">
                <h4 style="font-size: 14px; margin-bottom: 10px;">Error Response (422/409/401):</h4>
                <pre style="background: #1e293b; color: #e2e8f0; padding: 15px; border-radius: 4px; overflow-x: auto; font-size: 12px;">{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "Category": ["The category field is required."]
  }
}</pre>
            </div>
        </div>

        <!-- Share Section -->
        <div style="background: #f0fdf4; padding: 20px; border-radius: 8px; border-left: 4px solid #10b981;">
            <h3 style="font-size: 18px; margin-bottom: 15px;">Share API Details</h3>
            <p style="font-size: 13px; color: #6b7280; margin-bottom: 15px;">
                Copy and share this information with external parties who need to integrate with your API.
            </p>
            <div style="background: white; padding: 15px; border-radius: 6px; border: 1px solid #d1fae5;">
                <div style="margin-bottom: 15px;">
                    <strong>Endpoint URL:</strong>
                    <div style="margin-top: 5px;">
                        <code style="background: #f9fafb; padding: 8px; border-radius: 4px; display: block; word-break: break-all;">{{ $endpointUrl }}</code>
                    </div>
                </div>
                <div style="margin-bottom: 15px;">
                    <strong>API Key:</strong>
                    <div style="margin-top: 5px;">
                        <code style="background: #f9fafb; padding: 8px; border-radius: 4px; display: block; word-break: break-all;">{{ $apiConfiguration->api_key }}</code>
                    </div>
                </div>
                <div>
                    <strong>Method:</strong> POST<br>
                    <strong>Content-Type:</strong> application/json<br>
                    <strong>Required Fields:</strong> category, name<br>
                    <strong>Optional Fields:</strong> regid (auto-generated if not provided), email, mobile, designation, company, country, state, city, additional1-5, receipt_number, is_lunch_allowed
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        alert('Copied to clipboard!');
    }, function(err) {
        // Fallback for older browsers
        const textarea = document.createElement('textarea');
        textarea.value = text;
        document.body.appendChild(textarea);
        textarea.select();
        document.execCommand('copy');
        document.body.removeChild(textarea);
        alert('Copied to clipboard!');
    });
}
</script>
@endpush
@endsection
