<?php

namespace App\Security;

use App\ReadModel\User\AuthView;
use App\ReadModel\User\UserFetcher;
use JetBrains\PhpStorm\Pure;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\Exception\UnsupportedException;
use function count;
use function get_class;

class UserProvider implements UserProviderInterface
{
    public function __construct(private UserFetcher $fetcher)
    {
    }

    public function refreshUser(UserInterface $user): UserInterface|UserIdentity
    {
        if (!$user instanceof UserIdentity) {
            throw new UnsupportedException('Invalid user class ' . get_class($user));
        }

        $user = $this->loadUser($user->getUserIdentifier());

        return self::identityByUser($user);
    }

    public function loadUser(string $username): ?AuthView
    {
        $chunks = explode(' :', $username);
        if (count($chunks) === 2 && $user = $this->fetcher->findForAuthByNetwork($chunks[0], $chunks[1])) {
            return $user;
        }
        if ($user = $this->fetcher->findForAuth($username)) {
            return $user;
        }

        throw new UserNotFoundException('');
    }

    #[Pure] public static function identityByUser(AuthView $user): ?UserIdentity
    {
        return new UserIdentity($user->email,
            $user->password,
            $user->role,
            $user->id,
            $user->status,
            $user->name);
    }

    public function supportsClass(string $class): bool
    {
        return UserIdentity::class === $class || is_subclass_of($class, UserIdentity::class);
    }

    public function loadUserByIdentifier(string $identifier): UserInterface
    {
        /** @var AuthView $user */
        $user = $this->loadUser($identifier);
        return self::identityByUser($user);
    }
}