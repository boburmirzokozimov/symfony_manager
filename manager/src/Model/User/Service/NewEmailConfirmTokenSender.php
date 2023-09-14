<?php

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;

class NewEmailConfirmTokenSender
{
	public function __construct(private MailerInterface $mailer)
	{
	}

	public function send(Email $email, string $token): void
	{
		$email = (new TemplatedEmail())
			->from('boburmirzo.kozimov@gmail.com')
			->to($email->getEmail())
			->subject('Sign Up Confirmation')
			->text($token)
			->htmlTemplate('mail/user/email.html.twig')
			->context(['token' => $token]);
		try {
			$this->mailer->send($email);
		} catch (TransportExceptionInterface $e) {
			throw new \DomainException('Unable to send');
		}
	}
}