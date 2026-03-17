<?php

namespace App\Http\Controllers\Lead;

use App\Http\Controllers\Controller;
use App\Models\LeadPasswordReset;
use App\Models\LeadScan;
use App\Models\LeadSetting;
use App\Models\MailConfiguration;
use App\Models\UserCredential;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class LeadPortalController extends Controller
{
    protected function leadPortalCookieResponse(int $minutes, string $token)
    {
        return cookie(
            'lead_portal_token',
            $token,
            $minutes,
            null,
            null,
            false,
            true,
            false,
            'lax'
        );
    }

    protected function findActiveMailConfiguration(): ?MailConfiguration
    {
        return MailConfiguration::where('is_active', true)->orderByDesc('id')->first()
            ?? MailConfiguration::orderByDesc('id')->first();
    }

    protected function sendHtmlMail(string $toEmail, string $subject, string $htmlBody): void
    {
        $config = $this->findActiveMailConfiguration();
        if (!$config) {
            return;
        }

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

        $message = (new \Symfony\Component\Mime\Email())
            ->from(new \Symfony\Component\Mime\Address($fromAddress, $fromName))
            ->to($toEmail)
            ->subject($subject)
            ->html($htmlBody);

        $symfonyMailer->send($message);
    }

    protected function resolveValidResetToken(string $email, string $plainToken): ?LeadPasswordReset
    {
        $tokenHash = hash('sha256', $plainToken);

        return LeadPasswordReset::where('email', $email)
            ->where('token_hash', $tokenHash)
            ->whereNull('used_at')
            ->where('expires_at', '>', now())
            ->latest('id')
            ->first();
    }

    protected function resolveLeadCredential(Request $request): ?UserCredential
    {
        $sessionCredentialId = session('lead_credential_id');
        if ($sessionCredentialId) {
            $credential = UserCredential::with('userDetail')
                ->where('is_active', true)
                ->find($sessionCredentialId);
            if ($credential) {
                return $credential;
            }
        }

        $token = $request->cookie('lead_portal_token');
        if (!$token) {
            return null;
        }

        $credential = UserCredential::with('userDetail')
            ->where('remember_token', $token)
            ->where('is_active', true)
            ->first();

        if ($credential) {
            session([
                'lead_credential_id' => $credential->id,
                'lead_user_detail_id' => $credential->user_detail_id,
            ]);
        }

        return $credential;
    }

    public function showLoginForm(Request $request)
    {
        $credential = $this->resolveLeadCredential($request);
        if ($credential) {
            return redirect()->route('lead.portal');
        }

        return view('leads.login');
    }

    public function showForgotPasswordForm()
    {
        return view('leads.forgot-password');
    }

    public function sendPasswordResetLink(Request $request)
    {
        $validated = $request->validate([
            'identifier' => 'required|string',
        ]);

        $identifier = trim($validated['identifier']);

        $credential = UserCredential::with('userDetail')
            ->where('is_active', true)
            ->where(function ($query) use ($identifier) {
                $query->where('username', $identifier)
                    ->orWhereHas('userDetail', function ($q) use ($identifier) {
                        $q->where('Email', $identifier);
                    });
            })
            ->first();

        // Do not reveal whether account exists.
        $genericMessage = 'If your account exists, a password reset link has been sent to your email.';

        if (!$credential || !$credential->userDetail || empty($credential->userDetail->Email)) {
            return back()->with('success', $genericMessage);
        }

        $email = $credential->userDetail->Email;
        $plainToken = Str::random(64);
        $reset = LeadPasswordReset::create([
            'user_credential_id' => $credential->id,
            'email' => $email,
            'token_hash' => hash('sha256', $plainToken),
            'expires_at' => now()->addMinutes(60),
        ]);

        $resetUrl = route('lead.password.reset.form', ['token' => $plainToken, 'email' => $email]);
        $name = $credential->userDetail->Name ?? 'User';

        try {
            $this->sendHtmlMail(
                $email,
                'Lead Portal Password Reset',
                '<p>Dear ' . e($name) . ',</p>'
                . '<p>Click below to reset your lead portal password:</p>'
                . '<p><a href="' . e($resetUrl) . '">Reset Password</a></p>'
                . '<p>This link is valid for 60 minutes.</p>'
            );
        } catch (\Throwable $e) {
            // Keep response generic and non-blocking.
        }

        return back()->with('success', $genericMessage);
    }

    public function showResetPasswordForm(Request $request, string $token)
    {
        $email = (string) $request->query('email', '');
        if ($email === '') {
            return redirect()->route('lead.password.forgot.form')->with('error', 'Invalid reset link.');
        }

        $reset = $this->resolveValidResetToken($email, $token);
        if (!$reset) {
            return redirect()->route('lead.password.forgot.form')->with('error', 'Reset link is invalid or expired.');
        }

        return view('leads.reset-password', [
            'token' => $token,
            'email' => $email,
        ]);
    }

    public function resetPassword(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $reset = $this->resolveValidResetToken($validated['email'], $validated['token']);
        if (!$reset) {
            return redirect()->route('lead.password.forgot.form')->with('error', 'Reset link is invalid or expired.');
        }

        $credential = UserCredential::with('userDetail')
            ->where('is_active', true)
            ->where(function ($query) use ($validated, $reset) {
                $query->where('id', $reset->user_credential_id)
                    ->orWhereHas('userDetail', function ($q) use ($validated) {
                        $q->where('Email', $validated['email']);
                    });
            })
            ->first();

        if (!$credential) {
            return redirect()->route('lead.password.forgot.form')->with('error', 'Account not found for this reset link.');
        }

        $credential->password = Hash::make($validated['password']);
        $credential->remember_token = null;
        $credential->save();

        $reset->used_at = now();
        $reset->save();

        return redirect()
            ->route('lead.login.form')
            ->with('success', 'Password has been reset successfully. Please login with your new password.')
            ->withCookie(Cookie::forget('lead_portal_token'));
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $credential = UserCredential::where('username', $credentials['username'])
            ->where('is_active', true)
            ->first();

        if (!$credential || !Hash::check($credentials['password'], $credential->password)) {
            return back()
                ->withInput(['username' => $credentials['username']])
                ->with('error', 'Invalid username or password.');
        }

        // Basic max devices enforcement: only allow new login if under limit
        if (!is_null($credential->max_devices)) {
            $activeCount = $credential->deviceLogins()->where('is_active', true)->count();
            if ($activeCount >= $credential->max_devices) {
                return back()
                    ->withInput(['username' => $credentials['username']])
                    ->with('error', 'Maximum device limit reached for this account. Please contact support to reset your logins.');
            }
        }

        session([
            'lead_credential_id' => $credential->id,
            'lead_user_detail_id' => $credential->user_detail_id,
        ]);

        $rememberToken = Str::random(60);
        $credential->remember_token = $rememberToken;
        $credential->save();

        return redirect()->route('lead.portal')->cookie(
            $this->leadPortalCookieResponse(60 * 24 * 30, $rememberToken)
        );
    }

    public function changePassword(Request $request)
    {
        $credential = $this->resolveLeadCredential($request);
        if (!$credential) {
            return redirect()->route('lead.login.form')->with('error', 'Please login to change password.');
        }

        $validated = $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:6|confirmed|different:current_password',
        ]);

        if (!Hash::check($validated['current_password'], $credential->password)) {
            return back()->with('error', 'Current password is incorrect.');
        }

        $credential->password = Hash::make($validated['new_password']);
        $newRememberToken = Str::random(60);
        $credential->remember_token = $newRememberToken;
        $credential->save();

        return redirect()->route('lead.portal')
            ->with('success', 'Password updated successfully.')
            ->cookie($this->leadPortalCookieResponse(60 * 24 * 30, $newRememberToken));
    }

    public function logout(Request $request)
    {
        $credential = $this->resolveLeadCredential($request);
        if ($credential) {
            $credential->remember_token = null;
            $credential->save();
        }

        $request->session()->forget(['lead_credential_id', 'lead_user_detail_id']);
        return redirect()
            ->route('lead.login.form')
            ->with('success', 'You have been logged out.')
            ->withCookie(Cookie::forget('lead_portal_token'));
    }

    public function portal(Request $request)
    {
        $credential = $this->resolveLeadCredential($request);

        if (!$credential) {
            return redirect()->route('lead.login.form')->with('error', 'Please login to access the portal.');
        }
        $credentialId = $credential->id;
        $leadSetting = LeadSetting::getDefault();
        $sharedFields = $leadSetting->sharedFields();
        $preferredOrder = ['RegID', 'Name', 'Company', 'Category', 'Email', 'Mobile', 'Designation', 'Country', 'State', 'City', 'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5'];
        $sharedFields = array_values(array_filter($preferredOrder, fn ($field) => in_array($field, $sharedFields, true)));
        $fieldLabels = [
            'RegID' => 'Registration ID',
            'Name' => 'Name',
            'Company' => 'Company',
            'Category' => 'Category',
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

        $recentScans = LeadScan::with('userDetail')
            ->where('scanned_by_user_id', $credentialId)
            ->orderByDesc('scanned_at')
            ->limit(5)
            ->get();

        $totalLeads = LeadScan::where('scanned_by_user_id', $credentialId)->count();

        return view('leads.portal', compact('credential', 'recentScans', 'totalLeads', 'sharedFields'));
    }

    public function downloadScans(Request $request)
    {
        $credential = $this->resolveLeadCredential($request);

        if (!$credential) {
            return redirect()->route('lead.login.form')->with('error', 'Please login to access the portal.');
        }
        $credentialId = $credential->id;

        $scans = LeadScan::with('userDetail')
            ->where('scanned_by_user_id', $credentialId)
            ->orderByDesc('scanned_at')
            ->get();

        $leadSetting = LeadSetting::getDefault();
        $sharedFields = $leadSetting->sharedFields();
        $preferredOrder = ['RegID', 'Name', 'Company', 'Category', 'Email', 'Mobile', 'Designation', 'Country', 'State', 'City', 'Additional1', 'Additional2', 'Additional3', 'Additional4', 'Additional5'];
        $sharedFields = array_values(array_filter($preferredOrder, fn ($field) => in_array($field, $sharedFields, true)));
        $fieldLabels = [
            'RegID' => 'Registration ID',
            'Name' => 'Name',
            'Company' => 'Company',
            'Category' => 'Category',
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

        $filename = 'lead_scans_' . $credential->userDetail->RegID . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function () use ($scans, $sharedFields, $fieldLabels) {
            $handle = fopen('php://output', 'w');

            $displayHeaders = array_map(function ($field) use ($fieldLabels) {
                return $fieldLabels[$field] ?? $field;
            }, $sharedFields);
            $headers = array_merge($displayHeaders, ['Lead Type', 'Comments', 'Scanned At']);
            fputcsv($handle, $headers);

            foreach ($scans as $scan) {
                $row = [];
                foreach ($sharedFields as $field) {
                    $row[] = $scan->userDetail ? ($scan->userDetail->{$field} ?? '') : '';
                }
                $row[] = $scan->lead_type ?? '';
                $row[] = $scan->lead_comments ?? '';
                $row[] = optional($scan->scanned_at)->format('Y-m-d H:i:s');
                fputcsv($handle, $row);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}

