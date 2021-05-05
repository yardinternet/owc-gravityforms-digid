<?php

namespace Tests\Yard\DigiD\Claim\StatusCodes;

use Mockery as m;
use Tests\Yard\DigiD\TestCase;
use WP_Mock;
use Yard\DigiD\Claim\Status;
use Yard\DigiD\Claim\StatusCodes\AuthnFailed;

class AuthnFailedTest extends TestCase
{
    protected $authnfailed;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $status            = m::mock(Status::class);
        $this->authnfailed = new AuthnFailed($status);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function authnfailed_class_is_a_correct_instance()
    {
        $this->assertInstanceOf(AuthnFailed::class, $this->authnfailed);
    }

    /** @test */
    public function status_is_correct()
    {
        $this->assertFalse($this->authnfailed->isSuccess());
    }
}
