<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\LeaveRequest;
use App\Models\ClientModule;
use Illuminate\Support\Facades\DB;

class LoadClientsController extends Controller // Fix naming here
{
    public function loadClientData(Request $request)
    {
        try {
            $clientCode = $request->query('client_code');
    
            if (!$clientCode) {
                return response()->json(['error' => 'Client code is required'], 400);
            }
    
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('EXEC sproc_LoadClientData ?');
            $stmt->execute([$clientCode]);
    
            // Result Set 1: Client
            $clients = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($clients)) {
                return response()->json(['error' => 'Client not found'], 404);
            }
    
            // Result Set 2: Modules
            $stmt->nextRowset();
            $modules = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
            // Result Set 3: Applications
            $stmt->nextRowset();
            $applications = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
            // Result Set 4: Technicians (tech_codes)
            $stmt->nextRowset();
            $technicians = $stmt->fetchAll(\PDO::FETCH_ASSOC);
    
            return response()->json([
                'success' => true,
                'clients' => $clients[0],
                'modules' => $modules,
                'applications' => $applications,
                'technicians' => $technicians,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function index()
{
    try {
        $clients = LeaveRequest::on(env('DB_CONNECTION'))->get();

        return response()->json($clients, 200);
    } catch (\Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}
    

}
