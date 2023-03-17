<?php

namespace Tests\Yard\DigiD\Claim\StatusCodes;

use Mockery as m;
use Tests\Yard\DigiD\TestCase;
use WP_Mock;
use Yard\DigiD\Claim\Status;
use Yard\DigiD\Claim\StatusCodes\Success;

class SuccessTest extends TestCase
{
    protected $success;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $status = m::mock(Status::class);
        $this->success = new success($status);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function success_class_is_a_correct_instance()
    {
        $this->assertInstanceOf(Success::class, $this->success);
    }

    /** @test */
    public function status_is_correct()
    {
        $this->assertTrue($this->success->isSuccess());
    }

    /** @test */
    public function message_is_correct()
    {
        $this->assertEmpty($this->success->message());
    }
}
