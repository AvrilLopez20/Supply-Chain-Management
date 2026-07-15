@extends('layouts.app')

@section('content')
<div class="space-y-6">
    <div class="rounded-2xl border border-erp-border bg-white p-6 shadow-sm">
        <div class="flex items-center gap-4">
            <div class="flex h-16 w-16 items-center justify-center rounded-full bg-erp-darkGreen/10 text-xl font-bold text-erp-darkGreen">
                {{ strtoupper(substr($user['name'], 0, 2)) }}
            </div>
            <div>
                <h1 class="text-2xl font-bold text-erp-text">{{ $user['name'] }}</h1>
                <p class="text-erp-textMuted">{{ $user['role'] }}</p>
                <p class="text-sm text-erp-textMuted">{{ $user['email'] }}</p>
            </div>
        </div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div class="rounded-2xl border border-erp-border bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-erp-text">Account Overview</h2>
            <div class="mt-4 space-y-3 text-sm text-erp-textMuted">
                <div class="flex justify-between"><span>Department</span><span class="font-medium text-erp-text">Operations</span></div>
                <div class="flex justify-between"><span>Timezone</span><span class="font-medium text-erp-text">UTC +3</span></div>
                <div class="flex justify-between"><span>Status</span><span class="font-medium text-erp-text">Active</span></div>
            </div>
        </div>

        <div class="rounded-2xl border border-erp-border bg-white p-6 shadow-sm">
            <h2 class="text-lg font-semibold text-erp-text">Quick Actions</h2>
            <div class="mt-4 flex flex-wrap gap-3">
                <a href="{{ route('dashboard') }}" class="rounded-lg bg-erp-darkGreen px-4 py-2 text-sm font-semibold text-white">Go to Dashboard</a>
                <a href="{{ route('logout') }}" class="rounded-lg border border-erp-border px-4 py-2 text-sm font-semibold text-erp-text">Logout</a>
            </div>
        </div>
    </div>
</div>
@endsection
