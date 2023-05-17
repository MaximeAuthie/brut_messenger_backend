<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ConversationRepository::class)]
class Conversation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_conversation = null;

    #[ORM\Column(length: 50)]
    private ?string $name_conversation = null;

    #[ORM\Column(length: 50)]
    private ?string $color_conversation = null;

    #[ORM\Column(length: 100)]
    private ?string $url_avatar_conversation = null;

    #[ORM\Column(length: 50)]
    private ?string $type_conversation = null;

    #[ORM\Column]
    private ?bool $status_conversation = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdConversation(): ?int
    {
        return $this->id_conversation;
    }

    public function setIdConversation(int $id_conversation): self
    {
        $this->id_conversation = $id_conversation;

        return $this;
    }

    public function getNameConversation(): ?string
    {
        return $this->name_conversation;
    }

    public function setNameConversation(string $name_conversation): self
    {
        $this->name_conversation = $name_conversation;

        return $this;
    }

    public function getColorConversation(): ?string
    {
        return $this->color_conversation;
    }

    public function setColorConversation(string $color_conversation): self
    {
        $this->color_conversation = $color_conversation;

        return $this;
    }

    public function getUrlAvatarConversation(): ?string
    {
        return $this->url_avatar_conversation;
    }

    public function setUrlAvatarConversation(string $url_avatar_conversation): self
    {
        $this->url_avatar_conversation = $url_avatar_conversation;

        return $this;
    }

    public function getTypeConversation(): ?string
    {
        return $this->type_conversation;
    }

    public function setTypeConversation(string $type_conversation): self
    {
        $this->type_conversation = $type_conversation;

        return $this;
    }

    public function isStatusConversation(): ?bool
    {
        return $this->status_conversation;
    }

    public function setStatusConversation(bool $status_conversation): self
    {
        $this->status_conversation = $status_conversation;

        return $this;
    }
}
