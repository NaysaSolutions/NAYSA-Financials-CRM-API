<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientModule extends Model
{
    use HasFactory;

    protected $table = 'ClientModule'; // Ensure this matches your table name
    protected $primaryKey = 'client_code'; // Adjust if your primary key is different
    protected $fillable = ['client_code', 'module_code', 'module_name']; // Adjust fields as needed
}