<?php


namespace Modules\Auth\Services;


 abstract class AuthService
{
     abstract public function register($allRequest,$item,$type);
     abstract public function updateProfile($allRequest,$item);
}
