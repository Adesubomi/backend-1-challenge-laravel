<?php

namespace App\Models;

use App\Traits\ModelTraits;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    use HasFactory;
    use ModelTraits;

    protected $fillable = [
        'amount_available',
        'cost',
        'product_name',
        'seller_id',
    ];

    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function newProduct(User $user, array $data)
    {
        return $this->create(
            [
                ...$this->filterFillableAttributes($data),
                "seller_id" => $user->id
            ]
        );
    }

    public function updateProduct(array $data): bool
    {
        return $this->update($this->filterEditableAttributes($data));
    }
}
