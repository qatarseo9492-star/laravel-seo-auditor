// In routes/web.php (inside your auth group unless you want guests to analyze)
use App\Http\Controllers\Seo\AnalyzeProxyController;

Route::middleware('auth')->group(function () {
    Route::post('/api/analyze-url', AnalyzeProxyController::class)->name('api.analyze.url');
});
