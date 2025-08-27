use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

public function boot(): void
{
    RateLimiter::for('content-detection', function ($request) {
        return [Limit::perHour(100)->by(optional($request->user())->id ?: $request->ip())];
    });
}
