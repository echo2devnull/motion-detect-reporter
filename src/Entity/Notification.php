<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;

/**
 * @ORM\Entity(repositoryClass="App\Repository\NotificationRepository")
 * @ORM\Table(
 *     name="notification",
 *     uniqueConstraints={
 *        @ORM\UniqueConstraint(
 *            name="device_date_start_date_end_udx",
 *            columns={"device", "date_start", "date_end"}
 *        )
 *     },
 *     indexes={
 *         @ORM\Index(name="device_date_start_idx", columns={"device", "date_start"}),
 *         @ORM\Index(name="date_end_idx", columns={"date_end"}),
 *     }
 * )
 * @Entity
 */
class Notification
{
    const DEVICE_FRONT_LEFT = 'Front left';
    const DEVICE_FRONT_RIGHT = 'Front right';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $device;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $dateEnd;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDevice(): ?string
    {
        return $this->device;
    }

    public function setDevice(string $device): self
    {
        $this->device = $device;

        return $this;
    }

    public function getDateStart(): ?\DateTimeInterface
    {
        return $this->dateStart;
    }

    public function setDateStart(\DateTimeInterface $dateStart): self
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    public function getDateEnd(): ?\DateTimeInterface
    {
        return $this->dateEnd;
    }

    public function setDateEnd(?\DateTimeInterface $dateEnd): self
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    public static function getDevices()
    {
        return [self::DEVICE_FRONT_LEFT, self::DEVICE_FRONT_RIGHT];
    }
}
