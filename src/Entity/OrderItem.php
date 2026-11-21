<?php

declare(strict_types=1);

namespace App\Entity;

use App\Repository\OrderItemRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderItemRepository::class)]
#[ORM\Table(name: 'order_items')]
#[ORM\Index(name: 'order_id', columns: ['order_id'])]
#[ORM\Index(name: 'product_id', columns: ['product_id'])]
class OrderItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[ORM\Column(name: 'id', type: Types::INTEGER)]
    private ?int $id = null;

    #[ORM\Column(name: 'product_name', type: Types::STRING, length: 150)]
    private string $productName;

    #[ORM\Column(name: 'size', type: Types::STRING, length: 50)]
    private string $size;

    #[ORM\Column(name: 'quantity', type: Types::INTEGER)]
    private int $quantity;

    #[ORM\Column(name: 'unit_price', type: Types::DECIMAL, precision: 10, scale: 2)]
    private string $unitPrice;

    #[ORM\Column(name: 'subtotal', type: Types::DECIMAL, precision: 10, scale: 2, options: ['default' => '0.00'])]
    private string $subtotal = '0.00';

    #[ORM\ManyToOne(targetEntity: Order::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'order_id', referencedColumnName: 'id', nullable: false)]
    private ?Order $order = null;

    #[ORM\ManyToOne(targetEntity: Product::class, inversedBy: 'orderItems')]
    #[ORM\JoinColumn(name: 'product_id', referencedColumnName: 'id', nullable: false)]
    private ?Product $product = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getProductName(): string
    {
        return $this->productName;
    }

    public function setProductName(string $productName): static
    {
        $this->productName = $productName;

        return $this;
    }

    public function getSize(): string
    {
        return $this->size;
    }

    public function setSize(string $size): static
    {
        $this->size = $size;

        return $this;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): static
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getUnitPrice(): string
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(string $unitPrice): static
    {
        $this->unitPrice = $unitPrice;

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

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    public function setOrder(?Order $order): static
    {
        $this->order = $order;

        return $this;
    }

    public function getProduct(): ?Product
    {
        return $this->product;
    }

    public function setProduct(?Product $product): static
    {
        $this->product = $product;

        return $this;
    }

    public function __toString(): string
    {
        $qty = method_exists($this, "getQuantity") ? $this->getQuantity() : 1;
        $name = method_exists($this, "getProductName") ? $this->getProductName() : "Item";
        $size = method_exists($this, "getSize") ? trim((string) $this->getSize()) : "";
        $subtotal = method_exists($this, "getSubtotal") ? (string) $this->getSubtotal() : "";
        $sizePart = $size !== "" ? " ({$size})" : "";
        $pricePart = $subtotal !== "" ? " — ₱{$subtotal}" : "";
        return "{$qty}x {$name}{$sizePart}{$pricePart}";
    }
}