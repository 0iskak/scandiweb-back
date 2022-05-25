<?php

namespace table;

use JsonSerializable;

/*
    For OOP you would need to demonstrate code
    structuring in meaningful classes that extend
    each other, so we would like to see an abstract
    class for the main product logic. Please take
    a look at the polymorphism provision.
*/

// idk how to implement top without bottom,
// so did one class for all

/*
    Avoid using conditional statements
    for handling differences in product types
*/

class Product implements JsonSerializable
{
    private ?int $id = null;
    private ?string $sku = null;
    private ?string $name = null;
    private ?float $price = null;
    private ?int $size = null;
    private ?int $height = null;
    private ?int $width = null;
    private ?int $length = null;
    private ?float $weight = null;

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }


    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getSize(): ?int
    {
        return $this->size;
    }

    public function setSize(int $size): void
    {
        $this->size = $size;
    }

    public function getHeight(): ?int
    {
        return $this->height;
    }

    public function setHeight(int $height): void
    {
        $this->height = $height;
    }

    public function getWidth(): ?int
    {
        return $this->width;
    }

    public function setWidth(int $width): void
    {
        $this->width = $width;
    }

    public function getLength(): ?int
    {
        return $this->length;
    }

    public function setLength(int $length): void
    {
        $this->length = $length;
    }

    public function getWeight(): ?float
    {
        return $this->weight;
    }

    public function setWeight(float $weight): void
    {
        $this->weight = $weight;
    }
}