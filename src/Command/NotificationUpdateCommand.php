<?php

namespace App\Command;

use App\Exception\ValidationException;
use App\Service\NotificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationUpdateCommand extends Command
{
    protected static $defaultName = 'app:notification-update';

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @var NotificationService
     */
    protected $service;

    /**
     * NotificationUpdateCommand constructor.
     * @param ValidatorInterface $validator
     * @param LoggerInterface $logger
     * @param NotificationService $service
     */
    public function __construct(ValidatorInterface $validator, LoggerInterface $logger, NotificationService $service)
    {
        $this->validator = $validator;
        $this->logger = $logger;
        $this->service = $service;

        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    protected function configure()
    {
        $this->addArgument('device', InputArgument::REQUIRED);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if (!$notification = $this->service->getLastUnfinished($input->getArgument('device'))) {
            $this->logger->error(sprintf('Cant find unfinished notification for %s', $input->getArgument('device')));

            return;
        }
        try {
            $this->service->update($notification->setDateEnd(new \DateTime()));
        } catch (ValidationException $e) {
            $this->logger->error((string) $e->getMessage());
        }
    }
}