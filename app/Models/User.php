<?php

namespace App\Models;

use App\Enums\Role;
use App\Traits\ModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, ModelTraits;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    protected array $editable = [
        'name',
        'role',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'role' => Role::class
    ];

    public function newUser(array $data)
    {
        return User::create(
            $this->filterFillableAttributes($data)
        );
    }

    public function updateUser(array $data)
    {
        return User::where('email', $this->email)
            ->update(
                $this->filterEditableAttributes($data)
            );
    }
}
