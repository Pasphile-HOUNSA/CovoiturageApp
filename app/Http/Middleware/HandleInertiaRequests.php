<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'name' => config('app.name'),
            'auth' => [
                'user' => $request->user() ? (function () use ($request) {
                    $user = $request->user();
                    $data = $user->toArray();

                    $userSlug = $user->idUser;
                    $avatarUrl = null;

                    $photoPath = $user->photoId
                        ? preg_replace('#^private/#', '', ltrim($user->photoId, '/'))
                        : null;

                    if ($photoPath && Storage::disk('local')->exists($photoPath)) {
                        $avatarUrl = route('users.avatar', ['user' => $userSlug]);
                    }

                    $data['avatar_url'] = $avatarUrl;
                    $data['user_slug'] = $userSlug;

                    return $data;
                })() : null,
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
