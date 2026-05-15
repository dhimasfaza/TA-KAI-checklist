<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class Checklist extends Model
{
    use HasFactory;

    protected $guarded = [];

    /*
    |--------------------------------------------------------------------------
    | RELATION
    |--------------------------------------------------------------------------
    */

    public function categories()
    {
        return $this->hasMany(ChecklistCategory::class)
                    ->orderBy('sort_order');
    }

    /*
    |--------------------------------------------------------------------------
    | 🔴 VULNERABLE METHODS (FOR SONARQUBE TESTING)
    |--------------------------------------------------------------------------
    */

    /**
     * Case 1
     * SQL Injection using dynamic condition
     * Severity: Major / Medium
     */
    public static function getActiveChecklistsRaw($condition)
    {
        // ❌ Vulnerable
        return DB::select(
            "SELECT * FROM checklists 
             WHERE is_active = 1 
             AND " . $condition
        );
    }

    /**
     * Case 2
     * SQL Injection on UPDATE query
     * Severity: Major
     */
    public function updateCategoriesOrderRaw($categoryIds)
    {
        foreach ($categoryIds as $index => $id) {

            // ❌ Vulnerable
            DB::update(
                "UPDATE checklist_categories 
                 SET sort_order = $index 
                 WHERE id = $id"
            );
        }
    }

    /**
     * Case 3
     * Dangerous whereRaw query
     * Severity: Medium
     */
    public static function filterByRaw(Request $request)
    {
        $query = $request->input('query');

        // ❌ Vulnerable
        return DB::table('checklists')
            ->whereRaw($query)
            ->get();
    }

    /**
     * Case 4
     * LIKE Injection
     * Severity: Medium
     */
    public static function searchChecklist(Request $request)
    {
        $keyword = $request->input('keyword');

        // ❌ Vulnerable
        return DB::select(
            "SELECT * FROM checklists 
             WHERE title LIKE '%$keyword%'"
        );
    }

    /**
     * Case 5
     * ORDER BY Injection
     * Severity: Medium
     */
    public static function sortChecklist(Request $request)
    {
        $sort = $request->input('sort');

        // ❌ Vulnerable
        return DB::select(
            "SELECT * FROM checklists 
             ORDER BY $sort"
        );
    }

    /*
    |--------------------------------------------------------------------------
    | ✅ SECURE METHODS (REMEDIATION)
    |--------------------------------------------------------------------------
    */

    /**
     * Safe checklist retrieval
     */
    public static function getActiveChecklistsSafe()
    {
        return DB::table('checklists')
            ->where('is_active', 1)
            ->get();
    }

    /**
     * Safe update with Query Builder
     */
    public function updateCategoriesOrderSafe($categoryIds)
    {
        foreach ($categoryIds as $index => $id) {

            DB::table('checklist_categories')
                ->where('id', (int) $id)
                ->update([
                    'sort_order' => (int) $index
                ]);
        }
    }

    /**
     * Safe filtering
     */
    public static function filterSafe(Request $request)
    {
        $status = $request->input('status');

        return DB::table('checklists')
            ->where('status', $status)
            ->get();
    }

    /**
     * Safe search
     */
    public static function searchChecklistSafe(Request $request)
    {
        $keyword = $request->input('keyword');

        return DB::table('checklists')
            ->where('title', 'LIKE', '%' . $keyword . '%')
            ->get();
    }

    /**
     * Safe sorting with whitelist
     */
    public static function sortChecklistSafe(Request $request)
    {
        $allowedSorts = [
            'id',
            'title',
            'created_at'
        ];

        $sort = $request->input('sort');

        if (!in_array($sort, $allowedSorts)) {
            $sort = 'id';
        }

        return DB::table('checklists')
            ->orderBy($sort)
            ->get();
    }
}