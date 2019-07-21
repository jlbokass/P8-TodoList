<?php

namespace App\Tests;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    private $user;

    public function setUp(): void
    {
        parent::setUp();
        $this->user = new User();
    }

    public function testSettingUsername()
    {
        $this->user->setUsername('jean');
        $this->assertSame('jean', $this->user->getUsername());
    }

    public function testSettingEmail()
    {
        $this->user->setEmail('email');
        $this->assertSame('email', $this->user->getEmail());
    }

    public function testSettingPassword()
    {
        $this->user->setPassword('password');
        $this->assertSame('password', $this->user->getPassword());
    }

    public function testSettingCreatingAt()
    {
        $date = new \DateTime();
        $this->user->setCreatedAt($date);
        $this->assertSame($date, $this->user->getCreatedAt());
    }
}
