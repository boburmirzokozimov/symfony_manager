<?php

namespace App\Event\Listener\Work\Projects\Task;

use App\Model\Work\Entity\Members\Member\Id as MemberId;
use App\Model\Work\Entity\Members\Member\MemberRepository;
use App\Model\Work\Entity\Projects\Task\Event\TaskExecutorAssigned;
use App\Model\Work\Entity\Projects\Task\Id;
use App\Model\Work\Entity\Projects\Task\TaskRepository;
use DomainException;
use JetBrains\PhpStorm\NoReturn;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class EmailNotificationSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment      $twig,
        private TaskRepository   $taskRepository,
        private MemberRepository $memberRepository,
        private MailerInterface  $mailer
    )
    {
    }

    public static function getSubscribedEvents(): array
    {
        return [
            TaskExecutorAssigned::class => [
                ['onTaskExecutorAssignedAuthor']
            ]
        ];
    }

    #[NoReturn] public function onTaskExecutorAssignedAuthor(TaskExecutorAssigned $event)
    {
        $task = $this->taskRepository->get(new Id($event->id));
        $executor = $this->memberRepository->get(new MemberId($event->memberId));


        $email = (new TemplatedEmail())
            ->from('boburmirzo.kozimov@gmail.com')
            ->to($executor->getEmail())
            ->subject('News')
            ->htmlTemplate('mail/user/assignment.html.twig')
            ->context([
                'executor' => $executor,
                'task' => $task,
            ]);
        try {
            $this->mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            throw new DomainException('Unable to send');
        }
    }

}