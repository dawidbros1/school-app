<?php

namespace App\Entity\Lesson;

use App\Repository\Lesson\LessonTimeRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=LessonTimeRepository::class)
 * @ORM\Table(name="lesson_time")
 */
class LessonTime
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="time")
     */
    private $fromTime;

    /**
     * @ORM\Column(type="time")
     */
    private $toTime;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setFromTime(DateTime $fromTime): self
    {
        $this->fromTime = $fromTime;

        return $this;
    }

    public function getFromTime(): ?DateTime
    {
        return $this->fromTime;
    }

    public function setToTime(DateTime $toTime): self
    {
        $this->toTime = $toTime;

        return $this;
    }

    public function getToTime(): ?DateTime
    {
        return $this->toTime;
    }

    public function time(): string
    {
        return ($this->fromTime->format("H:i") . " - " . $this->toTime->format("H:i"));
    }
}