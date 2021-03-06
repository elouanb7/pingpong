<?php

namespace App\Entity;

use App\Repository\TournamentRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=TournamentRepository::class)
 */
class Tournament
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\OneToMany(targetEntity=Game::class, mappedBy="tournament")
     */
    private $games;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $name;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $finishedAt;

    /**
     * @ORM\OneToMany(targetEntity=TournamentPlayers::class, mappedBy="tournament")
     */
    private $tournamentPlayers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $round;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->tournamentPlayers = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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
            $game->setTournament($this);
        }

        return $this;
    }

    public function removeGame(Game $game): self
    {
        if ($this->games->removeElement($game)) {
            // set the owning side to null (unless already changed)
            if ($game->getTournament() === $this) {
                $game->setTournament(null);
            }
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

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

    /**
     * @return Collection|TournamentPlayers[]
     */
    public function getTournamentPlayers(): Collection
    {
        return $this->tournamentPlayers;
    }

    public function addTournamentPlayer(TournamentPlayers $tournamentPlayer): self
    {
        if (!$this->tournamentPlayers->contains($tournamentPlayer)) {
            $this->tournamentPlayers[] = $tournamentPlayer;
            $tournamentPlayer->setTournament($this);
        }

        return $this;
    }

    public function removeTournamentPlayer(TournamentPlayers $tournamentPlayer): self
    {
        if ($this->tournamentPlayers->removeElement($tournamentPlayer)) {
            // set the owning side to null (unless already changed)
            if ($tournamentPlayer->getTournament() === $this) {
                $tournamentPlayer->setTournament(null);
            }
        }

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
}
