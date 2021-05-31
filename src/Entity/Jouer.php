<?php

namespace App\Entity;

use App\Repository\JouerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JouerRepository::class)
 */
class Jouer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Game::class, inversedBy="jouers")
     */
    private $Game;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="jouers")
     */
    private $Player;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $PlayedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWinner;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->Game;
    }

    public function setGame(?Game $Game): self
    {
        $this->Game = $Game;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->Player;
    }

    public function setPlayer(?Player $Player): self
    {
        $this->Player = $Player;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeInterface
    {
        return $this->PlayedAt;
    }

    public function setPlayedAt(?\DateTimeInterface $PlayedAt): self
    {
        $this->PlayedAt = $PlayedAt;

        return $this;
    }

    public function getIsWinner(): ?bool
    {
        return $this->isWinner;
    }

    public function setIsWinner(?bool $isWinner): self
    {
        $this->isWinner = $isWinner;

        return $this;
    }
}
