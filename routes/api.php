use App\Http\Controllers\ContentDetectionController;

Route::post('/detect',     [ContentDetectionController::class, 'detect']);      // JSON
Route::post('/detect/url', [ContentDetectionController::class, 'detectUrl']);   // JSON
