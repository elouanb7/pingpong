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
}
