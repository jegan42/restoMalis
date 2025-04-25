<?php

namespace App\Tests\Entity;

use App\Entity\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    public function testtheAutomaticGenerationOfApiTokenSettingWhenCreatingANewUser(): void
    {
        $user = new User();
        $this->assertNotNull($user->getApiToken());
        $this->assertMatchesRegularExpression('/^[a-f0-9]{120}$/', $user->getApiToken());
    }

    public function testGettersAndSetters(): void
    {
        $user = new User();
        $user->setEmail('test@test.test');
        $user->setPassword('password');
        $user->setCreatedAt(new \DateTimeImmutable());
        $user->setUpdatedAt(new \DateTimeImmutable());
        $user->setApiToken('token');
        $user->setRoles(['ROLE_USER']);
        $this->assertEquals('test@test.test', $user->getEmail());
        $this->assertEquals('password', $user->getPassword());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getCreatedAt());
        $this->assertInstanceOf(\DateTimeImmutable::class, $user->getUpdatedAt());
        $this->assertEquals('token', $user->getApiToken());
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testGetRolesWithNoRoles(): void
    {
        $user = new User();
        $this->assertEquals(['ROLE_USER'], $user->getRoles());
    }

    public function testThanUserHasAtLeastOneRole(): void
    {
        $user = new User();
        $this->assertContains('ROLE_USER', $user->getRoles());
    }
    
    public function testAnException(): void
    {
        $this->expectException(\TypeError::class);
        $user = new User();
        $user->setFirstName([10]);
    }

    public function provideFirstName(): \Generator
    {
        yield ['Thomas'];
        yield ['Eric'];
        yield ['Marie'];
    }
    /** @dataProvider provideFirstName */
    public function testFirstNameSetter(string $name): void
    {
        $user = new User();
        $user->setFirstName($name);
        $this->assertSame($name, $user->getFirstName());
    }
}