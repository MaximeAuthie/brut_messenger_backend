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

    #[ORM\Column(type: Types::TEXT)]
    private ?string $content_message = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $hour_date_message = null;

    #[ORM\Column]
    private ?bool $status_message = null;

    #[ORM\ManyToOne(inversedBy: 'messages_list')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation_message = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_message = null;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getConversationMessage(): ?Conversation
    {
        return $this->conversation_message;
    }

    public function setConversationMessage(?Conversation $conversation_message): self
    {
        $this->conversation_message = $conversation_message;

        return $this;
    }

    public function getUserMessage(): ?User
    {
        return $this->user_message;
    }

    public function setUserMessage(?User $user_message): self
    {
        $this->user_message = $user_message;

        return $this;
    }

}
