<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pesanan;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $reportData = $this->buildReportData((int) $request->query('tahun'), (int) $request->query('bulan'));

        return view('admin.laporan.index', [
            ...$reportData,
        ]);
    }

    public function exportPdf(Request $request): Response
    {
        $reportData = $this->buildReportData((int) $request->query('tahun'), (int) $request->query('bulan'));
        $selectedYear = $reportData['selectedYear'];
        $selectedMonth = $reportData['selectedMonth'];
        $filePeriod = $selectedMonth !== null
            ? $selectedYear.'-'.str_pad((string) $selectedMonth, 2, '0', STR_PAD_LEFT)
            : (string) $selectedYear;

        return Pdf::loadView('admin.laporan.pdf', $reportData)
            ->setPaper('a4', 'landscape')
            ->download('laporan-transaksi-'.$filePeriod.'.pdf');
    }

    public function print(Request $request): Response
    {
        $reportData = $this->buildReportData((int) $request->query('tahun'), (int) $request->query('bulan'));

        return response()->view('admin.laporan.print', $reportData);
    }

    private function buildReportData(?int $requestedYear, ?int $requestedMonth): array
    {
        $availableYears = Pesanan::query()
            ->selectRaw('YEAR(created_at) as year')
            ->whereNotNull('created_at')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->map(fn ($year) => (int) $year)
            ->values();

        $selectedYear = (int) ($requestedYear ?: ($availableYears->first() ?? now()->year));

        if ($availableYears->isNotEmpty() && ! $availableYears->contains($selectedYear)) {
            $selectedYear = (int) $availableYears->first();
        }

        $monthlyRaw = Pesanan::query()
            ->selectRaw('MONTH(created_at) as month')
            ->selectRaw('COUNT(*) as total_transaksi')
            ->selectRaw('SUM(CASE WHEN status_pesanan != ? THEN total_harga ELSE 0 END) as total_omzet', [Pesanan::STATUS_BATAL])
            ->selectRaw('SUM(CASE WHEN status_pesanan = ? THEN 1 ELSE 0 END) as selesai_count', [Pesanan::STATUS_SELESAI])
            ->selectRaw('SUM(CASE WHEN status_pesanan = ? THEN 1 ELSE 0 END) as diproses_count', [Pesanan::STATUS_DIPROSES])
            ->selectRaw('SUM(CASE WHEN status_pesanan = ? THEN 1 ELSE 0 END) as dikirim_count', [Pesanan::STATUS_DIKIRIM])
            ->selectRaw('SUM(CASE WHEN status_pesanan = ? THEN 1 ELSE 0 END) as menunggu_count', [Pesanan::STATUS_MENUNGGU_PEMBAYARAN])
            ->selectRaw('SUM(CASE WHEN status_pesanan = ? THEN 1 ELSE 0 END) as batal_count', [Pesanan::STATUS_BATAL])
            ->whereYear('created_at', $selectedYear)
            ->groupBy(DB::raw('MONTH(created_at)'))
            ->orderBy(DB::raw('MONTH(created_at)'))
            ->get()
            ->keyBy(fn ($item) => (int) $item->month);

        $monthLabels = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        $selectedMonth = array_key_exists($requestedMonth, $monthLabels) ? $requestedMonth : null;

        $monthlyReports = collect($monthLabels)->map(function ($label, $month) use ($monthlyRaw) {
            $row = $monthlyRaw->get($month);

            return (object) [
                'month_number' => $month,
                'month_label' => $label,
                'total_transaksi' => (int) ($row->total_transaksi ?? 0),
                'total_omzet' => (float) ($row->total_omzet ?? 0),
                'selesai_count' => (int) ($row->selesai_count ?? 0),
                'diproses_count' => (int) ($row->diproses_count ?? 0),
                'dikirim_count' => (int) ($row->dikirim_count ?? 0),
                'menunggu_count' => (int) ($row->menunggu_count ?? 0),
                'batal_count' => (int) ($row->batal_count ?? 0),
            ];
        })->values();

        $summary = [
            'total_transaksi' => $monthlyReports->sum('total_transaksi'),
            'total_omzet' => $monthlyReports->sum('total_omzet'),
            'selesai_count' => $monthlyReports->sum('selesai_count'),
            'diproses_count' => $monthlyReports->sum('diproses_count'),
            'dikirim_count' => $monthlyReports->sum('dikirim_count'),
            'menunggu_count' => $monthlyReports->sum('menunggu_count'),
            'batal_count' => $monthlyReports->sum('batal_count'),
        ];

        $monthlyOrders = collect();

        if ($selectedMonth !== null) {
            $monthlyOrders = Pesanan::with(['user', 'alamat', 'detail'])
                ->whereYear('created_at', $selectedYear)
                ->whereMonth('created_at', $selectedMonth)
                ->orderByDesc('created_at')
                ->get();
        }

        return [
            'availableYears' => $availableYears,
            'selectedYear' => $selectedYear,
            'selectedMonth' => $selectedMonth,
            'monthLabels' => $monthLabels,
            'summary' => $summary,
            'monthlyReports' => $monthlyReports,
            'monthlyOrders' => $monthlyOrders,
        ];
    }
}
