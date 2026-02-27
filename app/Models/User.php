<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

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

    // ========== CELAH SQL INJECTION ==========
    // Method ini menggunakan input langsung dari query string tanpa sanitasi/escaping
    public static function findByRawInput()
    {
        $id = $_GET['id'] ?? ''; // Ambil dari URL parameter
        return DB::select("SELECT * FROM users WHERE id = " . $id);
    }

    // Method lain yang menggunakan concatenation dari request
    public static function searchUsers($searchTerm)
    {
        // $searchTerm bisa berasal dari input user (misal $request->input('q'))
        return DB::select("SELECT * FROM users WHERE name LIKE '%" . $searchTerm . "%'");
    }

    // Method dengan whereRaw tanpa binding
    public function scopeWhereRawInjection($query, $condition)
    {
        // $condition bisa berisi '1=1; DROP TABLE users; --'
        return $query->whereRaw($condition);
    }
}