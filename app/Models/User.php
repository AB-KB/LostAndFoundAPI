<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Saad\ModelImages\Contracts\ImageableContract;
use Saad\ModelImages\Traits\HasImages;

class User extends Authenticatable implements ImageableContract
{
    use HasApiTokens, HasFactory, Notifiable, HasImages;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone_number',
        'village_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public static function imageableFields(): array
    {
        return ['profile'];
    }


    public function ads(){

        return $this->hasMany(Item::class, "added_by", "id");
    }


    public function village(){

        return $this->belongsTo(Village::class);
    }


    public function isAdmin(){

        return $this->role == "admin";
    }
}
