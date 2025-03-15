<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class ABTestingMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        // Kullanıcıya ait çerez var mı kontrol et
        if (!$request->cookie('ab_test_group')) {
            // %50 şansla "A" veya "B" grubu ata
            $group = rand(0, 1) ? 'A' : 'B';

            // Kullanıcıya çerez olarak grubu ata (30 gün geçerli)
            return redirect($request->url())->cookie('ab_test_group', $group, 43200);

        }

        return $next($request);
    }
}

?>