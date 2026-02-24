<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChecklistCategory extends Model
{
    use HasFactory;
    protected $fillable = ['checklist_id','title','sort_order'];

    public function items(){
        return $this->hasMany(ChecklistItem::class)->orderBy('sort_order');
    }
}