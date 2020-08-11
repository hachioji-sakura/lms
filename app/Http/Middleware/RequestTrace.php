<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\ActionLog;
use Illuminate\Support\Facades\Auth;

class RequestTrace
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
      $user = Auth::user();
      ActionLog::add($user->id);
      return $next($request);
    }
}
