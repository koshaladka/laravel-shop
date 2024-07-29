<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'thumbnail',
    ];

    /**
     * @return void
     * Генерация SLUG в момент создания записи
     */
   protected static function boot()
   {
       parent::boot();

       static::creating(function (Brand $brand) {
            $brand->slug = $brand->slug ?? str($brand->title)->slug();
       });
   }

   public function products(): HasMany
   {
       return $this->hasMany(Product::class);
   }
}
