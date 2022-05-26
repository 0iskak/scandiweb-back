<?php

namespace app\table;

use app\MySQL;
use ReflectionClass;
use ReflectionException;

class Repo
{
    private static Repo $instance;

    private ReflectionClass $reflection;

    public function __construct()
    {
        $this->reflection =
            new ReflectionClass(Product::class);

        $columns = $this->getColumns($this->reflection);

        array_unshift($columns, '`id` SERIAL PRIMARY KEY');

        $children = array_filter(get_declared_classes(),
            function ($class) {
                return is_subclass_of($class, Product::class);
            });
        foreach ($children as $child)
            $columns = array_merge(
                $columns,
                $this->getColumns(new ReflectionClass($child))
            );

        $columns = array_unique($columns);
        $query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (%s);',
            $this->reflection->getShortName(),
            implode(', ', $columns));

        MySQL::getInstance()->query($query);
    }

    private function getColumns(
        ReflectionClass $reflection
    ): array
    {
        $properties = array_filter(
            $reflection->getProperties(),
            function ($property) {
                return str_starts_with($property->getType(), '?');
            });

        return array_map(function ($property) {
            switch (substr($property->getType(), 1)) {
                case 'int':
                    $value = 'INT';
                    break;
                case 'float':
                    $value = 'FLOAT';
                    break;
                default:
                    $value = 'VARCHAR(255)';
            }

            return sprintf('`%s` %s',
                $property->getName(),
                $value);
        }, $properties);
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
        $query = sprintf('SELECT * FROM `%s`;',
            $this->reflection->getShortName());

        $result = MySQL::getInstance()->query($query);

        return array_map(function ($product) {
            return $this->createObject($product);
        }, $result->fetch_all(MYSQLI_ASSOC));
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

        $query = sprintf('INSERT INTO `%s` (%s) VALUES (%s);',
            $this->reflection->getShortName(),
            implode(', ', array_keys($array)),
            implode(', ', array_map(function ($value) {
                return $value ? sprintf("'%s'", $value) : 'NULL';
            }, $array)));

        MySQL::getInstance()->query($query);
    }

    public function getById(int $id): Product
    {
        $query = sprintf('SELECT * FROM `%s` WHERE `id` = %d;',
            $this->reflection->getShortName(),
            $id);

        $result = MySQL::getInstance()->query($query);
        return $this->createObject($result->fetch_assoc());
    }

    public function deleteById(int $id): void
    {
        $query = sprintf('DELETE FROM `%s` WHERE `id` = %d;',
            $this->reflection->getShortName(),
            $id);

        MySQL::getInstance()->query($query);
    }
}