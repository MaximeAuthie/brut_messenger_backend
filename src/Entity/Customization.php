<?php

namespace App\Entity;

use App\Repository\CustomizationRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CustomizationRepository::class)]
class Customization
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_customization = null;

    #[ORM\Column(length: 50)]
    private ?string $message_color_customization = null;

    #[ORM\Column(length: 50)]
    private ?string $user_nickname_customization = null;

    #[ORM\Column]
    private ?bool $user_status_customization = null;

    #[ORM\ManyToOne(inversedBy: 'customizations_list')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_customization = null;

    #[ORM\ManyToOne(inversedBy: 'Customizations_list')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation_customization = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdCustomization(): ?int
    {
        return $this->id_customization;
    }

    public function setIdCustomization(int $id_customization): self
    {
        $this->id_customization = $id_customization;

        return $this;
    }

    public function getMessageColorCustomization(): ?string
    {
        return $this->message_color_customization;
    }

    public function setMessageColorCustomization(string $message_color_customization): self
    {
        $this->message_color_customization = $message_color_customization;

        return $this;
    }

    public function getUserNicknameCustomization(): ?string
    {
        return $this->user_nickname_customization;
    }

    public function setUserNicknameCustomization(string $user_nickname_customization): self
    {
        $this->user_nickname_customization = $user_nickname_customization;

        return $this;
    }

    public function isUserStatusCustomization(): ?bool
    {
        return $this->user_status_customization;
    }

    public function setUserStatusCustomization(bool $user_status_customization): self
    {
        $this->user_status_customization = $user_status_customization;

        return $this;
    }

    public function getUserCustomization(): ?User
    {
        return $this->user_customization;
    }

    public function setUserCustomization(?User $user_customization): self
    {
        $this->user_customization = $user_customization;

        return $this;
    }

    public function getConversationCustomization(): ?Conversation
    {
        return $this->conversation_customization;
    }

    public function setConversationCustomization(?Conversation $conversation_customization): self
    {
        $this->conversation_customization = $conversation_customization;

        return $this;
    }
}
