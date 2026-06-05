<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ReviewEmailValidationTest extends TestCase
{
    public function test_email_format_tidak_valid()
    {
        $email = "userdomain.com";

        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);

        $this->assertFalse($isValid);
    }
}