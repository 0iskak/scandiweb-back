<?php

namespace app;

use app\table\Repo;

class Router
{
    private const INDEX = '/';

    private const
        GET = 'GET',
        POST = 'POST',
        OPTIONS = 'OPTIONS',
        DELETE = 'DELETE';

    public static function route(): int
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        header('Access-Control-Allow-Origin: *');

        switch ($uri) {
            case self::INDEX:
                return self::index($method);
            default:
                return self::isId($uri) ?
                    self::product($method, substr($uri, 1)) :
                    404;
        }
    }

    private static function product(string $method, int $id): int
    {
        switch ($method) {
            case self::GET:
                header('Content-Type: application/json');
                echo json_encode(Repo::getInstance()
                    ->getById($id));
                return 200;
            case self::OPTIONS:
                header('Access-Control-Allow-Methods: DELETE');
                return 204;
            case self::DELETE:
                Repo::getInstance()
                    ->deleteById($id);
                return 204;
            default:
                return 405;
        }
    }

    private static function index(mixed $method): int
    {
        switch ($method) {
            case self::GET:
                header('Content-Type: application/json');
                echo json_encode(Repo::getInstance()->getAll());
                return 200;
            case self::POST:
                $object = Repo::getInstance()->createObject(
                    json_decode(file_get_contents(
                        'php://input'),
                        true)
                );

                Repo::getInstance()->save($object);
                return 201;
            default:
                return 405;
        }
    }

    private static function isId(string $uri): bool
    {
        return preg_match('/^\/\d+$/', $uri);
    }
}