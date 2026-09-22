<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class DocumentCarCheckController extends Controller
{
    /**
     * Vérifie si l'utilisateur a complété ses documents et sa voiture
     */
    public function check(Request $request)
    {
        $user = Auth::user();
        $document = $user->document;
        $car = $user->car;

        // Vérifier si les documents et voiture sont complétés
        $documentComplete = $document &&
            !empty($document->numPermis) &&
            !empty($document->scanPermis) &&
            !empty($document->certificatApt) &&
            !empty($document->certificatApt_scan);

        $carComplete = $car &&
            !empty($car->mark) &&
            !empty($car->model) &&
            !empty($car->registrationNumber) &&
            !empty($car->nbrPlaces) &&
            !empty($car->carte_grise_scan) &&
            !empty($car->assurance_numero) &&
            !empty($car->assurance_expiration);

        return response()->json([
            'documentComplete' => $documentComplete,
            'carComplete' => $carComplete,
            'document' => $document,
            'car' => $car,
            'redirectTo' => (!$documentComplete || !$carComplete) ?
                route('rides.verify-before-publish') :
                route('rides.create')
        ]);
    }

    /**
     * Page de vérification avant publication
     */
    public function verifyBeforePublish()
    {
        $user = Auth::user();
        $document = $user->document;
        $car = $user->car;

        $documentComplete = $document &&
            !empty($document->numPermis) &&
            !empty($document->scanPermis) &&
            !empty($document->certificatApt) &&
            !empty($document->certificatApt_scan);

        $carComplete = $car &&
            !empty($car->mark) &&
            !empty($car->model) &&
            !empty($car->registrationNumber) &&
            !empty($car->nbrPlaces) &&
            !empty($car->carte_grise_scan) &&
            !empty($car->assurance_numero) &&
            !empty($car->assurance_expiration);

        // Si tout est complet, rediriger vers la création
        if ($documentComplete && $carComplete) {
            return redirect()->route('rides.create');
        }

        return Inertia::render('rides/verify-before-publish', [
            'documentComplete' => $documentComplete,
            'document' => $document,
            'carComplete' => $carComplete,
            'car' => $car,
        ]);
    }
}
