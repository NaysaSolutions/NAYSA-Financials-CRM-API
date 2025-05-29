<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientData extends Model
{
    use HasFactory;

    protected $table = 'client_data'; // Ensure the table name is correct

    protected $fillable = ['data'];

    protected $casts = [
        'data' => 'json', // Automatically casts to and from JSON
    ];
}
