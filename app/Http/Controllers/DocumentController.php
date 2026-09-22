<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DocumentController extends Controller
{
    /**
     * Affiche la liste de tous les documents (Admin).
     */
    public function index()
    {
        try {
            $documents = Document::with('user')->get();

            return response()->json([
                'success' => true,
                'message' => 'Liste des documents récupérée avec succès.',
                'data' => $documents,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des documents.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Rendu de la page Inertia de création/modification de document.
     */
    public function create(Request $request)
    {
        $user = $request->user();
        $document = Document::where('idUser', $user->idUser)->first();

        return Inertia::render('documents/create', [
            'document' => $document,
            'auth' => [
                'user' => $user ? [
                    'id' => $user->idUser,
                    'firstName' => $user->firstName ?? null,
                    'lastName' => $user->lastName ?? null,
                    'email' => $user->email ?? null,
                ] : null,
            ],
        ]);
    }

    /**
     * Récupère le document d'un utilisateur spécifique.
     */
    public function getUserDocuments($userId)
    {
        try {
            $document = Document::where('idUser', $userId)->first();

            return response()->json([
                'success' => true,
                'message' => 'Document de l\'utilisateur récupéré.',
                'data' => $document,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération du document.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Enregistre ou met à jour les documents de l'utilisateur connecté.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'numPermis' => 'nullable|string|max:255',
            'permis_expiration' => 'nullable|date',
            'scanPermis' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
            'certificatApt' => 'nullable|string|max:255',
            'certificatApt_scan' => 'nullable|file|mimes:pdf,jpg,jpeg,png,webp|max:5120',
        ], [
            'scanPermis.mimes' => 'Le scan du permis doit être un fichier PDF, JPG, JPEG, PNG ou WEBP.',
            'scanPermis.max' => 'Le scan du permis ne doit pas dépasser 5 Mo.',
            'certificatApt_scan.mimes' => 'Le certificat d\'aptitude doit être un fichier PDF, JPG, JPEG, PNG ou WEBP.',
            'certificatApt_scan.max' => 'Le certificat d\'aptitude ne doit pas dépasser 5 Mo.',
        ]);

        try {
            $userId = Auth::id();

            // Récupère le document existant ou instancie un nouveau
            $document = Document::firstOrNew(['idUser' => $userId]);

            // Mise à jour des champs textuels
            if ($request->has('numPermis')) {
                $document->numPermis = $validated['numPermis'];
            }
            if ($request->has('permis_expiration')) {
                $document->permis_expiration = $validated['permis_expiration'];
            }
            if ($request->has('certificatApt')) {
                $document->certificatApt = $validated['certificatApt'];
            }

            // Gestion du téléversement du scan de permis
            if ($request->hasFile('scanPermis')) {
                $this->deleteStoredDocument($document->scanPermis);
                $document->scanPermis = $request->file('scanPermis')->store('images/scanPermis', 'local');
            }

            // Gestion du téléversement du certificat d'aptitude
            if ($request->hasFile('certificatApt_scan')) {
                $this->deleteStoredDocument($document->certificatApt_scan);
                $document->certificatApt_scan = $request->file('certificatApt_scan')->store('images/certificatApt_scan', 'local');
            }

            $document->save();

            return redirect()->route('documents.create')
                ->with('success', 'Documents enregistrés avec succès.');

        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'enregistrement du document :', [
                'user_id' => Auth::id(),
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de l\'enregistrement du document.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Affiche un document spécifique.
     */
    public function show($id)
    {
        try {
            $document = Document::with('user')->findOrFail($id);

            return response()->json([
                'success' => true,
                'data' => $document,
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Document introuvable.',
            ], 404);
        }
    }

    /**
     * Supprime un document (Soft Delete + nettoyage du disque).
     */
    public function destroy($id)
    {
        try {
            $document = Document::findOrFail($id);

            // Vérification des droits d'accès
            if ((int) $document->idUser !== (int) Auth::id()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Action non autorisée.',
                ], 403);
            }

            // Nettoyage des fichiers physiquement présents
            $this->deleteStoredDocument($document->scanPermis);
            $this->deleteStoredDocument($document->certificatApt_scan);

            // Application du SoftDelete
            $document->delete();

            return response()->json([
                'success' => true,
                'message' => 'Document supprimé avec succès.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function deleteStoredDocument(?string $path): void
    {
        if (!$path) {
            return;
        }

        foreach (['local', 'public'] as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                Storage::disk($disk)->delete($path);
            }
        }
    }
}
