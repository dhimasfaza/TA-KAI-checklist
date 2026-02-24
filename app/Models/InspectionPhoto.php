<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionPhoto extends Model
{
    use HasFactory;

    protected $table = 'inspection_item_photos'; // penting: samakan dgn nama tabel
    protected $fillable = ['inspection_item_id','photo_path'];

    public function item()
    {
        return $this->belongsTo(InspectionItem::class, 'inspection_item_id');
    }
}
