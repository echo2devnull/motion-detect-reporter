<?php

namespace App\Service;

use App\Entity\Notification;
use App\Exception\ValidationException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class NotificationService
{
    const VALIDATION_GROUP_CREATE = 'create';
    const VALIDATION_GROUP_UPDATE = 'update';

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * @var ValidatorInterface
     */
    protected $validator;

    /**
     * NotificationService constructor.
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager, ValidatorInterface $validator)
    {
        $this->entityManager = $entityManager;
        $this->validator = $validator;
    }

    /**
     * @param Notification $notification
     * @return Notification
     * @throws ValidationException
     */
    public function create(Notification $notification)
    {
        $errors = $this->validate($notification, [self::VALIDATION_GROUP_CREATE]);
        if ($errors->count()) {
            throw new ValidationException((string) $errors);
        }

        $this->entityManager->persist($notification);
        $this->entityManager->flush($notification);

        return $notification;
    }

    /**
     * @param Notification $notification
     * @return Notification
     * @throws ValidationException
     */
    public function update(Notification $notification)
    {
        $errors = $this->validate($notification, [self::VALIDATION_GROUP_UPDATE]);
        if ($errors->count()) {
            throw new ValidationException((string) $errors);
        }
        $this->entityManager->flush($notification);

        return $notification;
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
    ): Query {
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

    /**
     * @param string $device
     * @return Notification|null
     * @throws NonUniqueResultException
     */
    public function getLastUnfinished(string $device): ?Notification
    {
        /** @var QueryBuilder $builder */
        $builder = $this->entityManager->getRepository(Notification::class)->createQueryBuilder('t');
        $builder->andWhere('t.dateEnd IS NULL');
        $builder->andWhere('t.device=:device')->setParameter('device', $device);
        $builder->addOrderBy('t.dateStart', 'DESC');
        $builder->setMaxResults(1);

        return $builder->getQuery()->getOneOrNullResult();
    }

    /**
     * @param Notification $notification
     * @param array $groups
     * @return ConstraintViolationListInterface
     */
    protected function validate(Notification $notification, array $groups = []): ConstraintViolationListInterface
    {
        return $this->validator->validate($notification, null, $groups);
    }
}
