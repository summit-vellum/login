<?php

namespace Quill\Login\Models;

use Illuminate\Support\Str;
use Quill\Login\Events\LoginCreating;
use Quill\Login\Events\LoginCreated;
use Quill\Login\Events\LoginSaving;
use Quill\Login\Events\LoginSaved;
use Quill\Login\Events\LoginUpdating;
use Quill\Login\Events\LoginUpdated;
use Quill\Login\Models\Login;

class LoginObserver
{

    public function creating(Login $login)
    {
        // creating logic... 
        event(new LoginCreating($login));
    }

    public function created(Login $login)
    {
        // created logic...
        event(new LoginCreated($login));
    }

    public function saving(Login $login)
    {
        // saving logic...
        event(new LoginSaving($login));
    }

    public function saved(Login $login)
    {
        // saved logic...
        event(new LoginSaved($login));
    }

    public function updating(Login $login)
    {
        // updating logic...
        event(new LoginUpdating($login));
    }

    public function updated(Login $login)
    {
        // updated logic...
        event(new LoginUpdated($login));
    }

}