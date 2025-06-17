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
            // It's good practice to get 'voucher_code' here if that's what the frontend sends.
            $clientCode = $request->query('voucher_code');
            // Default to 'VOUCHERS' as confirmed in your frontend logic.
            $appType = $request->query('app_type', 'VOUCHERS'); 

            if (!$clientCode) {
                return response()->json(['error' => 'Client code is required'], 400);
            }

            $pdo = DB::connection()->getPdo();
            $stmt = $pdo->prepare('EXEC sproc_LoadClientData @client_code = ?, @app_type = ?');
            $stmt->execute([$clientCode, $appType]);

            // --- Fetching Result Sets according to SPROC order ---

            // Result Set 1: Clients
            $clients = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            if (empty($clients)) {
                // Return success:false for client not found, as per your updated comment
                return response()->json(['success' => false, 'error' => 'Client not found'], 404); 
            }

            // Result Set 2: Modules
            $stmt->nextRowset(); // Move to the 2nd result set
            $modules = $stmt->fetchAll(\PDO::FETCH_ASSOC);

            // Result Set 3: Technicians (this is the actual assigned personnel with tech_code from SPROC)
            $stmt->nextRowset(); // Move to the 3rd result set
            $actualTechniciansData = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Renamed for clarity

            // Result Set 4: Client Contact Details (from SPROC)
            $stmt->nextRowset(); // Move to the 4th result set
            $actualContactDetailsData = $stmt->fetchAll(\PDO::FETCH_ASSOC); // Renamed for clarity

            // --- IMPORTANT: If your SPROC returns MORE than 4 result sets,
            // you *must* add more $stmt->nextRowset() calls here,
            // even if you don't fetch their data, to ensure PDO doesn't get stuck.
            // Example: $stmt->nextRowset(); // for 5th result set
            //          $stmt->nextRowset(); // for 6th result set, etc.

            // --- Constructing the final JSON response ---
            return response()->json([
                'success' => true,
                'clients' => $clients[0], // Assuming only one client record
                'modules' => $modules,
                'technicians' => $actualTechniciansData, // This is your correct technicians (tech_code) data
                'client_contact' => $actualContactDetailsData, // This is your correct contact details data
                // Remove 'applications' from the response if it's not a distinct result set from your SPROC
                // or if it's implicitly part of the 'technicians' result set data.
                // Based on previous outputs, 'applications' data was actually the 'technicians' data.
            ], 200);

        } catch (\Exception $e) {
            // Log the full exception for debugging in your Laravel logs
            \Log::error('Error loading client data: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
            return response()->json([
                'success' => false,
                'error' => 'An internal server error occurred: ' . $e->getMessage()
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
        $result = DB::select('SELECT dbo.fnClientCode() AS voucher_code');

        return response()->json([
            'success' => true,
            'voucher_code' => $result[0]->voucher_code ?? null
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}


}
