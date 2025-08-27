// routes/api.php  (or a file included by your RouteServiceProvider)
use App\Http\Controllers\ContentDetectionController;

Route::post('/detect', [ContentDetectionController::class, 'detect']);      // returns JSON
Route::post('/detect/url', [ContentDetectionController::class, 'detectUrl']); // returns JSON
