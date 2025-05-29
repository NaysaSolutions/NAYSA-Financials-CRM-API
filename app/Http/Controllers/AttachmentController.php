<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AttachmentController extends Controller
{
    public function generate(): JsonResponse
    {
        try {
            $result = DB::select('EXEC sproc_php_auto_newid');
            return response()->json([
                'success' => true,
                'data' => $result[0]->new_id ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error executing stored procedure.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function uploadClientAttachment(Request $request) {
        try {
            $clientCode = $request->input('client_code');
            $originalFilename = $request->input('file_name');
            $uploadDate = now()->toDateTimeString();
            
            // Generate a new transaction key (tran_key)
            $tranKey = 'ATTACH_' . uniqid() . '_' . time();
            $fileExtension = $request->file('file')->getClientOriginalExtension();
            $generatedFilename = "{$tranKey}.{$fileExtension}";
            
            // Save file to Attachments folder using tran_key as filename
            $request->file('file')->storeAs('Attachments', $generatedFilename);
            
            // Insert into ClientAttachment table with your specified columns
            DB::table('ClientAttachment')->insert([
                'client_code' => $clientCode,
                'upload_date' => $uploadDate,
                'signed_date' => null, // Will be updated later
                'file_name' => $originalFilename,
                'tran_key' => $tranKey,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            
            return response()->json([
                'success' => true,
                'tran_key' => $tranKey,
                'file_name' => $originalFilename,
                'upload_date' => $uploadDate,
                'signed_date' => null
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // In your controller
public function confirmSignedDate(Request $request)
{
    try {
        $validated = $request->validate([
            'tran_key' => 'required',
            'signed_date' => 'required|date',
            'client_code' => 'required'
        ]);

        DB::table('ClientAttachment')
            ->where('tran_key', $validated['tran_key'])
            ->where('client_code', $validated['client_code'])
            ->update([
                'signed_date' => $validated['signed_date'],
                'confirmed' => true, // Add this column to your table if needed
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Signed date confirmed successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}

// In your controller
public function saveAttachmentRow(Request $request)
{
    try {
        $validated = $request->validate([
            'tran_key' => 'required',
            'client_code' => 'required',
            'signed_date' => 'required|date'
        ]);

        DB::table('ClientAttachment')
            ->where('tran_key', $validated['tran_key'])
            ->where('client_code', $validated['client_code'])
            ->update([
                'signed_date' => $validated['signed_date'],
                'updated_at' => now()
            ]);

        return response()->json([
            'success' => true,
            'message' => 'Attachment data saved successfully'
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage()
        ], 500);
    }
}
}