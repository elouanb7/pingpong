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
    private $goldenRacket;

    /**
     * @ORM\ManyToOne(targetEntity=Tournament::class, inversedBy="games")
     */
    private $tournament;

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

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreP1;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $scoreP2;

    /**
     * @ORM\OneToMany(targetEntity=Jouer::class, mappedBy="game")
     */
    private $jouers;

    public function __construct()
    {
        $this->jouers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getGoldenRacket(): ?GoldenRacket
    {
        return $this->goldenRacket;
    }

    public function setGoldenRacket(?GoldenRacket $goldenRacket): self
    {
        $this->goldenRacket = $goldenRacket;

        return $this;
    }

    public function getTournament(): ?Tournament
    {
        return $this->tournament;
    }

    public function setTournament(?Tournament $tournament): self
    {
        $this->tournament = $tournament;

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

    public function getScoreP1(): ?int
    {
        return $this->scoreP1;
    }

    public function setScoreP1(?int $scoreP1): self
    {
        $this->scoreP1 = $scoreP1;

        return $this;
    }

    public function getScoreP2(): ?int
    {
        return $this->scoreP2;
    }

    public function setScoreP2(?int $scoreP2): self
    {
        $this->scoreP2 = $scoreP2;

        return $this;
    }

    /**
     * @return Collection|Jouer[]
     */
    public function getJouers(): Collection
    {
        return $this->jouers;
    }

    public function addJouer(Jouer $jouer): self
    {
        if (!$this->jouers->contains($jouer)) {
            $this->jouers[] = $jouer;
            $jouer->setGame($this);
        }

        return $this;
    }

    public function removeJouer(Jouer $jouer): self
    {
        if ($this->jouers->removeElement($jouer)) {
            // set the owning side to null (unless already changed)
            if ($jouer->getGame() === $this) {
                $jouer->setGame(null);
            }
        }

        return $this;
    }


}
