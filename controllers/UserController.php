<?php
namespace controllers;

use models\User;

class UserController{
    public function hello(){
        $user = new User;
        $name = $user->getName();

        view('users.hello',[
            'name'=> $name
        ]);
    }

    public function world(){
        echo 'world';
    }



















}


