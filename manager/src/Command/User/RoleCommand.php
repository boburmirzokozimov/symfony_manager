<?php

namespace App\Command\User;

use App\Model\User\Entity\User\Role;
use App\Model\User\UseCase\Role\Command as CommandAlias;
use App\Model\User\UseCase\Role\Handler;
use App\ReadModel\User\UserFetcher;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;

class RoleCommand extends Command
{
	public function __construct(private UserFetcher $fetcher,
								private Handler     $handler)
	{
		parent::__construct();
	}

	protected function configure()
	{
		$this->setName('user:role')->setDescription('Changes user role');
	}

	protected function execute(InputInterface $input, OutputInterface $output)
	{
		$helper = $this->getHelper('question');

		$email = $helper->ask($input, $output, new Question('Email: '));

		if (!$user = $this->fetcher->findByEmail($email)) {
			throw new \DomainException('User does not exist');
		}

		$command = new CommandAlias($user['id']);
		$roles = [Role::USER, Role::ADMIN];

		$command->role = $helper->ask($input, $output, new ChoiceQuestion('Role: ', $roles, 0));

		$this->handler->handle($command);

		$output->writeln('<info>Done!</info>');
	}
}