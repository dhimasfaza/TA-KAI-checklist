<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inspection extends Model
{
    use HasFactory;

    protected $fillable = [
        'checklist_id','location_id','sloc',
        'opening_balance','income_total','expense_total','closing_balance',
        'movement_freq', // frekuensi per semester
        'semester','year', // NEW
        'inspector_name','visited_at',
        'status','overall_note','overall_score'
    ];

    protected $casts = [
        'visited_at'    => 'date',
        'overall_score' => 'float',
        'semester'      => 'integer',
        'year'          => 'integer',
        'opening_balance' => 'integer',
        'income_total'    => 'integer',
        'expense_total'   => 'integer',
        'closing_balance' => 'integer',
        'movement_freq'   => 'integer',
    ];

    public function items(){ return $this->hasMany(InspectionItem::class); }
    public function location(){ return $this->belongsTo(Location::class); }
    public function checklist(){ return $this->belongsTo(Checklist::class); }
}
