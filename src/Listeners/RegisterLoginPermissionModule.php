<?php

namespace Quill\Login\Listeners;

class RegisterLoginPermissionModule
{ 
    public function handle()
    {
        return [
            'Login' => [
                'view'
            ]
        ];
    }
}
