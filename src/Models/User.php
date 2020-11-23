<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';

    const CREATED_AT = null;
    const UPDATED_AT = null;
    const LAST_UPDATE = null;

    public function type_user()
    {
        return $this->hasOne('App\Models\TypeUser');
    }
}
