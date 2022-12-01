<?php

namespace App\Http\Controllers;

use Exception;
use GuzzleHttp\Client;
use \GuzzleHttp\Psr7\Request as RequestGuzzle;

class LibraryController extends Controller
{

    public static function responseApi($data = [], $message = '', $error = 0): Array
    {
        return [
            'error' => $error,
            'data' => $data,
            'message' => $message
        ];
    }

    public static function requestAsync($method = 'GET', $route, $body = null, $head = null)
    {
        try {
            if (!$head) {
                $tokenController = new TokenController;
                $head = $tokenController->getheaderAuth();
            }
            $client = new Client();
            if ($body) {
                $request = new RequestGuzzle($method, env('APP_URL') . $route, $head, json_encode($body));
                $promise = $client->sendAsync($request)->then(function ($response) {
                    return json_decode($response->getBody()->getContents(),true);
                });
            } else {
                $request = new RequestGuzzle($method, env('APP_URL') . $route, $head);
                $promise = $client->sendAsync($request)->then(function ($response) {
                    return json_decode($response->getBody()->getContents(),true);
                });
            }
            $result = $promise->wait();
            if (isset($result['error']) && $result['error']) {
                throw new Exception();
            }
            return $result;
        } catch (Exception $e) {
            throw $e;
        }

    }
}
