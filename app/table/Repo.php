<?php

namespace app\table;

use app\MySQL;
use ReflectionClass;
use ReflectionException;

class Repo
{
    private static Repo $instance;

    private ReflectionClass $reflection;

    private const
        ID = 'SERIAL PRIMARY KEY',
        INT = 'INT',
        FLOAT = 'FLOAT',
        STRING = 'VARCHAR(255)';
    
    private const COLUMN_SEPARATOR = ', ';

    public function __construct()
    {
        $this->reflection =
            new ReflectionClass(Product::class);

        $columns = $this->getColumns($this->reflection);

        array_unshift($columns, '`id` ' . self::ID);

        $children = Product::getChildren();
        foreach ($children as $child)
            foreach ($this->getColumns(new ReflectionClass($child))
                     as $column)
                $columns[] = $column;

        $columns = array_unique($columns);

        MySQL::getInstance()->createTable(
            $this->reflection->getShortName(),
            join(self::COLUMN_SEPARATOR, $columns)
        );
    }

    private function getColumns(
        ReflectionClass $reflection
    ): array
    {
        $properties = [];

        foreach ($reflection->getProperties() as $property)
            if (str_starts_with($property->getType(), '?'))
                $properties[] = $property;

        for ($i = count($properties) - 1; $i >= 0; $i--) {
            $property = $properties[$i];

            switch (substr($property->getType(), 1)) {
                case 'int':
                    $value = self::INT;
                    break;
                case 'float':
                    $value = self::FLOAT;
                    break;
                default:
                    $value = self::STRING;
            }

            $properties[$i] = sprintf('`%s` %s',
                $property->getName(), $value);
        }

        return $properties;
    }

    public static function getInstance(): Repo
    {
        return self::$instance;
    }

    public static function setInstance(Repo $instance): void
    {
        self::$instance = $instance;
    }

    public function getAll(): array
    {
        $products = MySQL::getInstance()->getAll(
            $this->reflection->getShortName()
        );

        for ($i = count($products) - 1; $i >= 0; $i--)
            $products[$i] = $this->createObject($products[$i]);

        return $products;
    }

    public function createObject(array $product): Product
    {
        $class = new (__NAMESPACE__ . '\\' . $product['type'])();

        foreach ($product as $key => $value) {
            try {
                $reflection = new ReflectionClass($class);
                $property = $reflection->getProperty($key);
                $property->setAccessible(true);
                $property->setValue($class, $value);
            } catch (ReflectionException) {
            }
        }

        return $class;
    }

    public function save(Product $object): void
    {
        $array = $object->jsonSerialize();

        $keys = [];
        $values = [];

        foreach ($array as $key => $value) {
            $keys[] = $key;
            $values[] = $value ? sprintf("'%s'", $value) : 'NULL';
        }

        MySQL::getInstance()->insert(
            $this->reflection->getShortName(),
            join(self::COLUMN_SEPARATOR, $keys),
            join(self::COLUMN_SEPARATOR, $values)
        );
    }

    public function getById(int $id): Product
    {
        $product = MySQL::getInstance()->getById(
            $this->reflection->getShortName(),
            $id
        );

        return $this->createObject($product);
    }

    public function deleteById(int $id): void
    {
        MySQL::getInstance()->deleteById(
            $this->reflection->getShortName(),
            $id
        );
    }
}