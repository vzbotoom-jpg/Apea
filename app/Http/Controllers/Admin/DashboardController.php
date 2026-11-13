<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\DetailPeminjaman;
use App\Models\Kategori;
use App\Models\Notifikasi;
use App\Models\Peminjaman;
use App\Models\Pengembalian;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class DashboardController extends Controller
{
    private const BULAN = [
        1 => 'Jan',
        2 => 'Feb',
        3 => 'Mar',
        4 => 'Apr',
        5 => 'Mei',
        6 => 'Jun',
        7 => 'Jul',
        8 => 'Agu',
        9 => 'Sep',
        10 => 'Okt',
        11 => 'Nov',
        12 => 'Des',
    ];

    public function index(Request $request)
    {
        $categoryOptions = Kategori::query()
            ->pluck('id')
            ->map(fn ($id) => (string) $id)
            ->all();

        $filters = $request->validate([
            'bulan' => ['sometimes', 'regex:/^(all|[1-9]|1[0-2])$/'],
            'tahun' => ['sometimes', 'integer', 'between:2000,'.(now()->year + 1)],
            'compare' => ['sometimes', Rule::in(['yoy', 'mom'])],
            'kategori' => ['sometimes', Rule::in(array_merge(['all'], $categoryOptions))],
        ]);

        $filters = [
            'bulan' => $filters['bulan'] ?? 'all',
            'tahun' => (int) ($filters['tahun'] ?? now()->year),
            'compare' => $filters['compare'] ?? 'yoy',
            'kategori' => $filters['kategori'] ?? 'all',
        ];

        $categoryId = $filters['kategori'] === 'all' ? null : (int) $filters['kategori'];
        $selectedCategoryName = $categoryId
            ? Kategori::query()->whereKey($categoryId)->value('nama_kategori')
            : 'Semua Kategori';

        $alatQuery = Alat::query();
        if ($categoryId) {
            $alatQuery->where('kategori_id', $categoryId);
        }

        $totalAlat = (int) (clone $alatQuery)->sum('stok');
        $alatTersedia = (int) (clone $alatQuery)->sum('stok_tersedia');

        $peminjamanQuery = $this->applyFilters(Peminjaman::query(), $filters);
        $totalPeminjaman = (clone $peminjamanQuery)->count();
        $peminjamanAktif = (clone $peminjamanQuery)
            ->whereIn('status', ['menunggu', 'diverifikasi', 'dipinjam', 'terlambat'])
            ->count();

        $totalUser = User::query()->where('role', 'user')->count();
        $userAktif = (clone $peminjamanQuery)->distinct()->count('user_id');
        $unreadNotifications = Notifikasi::query()
            ->where('user_id', $request->user()->id)
            ->where('is_read', false)
            ->count();
        $notifications = Notifikasi::query()
            ->where('user_id', $request->user()->id)
            ->latest()
            ->limit(5)
            ->get();

        $recentPeminjaman = (clone $peminjamanQuery)
            ->with('user')
            ->latest('created_at')
            ->limit(5)
            ->get();

        $chartData = $this->buildCharts($filters, $categoryId);
        $kpis = [
            'totalAlat' => $totalAlat,
            'alatTersedia' => $alatTersedia,
            'pctTersedia' => $totalAlat > 0 ? round(($alatTersedia / $totalAlat) * 100, 2) : 0,
            'totalPeminjaman' => $totalPeminjaman,
            'peminjamanAktif' => $peminjamanAktif,
            'pctAktif' => $totalPeminjaman > 0 ? round(($peminjamanAktif / $totalPeminjaman) * 100, 2) : 0,
            'totalUser' => $totalUser,
            'unreadNotifications' => $unreadNotifications,
            'pctUser' => $totalUser > 0 ? round(($userAktif / $totalUser) * 100, 2) : 0,
        ];

        if ($request->ajax()) {
            return response()->json([
                'kpis' => $kpis,
                'charts' => $chartData,
                'recentPeminjaman' => $recentPeminjaman->map(fn (Peminjaman $peminjaman) => [
                    'kode' => $peminjaman->kode_peminjaman,
                    'user' => $peminjaman->user?->name ?? '-',
                    'status' => $peminjaman->status,
                    'waktu' => $peminjaman->created_at?->format('d/m H:i') ?? '-',
                ])->values(),
            ]);
        }

        $categories = Kategori::query()->orderBy('nama_kategori')->get();

        return view('admin.dashboard', [
            ...$kpis,
            'notifications' => $notifications,
            'recentPeminjaman' => $recentPeminjaman,
            'chartTrenPeminjaman' => $chartData['tren'],
            'chartKunjungan' => $chartData['sirkulasi'],
            'chartTopAlat' => $chartData['topAlat'],
            'chartStatus' => $chartData['status'],
            'chartKategori' => $chartData['kategori'],
            'categories' => $categories,
            'filters' => $filters,
            'selectedCategoryName' => $selectedCategoryName,
        ]);
    }

    private function applyFilters(Builder $query, array $filters): Builder
    {
        $query->whereYear('tanggal_pinjam', $filters['tahun']);

        if ($filters['bulan'] !== 'all') {
            $query->whereMonth('tanggal_pinjam', (int) $filters['bulan']);
        }

        if ($filters['kategori'] !== 'all') {
            $query->whereHas('detailPeminjamans.alat', function (Builder $alatQuery) use ($filters) {
                $alatQuery->where('kategori_id', (int) $filters['kategori']);
            });
        }

        return $query;
    }

    private function buildCharts(array $filters, ?int $categoryId): array
    {
        $year = $filters['tahun'];
        $compare = $filters['compare'];
        $loanMonthExpression = $this->monthExpression('peminjamans.tanggal_pinjam');
        $returnMonthExpression = $this->monthExpression('pengembalians.tanggal_kembali');

        $loanStats = Peminjaman::query()
            ->when($categoryId, function (Builder $query) use ($categoryId) {
                $query->whereHas('detailPeminjamans.alat', fn (Builder $alatQuery) => $alatQuery->where('kategori_id', $categoryId));
            })
            ->whereBetween('tanggal_pinjam', [
                Carbon::create($year - 1, 1, 1)->toDateString(),
                Carbon::create($year, 12, 31)->toDateString(),
            ])
            ->selectRaw("{$loanMonthExpression} as period, status, COUNT(*) as total")
            ->groupByRaw("{$loanMonthExpression}, status")
            ->get()
            ->groupBy('period');

        $returnRecords = Pengembalian::query()
            ->whereHas('peminjaman', function (Builder $query) use ($categoryId) {
                if ($categoryId) {
                    $query->whereHas('detailPeminjamans.alat', fn (Builder $alatQuery) => $alatQuery->where('kategori_id', $categoryId));
                }
            })
            ->whereBetween('tanggal_kembali', [
                Carbon::create($year - 1, 1, 1)->toDateString(),
                Carbon::create($year, 12, 31)->toDateString(),
            ])
            ->selectRaw("{$returnMonthExpression} as period, COUNT(*) as total")
            ->groupByRaw($returnMonthExpression)
            ->get()
            ->keyBy('period');

        $annualPeriods = array_map(fn (int $month) => [
            'year' => $year,
            'month' => $month,
            'label' => self::BULAN[$month],
        ], range(1, 12));

        $comparisonPeriods = $compare === 'yoy'
            ? array_map(fn (int $month) => [
                'year' => $year - 1,
                'month' => $month,
                'label' => self::BULAN[$month],
            ], range(1, 12))
            : array_map(fn (int $month) => $this->periodArray($this->periodFor($year, $month)->subMonth()), range(1, 12));

        $monthlyTrend = $filters['bulan'] === 'all';
        if ($monthlyTrend) {
            $trendLabels = array_column($annualPeriods, 'label');
            $trendDatasets = [
                [
                    'type' => 'bar',
                    'label' => (string) $year,
                    'data' => $this->countLoansByPeriods($loanStats, $annualPeriods),
                    'backgroundColor' => '#2563eb',
                    'borderRadius' => 2,
                    'barPercentage' => 0.7,
                ],
                [
                    'type' => 'bar',
                    'label' => $compare === 'yoy' ? (string) ($year - 1) : 'Bulan sebelumnya',
                    'data' => $this->countLoansByPeriods($loanStats, $comparisonPeriods),
                    'backgroundColor' => '#7dd3fc',
                    'borderRadius' => 2,
                    'barPercentage' => 0.7,
                ],
            ];
        } else {
            $month = (int) $filters['bulan'];
            $currentPeriod = $this->periodFor($year, $month);
            $previousPeriod = $compare === 'yoy'
                ? $currentPeriod->copy()->subYear()
                : $currentPeriod->copy()->subMonth();
            $selectedPeriods = [
                $this->periodArray($previousPeriod),
                $this->periodArray($currentPeriod),
            ];
            $trendLabels = array_column($selectedPeriods, 'label');
            $trendDatasets = [[
                'type' => 'bar',
                'label' => 'Jumlah Peminjaman',
                'data' => $this->countLoansByPeriods($loanStats, $selectedPeriods),
                'backgroundColor' => '#2563eb',
                'borderRadius' => 2,
                'barPercentage' => 0.7,
            ]];
        }

        $chartPeriods = $monthlyTrend
            ? $annualPeriods
            : $selectedPeriods;
        $chartLabels = array_column($chartPeriods, 'label');

        $newLoans = $this->countLoansByPeriods($loanStats, $chartPeriods);
        $returnedLoans = $this->countReturnsByPeriods($returnRecords, $chartPeriods);
        $statuses = $this->countStatusesByPeriods($loanStats, $chartPeriods);

        $topAlat = DetailPeminjaman::query()
            ->join('alats', 'alats.id', '=', 'detail_peminjamans.alat_id')
            ->whereHas('peminjaman', fn (Builder $query) => $this->applyFilters($query, $filters))
            ->select('alats.nama_alat')
            ->selectRaw('COUNT(DISTINCT detail_peminjamans.peminjaman_id) as total')
            ->groupBy('alats.id', 'alats.nama_alat')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $categories = Kategori::query()
            ->when($categoryId, fn (Builder $query) => $query->whereKey($categoryId))
            ->withSum('alats', 'stok')
            ->orderBy('nama_kategori')
            ->get();

        $chartColors = ['#3b82f6', '#fb923c', '#7c3aed', '#60a5fa', '#facc15', '#34d399', '#f97316'];

        return [
            'tren' => [
                'labels' => $trendLabels,
                'datasets' => $trendDatasets,
            ],
            'sirkulasi' => [
                'labels' => $chartLabels,
                'datasets' => [
                    [
                        'label' => 'Peminjaman Baru',
                        'data' => $newLoans,
                        'borderColor' => '#2563eb',
                        'backgroundColor' => 'rgba(37, 99, 235, 0.14)',
                        'fill' => true,
                        'tension' => 0.45,
                        'pointRadius' => 0,
                        'borderWidth' => 2,
                    ],
                    [
                        'label' => 'Pengembalian',
                        'data' => $returnedLoans,
                        'borderColor' => '#eab308',
                        'backgroundColor' => 'rgba(234, 179, 8, 0.14)',
                        'fill' => true,
                        'tension' => 0.45,
                        'pointRadius' => 0,
                        'borderWidth' => 2,
                    ],
                ],
            ],
            'topAlat' => [
                'labels' => $topAlat->pluck('nama_alat')->values(),
                'datasets' => [[
                    'label' => 'Frekuensi Dipinjam',
                    'data' => $topAlat->pluck('total')->map(fn ($total) => (int) $total)->values(),
                    'backgroundColor' => '#7cc4fa',
                    'borderRadius' => 2,
                    'barThickness' => 12,
                ]],
            ],
            'status' => [
                'labels' => $chartLabels,
                'datasets' => [
                    [
                        'label' => 'Peminjaman Selesai',
                        'data' => $statuses['dikembalikan'],
                        'backgroundColor' => '#2563eb',
                        'stack' => 'status',
                        'barPercentage' => 0.55,
                    ],
                    [
                        'label' => 'Sedang Berjalan',
                        'data' => $statuses['aktif'],
                        'backgroundColor' => '#eab308',
                        'stack' => 'status',
                        'barPercentage' => 0.55,
                    ],
                ],
            ],
            'kategori' => [
                'labels' => $categories->pluck('nama_kategori')->values(),
                'datasets' => [[
                    'data' => $categories->map(fn (Kategori $kategori) => (int) ($kategori->alats_sum_stok ?? 0))->values(),
                    'backgroundColor' => $categories->keys()->map(fn (int $index) => $chartColors[$index % count($chartColors)])->values(),
                    'borderWidth' => 2,
                    'borderColor' => '#ffffff',
                ]],
            ],
        ];
    }

    private function countLoansByPeriods($recordsByPeriod, array $periods): array
    {
        return array_map(function (array $period) use ($recordsByPeriod) {
            $periodKey = sprintf('%04d-%02d', $period['year'], $period['month']);

            return $recordsByPeriod->get($periodKey, collect())->sum('total');
        }, $periods);
    }

    private function countReturnsByPeriods($recordsByPeriod, array $periods): array
    {
        return array_map(function (array $period) use ($recordsByPeriod) {
            $periodKey = sprintf('%04d-%02d', $period['year'], $period['month']);

            return (int) ($recordsByPeriod->get($periodKey)?->total ?? 0);
        }, $periods);
    }

    private function countStatusesByPeriods($recordsByPeriod, array $periods): array
    {
        $finished = [];
        $active = [];

        foreach ($periods as $period) {
            $periodKey = sprintf('%04d-%02d', $period['year'], $period['month']);
            $monthlyRecords = $recordsByPeriod->get($periodKey, collect());
            $finished[] = (int) $monthlyRecords->where('status', 'dikembalikan')->sum('total');
            $active[] = (int) $monthlyRecords->whereIn('status', ['dipinjam', 'menunggu', 'terlambat'])->sum('total');
        }

        return ['dikembalikan' => $finished, 'aktif' => $active];
    }

    private function monthExpression(string $column): string
    {
        return match (DB::connection()->getDriverName()) {
            'mysql', 'mariadb' => "DATE_FORMAT({$column}, '%Y-%m')",
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlite' => "strftime('%Y-%m', {$column})",
            default => throw new \RuntimeException('Driver database tidak didukung untuk agregasi dashboard.'),
        };
    }

    private function periodFor(int $year, int $month): Carbon
    {
        return Carbon::create($year, $month, 1);
    }

    private function periodArray(Carbon $period): array
    {
        return [
            'year' => (int) $period->year,
            'month' => (int) $period->month,
            'label' => self::BULAN[$period->month].' '.$period->year,
        ];
    }
}
