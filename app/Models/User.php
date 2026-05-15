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
    // ✅ FIXED VERSION (SAFE)
    // ===============================

    /**
     * Case 1: Safe query by ID
     */
    public static function findByRawInput(Request $request)
    {
        $id = (int) $request->input('id');

        // ✅ Aman menggunakan parameter binding
        return DB::table('users')
            ->where('id', $id)
            ->get();

        // Alternatif:
        // return DB::select(
        //     "SELECT * FROM users WHERE id = ?",
        //     [$id]
        // );
    }

    /**
     * Case 2: Safe LIKE query
     */
    public static function searchUsers(Request $request)
    {
        $search = $request->input('q');

        // ✅ Aman
        return DB::table('users')
            ->where('name', 'LIKE', '%' . $search . '%')
            ->get();

        // Alternatif:
        // return DB::select(
        //     "SELECT * FROM users WHERE name LIKE ?",
        //     ["%{$search}%"]
        // );
    }

    /**
     * Case 3: Safe scope filtering
     */
    public function scopeSafeFilter($query, Request $request)
    {
        $allowedColumns = ['name', 'email'];
        $column = $request->input('column');
        $value  = $request->input('value');

        // ✅ Validasi whitelist column
        if (in_array($column, $allowedColumns)) {
            return $query->where($column, $value);
        }

        return $query;
    }

    /**
     * Case 4: Safe multiple parameters
     */
    public static function filterUsers(Request $request)
    {
        $email = $request->input('email');
        $name  = $request->input('name');

        // ✅ Aman menggunakan Query Builder
        return DB::table('users')
            ->where('email', $email)
            ->where('name', $name)
            ->get();

        // Alternatif:
        // return DB::select(
        //     "SELECT * FROM users WHERE email = ? AND name = ?",
        //     [$email, $name]
        // );
    }
}