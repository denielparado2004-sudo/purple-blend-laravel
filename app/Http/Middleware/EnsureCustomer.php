<?php

namespace App\Http\Middleware;

use App\Models\Customer;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCustomer
{
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Customer::find($request->session()->get('customer_id'));

        if (!$customer) {
            $request->session()->forget('customer_id');

            return redirect()->guest(route('login'))->with('status', 'Please sign in to continue.');
        }

        $request->attributes->set('customer', $customer);

        return $next($request);
    }
}
