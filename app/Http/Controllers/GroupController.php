<?php

namespace App\Http\Controllers;

use App\Mail\GroupMemberAddedNotification;
use App\Mail\MemberAddedConfirmation;
use App\Models\Fichier;
use App\Models\Groupe;
use App\Models\Membre;
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
            'description'=> 'required|string|max:255',
        ]);
        

        // Création du groupe

       try {
        $groupe = Groupe::create([
            'name' => $request->name,
            'description'=>$request->description,
        ]);

        return response()->json([
            'message' => 'Groupe créé avec succès!',
            'groupe' => $groupe
        ], 201);
       } catch (\Throwable $th) {
        // return $th;
        return response()->json(['message'=> 'erreur de creation', 500]);
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
    //         'email' => 'required|email|unique:membres,email',
    //         'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
    //     ]);

    //     // Ajout du membre
    //     $membre = Membre::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'groupe_id' => $request->groupe_id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Membre ajouté avec succès au groupe!',
    //         'membre' => $membre
    //     ], 201);

      
    // }




    // public function addMember(Request $request, $groupId)
    // {
    //     // Validation des données
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|email|unique:membres,email',
    //         'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
    //     ]);
    
    //     // Ajout du membre
    //     $membre = Membre::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'groupe_id' => $request->groupe_id,
    //     ]);
    
    //     // Récupérer les détails du groupe et du créateur
    //     $group = Groupe::find($groupId);
    //     $addedBy = auth()->user(); // L'utilisateur qui ajoute le membre
    
    //     // Envoyer un email au nouveau membre pour confirmer son ajout
    //     Mail::to($membre->email)->send(new MemberAddedConfirmation($membre->name, $group->name));
    
    //     // Récupérer tous les membres du groupe sauf le nouveau membre
    //     $groupMembers = Membre::where('groupe_id', $groupId)
    //                           ->where('id', '!=', $membre->id) // Exclure le nouveau membre
    //                           ->get();
    
    //     // Envoyer un email à chaque membre existant du groupe pour notifier de l'ajout
    //     foreach ($groupMembers as $member) {
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
        'email' => 'required|email|unique:membres,email',
        'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
    ]);

    // Récupérer le groupe
    $group = Groupe::find($groupId);

    if (!$group) {
        return response()->json(['message' => 'Groupe introuvable.'], 404);
    }

    // Ajout du membre
    $membre = Membre::create([
        'name' => $request->name,
        'email' => $request->email,
        'groupe_id' => $group->id,
    ]);

    // Récupérer l'utilisateur qui a ajouté le membre (utilisateur actuellement connecté)
    $addedBy = auth()->user();

    // Envoi de l'email au nouveau membre
    Mail::to($membre->email)->send(new MemberAddedConfirmation($membre->name, $group->name));

    // Récupérer les autres membres du groupe
    $members = Membre::where('groupe_id', $group->id)->get();

    // Envoi de l'email de notification aux autres membres du groupe
    foreach ($members as $member) {
        Mail::to($member->email)->send(new GroupMemberAddedNotification($membre->name, $addedBy->name, $group->name));
    }

    return response()->json([
        'message' => 'Membre ajouté avec succès au groupe!',
        'membre' => $membre
    ], 201);
}


    

    

    public function uploadFile(Request $request, $groupId)
    {
        // Validation des données
        $request->validate([
            'file_name' => 'required|file|max:10000000',
            // 'file_path' => 'required|string', // Path (chemin) du fichier requis
            'groupe_id' => 'required|exists:groupes,id', // Le groupe doit exister
        ]);

        if ($request->hasFile('file_name') && $request->file('file_name')->isValid()) {
            $filePath = $request->file('file_name')->store('services', 'public');
            $data['file_name'] = $filePath;

            $fichier = Fichier::create([
                // 'file_name' => $request->file_name,
                'file_path' => $filePath,
                'groupe_id' => $request->groupe_id,
            ]);
        }

        // Ajout du fichier
       

        return response()->json([
            'message' => 'Fichier ajouté au groupe avec succès!',
            'fichier' => $fichier
        ], 201);





        // $groupe = Groupe::find($groupId);

        // if (!$groupe) {
        //     return response()->json(['message' => 'Group not found'], 404);
        // }

        // $file = $request->file('file');
        // $filePath = $file->store('group_files', 'public');

        // $fichier = $groupe->fichiers()->create([
        //     'file_name' => $file->getClientOriginalName(),
        //     'file_path' => $filePath,
        // ]);

        // return response()->json(['message' => 'File uploaded successfully', 'file' => $fichier]);
    }
}