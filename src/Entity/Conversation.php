<?php

namespace App\Entity;

use App\Repository\ConversationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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

    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'conversations_list')]
    private Collection $users_list;

    #[ORM\OneToMany(mappedBy: 'conversation_customization', targetEntity: Customization::class)]
    private Collection $Customizations_list;

    #[ORM\OneToMany(mappedBy: 'conversation_message', targetEntity: Message::class)]
    private Collection $messages_list;

    #[ORM\OneToMany(mappedBy: 'conversation_public_keys', targetEntity: PublicKeys::class)]
    private Collection $public_keys_list;

    public function __construct()
    {
        $this->users_list = new ArrayCollection();
        $this->Customizations_list = new ArrayCollection();
        $this->messages_list = new ArrayCollection();
        $this->public_keys_list = new ArrayCollection();
    }

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

    /**
     * @return Collection<int, User>
     */
    public function getUsersList(): Collection
    {
        return $this->users_list;
    }

    public function addUsersList(User $usersList): self
    {
        if (!$this->users_list->contains($usersList)) {
            $this->users_list->add($usersList);
            $usersList->addConversationsList($this);
        }

        return $this;
    }

    public function removeUsersList(User $usersList): self
    {
        if ($this->users_list->removeElement($usersList)) {
            $usersList->removeConversationsList($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Customization>
     */
    public function getCustomizationsList(): Collection
    {
        return $this->Customizations_list;
    }

    public function addCustomizationsList(Customization $customizationsList): self
    {
        if (!$this->Customizations_list->contains($customizationsList)) {
            $this->Customizations_list->add($customizationsList);
            $customizationsList->setConversationCustomization($this);
        }

        return $this;
    }

    public function removeCustomizationsList(Customization $customizationsList): self
    {
        if ($this->Customizations_list->removeElement($customizationsList)) {
            // set the owning side to null (unless already changed)
            if ($customizationsList->getConversationCustomization() === $this) {
                $customizationsList->setConversationCustomization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Message>
     */
    public function getMessagesList(): Collection
    {
        return $this->messages_list;
    }

    public function addMessagesList(Message $messagesList): self
    {
        if (!$this->messages_list->contains($messagesList)) {
            $this->messages_list->add($messagesList);
            $messagesList->setConversationMessage($this);
        }

        return $this;
    }

    public function removeMessagesList(Message $messagesList): self
    {
        if ($this->messages_list->removeElement($messagesList)) {
            // set the owning side to null (unless already changed)
            if ($messagesList->getConversationMessage() === $this) {
                $messagesList->setConversationMessage(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, PublicKeys>
     */
    public function getPublicKeysList(): Collection
    {
        return $this->public_keys_list;
    }

    public function addPublicKeysList(PublicKeys $publicKeysList): self
    {
        if (!$this->public_keys_list->contains($publicKeysList)) {
            $this->public_keys_list->add($publicKeysList);
            $publicKeysList->setConversationPublicKeys($this);
        }

        return $this;
    }

    public function removePublicKeysList(PublicKeys $publicKeysList): self
    {
        if ($this->public_keys_list->removeElement($publicKeysList)) {
            // set the owning side to null (unless already changed)
            if ($publicKeysList->getConversationPublicKeys() === $this) {
                $publicKeysList->setConversationPublicKeys(null);
            }
        }

        return $this;
    }
}
