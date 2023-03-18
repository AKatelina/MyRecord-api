<?php


namespace App\Security;

use  Lexik\Bundle\JWTAuthenticationBundle\Security\User\JWTUserInterface;
final class User implements JWTUserInterface
{
    private $userIdentifier;
    private $roles;
    private $id;

    public function __construct(string $userIdentifier, array $roles, $id)
    {
        $this->userIdentifier = $userIdentifier;
        $this->roles = $roles;
        $this->id = $id;
    }

    /**
     * {@inheritdoc}
     */
    public static function createFromPayload($username, array $payload)
    {
//        if (isset($payload['roles'])) {
//            return new static($username, (array) $payload['roles']);
//        }
//
//        return new static($username);
        return new self(
            $username,
            $payload['roles'], // Added by default
            $payload['id']  // Custom
        );
    }

    /**
     * {@inheritdoc}
     */
    public function getUsername(): string
    {
        return $this->getUserIdentifier();
    }

    public function getUserIdentifier(): string
    {
        return $this->userIdentifier;
    }
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(): array
    {

        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassword(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * {@inheritdoc}
     */
    public function eraseCredentials()
    {
    }
}