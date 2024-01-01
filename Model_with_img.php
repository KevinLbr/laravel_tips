<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    
    protected $guarded = [
        "id"
    ];

    private $product_folder = "product_folder";

    public function getImageAttribute(): string
    {
        return \Storage::disk('public')->url("{$this->product_folder}/{$this->attributes['image']}");
    }

    public function setImageAttribute($value): void
    {
        if (starts_with($value, 'data:image')) 
        {
            $image               = \Image::make($value)->encode('png')->resize(100,100);
            $filename            = "image". str_random(5).".png";
            $annee               = date('Y');
            $mois                = date('m');
            $destination_path    = "{$annee}/{$mois}";
            if (\Storage::disk('public')->exists("{$this->product_folder}/{$destination_path}/{$filename}")) 
            {
                \Storage::disk('public')->delete("{$this->product_folder}/{$destination_path}/{$filename}");
            }
            \Storage::disk('public')->put("{$this->product_folder}/{$destination_path}/{$filename}", $image->stream());
            $this->attributes['image'] = "{$destination_path}/{$filename}";
        }
    }
}
