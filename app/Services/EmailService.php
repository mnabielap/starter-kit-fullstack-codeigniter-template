<?php

namespace App\Services;

class EmailService
{
    protected $email;
    protected $fromEmail;
    protected $fromName;

    public function __construct()
    {
        $this->email = \Config\Services::email();
        $this->fromEmail = getenv('email.fromEmail') ?: 'no-reply@app.com';
        $this->fromName = getenv('email.fromName') ?: 'StarterKit';
    }

    public function sendEmail(string $to, string $subject, string $text): void
    {
        if (getenv('CI_ENVIRONMENT') === 'testing') return;

        // In Development, we just log it to avoid SMTP setup errors for beginners
        if (getenv('CI_ENVIRONMENT') === 'development') {
            log_message('info', "--- MOCK EMAIL ---\nTo: $to\nSubject: $subject\nBody: $text\n------------------");
            return;
        }

        $this->email->setFrom($this->fromEmail, $this->fromName);
        $this->email->setTo($to);
        $this->email->setSubject($subject);
        $this->email->setMessage($text);
        $this->email->send();
    }

    public function sendResetPasswordEmail(string $to, string $token): void
    {
        $url = base_url("reset-password?token={$token}");
        $text = "Dear user,\nTo reset your password, click: {$url}";
        $this->sendEmail($to, 'Reset Password', $text);
    }

    public function sendVerificationEmail(string $to, string $token): void
    {
        $url = base_url("verify-email?token={$token}");
        $text = "Dear user,\nTo verify your email, click: {$url}";
        $this->sendEmail($to, 'Verify Email', $text);
    }
}