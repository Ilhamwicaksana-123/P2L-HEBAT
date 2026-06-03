<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class ActivityLogController extends Controller
{
    public function index(Request $request)
    {
        $this->authorizeSuperAdmin();

        $search = trim((string) $request->query('search'));
        $role = trim((string) $request->query('role'));
        $action = trim((string) $request->query('action'));
        $module = trim((string) $request->query('module'));
        $date = trim((string) $request->query('date'));

        $logs = ActivityLog::query()
            ->with(['user', 'produk'])
            ->whereIn('role', ['admin', 'user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($builder) use ($search) {
                    $builder->where('nama', 'like', "%{$search}%")
                        ->orWhere('role', 'like', "%{$search}%")
                        ->orWhere('action', 'like', "%{$search}%")
                        ->orWhere('module', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('ip_address', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('nama', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                        })
                        ->orWhereHas('produk', function ($produkQuery) use ($search) {
                            $produkQuery->where('nama_produk', 'like', "%{$search}%");
                        });
                });
            })
            ->when($role !== '', fn ($query) => $query->where('role', $role))
            ->when($action !== '', fn ($query) => $query->where('action', $action))
            ->when($module !== '', fn ($query) => $query->where('module', $module))
            ->when($date !== '', fn ($query) => $query->whereDate('created_at', $date))
            ->orderByDesc('created_at')
            ->paginate(5)
            ->withQueryString();

        $roles = ActivityLog::query()
            ->whereIn('role', ['admin', 'user'])
            ->whereNotNull('role')
            ->where('role', '!=', '')
            ->distinct()
            ->orderBy('role')
            ->pluck('role');

        $actions = ActivityLog::query()
            ->whereIn('role', ['admin', 'user'])
            ->whereNotNull('action')
            ->where('action', '!=', '')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        $modules = ActivityLog::query()
            ->whereIn('role', ['admin', 'user'])
            ->whereNotNull('module')
            ->where('module', '!=', '')
            ->distinct()
            ->orderBy('module')
            ->pluck('module');

        return view('admin.activity-logs.index', compact(
            'logs',
            'search',
            'role',
            'action',
            'module',
            'date',
            'roles',
            'actions',
            'modules'
        ));
    }

    protected function authorizeSuperAdmin(): void
    {
        abort_unless($this->authUser()->role === 'super_admin', 403, 'Hanya super admin yang bisa melihat log aktivitas.');
    }
}
