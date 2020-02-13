<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Passwords\CanResetPassword;
use Illuminate\Foundation\Auth\Access\Authorizable;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordContract;
use Storage;

class User extends Model implements AuthenticatableContract,
                                    AuthorizableContract,
                                    CanResetPasswordContract
{
    use Authenticatable, Authorizable, CanResetPassword;


    /**
     * The database connection used by the model.
     *
     * @var string
     */
    protected $connection = 'uam';

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'user';

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = [];

	/**
	 * The attributes excluded from the model's JSON form.
	 *
	 * @var array
	 */
	protected $hidden = [];

	/**
     * The attributes added from the model's JSON form.
     *
     * @var array
     */
    protected $appends = ['name'];

    public function getNameAttribute()
    {
        return $this->first_name;
    }

    public function policies()
    {
    	Storage::disk('local')->put('policies.json', json_encode(session()->get('permissions')));
        $policiesString = file_get_contents(storage_path('app/policies.json'));
        $policies = json_decode($policiesString, true);

        return collect($policies);
    }

    public function modules()
    {
        return $this->policies()->keys();
    }

    public function permissions($module)
    {
        return collect($this->policies()[$module]);
    }
}
