<?php

namespace Yard\Tests\DigiD\DigiD\Claim\StatusCodes;

use Mockery as m;
use WP_Mock;
use Yard\DigiD\DigiD\Claim\Status;
use Yard\DigiD\DigiD\Claim\StatusCodes\RequestDenied;
use Yard\Tests\DigiD\TestCase;

class RequestDeniedTest extends TestCase
{
    protected $requestDenied;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $status              = m::mock(Status::class);
        $this->requestDenied = new RequestDenied($status);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function requestDenied_class_is_a_correct_instance()
    {
        $this->assertInstanceOf(RequestDenied::class, $this->requestDenied);
    }

    /** @test */
    public function status_is_correct()
    {
        $this->assertFalse($this->requestDenied->isSuccess());
    }
}
