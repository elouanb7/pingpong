<?php

namespace App\Entity;

use App\Repository\PlayerRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=PlayerRepository::class)
 * @UniqueEntity("email",message="Cet email est déja utilisé")
 */
class Player implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=TournamentPlayers::class, inversedBy="players")
     */
    private $tournamentPlayers;

    /**
     * @ORM\ManyToOne(targetEntity=GoldenRacketPlayers::class, inversedBy="players")
     */
    private $goldenRacketPlayers;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchScore;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     * @Assert\Email
     */
    private $email;

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    protected $password;

    /**
     * @Assert\Length(max=4096)
     */
    private $plainPassword;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchPlayed;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchLost;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $matchAveragePointsOf11;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $matchAveragePointsOf21;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tournamentWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $tournamentPlayed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $tournamentAveragePlacement;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goldenRacketWon;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $goldenRacketPlayed;

    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $goldenRacketAveragePlacement;

    /**
     * @ORM\OneToMany(targetEntity=Jouer::class, mappedBy="Player")
     */
    private $jouers;

    public function __construct()
    {
        $this->games = new ArrayCollection();
        $this->jouers = new ArrayCollection();
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTournamentPlayers(): ?TournamentPlayers
    {
        return $this->tournamentPlayers;
    }

    public function setTournamentPlayers(?TournamentPlayers $tournamentPlayers): self
    {
        $this->tournamentPlayers = $tournamentPlayers;

        return $this;
    }

    public function getGoldenRacketPlayers(): ?GoldenRacketPlayers
    {
        return $this->goldenRacketPlayers;
    }

    public function setGoldenRacketPlayers(?GoldenRacketPlayers $goldenRacketPlayers): self
    {
        $this->goldenRacketPlayers = $goldenRacketPlayers;

        return $this;
    }

    public function getMatchScore(): ?int
    {
        return $this->matchScore;
    }

    public function setMatchScore(?int $matchScore): self
    {
        $this->matchScore = $matchScore;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getFullName(): ?string
    {
       return $this->firstName . ' ' . $this->lastName;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(?string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

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

    public function getMatchPlayed(): ?int
    {
        return $this->matchPlayed;
    }

    public function setMatchPlayed(?int $matchPlayed): self
    {
        $this->matchPlayed = $matchPlayed;

        return $this;
    }

    public function getMatchWon(): ?int
    {
        return $this->matchWon;
    }

    public function setMatchWon(?int $matchWon): self
    {
        $this->matchWon = $matchWon;

        return $this;
    }

    public function getMatchLost(): ?int
    {
        return $this->matchLost;
    }

    public function setMatchLost(?int $matchLost): self
    {
        $this->matchLost = $matchLost;

        return $this;
    }

    public function getMatchAveragePointsOf11(): ?int
    {
        return $this->matchAveragePointsOf11;
    }

    public function setMatchAveragePointsOf11(?int $matchAveragePointsOf11): self
    {
        $this->matchAveragePointsOf11 = $matchAveragePointsOf11;

        return $this;
    }

    public function getMatchAveragePointsOf21(): ?float
    {
        return $this->matchAveragePointsOf21;
    }

    public function setMatchAveragePointsOf21(?float $matchAveragePointsOf21): self
    {
        $this->matchAveragePointsOf21 = $matchAveragePointsOf21;

        return $this;
    }

    public function getTournamentWon(): ?int
    {
        return $this->tournamentWon;
    }

    public function setTournamentWon(?int $tournamentWon): self
    {
        $this->tournamentWon = $tournamentWon;

        return $this;
    }

    public function getTournamentPlayed(): ?int
    {
        return $this->tournamentPlayed;
    }

    public function setTournamentPlayed(?int $tournamentPlayed): self
    {
        $this->tournamentPlayed = $tournamentPlayed;

        return $this;
    }

    public function getTournamentAveragePlacement(): ?float
    {
        return $this->tournamentAveragePlacement;
    }

    public function setTournamentAveragePlacement(?float $tournamentAveragePlacement): self
    {
        $this->tournamentAveragePlacement = $tournamentAveragePlacement;

        return $this;
    }

    public function getGoldenRacketWon(): ?int
    {
        return $this->goldenRacketWon;
    }

    public function setGoldenRacketWon(?int $goldenRacketWon): self
    {
        $this->goldenRacketWon = $goldenRacketWon;

        return $this;
    }

    public function getGoldenRacketPlayed(): ?int
    {
        return $this->goldenRacketPlayed;
    }

    public function setGoldenRacketPlayed(?int $goldenRacketPlayed): self
    {
        $this->goldenRacketPlayed = $goldenRacketPlayed;

        return $this;
    }

    public function getGoldenRacketAveragePlacement(): ?float
    {
        return $this->goldenRacketAveragePlacement;
    }

    public function setGoldenRacketAveragePlacement(?float $goldenRacketAveragePlacement): self
    {
        $this->goldenRacketAveragePlacement = $goldenRacketAveragePlacement;

        return $this;
    }

    public function getRoles()
    {
        // TODO: Implement getRoles() method.
    }

    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    public function getUsername()
    {
        // TODO: Implement getUsername() method.
    }

    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
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
            $jouer->setPlayer($this);
        }

        return $this;
    }

    public function removeJouer(Jouer $jouer): self
    {
        if ($this->jouers->removeElement($jouer)) {
            // set the owning side to null (unless already changed)
            if ($jouer->getPlayer() === $this) {
                $jouer->setPlayer(null);
            }
        }

        return $this;
    }
}
