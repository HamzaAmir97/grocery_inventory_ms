<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockMovement extends Model
{
    public const UPDATED_AT = null;

    public const REASON_MANUAL_EDIT = 'manual_edit';

    public const REASON_INITIAL = 'initial';

    public const REASON_RESTOCK = 'restock';

    public const REASON_SALE = 'sale';

    public const REASON_DELETED = 'deleted';

    protected $fillable = [
        'item_id',
        'user_id',
        'delta',
        'reason',
        'note',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'delta' => 'integer',
        ];
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
