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
            $appType = $request->query('app_type', 'FINANCIALS'); // default to 'FINANCIALS'
    
            if (!$clientCode) {
                return response()->json(['error' => 'Client code is required'], 400);
            }
    
            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('EXEC sproc_LoadClientData @client_code = ?, @app_type = ?');
            $stmt->execute([$clientCode, $appType]);
    
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
    
            // After technicians
            $stmt->nextRowset();
            $contractDetails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // After technicians
            $stmt->nextRowset();
            $contactDetails = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            return response()->json([
                'success' => true,
                'clients' => $clients[0],
                'modules' => $modules,
                'applications' => $applications,
                'technicians' => $technicians,
                'client_contract' => $contractDetails[0] ?? null,
                'client_contact' => $contactDetails,
            ], 200);
    
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

//     public function index()
// {
//     try {
//         $clients = LeaveRequest::on(env('DB_CONNECTION'))->get();

//         return response()->json($clients, 200);
//     } catch (\Exception $e) {
//         return response()->json(['error' => $e->getMessage()], 500);
//     }
// }

public function getClientPerApp(Request $request) {

    $request->validate([
        'PARAMS' => 'required',
    ]);

    $params = $request->input('PARAMS');

    try {

        $results = DB::select(
            'EXEC sproc_LoadClientData_App @app_type = ?',
            [$params]
        );

        return response()->json([
            'success' => true,
            'data' => $results,
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }


}
    

public function index(Request $request) {

    try {

        $results = DB::select(
            'EXEC sproc_LoadClientData_All',
        );

        return response()->json([
            'success' => true,
            'data' => $results,
        ], 200);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }


}
    

    public function dashboard(Request $request)
{
    try {
        $userCode = $request->query('user');
        $mode = $request->query('mode', 'Top10');

        if (!$userCode) {
            return response()->json(['error' => 'User code is required'], 400);
        }

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('EXEC sproc_LoadDashboard_User @user = ?, @mode = ?');
        $stmt->execute([$userCode, $mode]);

        // $stmt->nextRowset(); // if result set 1 was removed
        $dashboard1 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $stmt->nextRowset();
        $dashboard2 = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return response()->json([
            'success' => true,
            'dashboard1' => $dashboard1,
            'dashboard2' => $dashboard2,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

public function getDefaultClientCode()
{
    try {
        $result = DB::select('SELECT dbo.fnClientCode() AS client_code');

        return response()->json([
            'success' => true,
            'client_code' => $result[0]->client_code ?? null
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


}
