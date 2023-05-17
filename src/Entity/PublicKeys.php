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

    #[ORM\Column]
    private ?int $id_publickeys = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $key_publickeys = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdPublickeys(): ?int
    {
        return $this->id_publickeys;
    }

    public function setIdPublickeys(int $id_publickeys): self
    {
        $this->id_publickeys = $id_publickeys;

        return $this;
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
}
