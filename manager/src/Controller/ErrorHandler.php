<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;

class ErrorHandler
{
	public function __construct(private LoggerInterface $logger)
	{
	}

	public function handle(\DomainException $e)
	{
		$this->logger->warning($e->getMessage(), ['exception' => $e]);
	}
}