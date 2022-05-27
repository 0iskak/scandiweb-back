<?php

namespace app\table;

class Book extends Product
{
    private ?float $weight = null;

    public function __construct()
    {
        parent::__construct(self::class);
    }

    public function jsonSerialize(): array
    {
        return get_object_vars($this);
    }
}