<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    
    protected $fillable = [
        'name',
        'category',
        'desc',
        'price',
        'stock',
        'active',
        'images',
    ];


    protected $appends = [
        'image_names'
    ];

    public function getImageNamesAttribute(){
        return $this->category == 'Rabbit' ? 'rabbit breeds/' . $this->name . '.jpg' : $this->images ?? '';
    }

    public function rabbit(){
        return $this->hasOne(Rabbit::class, 'product_id');
    }

    public function order_details(){
        return $this->hasMany(OrderDetails::class, 'product_id');
    }

}
