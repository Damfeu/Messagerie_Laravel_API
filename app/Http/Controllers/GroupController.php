<?php

namespace App\Http\Controllers;

use App\Mail\GroupMemberAddedNotification;
use App\Mail\InviteToGroupMail;
use App\Mail\MemberAddedConfirmation;
use App\Models\Fichier;
use App\Models\Groupe;
use App\Models\InvitedMembers;
use App\Models\Membre;
use App\Models\User;
use App\Notifications\GroupNotification;
use App\Notifications\NewMemberNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use PhpParser\Node\Stmt\TryCatch;

class GroupController extends Controller
{
    public function createGroup(Request $request)
    {

        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255', // nom du groupe requis
            'description' => 'required|string|max:255',
        ]);


        // Création du groupe

        try {
            $groupe = Groupe::create([
                'name' => $request->name,
                'description' => $request->description,
            ]);

            return response()->json([
                'message' => 'Groupe créé avec succès!',
                'groupe' => $groupe
            ], 201);
        } catch (\Throwable $th) {
            // return $th;
            return response()->json(['message' => 'erreur de creation', 500]);
        }

        // $groupe = Groupe::create($request->all());
        // return response()->json(['message' => 'Group created successfully', 'group' => $groupe], 201);
    }



    // Méthode pour récupérer la liste des groupes



    public function index()
    {
        // Récupérer tous les groupes
        $groups = Groupe::all();

        // Retourner les groupes en réponse JSON
        return response()->json($groups, 200);
    }


// public function getUserGroups(Request $request)
// {
//     $user = $request->user(); // Récupère l'utilisateur connecté
//     $groups = $user->groups; // Supposons que l'utilisateur a une relation avec les groupes auxquels il est membre
    
//     return response()->json($groups, 201);
// }


    // pour voir les ficher envoyer par chaque membre
    public function getGroups()
    {
        return response()->json(Groupe::with('membres', 'fichiers')->get());
    }

   


    // public function addMember(Request $request, $groupId)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|:membres,email',
    //         'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
    //     ]);

    //     // Récupérer le groupe
    //     $group = Groupe::find($groupId);

    //     if (!$group) {
    //         return response()->json(['message' => 'Groupe introuvable.'], 404);
    //     }

    //     // Ajout du membre
    //     $membre = Membre::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'groupe_id' => $group->id,
    //     ]);

    //     // Récupérer l'utilisateur qui a ajouté le membre (utilisateur actuellement connecté)
    //     $addedBy = auth()->user();

    //     // Envoi de l'email au nouveau membre
    //     Mail::to($membre->email)->send(new MemberAddedConfirmation($request->name, $group->name));

    //     // Récupérer les autres membres du groupe
    //     $members = Membre::where('groupe_id', $group->id)->get();

       

    //     // Envoi de l'email de notification aux autres membres du groupe
    //     foreach ($members as $member) {
    //         Mail::to($member->email)->send(new GroupMemberAddedNotification($membre->name, $addedBy->name, $group->name));
    //     }

    //     return response()->json([
    //         'message' => 'Membre ajouté avec succès au groupe!',
    //         'membre' => $membre
    //     ], 201);


    // }



//     public function addMember(Request $request, $groupId)
// {
//     // Validation des données
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'email' => 'required|email|:membres,email',
//         'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
//     ]);

//     // Récupérer le groupe
//     $group = Groupe::find($groupId);

//     if (!$group) {
//         return response()->json(['message' => 'Groupe introuvable.'], 404);
//     }

//     // Vérifier si le membre est déjà inscrit
//     $membre = Membre::where('email', $request->email)->first();

//     if ($membre) {
//         return response()->json(['message' => 'Ce membre est déjà inscrit.'], 400);
//     }

//     // Ajout du membre
//     $membre = Membre::create([
//         'name' => $request->name,
//         'email' => $request->email,
//         'groupe_id' => $group->id,
//     ]);

//     // Récupérer l'utilisateur qui a ajouté le membre (utilisateur actuellement connecté)
//     $addedBy = auth()->user();

//     // Envoi de l'email au nouveau membre
//     Mail::to($membre->email)->send(new MemberAddedConfirmation($request->name, $group->name));

//     // Récupérer les autres membres du groupe
//     $members = Membre::where('groupe_id', $group->id)->get();

//     // Envoi de l'email de notification aux autres membres du groupe
//     foreach ($members as $member) {
//         Mail::to($member->email)->send(new GroupMemberAddedNotification($membre->name, $addedBy->name, $group->name));
//     }

//     return response()->json([
//         'message' => 'Membre ajouté avec succès au groupe!',
//         'membre' => $membre
//     ], 201);
// }





public function addMember(Request $request, $groupId)
    {
        // Validation des données
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:invited_members,email', // L'email ne doit pas déjà être dans la table des invitations
            'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
        ]);
    
        // Récupérer le groupe
        $group = Groupe::find($groupId);
    
        if (!$group) {
            return response()->json(['message' => 'Groupe introuvable.'], 404);
        }
    
        // Vérifier si l'utilisateur est déjà inscrit dans la table `users`
        $existingUser = User::where('email', $request->email)->first();
    
        if ($existingUser) {
            // Si l'utilisateur est déjà inscrit, on l'ajoute directement au groupe
            $membre = Membre::create([
                'name' => $request->name,
                'email' => $request->email,
                'groupe_id' => $group->id,
            ]);
    
             // Récupérer l'utilisateur qui a ajouté le membre (utilisateur actuellement connecté)
        $addedBy = auth()->user();
    
    //     // Envoi de l'email au nouveau membre
        Mail::to($membre->email)->send(new MemberAddedConfirmation($request->name, $group->name));
    
            // Notifier les autres membres
            $members = Membre::where('groupe_id', $group->id)->get();
            $addedBy = auth()->user();
            foreach ($members as $member) {
                Mail::to($member->email)->send(new GroupMemberAddedNotification($membre->name, $addedBy->name, $group->name));
            }
    
            return response()->json([
                'message' => 'Membre ajouté avec succès au groupe!',
                'membre' => $membre
            ], 201);
        } else {
            // Si l'utilisateur n'est pas inscrit, envoyer une invitation par email
            // Ajouter à la table `invited_members`
            InvitedMembers::create([
                'email' => $request->email,
                'groupe_id' => $group->id,
            ]);
    
            // Envoyer l'email d'invitation
            Mail::to($request->email)->send(new InviteToGroupMail($group->name, $request->name));
    
            return response()->json([
                'message' => 'Invitation envoyée avec succès à l\'email: ' . $request->email,
            ], 201);
        }
    }



public function uploadFile(Request $request, $groupId)
{
    $request->validate([
        'file_name' => 'required|file|max:10000000',
        'groupe_id' => 'required|exists:groupes,id',
    ]);

    if ($request->hasFile('file_name') && $request->file('file_name')->isValid()) {
        $filePath = $request->file('file_name')->store('services', 'public');
        $data['file_name'] = $filePath;

        $fichier = Fichier::create([
            'file_path' => $filePath,
            'groupe_id' => $request->groupe_id,
        ]);
    }

    // Ajout de l'utilisateur qui a uploadé le fichier (si authentifié)
    $uploadedBy = auth()->check() ? auth()->user()->name : 'Utilisateur inconnu';

    return response()->json([
        'message' => 'Fichier ajouté au groupe avec succès!',
        'fichier' => [
            'file_path' => $fichier->file_path,
            'uploaded_by' => $uploadedBy,  // Retourne le nom de l'utilisateur ou 'Utilisateur inconnu'
        ]
    ], 201);
}
}