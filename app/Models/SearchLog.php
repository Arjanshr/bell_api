<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SearchLog extends Model
{
    use HasFactory;

    protected $table = 'search_logs';

    protected $fillable = [
        'term',
        'user_id',
        'ip_address',
        'user_agent',
        'device_type',
        'platform',
        'browser',
        'searched_at',
    ];

    protected $casts = [
        'searched_at' => 'datetime',
    ];

    public $timestamps = true;

    // Optional: relationship with User
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
