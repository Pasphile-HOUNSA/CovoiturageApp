<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class UserController extends Controller
{
    public function update(Request $request, $userParam = null)
    {
        // Si $userParam est fourni, on cherche cet utilisateur, sinon on prend l'utilisateur connecté
        if ($userParam) {
            $user = User::where('firstName', $userParam)
                ->orWhere('idUser', $userParam)
                ->first();
        } else {
            $user = Auth::user();
        }

        if (!$user) {
            return redirect()->back()->withErrors(['error' => 'Utilisateur introuvable.']);
        }

        // Diagnostics: log state des fichiers/en-têtes pour debug des uploads
        try {
            $filesSuper = [];
            foreach ($_FILES ?? [] as $k => $v) {
                $filesSuper[$k] = [
                    'name' => $v['name'] ?? null,
                    'type' => $v['type'] ?? null,
                    'tmp_name' => $v['tmp_name'] ?? null,
                    'error' => $v['error'] ?? null,
                    'size' => $v['size'] ?? null,
                ];
            }

            Log::info('Upload debug — request files', [
                'hasFile_photoId' => $request->hasFile('photoId'),
                'hasFile_scanPiece' => $request->hasFile('scanPiece'),
                'allFiles' => array_keys($request->allFiles()),
                '$_FILES' => $filesSuper,
                'content_length' => $request->header('content-length'),
                'content_type' => $request->header('content-type'),
                'post_max_size' => ini_get('post_max_size'),
                'upload_max_filesize' => ini_get('upload_max_filesize'),
                'upload_tmp_dir' => ini_get('upload_tmp_dir'),
            ]);
        } catch (\Exception $e) {
            Log::warning('Upload debug failed to collect diagnostics: '.$e->getMessage());
        }

        $rules = [
            'numPiece'  => ['nullable', 'string', 'max:255'],
            'photoId'   => ['nullable', 'file', 'mimes:jpg,jpeg,png,webp', 'max:5120'],
            'scanPiece' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ];

        Log::info('upload debug', [ 'error_code' => $request->file('photoId')?->getError(), 'is_valid' => $request->file('photoId')?->isValid(), ]);

        foreach (['photoId', 'scanPiece'] as $fileKey) {
            $file = $request->file($fileKey);
            if ($file && $file->getError() !== UPLOAD_ERR_OK) {
                $message = match ($file->getError()) {
                    UPLOAD_ERR_NO_TMP_DIR => 'Le dossier temporaire PHP est absent. Redémarrez le serveur PHP après sa création.',
                    UPLOAD_ERR_CANT_WRITE => 'PHP ne peut pas écrire le fichier temporaire. Vérifiez les droits du dossier temporaire.',
                    UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'Le fichier dépasse la taille maximale autorisée par PHP.',
                    default => 'Le fichier n’a pas pu être reçu par PHP.',
                };

                return redirect()->back()->withErrors([$fileKey => $message]);
            }
        }

        $request->validate($rules);

        $userSlugName = preg_replace('/[^a-z0-9]/', '_', strtolower(($user->firstName ?? 'user') . '_' . ($user->lastName ?? '')));

        // 1. Traitement de la Photo de profil
        if ($request->hasFile('photoId')) {
            $file = $request->file('photoId');
            $extension = $file->getClientOriginalExtension() ?: 'jpg';

            $pathPhoto = 'images/photo_identite/photo_identite_' . $user->idUser . '_' . $userSlugName . '_' . substr(md5(uniqid((string) time(), true)), 0, 8) . '.' . $extension;

            if (!empty($user->photoId) && Storage::disk('local')->exists($user->photoId)) {
                Storage::disk('local')->delete($user->photoId);
            }

            $contents = file_get_contents($file->getRealPath());
            Storage::disk('local')->put($pathPhoto, Crypt::encryptString((string) $contents));

            $user->photoId = $pathPhoto;
        }

        // 2. Traitement de la Pièce d'identité
        if ($request->hasFile('scanPiece')) {
            $file = $request->file('scanPiece');
            $extension = $file->getClientOriginalExtension() ?: 'pdf';

            $pathScan = 'images/scanPiece/scan_piece_' . $user->idUser . '_' . $userSlugName . '_' . substr(md5(uniqid((string) time(), true)), 0, 8) . '.' . $extension;

            if (!empty($user->scanPiece) && Storage::disk('local')->exists($user->scanPiece)) {
                Storage::disk('local')->delete($user->scanPiece);
            }

            $contents = file_get_contents($file->getRealPath());
            Storage::disk('local')->put($pathScan, Crypt::encryptString((string) $contents));

            $user->scanPiece = $pathScan;
        }

        if ($request->filled('numPiece')) {
            $user->numPiece = trim($request->input('numPiece'));
        }

        // Sauvegarde effective en Base de Données
        $user->save();

        // Redirection vers la page d'accueil après upload
        return redirect()->route('acceuil')->with('success', 'Documents mis à jour avec succès.');
    }

    /**
     * Stream user's avatar (decrypted) from local storage.
     */
    public function streamAvatar(User $user)
    {
        $path = $this->normalizePrivatePath($user->photoId);
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        try {
            $encrypted = Storage::disk('local')->get($path);
            $contents = Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            Log::error('Failed to read/decrypt avatar: '.$e->getMessage());
            abort(500);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($contents) ?: 'application/octet-stream';

        return response($contents, 200)->header('Content-Type', $mime);
    }

    /**
     * Stream user's scanPiece (decrypted) from local storage.
     */
    public function streamScan(User $user)
    {
        $path = $this->normalizePrivatePath($user->scanPiece);
        if (!$path || !Storage::disk('local')->exists($path)) {
            abort(404);
        }

        try {
            $encrypted = Storage::disk('local')->get($path);
            $contents = Crypt::decryptString($encrypted);
        } catch (\Exception $e) {
            Log::error('Failed to read/decrypt scanPiece: '.$e->getMessage());
            abort(500);
        }

        $finfo = new \finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->buffer($contents) ?: 'application/octet-stream';

        return response($contents, 200)->header('Content-Type', $mime);
    }

    private function normalizePrivatePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return preg_replace('#^private/#', '', ltrim($path, '/'));
    }
}
