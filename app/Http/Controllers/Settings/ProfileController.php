<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\ProfileDeleteRequest;
use App\Http\Requests\Settings\ProfileUpdateRequest;
use App\Models\Document;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Show the user's profile settings page.
     */
    public function edit(Request $request): Response
    {
        $user = $request->user();

        // Récupération de l'URL de la photo d'identité : d'abord depuis la table users (private), sinon document/public
        $avatarUrl = null;

        if (!empty($user->photoId)) {
            // Build avatar URL using route key (firstName)
            $avatarUrl = route('users.avatar', ['user' => $user->firstName]);
        } else {
            $document = Document::where('idUser', $user->idUser)->first();
            if ($document) {
                $publicPath = $user->scanPiece ?? $document->scanPermis ?? $document->carte_grise_scan ?? $document->certificatApt_scan ?? null;
                if (!empty($publicPath) && Storage::disk('public')->exists($publicPath)) {
                    $avatarUrl = asset('storage/' . ltrim($publicPath, '/'));
                }
            }
        }

        return Inertia::render('settings/profile', [
            'mustVerifyEmail' => $user instanceof MustVerifyEmail,
            'status' => session('status'),
            'user' => array_merge($user->toArray(), [
                'avatar_url' => $avatarUrl,
                'avatar'     => $avatarUrl,
                'photo'      => $avatarUrl,
            ]),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $validated = $request->validated();

        $nameInput = $validated['name'] ?? $request->input('name');
        if (!empty($nameInput)) {
            $parts = preg_split('/\s+/', trim($nameInput));
            if (count($parts) === 1) {
                $user->firstName = $parts[0];
                $user->lastName = '';
            } else {
                $user->firstName = array_shift($parts);
                $user->lastName = implode(' ', $parts);
            }
        }
        if (array_key_exists('last_name', $validated)) {
            $user->lastName = $validated['last_name'];
        }

        if (array_key_exists('first_name', $validated)) {
            $user->firstName = $validated['first_name'];
        }

        if (array_key_exists('phone_number', $validated)) {
            $user->phoneNumber = $validated['phone_number'];
        }

        if (array_key_exists('sex', $validated)) {
            $user->sex = $validated['sex'];
        }

        if (array_key_exists('birthday', $validated)) {
            $user->birthday = $validated['birthday'];
        }

        if (array_key_exists('address', $validated)) {
            $user->address = $validated['address'];
        }

        if (array_key_exists('numPiece', $validated)) {
            $user->numPiece = trim((string) $validated['numPiece']);
        }

        if (array_key_exists('email', $validated)) {
            $user->email = $validated['email'];
        }

        $file = $request->file('photoId') ?? $request->file('photo_identite') ?? $request->file('avatar');

        if ($file) {
            $extension = pathinfo($file->getClientOriginalName() ?? 'photo.jpg', PATHINFO_EXTENSION) ?: 'jpg';
            $privateBase = 'images/photo_identite/photo_identite_' . $user->idUser . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($user->firstName . '_' . $user->lastName)) . '_' . substr(md5(uniqid((string) time(), true)), 0, 12) . '.' . $extension;
            $contents = file_get_contents($file->getRealPath());
            $encrypted = Crypt::encryptString((string) $contents);

            $oldPhotoPath = $this->normalizePrivatePath($user->photoId);
            if ($oldPhotoPath && Storage::disk('local')->exists($oldPhotoPath)) {
                Storage::disk('local')->delete($oldPhotoPath);
            }

            Storage::disk('local')->put($privateBase, $encrypted);
            $user->photoId = $privateBase;
        }

        $scanFile = $request->file('scanPiece');
        if ($scanFile) {
            $extension = strtolower(pathinfo($scanFile->getClientOriginalName() ?? 'piece.pdf', PATHINFO_EXTENSION)) ?: 'pdf';
            $privateScan = 'images/scanPiece/scan_piece_' . $user->idUser . '_' . preg_replace('/[^a-z0-9]/', '_', strtolower($user->firstName . '_' . $user->lastName)) . '_' . substr(md5(uniqid((string) time(), true)), 0, 12) . '.' . $extension;
            $contents = file_get_contents($scanFile->getRealPath());
            $encrypted = Crypt::encryptString((string) $contents);

            $oldScanPath = $this->normalizePrivatePath($user->scanPiece);
            if ($oldScanPath && Storage::disk('local')->exists($oldScanPath)) {
                Storage::disk('local')->delete($oldScanPath);
            }

            Storage::disk('local')->put($privateScan, $encrypted);
            $user->scanPiece = $privateScan;
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Profile updated.')]);

        return to_route('profile.edit');
    }

    private function normalizePrivatePath(?string $path): ?string
    {
        if (!$path) {
            return null;
        }

        return preg_replace('#^private/#', '', ltrim($path, '/'));
    }

    /**
     * Delete the user's profile.
     */
    public function destroy(ProfileDeleteRequest $request): RedirectResponse
    {
        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
