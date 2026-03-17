<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\MailConfiguration;
use App\Models\LeadSetting;
use App\Models\UserCredential;
use App\Models\UserDetail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class LeadSettingsController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::all();
        $mailConfigs = MailConfiguration::orderByDesc('id')->get();
        $activeMailConfig = $mailConfigs->first();
        $leadSetting = LeadSetting::getDefault();

        $selectedCategory = $request->query('category');
        $search = $request->query('search');

        $users = collect();
        if ($selectedCategory) {
            $query = UserDetail::where('Category', $selectedCategory);

            if ($search) {
                $term = '%' . $search . '%';
                $query->where(function ($q) use ($term) {
                    $q->where('RegID', 'like', $term)
                        ->orWhere('Name', 'like', $term)
                        ->orWhere('Company', 'like', $term)
                        ->orWhere('Email', 'like', $term)
                        ->orWhere('Mobile', 'like', $term)
                        ->orWhere('Designation', 'like', $term);
                });
            }

            $users = $query->orderBy('Name')->limit(500)->get();
        }

        return view('admin.leads.settings', compact(
            'categories',
            'mailConfigs',
            'leadSetting',
            'selectedCategory',
            'search',
            'users',
            'activeMailConfig'
        ));
    }

    public function saveMailConfig(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'host' => 'required|string|max:255',
            'port' => 'required|integer',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'encryption' => 'nullable|string|max:10',
            'from_address' => 'nullable|email',
            'from_name' => 'nullable|string|max:255',
            // Flags are handled manually as booleans from checkboxes
            'use_auth' => 'nullable',
            'is_active' => 'nullable',
        ]);

        $data['use_auth'] = $request->has('use_auth') ? (bool) $request->input('use_auth') : true;
        $data['is_active'] = $request->has('is_active') ? (bool) $request->input('is_active') : true;

        $config = MailConfiguration::where('name', $data['name'])->first();
        if ($config) {
            // If password field left blank, keep existing password
            if (empty($data['password'])) {
                unset($data['password']);
            }
            $config->update($data);
        } else {
            MailConfiguration::create($data);
        }

        return redirect()->route('admin.leads.settings')->with('success', 'Mail configuration saved.');
    }

    public function generateAndSendCredentials(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|string',
            'user_ids' => 'nullable|array',
            'user_ids.*' => 'integer',
            'max_devices' => 'nullable|integer|min:1',
            'mail_configuration_id' => 'required|exists:mail_configurations,id',
        ]);

        $query = UserDetail::where('Category', $validated['category']);
        if (!empty($validated['user_ids'])) {
            $query->whereIn('id', $validated['user_ids']);
        }

        $users = $query->get();
        if ($users->isEmpty()) {
            return redirect()->back()->with('error', 'No users found for selected criteria.');
        }

        $mailConfig = MailConfiguration::findOrFail($validated['mail_configuration_id']);
        $leadSetting = LeadSetting::getDefault();

        foreach ($users as $user) {
            $credential = UserCredential::firstOrNew([
                'user_detail_id' => $user->id,
            ]);

            $rawPassword = strtoupper(substr((string) $user->RegID, -4)) . random_int(1000, 9999);

            if (!$credential->exists) {
                $credential->username = $user->Email ?: $user->RegID;
            }
            if (array_key_exists('max_devices', $validated)) {
                $credential->max_devices = $validated['max_devices'];
            }
            // Always rotate password when admin sends credentials again.
            $credential->password = Hash::make($rawPassword);
            $credential->remember_token = null;
            $credential->is_active = true;
            $credential->save();

            if (!$user->Email) {
                continue;
            }

            $this->sendCredentialMail($user, $credential, $rawPassword, $mailConfig, $leadSetting);
        }

        return redirect()->route('admin.leads.settings')->with('success', 'Credentials regenerated and emails dispatched where possible.');
    }

    protected function sendCredentialMail(UserDetail $user, UserCredential $credential, ?string $rawPassword, MailConfiguration $config, LeadSetting $leadSetting): void
    {
        $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
            $config->host,
            $config->port,
            $config->encryption === 'ssl' || $config->encryption === 'tls'
        );

        if ($config->use_auth && $config->username) {
            $transport->setUsername($config->username);
            if ($config->password) {
                $transport->setPassword($config->password);
            }
        }

        $symfonyMailer = new \Symfony\Component\Mailer\Mailer($transport);

        $fromAddress = $config->from_address ?: config('mail.from.address');
        $fromName = $config->from_name ?: config('mail.from.name');

        $subject = $leadSetting->credential_email_subject ?: 'Your Event Access Credentials';

        $bodyTemplate = $leadSetting->credential_email_body;
        if (!$bodyTemplate) {
            $bodyTemplate = <<<HTML
<p>Dear {{Name}},</p>
<p>Here are your access credentials:</p>
<ul>
    <li><strong>Username:</strong> {{Username}}</li>
    <li><strong>Password:</strong> {{Password}}</li>
</ul>
<p>Max devices: {{MaxDevices}}</p>
HTML;
        }

        $maxDevicesText = $credential->max_devices ? (string) $credential->max_devices : 'Unlimited';
        $passwordText = $rawPassword ?: '(unchanged)';

        $leadPortalUrl = url('/lead/login');
        $resetPageUrl = url('/lead/forgot-password');

        $replacements = [
            '{{Name}}' => $user->Name ?? '',
            '{{Company}}' => $user->Company ?? '',
            '{{Category}}' => $user->Category ?? '',
            '{{RegID}}' => $user->RegID ?? '',
            '{{Email}}' => $user->Email ?? '',
            '{{Mobile}}' => $user->Mobile ?? '',
            '{{Username}}' => $credential->username,
            '{{Password}}' => $passwordText,
            '{{MaxDevices}}' => $maxDevicesText,
            '{{LeadLink}}' => $leadPortalUrl,
            '{{ResetPasswordLink}}' => $resetPageUrl,
        ];

        $renderedBody = str_replace(array_keys($replacements), array_values($replacements), $bodyTemplate);

        $message = (new \Symfony\Component\Mime\Email())
            ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
            ->to($user->Email)
            ->subject($subject)
            ->html($renderedBody);

        $symfonyMailer->send($message);
    }

    public function saveLeadShareSettings(Request $request)
    {
        $data = $request->validate([
            'share_RegID' => 'nullable|boolean',
            'share_Name' => 'nullable|boolean',
            'share_Category' => 'nullable|boolean',
            'share_Company' => 'nullable|boolean',
            'share_Email' => 'nullable|boolean',
            'share_Mobile' => 'nullable|boolean',
            'share_Designation' => 'nullable|boolean',
            'share_Country' => 'nullable|boolean',
            'share_State' => 'nullable|boolean',
            'share_City' => 'nullable|boolean',
            'share_Additional1' => 'nullable|boolean',
            'share_Additional2' => 'nullable|boolean',
            'share_Additional3' => 'nullable|boolean',
            'share_Additional4' => 'nullable|boolean',
            'share_Additional5' => 'nullable|boolean',
            'credential_email_subject' => 'nullable|string|max:255',
            'credential_email_body' => 'nullable|string',
        ]);

        $leadSetting = LeadSetting::getDefault();

        foreach ($leadSetting->getFillable() as $field) {
            if (str_starts_with($field, 'share_')) {
                $leadSetting->{$field} = isset($data[$field]) ? (bool) $data[$field] : false;
            }
        }

        if (array_key_exists('credential_email_subject', $data)) {
            $leadSetting->credential_email_subject = $data['credential_email_subject'];
        }
        if (array_key_exists('credential_email_body', $data)) {
            $leadSetting->credential_email_body = $data['credential_email_body'];
        }

        $leadSetting->save();

        return redirect()->back()->with('success', 'Lead sharing and email template settings saved.');
    }

    public function shareIndex()
    {
        $leadSetting = LeadSetting::getDefault();
        return view('admin.leads.share', compact('leadSetting'));
    }
}

