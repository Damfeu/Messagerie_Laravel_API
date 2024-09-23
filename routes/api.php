<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\GroupController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

// Route pour l'inscription
Route::post('/register', [AuthController::class, 'register']);

// Route pour la connexion
Route::post('/login', [AuthController::class, 'login']);

// Routes protégées par Sanctum (pour l'utilisateur authentifié)
Route::middleware('auth:sanctum')->group(function () {
    // Route pour obtenir les informations de l'utilisateur connecté
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    // Route pour la déconnexion
    // Route::post('/logout', [AuthController::class, 'logout']);
    Route::post('/logout', [AuthController::class, 'logout']);
});


// Route pour créer un groupe
Route::post('/groups', [GroupController::class, 'createGroup']);

// Route pour récupérer tous les groupes
Route::get('/groups', [GroupController::class, 'getGroups']);

// Route pour ajouter un membre à un groupe
Route::post('/groups/{groupId}/members', [GroupController::class, 'addMember']);

// Route pour uploader un fichier à un groupe
Route::post('/groups/{groupId}/files', [GroupController::class, 'uploadFile']);






// Route::post('/register', [AuthController::class, 'register']);
Route::post('/verify-code', [AuthController::class, 'verifyCode']);


Route::get('/groups', [GroupController::class, 'index']);
