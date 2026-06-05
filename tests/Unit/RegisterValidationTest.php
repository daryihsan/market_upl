<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class RegisterValidationTest extends TestCase
{
    /**
     * TC-REG-09
     * Email PIC tidak valid (tanpa @)
     */
    public function test_email_pic_tidak_valid_tanpa_at()
    {
        $email = "userdomain.com";

        $isValid = filter_var($email, FILTER_VALIDATE_EMAIL);

        $this->assertFalse($isValid);
    }
}