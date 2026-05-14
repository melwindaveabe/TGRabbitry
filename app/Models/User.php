<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Auth\MustVerifyEmail as MustVerifyEmailContract;

use App\Services\MustVerifyMobile;
// use App\Services\IMustVerifyMobile;

class User extends Authenticatable implements MustVerifyEmailContract
{
    use HasApiTokens, HasFactory, Notifiable, MustVerifyMobile;

    protected $fillable = [
        'name',
        'address',
        'phone_no',
        'city',
        'email',
        'password',
        'last_online_at',
        'email_verified_at',
        'image'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'password' => 'hashed',
    ];

    protected $appends = [
        'image_name'
    ];

    public function getImageNameAttribute(){
        return $this->image ? 'users/' . $this->image : 'website/user.png';
    }
    
    public function routeNotificationForVonage(Notification $notification): string
    {
        return $this->phone_no;
    }

    public function user_messages(){
        return $this->hasMany(Message::class, 'user_id');
    }

    public function admin_messages(){
        return $this->hasMany(Message::class, 'admin_id');
    }

    public function getDeliveryFee(){
        if ($this->city == 'Bansud') return 50;
        if ($this->city == 'Roxas') return 30;
        if ($this->city == 'Bongabong') return 40;
        if ($this->city == 'Mansalay') return 40;
        return 125;
    }

}
