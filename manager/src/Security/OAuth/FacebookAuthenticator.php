<?php

namespace App\Security\OAuth;

use App\Model\User\UseCase\Network\Auth\Command;
use App\Model\User\UseCase\Network\Auth\Handler;
use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use KnpU\OAuth2ClientBundle\Client\OAuth2ClientInterface;
use KnpU\OAuth2ClientBundle\Security\Authenticator\SocialAuthenticator;
use League\OAuth2\Client\Provider\FacebookUser;
use League\OAuth2\Client\Token\AccessToken;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\UserNotFoundException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authenticator\Passport\Badge\UserBadge;
use Symfony\Component\Security\Http\Authenticator\Passport\Passport;
use Symfony\Component\Security\Http\Authenticator\Passport\SelfValidatingPassport;

class FacebookAuthenticator
{
	public function __construct(private ClientRegistry        $clientRegistry,
								private RouterInterface       $router,
								private Handler               $handler,
								private UrlGeneratorInterface $urlGenerator,
								private UserProviderInterface $userProvider)
	{
	}

	public function supports(Request $request): bool
	{
		return $request->attributes->get('_route') === 'connect_facebook_check';
	}

	public function onAuthenticationSuccess(Request $request, TokenInterface $token, $firewallName): ?RedirectResponse
	{
		$targetUrl = $this->router->generate('home');

		return new RedirectResponse($targetUrl);
	}

	public function onAuthenticationFailure(Request $request, AuthenticationException $exception): ?Response
	{
		$message = strtr($exception->getMessageKey(), $exception->getMessageData());

		return new Response($message, Response::HTTP_FORBIDDEN);
	}

	public function start(Request $request, AuthenticationException $authException = null): RedirectResponse
	{
		return new RedirectResponse(
			$this->urlGenerator->generate('app_login'), // might be the site, where users choose their oauth provider
			Response::HTTP_TEMPORARY_REDIRECT
		);
	}

	public function authenticate(Request $request): Passport
	{
		$credentials = $this->getCredentials($request);

		/** @var FacebookUser $facebookUser */
		$facebookUser = $this->getFacebookClient()->fetchUserFromToken($credentials);

		return new SelfValidatingPassport(
			new UserBadge($facebookUser->getEmail(),
				function () use ($facebookUser) {
					$network = 'facebook';
					$email = $facebookUser->getEmail();
					$id = $facebookUser->getId();
					$username = $network . ':' . $id;

					try {
						$user = $this->userProvider->loadUserByIdentifier($email);
					} catch (UserNotFoundException $e) {
						$this->handler->handle((new Command())->setNetwork($network)->setIdentity($id));
						$user = $this->userProvider->loadUserByIdentifier($username);
					}
					return $user;
				}
			));
	}

	public function getCredentials(Request $request): AccessToken
	{
		return $this->fetchAccessToken($this->getFacebookClient());
	}

	private function getFacebookClient(): OAuth2ClientInterface
	{
		return $this->clientRegistry->getClient('facebook_main');
	}
}