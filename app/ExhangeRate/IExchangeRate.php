<?php

namespace App\ExchangeRate;

use App\Models\User;

interface IExchangeRate {
    public function AddNewUser(User $user);
    public function AuthenticateUser($email, $access_key);
}