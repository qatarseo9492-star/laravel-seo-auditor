use App\Http\Controllers\ContentDetectionController;

Route::post('/detect',     [ContentDetectionController::class, 'detect']);
Route::post('/detect/url', [ContentDetectionController::class, 'detectUrl']);
