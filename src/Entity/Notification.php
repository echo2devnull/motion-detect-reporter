<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\Entity;
use Symfony\Component\Validator\Constraints as Assert;

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
    const DEVICE_FRONT_LEFT = 'front-left';
    const DEVICE_FRONT_RIGHT = 'front-right';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(groups={"create", "update"})
     * @Assert\Length(max=255)
     * @Assert\Choice(callback="getDevices")
     */
    private $device;

    /**
     * @ORM\Column(type="datetime")
     * @Assert\NotBlank(groups={"create", "update"})
     */
    private $dateStart;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Assert\NotBlank(groups={"update"})
     */
    private $dateEnd;

    /**
     * @var \SplFileInfo[]|array
     */
    private $files = [];

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

    public static function getDevices(): array
    {
        return [self::DEVICE_FRONT_LEFT, self::DEVICE_FRONT_RIGHT];
    }

    public function isFinished(): bool
    {
        return (bool) $this->dateEnd;
    }

    /**
     * @return array|\SplFileInfo[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @param array|\SplFileInfo[] $files
     * @return Notification
     */
    public function setFiles(array $files): self
    {
        $this->files = $files;

        return $this;
    }


}
