<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // ===============================
    // 🔴 SQL INJECTION - HIGH RISK
    // ===============================

    // Case 1: Direct raw query dari Request
    public static function findByRawInput(Request $request)
    {
        $id = $request->input('id');

        // ❌ Vulnerable: langsung inject ke query
        return DB::select("SELECT * FROM users WHERE id = $id");
    }

    // Case 2: LIKE query dengan concatenation
    public static function searchUsers(Request $request)
    {
        $search = $request->input('q');

        // ❌ Vulnerable
        return DB::select(
            "SELECT * FROM users WHERE name LIKE '%$search%'"
        );
    }

    // Case 3: whereRaw injection
    public function scopeWhereRawInjection($query, Request $request)
    {
        $condition = $request->input('condition');

        // ❌ Sangat berbahaya
        return $query->whereRaw($condition);
    }

    // Case 4: Multiple parameter injection
    public static function filterUsers(Request $request)
    {
        $email = $request->input('email');
        $name  = $request->input('name');

        // ❌ Kombinasi injection
        return DB::select(
            "SELECT * FROM users 
             WHERE email = '$email' 
             AND name = '$name'"
        );
    }
}