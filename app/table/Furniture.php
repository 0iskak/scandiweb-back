<?php

namespace app\table;

class Furniture extends Product
{
    private ?int $width = null;
    private ?int $height = null;
    private ?int $length = null;

    public function __construct()
    {
        parent::__construct(self::class);
    }

    public function jsonSerialize(): array
    {
        return array_merge(
            parent::jsonSerialize(),
            get_object_vars($this)
        );
    }
}