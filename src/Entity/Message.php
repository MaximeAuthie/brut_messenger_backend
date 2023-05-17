<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_message = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content_message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $hour_date_message = null;

    #[ORM\Column]
    private ?bool $status_message = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdMessage(): ?int
    {
        return $this->id_message;
    }

    public function setIdMessage(int $id_message): self
    {
        $this->id_message = $id_message;

        return $this;
    }

    public function getContentMessage(): ?string
    {
        return $this->content_message;
    }

    public function setContentMessage(string $content_message): self
    {
        $this->content_message = $content_message;

        return $this;
    }

    public function getHourDateMessage(): ?\DateTimeInterface
    {
        return $this->hour_date_message;
    }

    public function setHourDateMessage(\DateTimeInterface $hour_date_message): self
    {
        $this->hour_date_message = $hour_date_message;

        return $this;
    }

    public function isStatusMessage(): ?bool
    {
        return $this->status_message;
    }

    public function setStatusMessage(bool $status_message): self
    {
        $this->status_message = $status_message;

        return $this;
    }
}