<?php

namespace Tests\Unit;

use App\Rules\AllowedEmailDomain;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class AllowedEmailDomainTest extends TestCase
{
    private function validate($email)
    {
        return Validator::make(['email' => $email], ['email' => ['required', 'email', new AllowedEmailDomain]]);
    }

    public function test_standard_providers_are_allowed()
    {
        foreach (['john@gmail.com', 'john@hotmail.com', 'john@outlook.com', 'john@yahoo.com', 'John@GMAIL.COM'] as $email) {
            $this->assertTrue($this->validate($email)->passes(), $email);
        }
    }

    public function test_custom_domains_are_rejected_with_suggestion()
    {
        foreach (['gmail1.com', 'gmail123.com', 'gmial.com'] as $domain) {
            $validator = $this->validate('john@'.$domain);

            $this->assertTrue($validator->fails(), $domain);
            $this->assertStringContainsString('Did you mean "@gmail.com"?', $validator->errors()->first('email'));
        }
    }

    public function test_unknown_domain_is_rejected_without_suggestion()
    {
        $validator = $this->validate('john@mycompany.xyz');

        $this->assertTrue($validator->fails());
        $this->assertStringNotContainsString('Did you mean', $validator->errors()->first('email'));
    }
}
