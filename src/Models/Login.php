<?php

namespace Quill\Login\Models;

use Vellum\Models\BaseModel;

class Login extends BaseModel
{

    protected $table = 'logins';

    public function history()
    {
        return $this->morphOne('Quill\History\Models\History', 'historyable');
    }

    public function resourceLock()
    {
        return $this->morphOne('Vellum\Models\ResourceLock', 'resourceable');
    }

}
