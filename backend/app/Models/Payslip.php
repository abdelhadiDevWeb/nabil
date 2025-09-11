<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payslip extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'file_name',
        'file_url',
        'month',
        'year',
        'uploaded_by',
    ];

    /**
     * Get the user that owns the payslip.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}