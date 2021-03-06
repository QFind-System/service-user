<?php

namespace App\Tests\Unit\Service\Auth;

use App\Entity\User\User;
use App\Service\Auth\JWTService;
use App\Tests\Unit\Base;
use Mockery;

class JWTServiceTest extends Base
{
    /**
     * @test
     */
    public function createToken(): void
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getId')->andReturn($this->faker->numberBetween());
        $userMock->shouldReceive('getEmail')->andReturn($this->faker->email);
        $userMock->shouldReceive('getRoles')->andReturn([User::$ROLE_USER]);
        $jwtService = new JWTService($this->faker->title, $this->faker->unixTime);
        $result = $jwtService->create($userMock);

        $this->assertTrue(is_string($result));
    }
}