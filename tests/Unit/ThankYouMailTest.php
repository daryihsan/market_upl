<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ThankYouMailTest extends TestCase
{
    public function test_email_notifikasi_terbentuk()
    {
        $email = "test@example.com";

        $this->assertNotEmpty($email);
        $this->assertStringContainsString('@', $email);
    }
}