<?php

namespace app\table;

class DVD extends Product
{
    private ?int $size = null;

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