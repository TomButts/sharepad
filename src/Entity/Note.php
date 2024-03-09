<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use DateTimeImmutable;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\SerializedName;

/**
 * @ORM\Entity(repositoryClass=NoteRepository::class)
 * @ORM\HasLifecycleCallbacks
 */
class Note
{
    /**
     * @Groups("note")
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("user_data")
     * @ORM\ManyToOne(targetEntity=User::class, inversedBy="notes")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id", nullable=false)
     */
    private $owner;

    /**
     * @Groups("note")
     * @ORM\Column(type="text", nullable=true)
     */
    private $body;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $created_at;

    /**
     * @ORM\Column(type="datetime_immutable")
     */
    private $updated_at;

    /**
     * @Groups("note")
     * @ORM\ManyToMany(targetEntity=User::class, inversedBy="sharedNotes")
     */
    private $participants;

    public function __construct()
    {
        $this->participants = new ArrayCollection();
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
    */
    public function updatedTimestamps(): void
    {
        $this->setUpdatedAt(new DateTimeImmutable());

        if (null === $this->getCreatedAt()) {
            $this->setCreatedAt(new DateTimeImmutable());
        }
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOwner(): ?User
    {
        return $this->owner;
    }

    public function setOwner(?User $user): self
    {
        $this->owner = $user;

        return $this;
    }

    public function getBody(): ?string
    {
        return $this->body;
    }

    public function setBody(?string $body): self
    {
        $this->body = $body;

        return $this;
    }

    public function getCreatedAt(): ?DateTimeImmutable
    {
        return $this->created_at;
    }

    /**
     * @SerializedName("created_at")
     * @Groups("note")
     */
    public function getCreatedAtFormatted(): ?string
    {
        if (null !== $this->created_at) {
            return $this->created_at->format('d-m-Y H:i:s');
        }

        return null;
    }

    public function setCreatedAt(DateTimeImmutable $created_at): self
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?DateTimeImmutable
    {
        return $this->updated_at;
    }

    /**
     * @SerializedName("updated_at")
     * @Groups("note")
     */
    public function getUpdatedAtFormatted(): ?string
    {
        if (null !== $this->updated_at) {
            return $this->updated_at->format('d-m-Y H:i:s');
        }

        return null;
    }

    public function setUpdatedAt(DateTimeImmutable $updated_at): self
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getParticipants(): Collection
    {
        return $this->participants;
    }

    public function addParticipant(User $participant): self
    {
        if (!$this->participants->contains($participant)) {
            $this->participants[] = $participant;
        }

        return $this;
    }

    public function removeParticipant(User $participant): self
    {
        $this->participants->removeElement($participant);

        return $this;
    }
}
