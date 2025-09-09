@extends('layouts.app')
<tbody>
<tr><td class="muted" colspan="6">Loading…</td></tr>
</tbody>
</table>
</div>
</div>


<!-- Quick tabs linking to existing pages -->
<div class="grid" style="margin-top:16px">
<div class="card">
<div class="card-head">
<div>
<div class="card-title">User Management</div>
<div class="card-sub">Searchable list, limits, impersonate</div>
</div>
<a class="btn ghost" href="{{ route('admin.users') }}">Open</a>
</div>
<p class="card-sub">Go to Users to grant credits, suspend, reset password, or view usage logs.</p>
</div>
<div class="card">
<div class="card-head">
<div>
<div class="card-title">Subscriptions & Billing</div>
<div class="card-sub">Plans, MRR/ARR, coupons</div>
</div>
<a class="btn ghost" href="{{ route('admin.billing') }}">Open</a>
</div>
<p class="card-sub">Create plans, track revenue, view transactions, manage discounts.</p>
</div>
<div class="card">
<div class="card-head">
<div>
<div class="card-title">Announcements</div>
<div class="card-sub">Global banners & popups</div>
</div>
<a class="btn ghost" href="{{ route('admin.announcements.index') }}">Open</a>
</div>
<p class="card-sub">Publish maintenance notices or feature launches.</p>
</div>
</div>
</div>


<!-- Your uploaded JS powers the live refresh + history filter/export -->
<script src="{{ asset('js/admin-dashboard.js') }}" defer></script>
<script>
// Boot a Chart.js line chart; admin-dashboard.js will keep it updated via window.AdminTrafficChart
document.addEventListener('DOMContentLoaded', function(){
const ctx = document.getElementById('trafficChart');
if (!ctx || !window.Chart) return;
const ds = {
label: 'Daily Analyses',
data: [],
fill: false,
tension: 0.3,
};
window.AdminTrafficChart = new Chart(ctx, {
type: 'line',
data: { labels: [], datasets: [ds] },
options: {
responsive: true,
maintainAspectRatio: false,
plugins: { legend: { display: false } },
scales: { y: { beginAtZero: true } }
}
});
});
</script>
@endsection
