<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
class Message
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private string $text ;

    #[ORM\Column]
    private DateTimeImmutable $createAt;

    #[ORM\ManyToOne(inversedBy: 'messages')]
    #[ORM\JoinColumn(nullable: false)]
    private Recipient $Recipient ;

    public function __construct(string $text,  Recipient $Recipient)
    {
        $this->text = $text;
        $this->createAt = new DateTimeImmutable();
        $this->Recipient = $Recipient;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function getCreateAt(): ?DateTimeImmutable
    {
        return $this->createAt;
    }

    public function getRecipient(): ?Recipient
    {
        return $this->Recipient;
    }
}
