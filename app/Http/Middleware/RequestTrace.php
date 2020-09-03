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
      $user_id = 0;
      \Log::warning("----RequestTrace start----");
      if(isset($user)) $user_id = $user->id;
      ActionLog::add($request, $user_id);
      \Log::warning("----RequestTrace end----");
      return $next($request);
    }
}
