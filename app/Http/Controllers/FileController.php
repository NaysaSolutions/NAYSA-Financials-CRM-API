<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FileController extends Controller
{
// FileController.php
public function upload(Request $request)
{
    try {
        // Validate the file input
        if (!$request->hasFile('file')) {
            return response()->json([
                'success' => false,
                'message' => 'No file uploaded'
            ], 400);
        }

        $file = $request->file('file');

        if (!$file->isValid()) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid file upload'
            ], 400);
        }

        $filename = $file->getClientOriginalName();
        $filetype = $file->getClientMimeType();
        $filesize = $file->getSize();
        $fileData = file_get_contents($file->getRealPath());

        // Log the values to verify
        Log::info('Uploading file:', [
            'filename' => $filename,
            'filetype' => $filetype,
            'filesize' => $filesize,
            'binary_size' => strlen($fileData)
        ]);

        // Use raw PDO for binary-safe binding
        $pdo = DB::connection('sqlsrv')->getPdo();
        $stmt = $pdo->prepare("EXEC sp_store_file @filename = ?, @filetype = ?, @filesize = ?, @filedata = ?");
        $stmt->bindParam(1, $filename);
        $stmt->bindParam(2, $filetype);
        $stmt->bindParam(3, $filesize);
        $stmt->bindParam(4, $fileData, \PDO::PARAM_LOB);
        $stmt->execute();

        return response()->json([
            'success' => true,
            'message' => 'File stored successfully',
            'file_info' => [
                'name' => $filename,
                'type' => $filetype,
                'size' => $filesize
            ]
        ]);
    } catch (\Exception $e) {
        // Log the actual exception
        Log::error('File upload error: ' . $e->getMessage(), [
            'trace' => $e->getTraceAsString()
        ]);

        return response()->json([
            'success' => false,
            'message' => 'File upload failed',
            'error' => $e->getMessage() // Show actual error temporarily
        ], 500);
    }
}

}