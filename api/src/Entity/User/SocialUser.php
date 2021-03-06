<?php

namespace App\Entity\User;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\User\SocialUserRepository")
 * @ORM\Table(name="social_users")
 */
class SocialUser
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Many SocialUsers has One User.
     * @ORM\ManyToOne(targetEntity="\App\Entity\User\User", inversedBy="social", cascade={"persist", "remove"})
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id",  nullable=false)
     */
    private $user;


    /**
     * @ORM\Column(type="string", length=20)
     */
    private $provider;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $image;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $appId;

    public static $PROVIDER_FACEBOOK = "facebook";
    public static $PROVIDER_GOOGLE = "google";

    /**
     * @var array
     */
    public static $listProviders = ['facebook', 'google'];

    /**
     * @return int|null
     */
    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     * @return SocialUser
     */
    public function setUser(User $user): self
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getProvider(): string
    {
        return $this->provider;
    }

    /**
     * @param mixed $provider
     * @return SocialUser
     */
    public function setProvider($provider): self
    {
        $this->provider = $provider;
        return $this;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return SocialUser
     */
    public function setName($name): self
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @return string
     */
    public function getImage(): string
    {
        return $this->image;
    }

    /**
     * @param mixed $image
     * @return SocialUser
     */
    public function setImage($image): self
    {
        $this->image = $image;
        return $this;
    }

    /**
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appId;
    }

    /**
     * @param mixed $appId
     * @return SocialUser
     */
    public function setAppId($appId): self
    {
        $this->appId = $appId;
        return $this;
    }
}