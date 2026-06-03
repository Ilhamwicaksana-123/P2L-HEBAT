@extends('admin.layouts.app')

@section('title', 'Log Aktivitas')
@section('heading', 'Log Aktivitas Pengguna')
@section('subheading', 'Pantau riwayat aktivitas pengguna yang tercatat di sistem.')

@section('content')
<div class="admin-card">
    <form method="GET" class="toolbar" data-activity-log-filter-form>
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama, aksi, modul, deskripsi, atau IP">

        <select name="role">
            <option value="">Semua role</option>
            @foreach($roles as $roleOption)
                <option value="{{ $roleOption }}" @selected($role === $roleOption)>{{ $roleOption }}</option>
            @endforeach
        </select>

        <select name="action">
            <option value="">Semua aksi</option>
            @foreach($actions as $actionOption)
                <option value="{{ $actionOption }}" @selected($action === $actionOption)>{{ $actionOption }}</option>
            @endforeach
        </select>

        <select name="module">
            <option value="">Semua modul</option>
            @foreach($modules as $moduleOption)
                <option value="{{ $moduleOption }}" @selected($module === $moduleOption)>{{ $moduleOption }}</option>
            @endforeach
        </select>

        <input type="date" name="date" value="{{ $date }}" data-auto-submit-date>

        <button type="submit" class="btn btn-secondary">Filter</button>
        @if($search !== '' || $role !== '' || $action !== '' || $module !== '' || $date !== '')
            <a href="{{ route('admin.activity-logs.index') }}" class="btn btn-secondary">Reset</a>
        @endif
    </form>

    <div class="table-wrap activity-log-table-wrap" data-log-table-wrap>
        <table class="admin-table activity-log-table">
            <thead>
                <tr>
                    <th>Waktu</th>
                    <th>Pengguna</th>
                    <th>Role</th>
                    <th>Aksi</th>
                    <th>Modul</th>
                    <th>Deskripsi</th>
                    <th>IP</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td>{{ optional($log->created_at)->format('d M Y H:i') }}</td>
                        <td>{{ $log->name ?: ($log->user?->nama ?? '-') }}</td>
                        <td>
                            <span class="badge {{ $log->role === 'super_admin' ? 'badge-danger-soft' : ($log->role === 'admin' ? 'badge-success' : 'badge-role') }}">
                                {{ $log->role ?: '-' }}
                            </span>
                        </td>
                        <td>{{ $log->action }}</td>
                        <td>{{ $log->module ?: '-' }}</td>
                        <td>{{ $log->description ?: '-' }}</td>
                        <td>{{ $log->ip_address ?: '-' }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="empty-state">Belum ada log aktivitas.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="pagination-wrap">
        {{ $logs->links() }}
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const form = document.querySelector('[data-activity-log-filter-form]');
        const dateInput = document.querySelector('[data-auto-submit-date]');

        if (!form || !dateInput) {
            return;
        }

        dateInput.addEventListener('change', () => {
            form.submit();
        });
    });
</script>
@endsection
