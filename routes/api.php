use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DetectController;

Route::post('/detect', [DetectController::class, 'detect'])->name('api.detect');
