<?php

namespace app;

use app\table\Repo;

class Router
{
    public static function route(): int
    {
        $method = $_SERVER['REQUEST_METHOD'];
        $uri = $_SERVER['REQUEST_URI'];

        header('Access-Control-Allow-Origin: *');

        switch ($uri) {
            case '/':
                switch ($method) {
                    case 'GET':
                        header('Content-Type: application/json');
                        echo json_encode(Repo::getInstance()->getAll());
                        return 200;
                    case 'POST':
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
            default:
                if (preg_match('/^\/\d+$/', $uri)) {
                    switch ($method) {
                        case 'GET':
                            header('Content-Type: application/json');
                            echo json_encode(Repo::getInstance()
                                ->getById(substr($uri, 1)));
                            return 200;
                        case 'OPTIONS':
                            header('Access-Control-Allow-Methods: DELETE');
                            return 204;
                        case 'DELETE':
                            Repo::getInstance()->deleteById(substr($uri, 1));
                            return 204;
                        default:
                            return 405;
                    }
                }

                return 404;
        }
    }
}