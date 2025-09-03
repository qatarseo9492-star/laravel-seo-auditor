@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('head')
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
@endpush

@section('content')
<div class="admin-container">
    <!-- Header with Breadcrumbs and Stats -->
    <header class="admin-header">
        <div>
            <h1 class="header-title">Dashboard</h1>
            <p class="breadcrumbs">Admin / Overview</p>
        </div>
        <div class="header-gauge" data-value="{{ $searchesToday }}" data-max="1000">
            <svg viewBox="0 0 36 36">
                <path class="gauge-track" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
                <path class="gauge-progress" id="dailyUsageGauge" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
            </svg>
            <div class="gauge-label">
                <span id="dailyUsageValue">{{ $searchesToday }}</span>
                <span class="gauge-sublabel">Searches Today</span>
            </div>
        </div>
    </header>

    <!-- Stat Cards -->
    <div class="stat-cards-grid">
        <div class="stat-card">
            <div class="card-icon icon-users"></div>
            <div>
                <p class="card-title">Total Users</p>
                <p class="card-value">{{ $totalUsers }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-icon icon-openai"></div>
            <div>
                <p class="card-title">OpenAI Cost Today</p>
                <p class="card-value">${{ number_format($openAiCostToday, 4) }}</p>
            </div>
        </div>
        <div class="stat-card">
            <div class="card-icon icon-active"></div>
            <div>
                <p class="card-title">Active Users (5 min)</p>
                <p class="card-value">{{ $activeUsers }}</p>
            </div>
        </div>
    </div>
    
    @if (session('success'))
        <div class="alert-success">
            {{ session('success') }}
        </div>
    @endif

    <!-- Main Content with Tabs -->
    <div class="main-content">
        <div class="tabs">
            <button class="tab-button active" onclick="openTab(event, 'users')">User Management</button>
            <button class="tab-button" onclick="openTab(event, 'searches')">Search History</button>
        </div>

        <!-- Users Tab -->
        <div id="users" class="tab-content active">
            <div class="table-wrapper">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>User</th>
                            <th>Status</th>
                            <th>Searches (Today/Month)</th>
                            <th>Limits (Daily/Monthly)</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr>
                                <td>
                                    <div class="user-info">{{ $user->name }}</div>
                                    <div class="user-subinfo">{{ $user->email }}</div>
                                </td>
                                <td>
                                    @if($user->is_banned)
                                        <span class="status-pill status-banned">Banned</span>
                                    @else
                                        <span class="status-pill status-active">Active</span>
                                    @endif
                                </td>
                                <td>{{ $user->limit->searches_today ?? 0 }} / {{ $user->limit->searches_this_month ?? 0 }}</td>
                                <td>
                                    <form action="{{-- route('admin.users.limit', $user) --}}" method="POST" class="limit-form">
                                        @csrf
                                        @method('PATCH')
                                        <input type="number" name="daily_limit" class="limit-input" value="{{ $user->limit->daily_limit ?? 10 }}">
                                        <input type="number" name="monthly_limit" class="limit-input" value="{{ $user->limit->monthly_limit ?? 300 }}">
                                        <button type="submit" class="btn-save">Save</button>
                                    </form>
                                </td>
                                <td>
                                    <form action="{{-- route('admin.users.ban', $user) --}}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn-ban {{ $user->is_banned ? 'btn-unban' : '' }}">
                                            {{ $user->is_banned ? 'Unban' : 'Ban' }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5">No users found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="pagination-links">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Searches Tab -->
        <div id="searches" class="tab-content">
           <p style="text-align:center; padding: 2rem;">Search history table would go here.</p>
        </div>
    </div>
</div>

<script src="{{ asset('js/admin-dashboard.js') }}"></script>
@endsection
