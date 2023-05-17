<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?int $id_user = null;

    #[ORM\Column(length: 50)]
    private ?string $first_name_user = null;

    #[ORM\Column(length: 50)]
    private ?string $last_name_user = null;

    #[ORM\Column(length: 50)]
    private ?string $nickname_user = null;

    #[ORM\Column(length: 100)]
    private ?string $email_user = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $birthday_user = null;

    #[ORM\Column(length: 100)]
    private ?string $password_user = null;

    #[ORM\Column(length: 100)]
    private ?string $url_avatar_user = null;

    #[ORM\Column]
    private ?bool $status_user = null;

    #[ORM\Column(length: 50)]
    private ?string $font_size_user = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdUser(): ?int
    {
        return $this->id_user;
    }

    public function setIdUser(int $id_user): self
    {
        $this->id_user = $id_user;

        return $this;
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

    public function getEmailUser(): ?string
    {
        return $this->email_user;
    }

    public function setEmailUser(string $email_user): self
    {
        $this->email_user = $email_user;

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

    public function getPasswordUser(): ?string
    {
        return $this->password_user;
    }

    public function setPasswordUser(string $password_user): self
    {
        $this->password_user = $password_user;

        return $this;
    }

    public function getUrlAvatarUser(): ?string
    {
        return $this->url_avatar_user;
    }

    public function setUrlAvatarUser(string $url_avatar_user): self
    {
        $this->url_avatar_user = $url_avatar_user;

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
}
