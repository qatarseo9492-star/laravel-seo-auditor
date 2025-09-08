@props(['user'])

<form method="POST"
      action="{{ route('admin.users.updateLimits', $user) }}"
      class="d-flex align-items-center gap-2"
      onsubmit="return confirm('Apply changes for {{ $user->name ?? ('User #'.$user->id) }}?')">
    @csrf
    @method('PATCH')

    <input type="number"
           name="daily_limit"
           value="{{ optional($user->limit)->daily_limit ?? 200 }}"
           min="0"
           class="form-control"
           style="max-width:120px"
           title="Daily limit" />

    <input type="hidden"
           name="is_enabled"
           value="{{ (optional($user->limit)->is_enabled ?? true) ? 0 : 1 }}" />

    <input type="text"
           name="reason"
           value=""
           placeholder="Reason (optional)"
           class="form-control"
           style="max-width:220px" />

    <button class="btn btn-sm btn-primary">
        {{ (optional($user->limit)->is_enabled ?? true) ? 'Disable' : 'Enable' }} Limits
    </button>
</form>

{{-- Tiny status pill (read-only) --}}
@php $enabled = optional($user->limit)->is_enabled ?? true; @endphp
<span class="badge" style="margin-left:8px;background:rgba(255,255,255,.06);border:1px solid rgba(255,255,255,.12);border-radius:999px;padding:4px 8px;font-size:12px">
    <span style="display:inline-block;width:8px;height:8px;border-radius:999px;margin-right:6px;background:{{ $enabled ? '#00ff8a' : '#ff3b30' }}"></span>
    {{ $enabled ? 'Enabled' : 'Disabled' }}
</span>
