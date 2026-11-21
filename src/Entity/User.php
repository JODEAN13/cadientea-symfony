<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: 'users')]
#[ORM\UniqueConstraint(name: 'email', columns: ['email'])]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER, options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\Column(name: 'first_name', type: Types::STRING, length: 100)]
    private string $firstName;

    #[ORM\Column(name: 'last_name', type: Types::STRING, length: 100)]
    private string $lastName;

    #[ORM\Column(name: 'email', type: Types::STRING, length: 150)]
    private string $email;

    #[ORM\Column(name: 'password', type: Types::STRING, length: 255)]
    private string $password;

    #[ORM\Column(name: 'phone', type: Types::STRING, length: 20, nullable: true)]
    private ?string $phone = null;

    #[ORM\Column(name: 'address', type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $address = null;

    #[ORM\Column(name: 'role', type: Types::STRING)]
    private string $role;

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => 'CURRENT_TIMESTAMP'])]
    private \DateTimeImmutable $createdAt;

    /** @var Collection<int, Message> */
    #[ORM\OneToMany(targetEntity: Message::class, mappedBy: 'user')]
    private Collection $messages;

    /** @var Collection<int, Order> */
    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'user', cascade: ['remove'])]
    private Collection $orders;

    /** @var Collection<int, Product> */
    #[ORM\OneToMany(targetEntity: Product::class, mappedBy: 'createdBy')]
    private Collection $createdProducts;

    public function __construct()
    {
        $this->messages = new ArrayCollection();
        $this->orders = new ArrayCollection();
        $this->createdProducts = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): static
    {
        $this->firstName = $firstName;
        return $this;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): static
    {
        $this->lastName = $lastName;
        return $this;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;
        return $this;
    }

    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    public function getRoles(): array
    {
        $role = strtoupper($this->role);
        if (!str_starts_with($role, 'ROLE_')) {
            $role = 'ROLE_' . $role;
        }
        
        $roles = [$role];
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function getRole(): string
    {
        return $this->role;
    }

    public function setRole(string $role): static
    {
        $this->role = $role;
        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;
        return $this;
    }

    public function eraseCredentials(): void
    {
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): static
    {
        $this->phone = $phone;
        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): static
    {
        $this->address = $address;
        return $this;
    }

    public function getCreatedAt(): \DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /** @return Collection<int, Message> */
    public function getMessages(): Collection
    {
        return $this->messages;
    }

    public function addMessage(Message $message): static
    {
        if (!$this->messages->contains($message)) {
            $this->messages->add($message);
            $message->setUser($this);
        }
        return $this;
    }

    public function removeMessage(Message $message): static
    {
        if ($this->messages->removeElement($message) && $this === $message->getUser()) {
            $message->setUser(null);
        }
        return $this;
    }

    /** @return Collection<int, Order> */
    public function getOrders(): Collection
    {
        return $this->orders;
    }

    public function addOrder(Order $order): static
    {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setUser($this);
        }
        return $this;
    }

    public function removeOrder(Order $order): static
    {
        if ($this->orders->removeElement($order) && $this === $order->getUser()) {
            $order->setUser(null);
        }
        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getCreatedProducts(): Collection
    {
        return $this->createdProducts;
    }

    public function addCreatedProduct(Product $createdProduct): static
    {
        if (!$this->createdProducts->contains($createdProduct)) {
            $this->createdProducts->add($createdProduct);
            $createdProduct->setCreatedBy($this);
        }
        return $this;
    }

    public function removeCreatedProduct(Product $createdProduct): static
    {
        if ($this->createdProducts->removeElement($createdProduct)) {
            if ($createdProduct->getCreatedBy() === $this) {
                $createdProduct->setCreatedBy(null);
            }
        }
        return $this;
    }

    public function __toString(): string
    {
        $first = method_exists($this, "getFirstName") ? (string) $this->getFirstName() : "";
        $last = method_exists($this, "getLastName") ? (string) $this->getLastName() : "";
        $fullName = trim($first . " " . $last);
        if ($fullName !== "") {
            return $fullName;
        }
        $email = method_exists($this, "getEmail") ? (string) $this->getEmail() : "";
        if ($email !== "") {
            return $email;
        }
        $id = method_exists($this, "getId") ? $this->getId() : null;
        return "User #" . ($id ?? "");
    }
}