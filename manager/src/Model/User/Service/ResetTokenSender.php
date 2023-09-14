<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use RuntimeException;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class ResetTokenSender
{
	public function __construct(private MailerInterface $mailer)
	{
	}

	public function send(Email $email, ResetToken $token): void
	{
		$email = (new TemplatedEmail())
			->from('boburmirzo.kozimov@gmail.com')
			->to($email->getEmail())
			->subject('Sign Up Confirmation')
			->text($token->getToken())
			->htmlTemplate('mail/user/reset.html.twig')
			->context(['token' => $token->getToken()]);
		
		try {
			$this->mailer->send($email);
		} catch (TransportExceptionInterface $e) {
			throw new RuntimeException('Unable to send');
		}
	}
}