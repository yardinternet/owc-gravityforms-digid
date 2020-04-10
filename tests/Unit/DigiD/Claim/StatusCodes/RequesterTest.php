<?php

namespace Yard\Tests\DigiD\DigiD\Claim\StatusCodes;

use Mockery as m;
use WP_Mock;
use Yard\DigiD\DigiD\Claim\Status;
use Yard\DigiD\DigiD\Claim\StatusCodes\Requester;
use Yard\Tests\DigiD\TestCase;

class RequesterTest extends TestCase
{
    protected $requester;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $status              = m::mock(Status::class);
        $this->requester     = new Requester($status);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function requester_class_is_a_correct_instance()
    {
        $this->assertInstanceOf(Requester::class, $this->requester);
    }

    /** @test */
    public function status_is_correct()
    {
        $this->assertFalse($this->requester->isSuccess());
    }
}
