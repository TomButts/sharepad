<?php

namespace App\Entity;

use App\Entity\Note;
use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @Groups("note")
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Note::class, mappedBy="owner")
     */
    private $notes;

    /**
     * @ORM\ManyToMany(targetEntity=Note::class, mappedBy="participants")
     */
    private $sharedNotes;

    public function __construct()
    {
        $this->notes = new ArrayCollection();
        $this->sharedNotes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
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

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Note>
     */
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
