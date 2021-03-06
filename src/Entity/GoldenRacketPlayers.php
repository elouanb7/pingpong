<?php

namespace App\Entity;

use App\Repository\GoldenRacketPlayersRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=GoldenRacketPlayersRepository::class)
 */
class GoldenRacketPlayers
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    /**
     * @ORM\ManyToOne(targetEntity=Player::class, inversedBy="goldenRacketPlayers")
     */
    private $player;

    /**
     * @ORM\ManyToOne(targetEntity=GoldenRacket::class, inversedBy="goldenRacketPlayers")
     */
    private $goldenRacket;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $nbGames;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $pointsAverage;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $ratioWL;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): self
    {
        $this->rank = $rank;

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

    public function getGoldenRacket(): ?GoldenRacket
    {
        return $this->goldenRacket;
    }

    public function setGoldenRacket(?GoldenRacket $goldenRacket): self
    {
        $this->goldenRacket = $goldenRacket;

        return $this;
    }

    public function getNbGames(): ?int
    {
        return $this->nbGames;
    }

    public function setNbGames(?int $nbGames): self
    {
        $this->nbGames = $nbGames;

        return $this;
    }

    public function getPointsAverage(): ?float
    {
        return $this->pointsAverage;
    }

    public function setPointsAverage(?float $pointsAverage): self
    {
        $this->pointsAverage = $pointsAverage;

        return $this;
    }

    public function getRatioWL(): ?float
    {
        return $this->ratioWL;
    }

    public function setRatioWL(?float $ratioWL): self
    {
        $this->ratioWL = $ratioWL;

        return $this;
    }
}
