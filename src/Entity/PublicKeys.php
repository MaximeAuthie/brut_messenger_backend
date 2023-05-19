<?php

namespace App\Entity;

use App\Repository\PublicKeysRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PublicKeysRepository::class)]
class PublicKeys
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $key_publickeys = null;

    #[ORM\ManyToOne(inversedBy: 'public_keys_list')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Conversation $conversation_public_keys = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user_public_keys = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getKeyPublickeys(): ?string
    {
        return $this->key_publickeys;
    }

    public function setKeyPublickeys(string $key_publickeys): self
    {
        $this->key_publickeys = $key_publickeys;

        return $this;
    }

    public function getConversationPublicKeys(): ?Conversation
    {
        return $this->conversation_public_keys;
    }

    public function setConversationPublicKeys(?Conversation $conversation_public_keys): self
    {
        $this->conversation_public_keys = $conversation_public_keys;

        return $this;
    }

    public function getUserPublicKeys(): ?User
    {
        return $this->user_public_keys;
    }

    public function setUserPublicKeys(User $user_public_keys): self
    {
        $this->user_public_keys = $user_public_keys;

        return $this;
    }

}
