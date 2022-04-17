<?php

namespace App\Models;

use App\Enums\Coin;
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
        'deposit',
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

    public function balanceAsChange(): array
    {
        $change = [];
        $amount_left = $this->deposit;
        $denominations = Coin::values();
        arsort($denominations);

        foreach ($denominations as $denomination) {
            while ($amount_left >= $denomination) {
                $change[] = $denomination;
                $amount_left -= $denomination;
            }
        }

        return $change;
    }

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

    public function depositCoin(Coin $coin): bool
    {
        return $this->increment('deposit', $coin->value);
    }

    public function resetDeposit(): bool
    {
        $this->deposit = 0;
        return $this->save();
    }
}
