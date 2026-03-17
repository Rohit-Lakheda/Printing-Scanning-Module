@extends('layouts.app')

@section('title', 'Lead Sharing & Export')

@section('content')
<div class="container">
    <h1 class="mb-4">Lead Sharing &amp; Export</h1>

    <div class="lead-settings-grid">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Fields to Share &amp; Export</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.leads.share-settings.save') }}">
                    @csrf
                    @php
                        $fields = [
                            'RegID' => 'RegID',
                            'Name' => 'Name',
                            'Category' => 'Category',
                            'Company' => 'Company',
                            'Email' => 'Email',
                            'Mobile' => 'Mobile',
                            'Designation' => 'Designation',
                            'Country' => 'Country',
                            'State' => 'State',
                            'City' => 'City',
                            'Additional1' => 'Additional 1',
                            'Additional2' => 'Additional 2',
                            'Additional3' => 'Additional 3',
                            'Additional4' => 'Additional 4',
                            'Additional5' => 'Additional 5',
                        ];
                    @endphp
                    <div class="lead-fields-grid">
                        @foreach($fields as $fieldKey => $label)
                            @php $prop = 'share_' . $fieldKey; @endphp
                            <label class="lead-field-checkbox">
                                <input type="checkbox"
                                       name="share_{{ $fieldKey }}"
                                       value="1"
                                       {{ $leadSetting->$prop ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>

                    <button type="submit" class="btn btn-primary mt-3">Save Fields</button>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Credential Email Template</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.leads.share-settings.save') }}">
                    @csrf
                    <div class="form-group">
                        <label class="form-label">Email Subject</label>
                        <input type="text"
                               name="credential_email_subject"
                               class="form-control"
                               value="{{ old('credential_email_subject', $leadSetting->credential_email_subject ?? 'Your Event Access Credentials') }}">
                    </div>
                    <div class="form-group">
                        <label class="form-label">
                            Email Body (HTML)
                            <small class="text-muted d-block" style="font-size: 12px; margin-top: 4px;">
                                Available variables:
                                {{ '{' }}{Name}}, {{ '{' }}{Company}}, {{ '{' }}{Category}},
                                {{ '{' }}{RegID}}, {{ '{' }}{Email}}, {{ '{' }}{Mobile}},
                                {{ '{' }}{Username}}, {{ '{' }}{Password}}, {{ '{' }}{MaxDevices}},
                                {{ '{' }}{LeadLink}}, {{ '{' }}{ResetPasswordLink}}
                            </small>
                        </label>
                        <textarea name="credential_email_body"
                                  rows="10"
                                  class="form-control"
                                  placeholder="HTML body for the credentials email">{{ old('credential_email_body', $leadSetting->credential_email_body) }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary mt-2">Save Template</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

