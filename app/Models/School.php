<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class School extends Model
{
    protected $fillable = ['name', 'address', 'contact_email', 'status', 'max_users', 'max_classes'];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public static function sisaKuotaUser(int $schoolId) : int
    {
       $max = (int) DB::table('schools')->where('id', $schoolId)->value('max_users');
       $pakai = DB::table('users')->where('school_id', $schoolId)->count();
       return $max - $pakai;
    }
}
