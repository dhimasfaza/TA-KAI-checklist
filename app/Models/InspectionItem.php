<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class InspectionItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'inspection_id',
        'checklist_item_id',
        'rating',
        'note',
        'photo_path_1',
        'photo_path_2',
    ];

    public function item()
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    public function photos()
    {
        return $this->hasMany(InspectionPhoto::class, 'inspection_item_id');
    }
}
