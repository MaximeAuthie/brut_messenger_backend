<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('user:getUserById')]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Groups('user:getUserById')]
    private ?string $email = null;

    #[ORM\Column]
    #[Groups('user:getUserById')]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    #[Groups('user:getUserById')]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    #[Groups('user:getUserById')]
    private ?string $first_name_user = null;

    #[ORM\Column(length: 50)]
    #[Groups('user:getUserById')]
    private ?string $last_name_user = null;

    #[ORM\Column(length: 50)]
    #[Groups('user:getUserById')]
    private ?string $nickname_user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Groups('user:getUserById')]
    private ?\DateTimeInterface $birthday_user = null;

    #[ORM\Column(length: 100)]
    #[Groups('user:getUserById')]
    private ?string $avatar_url_user = null;

    #[ORM\Column]
    private ?bool $status_user = null;

    #[ORM\Column(length: 50)]
    private ?string $font_size_user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $public_key_user = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $private_key_user = null;

    #[ORM\OneToMany(mappedBy: 'user_customization', targetEntity: Customization::class)]
    //! impossible d'ajouter un groupe pour l'API User sur une relation OneToMany
    private Collection $customizations_list;

    #[ORM\ManyToMany(targetEntity: Conversation::class, inversedBy: 'users_list')]
    private Collection $conversations_list;

    #[ORM\ManyToMany(targetEntity: self::class, inversedBy: 'users_list')]
    private Collection $users_list;

    public function __construct()
    {
        $this->customizations_list = new ArrayCollection();
        $this->conversations_list = new ArrayCollection();
        $this->users_list = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getFirstNameUser(): ?string
    {
        return $this->first_name_user;
    }

    public function setFirstNameUser(string $first_name_user): self
    {
        $this->first_name_user = $first_name_user;

        return $this;
    }

    public function getLastNameUser(): ?string
    {
        return $this->last_name_user;
    }

    public function setLastNameUser(string $last_name_user): self
    {
        $this->last_name_user = $last_name_user;

        return $this;
    }

    public function getNicknameUser(): ?string
    {
        return $this->nickname_user;
    }

    public function setNicknameUser(string $nickname_user): self
    {
        $this->nickname_user = $nickname_user;

        return $this;
    }

    public function getBirthdayUser(): ?\DateTimeInterface
    {
        return $this->birthday_user;
    }

    public function setBirthdayUser(\DateTimeInterface $birthday_user): self
    {
        $this->birthday_user = $birthday_user;

        return $this;
    }

    public function getAvatarUrlUser(): ?string
    {
        return $this->avatar_url_user;
    }

    public function setAvatarUrlUser(string $avatar_url_user): self
    {
        $this->avatar_url_user = $avatar_url_user;

        return $this;
    }

    public function isStatusUser(): ?bool
    {
        return $this->status_user;
    }

    public function setStatusUser(bool $status_user): self
    {
        $this->status_user = $status_user;

        return $this;
    }

    public function getFontSizeUser(): ?string
    {
        return $this->font_size_user;
    }

    public function setFontSizeUser(string $font_size_user): self
    {
        $this->font_size_user = $font_size_user;

        return $this;
    }

    public function getPublicKeyUser(): ?string
    {
        return $this->public_key_user;
    }

    public function setPublicKeyUser(string $public_key_user): self
    {
        $this->public_key_user = $public_key_user;

        return $this;
    }

    public function getPrivateKeyUser(): ?string
    {
        return $this->private_key_user;
    }

    public function setPrivateKeyUser(string $private_key_user): self
    {
        $this->private_key_user = $private_key_user;

        return $this;
    }

    /**
     * @return Collection<int, Customization>
     */
    public function getCustomizationsList(): Collection
    {
        return $this->customizations_list;
    }

    public function addCustomizationsList(Customization $customizationsList): self
    {
        if (!$this->customizations_list->contains($customizationsList)) {
            $this->customizations_list->add($customizationsList);
            $customizationsList->setUserCustomization($this);
        }

        return $this;
    }

    public function removeCustomizationsList(Customization $customizationsList): self
    {
        if ($this->customizations_list->removeElement($customizationsList)) {
            // set the owning side to null (unless already changed)
            if ($customizationsList->getUserCustomization() === $this) {
                $customizationsList->setUserCustomization(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Conversation>
     */
    public function getConversationsList(): Collection
    {
        return $this->conversations_list;
    }

    public function addConversationsList(Conversation $conversationsList): self
    {
        if (!$this->conversations_list->contains($conversationsList)) {
            $this->conversations_list->add($conversationsList);
        }

        return $this;
    }

    public function removeConversationsList(Conversation $conversationsList): self
    {
        $this->conversations_list->removeElement($conversationsList);

        return $this;
    }

    /**
     * @return Collection<int, self>
     */
    public function getUsersList(): Collection
    {
        return $this->users_list;
    }

    public function addUsersList(self $usersList): self
    {
        if (!$this->users_list->contains($usersList)) {
            $this->users_list->add($usersList);
        }

        return $this;
    }

    public function removeUsersList(self $usersList): self
    {
        $this->users_list->removeElement($usersList);

        return $this;
    }

}
