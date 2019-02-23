<?php

namespace App\Models;

use App\Models\Category;
use Illuminate\Database\Eloquent\Model;
use Backpack\CRUD\CrudTrait;

class Product extends Model
{
    use CrudTrait;

    protected $table = 'products';
    protected $fillable = ["name", "description", "image", "category_id", "price"];

    private $product_folder = "product_folder";

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function getImageAttribute()
    {
        return \Storage::disk('public')->url("{$this->product_folder}/{$this->attributes['image']}");
    }

    public function setImageAttribute($value)
    {
        if (starts_with($value, 'data:image')) {
            $image               = \Image::make($value)->encode('png')->resize(100,100);
            $filename            = "image". str_random(5).".png";
            $annee               = date('Y');
            $mois                = date('m');
            $destination_path    = "{$annee}/{$mois}";
            if (\Storage::disk('public')->exists("{$this->product_folder}/{$destination_path}/{$filename}")) {
                \Storage::disk('public')->delete("{$this->product_folder}/{$destination_path}/{$filename}");
            }
            \Storage::disk('public')->put("{$this->product_folder}/{$destination_path}/{$filename}", $image->stream());
            $this->attributes['image'] = "{$destination_path}/{$filename}";
        }
    }
}
