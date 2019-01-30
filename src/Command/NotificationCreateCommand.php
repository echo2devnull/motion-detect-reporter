<?php

namespace App\Command;

use App\Entity\Notification;
use App\Exception\ValidationException;
use App\Service\NotificationService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationCreateCommand extends Command
{
    protected static $defaultName = 'app:notification-create';

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
     * NotificationCreateCommand constructor.
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
        $notification = new Notification();
        $notification->setDevice($input->getArgument('device'));
        $notification->setDateStart(new \DateTime());

        try {
            $this->service->create($notification);
        } catch (ValidationException $e) {
            $this->logger->error($e->getMessage());
        }
    }
}