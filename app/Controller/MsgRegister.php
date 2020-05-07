<?php
declare (strict_types = 1);

namespace App\Controller;

/**
 *
 */
class MsgRegister
{
    public $msgList = [
        'CLIENT_VERSION'   => ['\App\Controller\Auth\AuthHandler', 'clientVersion'],
        'KEEP_ALIVE'       => ['\App\Controller\Auth\AuthHandler', 'keepAlive'],
        'NEW_ACCOUNT'      => ['\App\Controller\Auth\AuthHandler', 'newAccount'],
        'CHANGE_PASSWORD'  => ['\App\Controller\Auth\AuthHandler', 'changePassword'],
        'LOGIN'            => ['\App\Controller\Auth\AuthHandler', 'login'],
        'NEW_CHARACTER'    => ['\App\Controller\Auth\AuthHandler', 'newCharacter'],
        'DELETE_CHARACTER' => ['\App\Controller\Auth\AuthHandler', 'deleteCharacter'],
        'START_GAME'       => ['\App\Controller\Auth\AuthHandler', 'startGame'],

        'WALK'             => ['\App\Controller\World\Handler', 'walk'],
        'RUN'              => ['\App\Controller\World\Handler', 'run'],
        'TURN'             => ['\App\Controller\World\Handler', 'turn'],
        'LOG_OUT'          => ['\App\Controller\World\Handler', 'logOut'],
    ];
}
