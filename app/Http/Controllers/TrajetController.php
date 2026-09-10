<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trajet;

class TrajetController extends Controller
{
    // POST /trajets
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'idCar' => 'required|integer',
            'lieuDepart' => 'required|string|max:255',
            'lieuArrivee' => 'required|string|max:255',
            'dateDepart' => 'required|date',
            'heureDepart' => 'required|date_format:H:i',
            'price' => 'required|numeric',
            'placesDispo' => 'required|integer',
            'statut' => 'required|string|max:255',
        ]);

        $trajet = Trajet::create($validatedData);

        return response()->json($trajet, 201);
    }

    // GET /trajets
    public function index()
    {
        return response()->json(Trajet::all());
    }

    // GET /trajets/search
    public function search(Request $request)
    {
        $query = Trajet::query();

        if ($request->has('dateDepart')) {
            $query->where('dateDepart', $request->dateDepart);
        }

        if ($request->has('heureDepart')) {
            $query->where('heureDepart', $request->heureDepart);
        }

        if ($request->has('lieuDepart')) {
            $query->where('lieuDepart', $request->lieuDepart);
        }

        if ($request->has('lieuArrivee')) {
            $query->where('lieuArrivee', $request->lieuArrivee);
        }

        return response()->json($query->get());
    }


    // PUT /trajets/{id}/localisation
    public function updateLocalisation(Request $request, $id)
    {
        $trajet = Trajet::findOrFail($id);
        $trajet->latitude = $request->latitude;
        $trajet->longitude = $request->longitude;
        $trajet->save();

        return response()->json($trajet);
    }
}
