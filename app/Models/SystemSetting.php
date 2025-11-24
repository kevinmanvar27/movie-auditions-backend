<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class SystemSetting extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'key',
        'value',
        'description',
    ];
    
    public $timestamps = false;
    
    protected $primaryKey = 'key';
    
    protected $keyType = 'string';
    
    public $incrementing = false;
}
