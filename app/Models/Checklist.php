<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checklist extends Model
{
    use HasFactory;
    protected $fillable = ['name','description','is_active'];

    public function categories(){
        return $this->hasMany(ChecklistCategory::class)->orderBy('sort_order');
    }
}