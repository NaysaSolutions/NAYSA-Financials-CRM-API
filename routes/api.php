<?php

use App\Http\Controllers\ReceivingReportController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Helpers\CryptoHelper;
use Illuminate\Support\Facades\Http;
use phpseclib3\Crypt\RSA;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

//fileUpload
// routes/api.php
use App\Http\Controllers\FileUploadController;

// Route::prefix('files')->group(function () {
    Route::get('/generate-id', [FileUploadController::class, 'generateId']);
    Route::post('/upload', [FileUploadController::class, 'upload']);
    Route::get('/client-files/{clientCode}', [FileUploadController::class, 'getClientFiles']);
    Route::get('/client-files/{clientCode}/{fileType}', [FileUploadController::class, 'getClientFiles']);
// });

// In routes/api.php
Route::prefix('files')->group(function () {
    Route::get('/verify/{fileId}', [FileUploadController::class, 'verify']);
    Route::get('/view/{fileId}', [FileUploadController::class, 'view']);
    Route::get('/download/{fileId}', [FileUploadController::class, 'download']);
});

//RR

Route::post('/getPO', [ReceivingReportController::class, 'getPO']);
Route::get('/warehouse', [ReceivingReportController::class, 'warehouse']);
Route::post('/location', [ReceivingReportController::class, 'location']);
Route::post('/save', [ReceivingReportController::class, 'save']);
Route::get('/getRRNo/{rrNo}', [ReceivingReportController::class, 'getRRNo']);


//Databridge
use App\Http\Controllers\AuthController;

//Route::middleware('bearer.token')->group(function () {
    // Add other routes that require authentication using the secret key
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/loginDB', [AuthController::class, 'login']);
    Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
//});

//Attachment
use App\Http\Controllers\AttachmentController;

Route::post('/generate', [AttachmentController::class, 'generate']);
Route::post('/confirm-signed-date', [AttachmentController::class, 'confirmSignedDate']);  
Route::post('/upload-client-attachment', [AttachmentController::class, 'uploadClientAttachment']); 
Route::post('/save-attachment-row', [AttachmentController::class, 'saveAttachmentRow']);


Route::post('/VehicleProfile',[AuthController::class,'VehicleProfile']);
Route::post('/PartsInventory',[AuthController::class,'PartsInventory']);

use App\Http\Controllers\DashBoardController;

Route::post('/dashBoard', [DashBoardController::class, 'index']);


use App\Http\Controllers\LoadClientsController;

Route::get('/getClients', [LoadClientsController::class, 'index']);
Route::post('/getClientPerApp', [LoadClientsController::class, 'getClientPerApp']);
Route::post('/getClientsdashboard', [LoadClientsController::class, 'dashboard']);
Route::get('/getClientsModule', [LoadClientsController::class, 'getClientModules']);
Route::get('/getClientApplications', [LoadClientsController::class, 'getClientApplications']);

Route::get('/load-client-data', [LoadClientsController::class, 'loadClientData']);
Route::get('/clients/default-code', [LoadClientsController::class, 'getDefaultClientCode']);


use App\Http\Controllers\ClientController;

Route::post('/client/save', [ClientController::class, 'saveClient']);





//encrypt
use App\Http\Controllers\EISController;


Route::post('/authenticate', [AuthController::class, 'authenticate']);
Route::post('/secure-data', [AuthController::class, 'secureData']);



Route::middleware(['auth.api_key'])->group(function () {
    Route::get('/protected-route', function () {
        return response()->json(['message' => 'Access granted!']);
    });
});

use App\Http\Controllers\EncryptController;

Route::post('/authenticate', [EncryptController::class, 'authenticate']);

use App\Http\Controllers\Auth1Controller;

Route::post('/request-token', [Auth1Controller::class, 'requestToken']);


use App\Http\Controllers\Auth2Controller;

Route::post('/encrypt-credentials', [Auth2Controller::class, 'encryptCredentials']);
Route::post('/authenticate', [Auth2Controller::class, 'authenticate']);



use App\Http\Controllers\EISEncryptController;
Route::post('/requestAuthToken', [EISEncryptController::class, 'requestAuthToken']);

// Generate Test Data
use Illuminate\Support\Facades\Log;

// Route::get('/generate-test-data', function () {
//     try {
//         // Load RSA Public Key
//         $publicKeyPath = config('crypto.rsa_public_key');
//         $publicKey = file_get_contents($publicKeyPath);
//         $rsa = RSA::loadPublicKey($publicKey);

//         // Load HMAC Secret from .env
//         $hmacSecret = env('HMAC_SECRET', 'b03465f3616f654faec7bbefeeb39f9a44db6a6158e4987eef0c82d7e9b7b51c');

//         // Data to Encrypt
//         $data = [
//             "accreditation_id" => "20SBeZ9D",
//             "application_id" => "SLHqUkNg",
//             "user_id" => "SOCCACCTNG",
//             "password" => "S@kam0to1988"
//         ];

//         // Compute HMAC Signature
//         $hmac = hash_hmac('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $hmacSecret);

//         // Encrypt Data with RSA
//         $encryptedData = base64_encode($rsa->encrypt(json_encode($data)));

//         return response()->json([
//             "encrypted_data" => $encryptedData,
//             "hmac" => $hmac // Include HMAC for validation
//         ]);

//     } catch (Exception $e) {
//         Log::error("Error in RSA Encryption: " . $e->getMessage());
//         return response()->json(["message" => "Encryption failed", "error" => $e->getMessage()], 500);
//     }
// });


use App\Http\Controllers\SecureController;

Route::middleware('auth.token')->get('/secure-data', [SecureController::class, 'getData']);

Route::middleware('auth.token')->post('/protected-endpoint', function (Request $request) {
    return response()->json(['message' => 'Access granted!']);
});


Route::post('/test-decrypt', function (Request $request) {
    // Fetch data from JSON payload
    $encryptedData = $request->input('data');
    $authKey = $request->input('auth_key');

    if (!$encryptedData || !$authKey) {
        return response()->json(['message' => 'Missing data or auth_key'], 400);
    }

    // Decrypt the response using AES
    $decryptedResponse = CryptoHelper::decryptWithAES($encryptedData, $authKey);
    $data = json_decode($decryptedResponse, true);

    return response()->json([
        'decrypted_data' => $data
    ]);
});


// $publicKeyPath = storage_path('keys/oauth-public.key');
// $publicKey = file_get_contents($publicKeyPath);

// $data = [
//     "accreditation_id" => "87654321",
//     "application_id" => "12345678",
//     "user_id" => "NSI",
//     "password" => "P@ssw0rd",
// ];

// // Generate a fixed auth_key for this request
// $authKey = bin2hex(random_bytes(16));

// // Encrypt using RSA Public Key
// openssl_public_encrypt(json_encode($data), $encrypted, $publicKey);
// $encryptedData = base64_encode($encrypted);

// // Generate HMAC Hash using the same authKey
// $hmac = hash_hmac('sha256', json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE), $authKey);

// // Print values to send in Postman
// echo json_encode([
//     "encrypted_data" => $encryptedData,
//     "hmac" => $hmac,
//     "auth_key" => $authKey
// ], JSON_PRETTY_PRINT);