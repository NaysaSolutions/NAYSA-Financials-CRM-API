<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LeaveRequest extends Model
{
    use HasFactory;

    // Define the table name
    protected $table = 'Client';

    // Disable timestamps if not present
    public $timestamps = false;
}
