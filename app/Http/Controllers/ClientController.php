<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log; 

class ClientController extends Controller
{
   public function saveClient(Request $request)
{
    $mode = $request->input('mode');
    $params = $request->input('params');

    try {
        // Assuming $params is already an array and no longer needs json_decode
        DB::statement("EXEC sproc_ClientData @mode = ?, @params = ?", [$mode, $params]);

        return response()->json(['success' => true]);
    } catch (\Exception $e) {
        Log::error('Error in saveClient:', ['message' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Save failed.']);
    }
}


public function getClients()
{
    $clients = DB::connection('API')->table('ClientData')->get();

    return response()->json($clients);
}

    
}