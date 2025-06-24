<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashBoardController extends Controller
{
    
 
// ** Leave Approval Inquiry Current
public function index(Request $request) {

    $request->validate([
        'EMP_NO' => 'required|string',
    ]);

    $employee_no = $request->input('EMP_NO');


    try {
        $results = DB::select(
            'EXEC sproc_PHP_EmpInq_SummInfo  @emp =?',
            [$employee_no] 
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


  public function dashboard_GL(Request $request)
{
    try {
        $userCode = $request->query('user');
        $mode = $request->query('mode', 'Top10');

        if (!$userCode) {
            return response()->json(['error' => 'User code is required'], 400);
        }

        $pdo = DB::connection()->getPdo();
        $stmt = $pdo->prepare('EXEC sproc_LoadDashboard_GL @user = ? , @mode = ?');
        $stmt->execute([$userCode, $mode]);

        // $stmt->nextRowset(); // if result set 1 was removed
        $dashboard = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        return response()->json([
            'success' => true,
            'dashboard' => $dashboard,
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => $e->getMessage()
        ], 500);
    }
}

}
