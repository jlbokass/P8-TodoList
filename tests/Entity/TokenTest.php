<?php

namespace App\Tests;

use App\Entity\Task;
use App\Entity\Token;
use App\Entity\User;
use PHPUnit\Framework\TestCase;

class TokenTest extends TestCase
{
    private $token;
    private $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = new User();
        $this->token = new Token($this->user);
    }

    public function testSettingId()
    {
        $this->token->getId();
        $this->assertEquals(null, $this->token->getId());
    }

    public function testSettingExpiredAt()
    {
        $date = new \DateTime();
        $this->token->setExpiresAt($date);
        $this->assertSame($date, $this->token->getExpiresAt());
    }

    public function testSettingToken()
    {
        $this->token->setToken('token');
        $this->assertEquals('token', $this->token->getToken());
    }

    public function testGetUser()
    {
        $this->token->getUser();
        $this->assertEquals($this->user, $this->token->getUser());
    }
}
