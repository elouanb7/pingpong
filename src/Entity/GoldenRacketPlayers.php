<?php

namespace App\Entity;

use App\Repository\GoldenRacketPlayersRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
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
     * @ORM\OneToMany(targetEntity=Player::class, mappedBy="GoldenRacketPlayers")
     */
    private $players;

    /**
     * @ORM\ManyToOne(targetEntity=GoldenRacket::class, inversedBy="goldenRacketPlayers")
     */
    private $GoldenRacket;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $rank;

    public function __construct()
    {
        $this->players = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection|Player[]
     */
    public function getPlayers(): Collection
    {
        return $this->players;
    }

    public function addPlayer(Player $player): self
    {
        if (!$this->players->contains($player)) {
            $this->players[] = $player;
            $player->setGoldenRacketPlayers($this);
        }

        return $this;
    }

    public function removePlayer(Player $player): self
    {
        if ($this->players->removeElement($player)) {
            // set the owning side to null (unless already changed)
            if ($player->getGoldenRacketPlayers() === $this) {
                $player->setGoldenRacketPlayers(null);
            }
        }

        return $this;
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

    public function getRank(): ?int
    {
        return $this->rank;
    }

    public function setRank(?int $rank): self
    {
        $this->rank = $rank;

        return $this;
    }
}
