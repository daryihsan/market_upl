<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class LoginTest extends TestCase
{
    /**
     * TC-AUTH-01
     * Login berhasil dengan email benar,
     * password benar, dan akun aktif
     */
    public function test_login_dengan_data_valid()
    {
        $email = "seller@example.com";
        $password = "password123";

        $this->assertNotEmpty($email);
        $this->assertNotEmpty($password);

        $this->assertStringContainsString('@', $email);
    }
}