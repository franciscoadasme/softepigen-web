<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

class Turnstile implements ValidationRule
{
    public function validate(
        string $attribute,
        mixed $value,
        Closure $fail,
    ): void {
        $secretKey = config('services.turnstile.secret');

        if (blank($secretKey)) {
            return;
        }

        $response = Http::asForm()->post(
            config('services.turnstile.verify_url'),
            [
                'secret' => $secretKey,
                'response' => $value,
                'remoteip' => request()->ip(),
            ],
        );

        if (!$response->json('success', false)) {
            $fail('CAPTCHA verification failed. Please try again.');
        }
    }
}
