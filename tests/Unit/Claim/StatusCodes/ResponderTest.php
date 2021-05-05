<?php

namespace Tests\Yard\DigiD\Claim\StatusCodes;

use Mockery as m;
use WP_Mock;
use Yard\DigiD\Claim\Status;
use Yard\DigiD\Claim\StatusCodes\Responder;
use Tests\Yard\DigiD\TestCase;

class ResponderTest extends TestCase
{
    protected $responder;

    public function setUp(): void
    {
        WP_Mock::setUp();
        $status              = m::mock(Status::class);
        $this->responder     = new Responder($status);
    }

    public function tearDown(): void
    {
        WP_Mock::tearDown();
    }

    /** @test */
    public function responder_class_is_a_correct_instance()
    {
        $this->assertInstanceOf(Responder::class, $this->responder);
    }

    /** @test */
    public function status_is_correct()
    {
        $this->assertFalse($this->responder->isSuccess());
    }
}
