<?php

namespace app;

use table\Product;
use table\Repo;

class Router
{
    public static function route(): void
    {
        $METHOD = $_SERVER['REQUEST_METHOD'];
        $URI = $_SERVER['REQUEST_URI'];

        header('Access-Control-Allow-Origin: *');
        switch ($METHOD) {
            case 'GET':
                self::get($URI);
                break;
            case 'POST':
                self::post($URI);
                break;
            case 'OPTIONS':
                header('Access-Control-Allow-Methods: DELETE, OPTIONS');
                break;
            case 'DELETE':
                self::delete($URI);
                break;
            default:
                http_response_code(405);
        }
    }

    private static function get($URI): void
    {
        $repo = new Repo(Product::class);

        if ($URI === '/')
            $value = json_encode($repo->getAll());
        elseif (self::isId($URI))
            $value = json_encode($repo->getById(substr($URI, 1)));
        else {
            http_response_code(404);
            return;
        }

        header('Content-Type: application/json');
        echo $value;
    }

    private static function post(string $URI): void
    {
        if ($URI === '/') {
            $repo = new Repo(Product::class);
            $product = json_decode(
                file_get_contents('php://input'),
                true);

            $repo->save($repo->createObject($product));
        } else {
            http_response_code(404);
        }
    }

    private static function delete(string $URI): void
    {
        if (self::isId($URI)) {
            $repo = new Repo(Product::class);
            $repo->deleteById(substr($URI, 1));
        } else {
            http_response_code(404);
        }
    }


    private static function isId($URI): bool
    {
        return preg_match('/^\/\d+$/', $URI);
    }
}