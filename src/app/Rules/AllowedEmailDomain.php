<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;
use Illuminate\Support\Str;

class AllowedEmailDomain implements Rule
{
    /**
     * The rejected domain, used to build the error message.
     *
     * @var string|null
     */
    protected $domain;

    /**
     * Determine if the email's domain is one of the allowed providers.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        if (!is_string($value) || !Str::contains($value, '@')) {
            // Let the "email" rule report malformed addresses.
            return true;
        }

        $this->domain = strtolower(trim(Str::afterLast($value, '@')));

        return in_array($this->domain, self::allowedDomains(), true);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        $message = 'The email domain "@'.$this->domain.'" is not supported.';

        if ($suggestion = self::suggest($this->domain)) {
            $message .= ' Did you mean "@'.$suggestion.'"?';
        }

        return $message.' Please use a standard email provider such as Gmail, Outlook, Hotmail, Yahoo or iCloud.';
    }

    /**
     * Get the list of allowed domains.
     *
     * @return array
     */
    public static function allowedDomains()
    {
        return array_map('strtolower', config('email.allowed_domains', []));
    }

    /**
     * Find the allowed domain the user most likely meant (e.g. gmail1.com -> gmail.com).
     *
     * @param  string|null  $domain
     * @return string|null
     */
    public static function suggest($domain)
    {
        if (!$domain) {
            return null;
        }

        $allowed = self::allowedDomains();

        $withoutDigits = preg_replace('/\d+/', '', $domain);
        if (in_array($withoutDigits, $allowed, true)) {
            return $withoutDigits;
        }

        $best = null;
        $bestDistance = 3; // only suggest for close typos
        foreach ($allowed as $candidate) {
            $distance = levenshtein($domain, $candidate);
            if ($distance < $bestDistance) {
                $best = $candidate;
                $bestDistance = $distance;
            }
        }

        return $best;
    }
}
