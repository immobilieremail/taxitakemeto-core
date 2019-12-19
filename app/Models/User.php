<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    /**
     * Table where users are stored
     * @var String
     */
    protected $table = 'users';

    /**
     * Model fillable data
     *
     * @var Array
     */
    protected $fillable = [
        'name', 'email', 'phone', 'password'
    ];

    /**
     * Constructor for eloquent model hierarchy
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function (User $user) {
            $user->profileFacet()->save(new UserProfileFacet);
        });
    }

    /**
     * ProfileFacet for specific User
     *
     * @return [type] [description]
     */
    public function profileFacet()
    {
        return $this->hasOne(UserProfileFacet::class, 'target_id')
                    ->where('type', 'App\Models\UserProfileFacet');
    }
}
