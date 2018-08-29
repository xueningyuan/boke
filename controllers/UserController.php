<?php
namespace controllers;

use models\User;

class UserControllers{
    public function hello(){
        $user = new User;
        $name = $user->getName();

        view('users.hello',[
            'name'=> $name
        ]);
    }
}