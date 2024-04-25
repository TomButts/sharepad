<?php

namespace App\Entity;

use App\Entity\Note;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private int $id;

    #[Groups("note")]
    #[ORM\Column(type: "string", length: 180, unique: true)]
    private string $email;

    #[ORM\Column(type: "json")]
    private array $roles = [];

    #[ORM\Column(type: "string")]
    private string $password;

    #[ORM\OneToMany(targetEntity: Note::class, mappedBy: "owner")]
    private Collection $notes;

    #[ORM\ManyToMany(targetEntity: Note::class, mappedBy: "participants")]
    private Collection $sharedNotes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->sharedNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getUsername(): string
    {
        return (string) $this->email;
    }

    public function getUserIdentifier(): string
    {
        return $this->getUsername();
    }

    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getSalt(): ?string
    {
        return null;
    }

    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getNotes(): Collection
    {
        return $this->notes;
    }

    public function addNote(Note $note): self
    {
        if (!$this->notes->contains($note)) {
            $this->notes[] = $note;
            $note->setOwner($this);
        }

        return $this;
    }

    public function removeNote(Note $note): self
    {
        if ($this->notes->removeElement($note)) {
            // set the owning side to null (unless already changed)
            if ($note->getOwner() === $this) {
                $note->setOwner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Note>
     */
    public function getSharedNotes(): Collection
    {
        return $this->sharedNotes;
    }

    public function addSharedNote(Note $sharedNote): self
    {
        if (!$this->sharedNotes->contains($sharedNote)) {
            $this->sharedNotes[] = $sharedNote;
            $sharedNote->addParticipant($this);
        }

        return $this;
    }

    public function removeSharedNote(Note $sharedNote): self
    {
        if ($this->sharedNotes->removeElement($sharedNote)) {
            $sharedNote->removeParticipant($this);
        }

        return $this;
    }

    public function getAllEditableNotes(): array
    {
        $allNotes = array_merge(
            $this->getNotes()->toArray(),
            $this->getSharedNotes()->toArray()
        );

        return $allNotes;
    }

    public function hasAccessToNote(int $noteId): bool
    {
        $accessibleNotes = $this->getAllEditableNotes();

        foreach ($accessibleNotes as $note) {
            if ($note->getId() === $noteId) {
                return true;
            }
        }

        return false;
    }

    public function isNoteOwner(int $noteId): bool
    {
        foreach ($this->getNotes() as $ownedNote) {
            if ($ownedNote->getId() === $noteId) {
                return true;
            }
        }

        return false;
    }
}
