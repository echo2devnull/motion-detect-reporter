<?php

namespace App\Service;

use App\Entity\Notification;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class NotificationService
{
    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * NotificationService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * @param string $device
     * @param \DateTimeInterface|null $dateStart
     * @param \DateTimeInterface|null $dateEnd
     * @return Query
     */
    public function getFindQuery(
        ?string $device,
        ?\DateTimeInterface $dateStart = null,
        ?\DateTimeInterface $dateEnd = null
    ) {
        /** @var QueryBuilder $builder */
        $builder = $this->entityManager->getRepository(Notification::class)->createQueryBuilder('t');
        if ($device) {
            $builder->andWhere('t.device=:device')->setParameter('device', $device);
        }
        if ($dateStart) {
            $builder->andWhere('t.dateStart>=:dateStart')->setParameter('dateStart', $dateStart->format('Y-m-d H:i:s'));
        }
        if ($dateEnd) {
            $builder->andWhere('t.dateEnd<=:dateEnd')->setParameter('dateEnd', $dateEnd->format('Y-m-d H:i:s'));
        }

        return $builder->getQuery();
    }
}
