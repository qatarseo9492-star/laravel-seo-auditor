<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminCheck
{
    /**
     * Handle an incoming request.
     *
     * Ensures the authenticated user is an administrator.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check() || !Auth::user()->is_admin) {
            // Redirect non-admins to the regular dashboard or home page
            return redirect('/dashboard')->with('error', 'You do not have permission to access this page.');
        }

        return $next($request);
    }
}
```

### **Step 2: Register the New Middleware**

Now, you need to tell Laravel about your new `AdminCheck` middleware.

1.  Open your `app/Http/Kernel.php` file.
2.  Add the following line to the `$middlewareAliases` array:

    ```php
    'admin' => \App\Http\Middleware\AdminCheck::class,
    
