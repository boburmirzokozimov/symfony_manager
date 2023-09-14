<?php

namespace App\Command\User;

use App\Model\User\UseCase\SignUp\Confirm\Manual\Command as ConsoleCommand;
use App\Model\User\UseCase\SignUp\Confirm\Manual\Handler;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;

class ConfirmCommand extends Command
{
	public function __construct(private UserFetcher $fetcher, private Handler $handler)
	{
		parent::__construct();
	}

	protected function configure()
	{
		$this->setName('user:confirm')->setDescription('Confirms signed up user');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');

		$email = $helper->ask($input, $output, new Question('Email: '));

		if (!$user = $this->fetcher->findByEmail($email)) {
			throw new \DomainException('User does not exist');
		}

		$command = new ConsoleCommand($user->id);

		$this->handler->handle($command);

		$output->writeln('<info>Done!</info>');
	}
}