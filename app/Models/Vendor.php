<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'priority', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function purchaseOrders()
    {
        return $this->belongsToMany(PurchaseOrder::class)
            ->withPivot(['quantity_assigned', 'quantity_fulfilled', 'status'])
            ->withTimestamps();
    }
}
