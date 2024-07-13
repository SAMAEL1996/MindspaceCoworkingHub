<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasUid;
use Spatie\Permission\Traits\HasRoles;
use Appstract\Meta\Metable;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasUid, HasRoles, Metable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'contact_no',
        'password',
        'is_staff',
        'status',
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
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function staff()
    {
        return $this->hasOne(\App\Models\Staff::class);
    }

    public function cashLogs()
    {
        return $this->hasMany(\App\Models\CashLog::class);
    }

    public function checkLatestCashLogs()
    {
        if($this->cashLogs->isEmpty()) {
            return false;
        }

        return $this->cashLogs()->latest()->first()->status;
    }

    public static function getCurrentCashier()
    {
        $latestCashier = \App\Models\CashLog::with('user')->latest()->first();

        if(!$latestCashier || !$latestCashier->status) {
            return false;
        }

        return $latestCashier->user;
    }
}
