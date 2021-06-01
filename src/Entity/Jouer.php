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
    private $game;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="jouers")
     */
    private $player;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $playedAt;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isWinner;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $score;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGame(): ?Game
    {
        return $this->game;
    }

    public function setGame(?Game $game): self
    {
        $this->game = $game;

        return $this;
    }

    public function getPlayer(): ?Player
    {
        return $this->player;
    }

    public function setPlayer(?Player $player): self
    {
        $this->player = $player;

        return $this;
    }

    public function getPlayedAt(): ?\DateTimeInterface
    {
        return $this->playedAt;
    }

    public function setPlayedAt(?\DateTimeInterface $playedAt): self
    {
        $this->playedAt = $playedAt;

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

    public function getScore(): ?int
    {
        return $this->score;
    }

    public function setScore(?int $score): self
    {
        $this->score = $score;

        return $this;
    }
}
