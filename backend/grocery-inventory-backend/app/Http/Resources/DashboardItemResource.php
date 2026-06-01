<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardItemResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $status = $this->stock_quantity <= 0
            ? ['value' => 'out_of_stock', 'label' => 'Out of stock', 'tone' => 'danger']
            : ($this->stock_quantity <= $this->low_stock_threshold
                ? ['value' => 'low_stock', 'label' => 'Low stock', 'tone' => 'warning']
                : ['value' => 'in_stock', 'label' => 'In stock', 'tone' => 'success']);

        return [
            'id' => $this->id,
            'name' => $this->name,
            'sku' => $this->sku,
            'category' => $this->category?->name,
            'supplier' => $this->supplier?->name,
            'unit_symbol' => $this->unit?->symbol,
            'stock_quantity' => $this->stock_quantity,
            'low_stock_threshold' => $this->low_stock_threshold,
            'price' => $this->price,
            'status' => $status['value'],
            'status_label' => $status['label'],
            'status_tone' => $status['tone'],
        ];
    }
}
