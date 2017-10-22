<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path', 'confirmation_token',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    protected $casts = [
        'confirmed' => 'boolean',
    ];

    public function confirm()
    {
        $this->confirmed = true;

        $this->save();
    }

    public function getRouteKeyName()
    {
        return 'name'; // TODO: Change the autogenerated stub
    }

    public function threads()
    {
        return $this->hasMany('App\Thread')->latest();
    }

    public function lastReply()
    {
        return $this->hasOne('App\Reply')->latest();
    }

    public function activities()
    {
        return $this->hasMany('App\Activity');
    }

    public function visitedThreadCacheKey($thread)
    {
        return sprintf("users.%s.visits.%s", $this->id, $thread->id);
    }

    public function read($thread)
    {
        return cache()->forever(
            $this->visitedThreadCacheKey($thread),
            \Carbon\Carbon::now()
        );
    }

    public function getAvatarPathAttribute($avatar)
    {
        return asset($avatar ?: 'images/avatars/default.png');
    }
}
