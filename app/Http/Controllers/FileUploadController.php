<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class FileUploadController extends Controller
{
    public function generateId()
{
    try {
        // Check if the stored procedure exists and is working
        $result = DB::select('EXEC sproc_php_auto_newid');
        
        if (empty($result)) {
            throw new \Exception('Stored procedure returned no results');
        }
        
        $id = $result[0]->new_id;
        
        return response()->json([
            'success' => true,
            'file_id' => $id
        ]);
        
    } catch (\Exception $e) {
        Log::error('Failed to generate ID: ' . $e->getMessage());
        return response()->json([
            'success' => false,
            'message' => 'Failed to generate ID: ' . $e->getMessage()
        ], 500);
    }
}

public function upload(Request $request)
{
    $request->validate([
        'file' => 'required|file|max:10240',
        'file_id' => 'required',
        'client_code' => 'required',
        'file_name' => 'required',
        'signed_date' => 'nullable|date',
    ]);

    try {
        $file = $request->file('file');
        $fileId = $request->input('file_id');
        $clientCode = $request->input('client_code');
        $originalName = $request->input('file_name');
        $signedDate = $request->input('signed_date');
        $uploadDate = now()->format('Y-m-d');

        $extension = $file->getClientOriginalExtension();
        $newFilename = $fileId . '.' . $extension;
        $path = $file->storeAs('attachments', $newFilename, 'public');

        DB::table('client_attachments')->insert([
            'file_id' => $fileId,
            'client_code' => $clientCode,
            'original_name' => $originalName,
            'file_path' => $path,
            'file_type' => $request->input('file_type', 'other'),
            'signed_date' => $signedDate,
            'upload_date' => $uploadDate,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'File uploaded successfully',
            'file_id' => $fileId,
            'path' => $path
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Upload failed: ' . $e->getMessage()
        ], 500);
    }
}


    public function download($fileId)
    {
        try {
            $file = DB::table('client_attachments')
                ->where('file_id', $fileId)
                ->first();

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $storagePath = storage_path('app/public/' . $file->file_path);
            
            if (!file_exists($storagePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }

            return response()->download($storagePath, $file->original_name, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'attachment; filename="'.$file->original_name.'"'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Download failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * View a file in browser
     */
    public function view($fileId)
    {
        try {
            $file = DB::table('client_attachments')
                ->where('file_id', $fileId)
                ->first();

            if (!$file) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found'
                ], 404);
            }

            $storagePath = storage_path('app/public/' . $file->file_path);
            
            if (!file_exists($storagePath)) {
                return response()->json([
                    'success' => false,
                    'message' => 'File not found on server'
                ], 404);
            }

            return response()->file($storagePath, [
                'Content-Type' => 'application/pdf',
                'Content-Disposition' => 'inline; filename="'.$file->original_name.'"'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'View failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Verify file exists
     */
    public function verify($fileId)
    {
        try {
            $file = DB::table('client_attachments')
                ->where('file_id', $fileId)
                ->first();

            $exists = $file && Storage::disk('public')->exists($file->file_path);

            return response()->json([
                'success' => true,
                'exists' => $exists
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Verification failed'
            ], 500);
        }
    }

    // FileUploadController.php

public function getClientFiles($clientCode, $fileType = null)
{
    try {
        $query = DB::table('client_attachments')
            ->where('client_code', $clientCode);

        if ($fileType) {
            $query->where('file_type', $fileType);
        }

        $files = $query->get();

        return response()->json([
            'success' => true,
            'files' => $files
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch files'
        ], 500);
    }
}
}