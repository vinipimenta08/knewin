<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Session;

class TokenController extends Controller
{
    public function getHeaderAuth()
    {
        $head = [
            'Content-Type' => 'application/json',
            'Authorization' => 'Bearer '. $this->getCredentials()['access_token']
        ];
        return $head;
    }

    public function getCredentials()
    {
        return Session::get('credentials');
    }

    public function setCredentials($credentials)
    {
        Session::put('credentials', $credentials);
    }
}
