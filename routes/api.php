// routes/api.php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('/detect', function (Request $req) {
    // TODO: call ZeroGPT/GPTZero/OriginalityAI etc. and aggregate.
    return response()->json([
        'ok' => true,
        'language'  => ['code' => 'en', 'confidence' => 0.98],
        'aiPct'     => 62,
        'humanPct'  => 38,
        'confidence'=> 87,
        'detectors' => [
            ['key' => 'zerogpt',     'label' => 'ZeroGPT',      'ai' => 64],
            ['key' => 'gptzero',     'label' => 'GPTZero',      'ai' => 59],
            ['key' => 'originality', 'label' => 'OriginalityAI','ai' => 63],
        ],
    ]);
})->name('detect');
