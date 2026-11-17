<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Schema\DefaultExpression\CurrentTimestamp;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: 'orders')]
#[ORM\UniqueConstraint(name: 'order_number', columns: ['order_number'])]
#[ORM\Index(name: 'user_id', columns: ['user_id'])]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'order_number', type: Types::STRING, length: 50)]
    private string $orderNumber;

    #[ORM\Column(name: 'subtotal', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $subtotal;

    #[ORM\Column(name: 'delivery_fee', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $deliveryFee;

    #[ORM\Column(name: 'total_amount', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $totalAmount;

    #[ORM\Column(name: 'payment_method', type: Types::STRING, length: 50)]
    private string $paymentMethod;

    #[ORM\Column(name: 'gcash_reference', type: Types::STRING, length: 50, nullable: true)]
    private ?string $gcashReference = null;

    #[ORM\Column(name: 'gcash_number', type: Types::STRING, length: 20, nullable: true)]
    private ?string $gcashNumber = null;

    #[ORM\Column(name: 'payment_status', type: Types::STRING, nullable: true, options: ['default' => 'pending'])]
    private ?string $paymentStatus = 'pending';

    #[ORM\Column(name: 'order_status', type: Types::STRING)]
    private string $orderStatus;

    #[ORM\Column(name: 'order_address', type: Types::TEXT, length: 65535, nullable: true)]
    private ?string $orderAddress = null;

    #[ORM\Column(name: 'order_type', type: Types::STRING, nullable: true, options: ['default' => 'delivery'])]
    private ?string $orderType = 'delivery';

    #[ORM\Column(name: 'created_at', type: Types::DATETIME_IMMUTABLE, options: ['default' => new CurrentTimestamp()])]
    private \DateTimeImmutable $createdAt;

    #[ORM\ManyToOne(targetEntity: User::class, inversedBy: 'orders')]
    #[ORM\JoinColumn(name: 'user_id', referencedColumnName: 'id', nullable: false)]
    private ?User $user = null;

    /** @var Collection<int, OrderItem> */
    #[ORM\OneToMany(targetEntity: OrderItem::class, mappedBy: 'order', cascade: ['remove'])]
    private Collection $orderItems;

    public function __construct()
    {
        $this->orderItems = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    public function setOrderNumber(string $orderNumber): static
    {
        $this->orderNumber = $orderNumber;

        return $this;
    }

    public function getSubtotal(): string
    {
        return $this->subtotal;
    }

    public function setSubtotal(string $subtotal): static
    {
        $this->subtotal = $subtotal;

        return $this;
    }

    public function getDeliveryFee(): string
    {
        return $this->deliveryFee;
    }

    public function setDeliveryFee(string $deliveryFee): static
    {
        $this->deliveryFee = $deliveryFee;

        return $this;
    }

    public function getTotalAmount(): string
    {
        return $this->totalAmount;
    }

    public function setTotalAmount(string $totalAmount): static
    {
        $this->totalAmount = $totalAmount;

        return $this;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function setPaymentMethod(string $paymentMethod): static
    {
        $this->paymentMethod = $paymentMethod;

        return $this;
    }

    public function getGcashReference(): ?string
    {
        return $this->gcashReference;
    }

    public function setGcashReference(?string $gcashReference): static
    {
        $this->gcashReference = $gcashReference;

        return $this;
    }

    public function getGcashNumber(): ?string
    {
        return $this->gcashNumber;
    }

    public function setGcashNumber(?string $gcashNumber): static
    {
        $this->gcashNumber = $gcashNumber;

        return $this;
    }

    public function getPaymentStatus(): ?string
    {
        return $this->paymentStatus;
    }

    public function setPaymentStatus(?string $paymentStatus): static
    {
        $this->paymentStatus = $paymentStatus;

        return $this;
    }

    public function getOrderStatus(): string
    {
        return $this->orderStatus;
    }

    public function setOrderStatus(string $orderStatus): static
    {
        $this->orderStatus = $orderStatus;

        return $this;
    }

    public function getOrderAddress(): ?string
    {
        return $this->orderAddress;
    }

    public function setOrderAddress(?string $orderAddress): static
    {
        $this->orderAddress = $orderAddress;

        return $this;
    }

    public function getOrderType(): ?string
    {
        return $this->orderType;
    }

    public function setOrderType(?string $orderType): static
    {
        $this->orderType = $orderType;

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

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Collection<int, OrderItem>
     */
    public function getOrderItems(): Collection
    {
        return $this->orderItems;
    }

    public function addOrderItem(OrderItem $orderItem): static
    {
        if (!$this->orderItems->contains($orderItem)) {
            $this->orderItems->add($orderItem);
            $orderItem->setOrder($this);
        }

        return $this;
    }

    public function removeOrderItem(OrderItem $orderItem): static
    {
        if ($this->orderItems->removeElement($orderItem) && $this === $orderItem->getOrder()) {
            $orderItem->setOrder(null);
        }

        return $this;
    }
}