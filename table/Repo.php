<?php

namespace table;

use app\MySQL;
use ReflectionClass;

// Bruh, no generics in PHP? :(
class Repo
{
    private ReflectionClass $reflection;

    public function __construct(
        private string $class
    )
    {
        $this->reflection = new ReflectionClass($this->class);
        $this->createTable();
    }

    public function createTable(): void
    {
        $properties = $this->reflection->getProperties();
        $properties = array_filter($properties, function ($property) {
            return $property->getName() !== 'id';
        });
        $properties = array_map(function ($property) {
            switch ($property->getType()) {
                case 'int':
                    $value = 'INT';
                    break;
                case 'float':
                    $value = 'FLOAT';
                    break;
                default:
                    $value = 'VARCHAR(255)';
                    break;
            }

            return sprintf('`%s` %s', $property->getName(), $value);
        }, $properties);

        array_unshift($properties, '`id` SERIAL PRIMARY KEY');

        $query = sprintf('CREATE TABLE IF NOT EXISTS `%s` (%s);',
            strtolower($this->reflection->getShortName()),
            join(', ', $properties));

        MySQL::getInstance()->query($query);
    }

    public function save($object): void
    {
        $properties = $this->reflection->getProperties();
        $properties = array_filter($properties, function ($property) {
            return $property->getName() !== 'id';
        });
        $values = [];
        foreach ($properties as $property) {
            $property->setAccessible(true);
            $value = $property->getValue($object);
            if (!$value)
                $value = 'NULL';
            else
                $value = sprintf("'%s'", $value);

            $values[sprintf('`%s`', $property->getName())] = $value;
        }

        $query = sprintf('INSERT INTO `%s` (%s) VALUES (%s);',
            strtolower($this->reflection->getShortName()),
            join(', ', array_keys($values)),
            join(', ', array_values($values))
        );

        MySQL::getInstance()->query($query);
    }

    public function getAll(): array
    {
        $class = strtolower($this->reflection->getShortName());

        $query = sprintf('SELECT * FROM `%s`;', $class);
        $query = MySQL::getInstance()
            ->query($query)
            ->fetch_all(MYSQLI_ASSOC);

        $objects = [];

        foreach ($query as $value) {
            $objects[] = $this->createObject($value);
        }

        return $objects;
    }

    public function getById(int $id): object
    {
        $query = 'SELECT * FROM `%s` WHERE `id` = %d;';
        $query = sprintf($query,
            strtolower((new ReflectionClass($this->class))->getShortName()),
            $id);

        $query = MySQL::getInstance()->query($query)->fetch_assoc();

        return $this->createObject($query);
    }

    public function createObject(array $item): object
    {
        $object = $this->reflection->newInstanceWithoutConstructor();

        foreach ($item as $key => $value) {
            $property = $this->reflection->getProperty($key);
            $property->setAccessible(true);
            $property->setValue($object, $value);
        }

        return $object;
    }

    public function deleteById(int $id): void
    {
        $query = sprintf('DELETE FROM `%s` WHERE `id` = %d;',
            strtolower($this->reflection->getShortName()),
            $id);

        MySQL::getInstance()->query($query);
    }
}