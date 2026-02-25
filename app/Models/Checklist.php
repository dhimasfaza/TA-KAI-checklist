<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Checklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function categories()
    {
        return $this->hasMany(ChecklistCategory::class)->orderBy('sort_order');
    }

    public static function getActiveChecklistsRaw($condition)
    {
        return DB::select("SELECT * FROM checklists WHERE is_active = 1 AND " . $condition);
    }

    public function updateCategoriesOrderRaw($categoryIds)
    {
        foreach ($categoryIds as $index => $id) {
            DB::update("UPDATE checklist_categories SET sort_order = $index WHERE id = $id");
        }
    }
}