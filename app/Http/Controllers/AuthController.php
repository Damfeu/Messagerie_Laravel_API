<?php

namespace App\Http\Controllers;

use App\Mail\GroupMemberAddedNotification;
use App\Mail\OtpCodeMail;
use App\Models\Groupe;
use App\Models\InvitedMembers;
use App\Models\Membre;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\GroupController;

class AuthController extends Controller
{
    // Méthode pour l'inscription

    public function register(Request $request)
    {
        // Valider les données reçues
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:4',
            'password_confirmation' => 'required|string|same:password',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
    
        // Générer un code d'authentification ou un code aléatoire pour l'e-mail
        $authCode = rand(1000, 9999); // Exemple : générer un code aléatoire à 4 chiffres
    
        // Vérifier si l'utilisateur a été invité
        $invitedMember = InvitedMembers::where('email', $request->email)->first();
    
        // Créer un nouvel utilisateur
        $user = new User();
        $user->name = $request->name;
        $user->email = $invitedMember->email;
        $user->password = Hash::make($request->password);
        $user->auth_code = $authCode;
        $user->email_verified = false;
        $user->save();
    
        // Créer un token pour l'utilisateur
        $token = $user->createToken('authToken')->plainTextToken;
    
        // Si l'utilisateur a été invité, ajouter l'utilisateur au groupe
        if ($user->email === $invitedMember->email ) {
            // Ajouter l'utilisateur comme membre du groupe
            Membre::create([
                'name' => $request->name,
                'email' => $user->email,
                'groupe_id' => $invitedMember->groupe_id,
            ]);
    
            // Notifier les autres membres du groupe
            $groupMembers = Membre::where('groupe_id', $invitedMember->groupe_id)->get();
            foreach ($groupMembers as $member) {
                Mail::to($member->email)->send(new GroupMemberAddedNotification($user->name, 'Admin', $invitedMember->group->name));
            }
    
            // Supprimer l'entrée de la table des invitations après l'inscription
            $invitedMember->delete();
        }
    
        // Envoyer l'e-mail de code OTP
        Mail::to($user->email)->send(new OtpCodeMail($authCode));
    
        return response()->json([
            'user' => $user,
            'token' => $token,
            'message' => $invitedMember ? 'Inscription réussie et ajouté au groupe.' : 'Inscription réussie.',
        ], 201);
    }
    


    // Méthode pour la connexion
    public function login(Request $request)
    {
        // Valider les données reçues
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        // Vérifier les informations de connexion
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Identifiants incorrects'], 401);
        }

        // Créer un token pour l'utilisateur
        $token = $user->createToken('authToken')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 200);
    }




    // Méthode pour la déconnexion
    public function logout(Request $request)
    {
        // Supprimer uniquement le token de l'utilisateur connecté
        $request->user()->currentAccessToken()->delete();

        // Redirection vers la page de connexion avec un message de déconnexion
        return response()->json([
            'message' => 'Déconnexion réussie. Veuillez vous reconnecter.',
            'redirect' => url('/login') // Redirection vers la page de connexion
        ], 200);
    }


    public function verifyCode(Request $request)
{
    // Validation des données reçues
    $validator = Validator::make($request->all(), [
        'email' => 'required|string|email|max:255',
        'auth_code' => 'required|numeric|digits:4',
    ]);

    if ($validator->fails()) {
        return response()->json($validator->errors(), 422);
    }

    // Trouver l'utilisateur par email
    $user = User::where('email', $request->email)->first();

    if (!$user || $user->auth_code !== $request->auth_code) {
        return response()->json(['message' => 'Code d\'authentification invalide.'], 400);
    }

    // Vérifier le code et mettre à jour le statut de vérification
    $user->email_verified = true;
    $user->auth_code = null; // Effacer le code après vérification
    $user->save();

    // Créer un nouveau token pour l'utilisateur
    $token = $user->createToken('authToken')->plainTextToken;

    return response()->json([
        'message' => 'Email vérifié avec succès!',
        'token' => $token,
    ], 200);
}

}

