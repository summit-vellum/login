<?php

namespace Quill\Login\Resource;

use Quill\Html\Fields\ID;
use Quill\Login\Models\Login;
use Vellum\Contracts\Formable;

class LoginResource extends Login implements Formable
{
    public function fields()
    {
        return [
            ID::make()->sortable()->searchable(),
        ];
    }

    public function filters()
    {
        return [
            //
        ];
    }

    public function actions()
    {
        return [
            new \Vellum\Actions\EditAction,
            new \Vellum\Actions\ViewAction,
            new \Vellum\Actions\DeleteAction,
        ];
    }

}
