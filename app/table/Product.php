<?php

namespace app\table;

use JsonSerializable;
use ReflectionClass;

abstract class Product implements JsonSerializable
{
    protected int $id;
    protected ?string $sku = null;
    protected ?string $name = null;
    protected ?float $price = null;
    protected ?string $type;

    public function __construct(string $class)
    {
        $reflection = new ReflectionClass($class);
        $this->type = $reflection->getShortName();
    }

    public abstract function jsonSerialize(): array;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getSku(): ?string
    {
        return $this->sku;
    }

    public function setSku(?string $sku): void
    {
        $this->sku = $sku;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): void
    {
        $this->price = $price;
    }

    public static function getChildren(): array
    {
        $children = [];
        foreach (get_declared_classes() as $class)
            if (is_subclass_of($class, Product::class))
                $children[] = $class;

        return $children;
    }
}