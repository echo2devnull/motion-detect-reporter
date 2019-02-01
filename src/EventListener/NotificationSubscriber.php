<?php

namespace App\EventListener;

use App\Entity\Notification;
use App\Service\FileFinder;
use Doctrine\Common\EventSubscriber;
use Doctrine\ORM\Events;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class NotificationSubscriber implements EventSubscriber
{
    /**
     * @var FileFinder
     */
    private $finder;

    /**
     * @var ParameterBagInterface
     */
    private $params;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * NotificationSubscriber constructor.
     * @param FileFinder $finder
     * @param ParameterBagInterface $params
     * @param LoggerInterface $logger
     */
    public function __construct(FileFinder $finder, ParameterBagInterface $params, LoggerInterface $logger)
    {
        $this->finder = $finder;
        $this->params = $params;
        $this->logger = $logger;
    }

    public function postLoad(Notification $notification)
    {
        if (!$notification->isFinished()) {
            return;
        }

        if (!array_key_exists($notification->getDevice(), $this->params->get('device_storage_path_map'))) {
            throw new \Exception('Storage path configuration not found for: '.$notification->getDevice());
        }

        $path = $this->params->get('device_storage_path_map')[$notification->getDevice()];

        $notification->setFiles(
            $this->finder->find($path, $notification->getDateStart(), $notification->getDateEnd())
        );
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public function getSubscribedEvents()
    {
        return [Events::postLoad];
    }
}
