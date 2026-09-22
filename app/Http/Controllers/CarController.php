<?php

namespace App\Http\Controllers;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class CarController extends Controller
{
    /**
     * Affiche le formulaire et la liste des voitures
     */
    public function create()
    {
        $user = Auth::user();
        $cars = $user->cars()->get();

        return Inertia::render('car/create', [
            'cars' => $cars,
        ]);
    }

    /**
     * Display all cars (admin only)
     */
    public function index()
    {
        try {
            $cars = Car::all();
            return response()->json([
                'success' => true,
                'message' => 'Liste des Véhicules récupérée avec succès',
                'data' => $cars
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des Véhicules',
                'errors' => ['exception' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Get user's cars
     */
    public function getUserCars($userId)
    {
        try {
            $cars = Car::where('idUser', $userId)->get();
            return response()->json([
                'success' => true,
                'message' => 'Véhicules de l\'utilisateur récupérées',
                'data' => $cars
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des Véhicules',
                'errors' => ['exception' => $e->getMessage()]
            ], 500);
        }
    }

    /**
     * Enregistrer un nouveau véhicule.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'mark' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:car,registrationNumber',
            'nbr_places' => 'required|integer|min:1|max:9',
            'color' => 'required|string|max:255',
            'carte_grise_numero' => 'nullable|string|max:255',
            'carte_grise_scan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'assurance_numero' => 'nullable|string|max:255',
            'assurance_expiration' => 'nullable|date',
        ], [
            'mark.required' => 'La marque est obligatoire.',
            'model.required' => 'Le modèle est obligatoire.',
            'registration_number.required' => 'Le numéro d\'immatriculation est obligatoire.',
            'registration_number.unique' => 'Ce numéro d\'immatriculation existe déjà.',
            'nbr_places.required' => 'Le nombre de places est obligatoire.',
        ]);

        $car = Car::create([
            'idUser' => Auth::id(),
            'type' => $validated['type'],
            'mark' => $validated['mark'],
            'model' => $validated['model'],
            'registrationNumber' => $validated['registration_number'],
            'nbrPlaces' => $validated['nbr_places'],
            'color' => $validated['color'],
            'carte_grise_numero' => $validated['carte_grise_numero'] ?? null,
            'carte_grise_scan' => $request->hasFile('carte_grise_scan')
                ? $request->file('carte_grise_scan')->store('images/carte_grise_scan', 'local')
                : null,
            'assurance_numero' => $validated['assurance_numero'] ?? null,
            'assurance_expiration' => $validated['assurance_expiration'] ?? null,
        ]);

        // Assigner le rôle "Conducteur" (ID: 2) de manière unique sans doublons
        $currentUser = Auth::user();
        if ($currentUser) {
            $currentUser->roles()->syncWithoutDetaching([2]);
        }

        return redirect()->route('cars.create')->with('success', 'Véhicule ajouté avec succès');
    }

    /**
     * Afficher les détails d'un véhicule spécifique.
     */
    public function show($id)
    {
        try {
            $car = Car::findOrFail($id);
            return response()->json([
                'success' => true,
                'message' => 'Véhicule récupéré avec succès',
                'data' => $car
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Véhicule non trouvé',
                'errors' => ['exception' => $e->getMessage()]
            ], 404);
        }
    }

    /**
     * Mettre à jour les informations d'un véhicule.
     */
    public function update(Car $car, Request $request)
    {
        // Vérification de l'autorisation de l'utilisateur
        if ($car->idUser !== Auth::id()) {
            return redirect()->back()->with('error', 'Non autorisé à modifier ce véhicule');
        }

        $validated = $request->validate([
            'type' => 'required|string|max:255',
            'mark' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'registration_number' => 'required|string|max:255|unique:car,registrationNumber,' . $car->idCar . ',idCar',
            'nbr_places' => 'required|integer|min:1|max:9',
            'color' => 'required|string|max:255',
            'carte_grise_numero' => 'nullable|string|max:255',
            'carte_grise_scan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
            'assurance_numero' => 'nullable|string|max:255',
            'assurance_expiration' => 'nullable|date',
        ]);

        // Mapper les clés snake_case vers camelCase pour le modèle
        $updateData = [
            'type' => $validated['type'],
            'mark' => $validated['mark'],
            'model' => $validated['model'],
            'registrationNumber' => $validated['registration_number'],
            'nbrPlaces' => $validated['nbr_places'],
            'color' => $validated['color'],
            'carte_grise_numero' => $validated['carte_grise_numero'] ?? $car->carte_grise_numero,
            'assurance_numero' => $validated['assurance_numero'] ?? $car->assurance_numero,
            'assurance_expiration' => $validated['assurance_expiration'] ?? $car->assurance_expiration,
        ];

        if ($request->hasFile('carte_grise_scan')) {
            if ($car->carte_grise_scan) {
                Storage::disk('local')->delete($car->carte_grise_scan);
            }

            $updateData['carte_grise_scan'] = $request->file('carte_grise_scan')
                ->store('images/carte_grise_scan', 'local');
        }

        $car->update($updateData);

        return redirect()->route('cars.create')->with('success', 'Véhicule mise à jour avec succès');
    }

    /**
     * Afficher le scan de carte grise du propriétaire du véhicule.
     */
    public function streamCarteGriseScan(Car $car)
    {
        abort_unless($car->idUser === Auth::id(), 403);

        abort_unless($car->carte_grise_scan, 404);

        $disk = Storage::disk('local');
        abort_unless($disk->exists($car->carte_grise_scan), 404);

        return response()->file($disk->path($car->carte_grise_scan));
    }

    /**
     * Delete the specified car
     */
    public function destroy(Car $car)
    {
        // Check authorization
        if ($car->idUser !== Auth::id()) {
            return redirect()->back()->with('error', 'Non autorisé à supprimer ce véhicule');
        }

        $car->delete();

        return redirect()->route('cars.create')->with('success', 'Véhicule supprimé avec succès');
    }
}
