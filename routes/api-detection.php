// app/Providers/RouteServiceProvider.php
public function boot(): void
{
    parent::boot();

    \Illuminate\Support\Facades\Route::middleware('api')
        ->prefix('api')
        ->group(base_path('routes/api-detection.php'));
}
