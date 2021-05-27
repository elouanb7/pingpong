<?php

namespace App\Entity;

use App\Repository\GameRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GameRepository::class)
 */
class Game
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=GoldenRacket::class, inversedBy="games")
     */
    private $GoldenRacket;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="games")
     */
    private $Tournament;

    /**
     * @ORM\ManyToMany(targetEntity=Player::class, inversedBy="games")
     */
    private $Players;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $playedAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $round;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isTournament;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $isGoldenRacket;

    public function __construct()
    {
        $this->Players = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoldenRacket(): ?GoldenRacket
    {
        return $this->GoldenRacket;
    }

    public function setGoldenRacket(?GoldenRacket $GoldenRacket): self
    {
        $this->GoldenRacket = $GoldenRacket;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->Tournament;
    }

    public function setTournament(?Tournament $Tournament): self
    {
        $this->Tournament = $Tournament;

        return $this;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->Players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->Players->contains($player)) {
            $this->Players[] = $player;
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        $this->Players->removeElement($player);

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

    public function getRound(): ?int
    {
        return $this->round;
    }

    public function setRound(?int $round): self
    {
        $this->round = $round;

        return $this;
    }

    public function getIsTournament(): ?bool
    {
        return $this->isTournament;
    }

    public function setIsTournament(?bool $isTournament): self
    {
        $this->isTournament = $isTournament;

        return $this;
    }

    public function getIsGoldenRacket(): ?bool
    {
        return $this->isGoldenRacket;
    }

    public function setIsGoldenRacket(?bool $isGoldenRacket): self
    {
        $this->isGoldenRacket = $isGoldenRacket;

        return $this;
    }

}
