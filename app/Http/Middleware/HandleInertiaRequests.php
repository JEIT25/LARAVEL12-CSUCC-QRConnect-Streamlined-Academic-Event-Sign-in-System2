<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
        public function share(Request $request): array
        {
            return array_merge(parent::share($request), [
                'logoUrl' => asset('assets/images/logos/qr-logo.png'),
                'qrBackground' => asset('assets/images/backgrounds/qr-gif.gif'),
                'coloredBackgroundImage' => asset('assets/images/backgrounds/welcome-bg.png'),
                'messages' => [
                    'success' => $request->session()->get('success'), //get if success exist in session and pass to templates
                    'failed' => $request->session()->get('failed'),//get if failed exist in session and pass to templates
                ],
                'user' => $request->user() ? [ //ternary conditional values
                    'id' => $request->user()->id, //if authenticated user return and included values id,fname,lname,type
                    'fname' => $request->user()->fname,
                    'lname' => $request->user()->lname,
                    'type' => $request->user()->type,
                ] : null //else if false , return null value, user is null not authenticated
            ]);
        }
}
