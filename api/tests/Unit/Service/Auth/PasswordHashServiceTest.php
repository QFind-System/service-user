<?php

namespace App\Tests\Unit\Service\Auth;

use App\Entity\User\User;
use App\Service\Auth\PasswordHashService;
use App\Tests\Unit\Base;
use Mockery;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordHashServiceTest extends Base
{
    private $userMock;

    private $userPasswordEncoderMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->userMock = Mockery::mock(User::class);
        $this->userPasswordEncoderMock = Mockery::mock(UserPasswordEncoderInterface::class);
    }

    /**
     * @test
     */
    public function hashPassword(): void
    {
        $this->userPasswordEncoderMock->shouldReceive('encodePassword')->andReturn($this->faker->title);
        $jwtService = new PasswordHashService($this->userPasswordEncoderMock, $this->faker->title, $this->faker->title);
        $result = $jwtService->hashPassword($this->userMock, $this->faker->password);

        $this->assertTrue(is_string($result));
    }

    /**
     * @test
     */
    public function checkPassword(): void
    {
        $this->userPasswordEncoderMock->shouldReceive('isPasswordValid')->andReturn(true);
        $jwtService = new PasswordHashService($this->userPasswordEncoderMock, $this->faker->title, $this->faker->title);
        $result = $jwtService->checkPassword($this->faker->password, $this->userMock);

        $this->assertTrue($result);
    }
}