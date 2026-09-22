<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureProfile
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();

        if ($user && ! $this->hasRequiredUserProfile($user) && ! $request->routeIs('user-profile.*')) {
            return redirect()->route('profile.upload');
        }

        return $next($request);
    }

    protected function hasRequiredUserProfile($user): bool
    {
        $required = config('user-profile.required', ['photoId','numPiece']);

        // First, check users table columns if present
        foreach ($required as $field) {
            if (in_array($field, ['photoId','numPiece','scanPiece'], true)) {
                if (empty($user->{$field})) {
                    // fallback to document table
                    $doc = $user->documents()->first();
                    if (! $doc || empty($doc->{$field})) return false;
                }
            } else {
                $doc = $user->documents()->first();
                if (! $doc || empty($doc->{$field})) return false;
            }
        }

        return true;
    }
}
