<?php

namespace App\Entity;

use App\Repository\GoldenRacketRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoldenRacketRepository::class)
 */
class GoldenRacket
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=GoldenRacketPlayers::class, mappedBy="GoldenRacket")
     */
    private $goldenRacketPlayers;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="GoldenRacket")
     */
    private $games;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    public function __construct()
    {
        $this->goldenRacketPlayers = new ArrayCollection();
        $this->games = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|GoldenRacketPlayers[]
     */
    public function getGoldenRacketPlayers(): Collection
    {
        return $this->goldenRacketPlayers;
    }

    public function addGoldenRacketPlayer(GoldenRacketPlayers $goldenRacketPlayer): self
    {
        if (!$this->goldenRacketPlayers->contains($goldenRacketPlayer)) {
            $this->goldenRacketPlayers[] = $goldenRacketPlayer;
            $goldenRacketPlayer->setGoldenRacket($this);
        }

        return $this;
    }

    public function removeGoldenRacketPlayer(GoldenRacketPlayers $goldenRacketPlayer): self
    {
        if ($this->goldenRacketPlayers->removeElement($goldenRacketPlayer)) {
            // set the owning side to null (unless already changed)
            if ($goldenRacketPlayer->getGoldenRacket() === $this) {
                $goldenRacketPlayer->setGoldenRacket(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Game[]
     */
    public function getGames(): Collection
    {
        return $this->games;
    }

    public function addGame(Game $game): self
    {
        if (!$this->games->contains($game)) {
            $this->games[] = $game;
            $game->setGoldenRacket($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getGoldenRacket() === $this) {
                $game->setGoldenRacket(null);
            }
        }

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(?\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getFinishedAt(): ?\DateTimeInterface
    {
        return $this->finishedAt;
    }

    public function setFinishedAt(?\DateTimeInterface $finishedAt): self
    {
        $this->finishedAt = $finishedAt;

        return $this;
    }
}
