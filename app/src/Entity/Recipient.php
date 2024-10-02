<?php

namespace App\Entity;

use App\Repository\RecipientRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: RecipientRepository::class)]
class Recipient
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20, unique: true)]
    private string $identifier ;

    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'Recipient')]
    private Collection $messages;

    #[ORM\Column(length: 45)]
    private string $encryptionKey;

    public function __construct(string $identifier, string $encryptionKey)
    {
        $this->identifier = $identifier;
        $this->messages = new ArrayCollection();
        $this->encryptionKey = $encryptionKey;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function getEncryptionKey(): string
    {
        return $this->encryptionKey;
    }
}
