<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $admin = Admin::whereKey($request->session()->get('admin_id'))
            ->where('is_active', true)
            ->first();

        if (!$admin) {
            $request->session()->forget(['admin_id', 'admin_username', 'admin_role']);

            return redirect()->guest(route('admin.login'))->with('status', 'Please sign in as an administrator.');
        }

        $request->attributes->set('admin', $admin);

        return $next($request);
    }
}
