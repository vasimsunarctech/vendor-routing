<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = ['po_number', 'required_date', 'status'];

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function vendors()
    {
        return $this->belongsToMany(Vendor::class)
            ->withPivot(['quantity_assigned', 'quantity_fulfilled', 'status'])
            ->withTimestamps();
    }
}
