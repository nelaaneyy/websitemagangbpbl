@extends('layouts.admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="space-y-6">

    <!-- Header & Navigation Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="px-2.5 py-0.5 bg-blue-100 text-blue-800 text-[10px] font-extrabold uppercase tracking-wider rounded-md border border-blue-200">
                    <i class="fa-solid fa-bolt-lightning text-blue-600 mr-1"></i> KPI Cards BPBL
                </span>
            </div>
            <h1 class="text-2xl font-black text-slate-900 tracking-tight">Dasbor Analisis & Ringkasan Tahapan Berkas</h1>
            <p class="text-xs text-slate-500 font-medium">Visualisasi KPI tahapan verifikasi pengajuan bantuan pasang baru listrik (BPBL) warga.</p>
        </div>

        <div class="flex items-center gap-2">
            <!-- Link ke Menu Sidebar Baru Data List -->
            <a href="{{ route('verifikator.datalist') }}"
               class="inline-flex items-center gap-2 px-4 py-2.5 bg-blue-700 hover:bg-blue-800 text-white font-extrabold text-xs rounded-xl transition shadow-md shadow-blue-600/20 hover:shadow-lg cursor-pointer">
                <i class="fa-solid fa-table-list"></i>
                <span>Buka Data List Permohonan</span>
                <i class="fa-solid fa-arrow-right text-[10px]"></i>
            </a>
        </div>
    </div>

    @if (session('success'))
        <div class="p-4 bg-emerald-50 border border-emerald-200/90 rounded-2xl text-xs font-bold text-emerald-900 flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <div class="w-7 h-7 rounded-xl bg-emerald-500 text-white flex items-center justify-center text-xs font-black shadow-xs">
                    <i class="fa-solid fa-check"></i>
                </div>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-700 hover:text-emerald-950 font-bold">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
    @endif

    <!-- KPI CARDS: RINGKASAN TAHAPAN BERKAS (4 Metrics) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

        <!-- Card 1: Total Permohonan Berkas -->
        <div class="bg-white p-5 rounded-3xl border border-slate-200/80 shadow-2xs hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-slate-400 uppercase tracking-wider block mb-1">Tahap 1 - 4 Total Berkas</span>
                    <h3 id="admin-kpi-total" class="text-3xl font-black text-slate-900 tracking-tight">{{ number_format($stats['total']) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition border border-blue-100">
                    <i class="fa-solid fa-folder-open"></i>
                </div>
            </div>

            <div class="mt-4 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px]">
                <span class="font-bold text-slate-500 flex items-center gap-1">
                    <i class="fa-solid fa-layer-group text-blue-500 text-[10px]"></i> Total Pengajuan Masuk
                </span>
                <span class="px-2 py-0.5 bg-blue-50 text-blue-700 font-extrabold rounded-md text-[10px]">100%</span>
            </div>
            <div class="w-full bg-slate-100 h-1.5 rounded-full mt-2 overflow-hidden">
                <div class="bg-blue-600 h-1.5 rounded-full" style="width: 100%"></div>
            </div>
        </div>

        <!-- Card 2: Menunggu Verifikasi ESDM -->
        <div class="bg-white p-5 rounded-3xl border border-amber-200/80 shadow-2xs hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-amber-700 uppercase tracking-wider block mb-1">Tahap 2 • Menunggu ESDM</span>
                    <h3 id="admin-kpi-menunggu" class="text-3xl font-black text-amber-950 tracking-tight">{{ number_format($stats['menunggu']) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition border border-amber-200">
                    <i class="fa-solid fa-hourglass-half animate-pulse"></i>
                </div>
            </div>

            @php
                $pctMenunggu = $stats['total'] > 0 ? round(($stats['menunggu'] / $stats['total']) * 100, 1) : 0;
            @endphp
            <div class="mt-4 pt-3 border-t border-amber-100 flex items-center justify-between text-[11px]">
                <span class="font-bold text-amber-800 flex items-center gap-1">
                    <i class="fa-solid fa-clock text-amber-500 text-[10px]"></i> Perlu Review Instansi
                </span>
                <span id="admin-pct-menunggu" class="px-2 py-0.5 bg-amber-100 text-amber-900 font-extrabold rounded-md text-[10px]">{{ $pctMenunggu }}%</span>
            </div>
            <div class="w-full bg-amber-100/70 h-1.5 rounded-full mt-2 overflow-hidden">
                <div id="admin-bar-menunggu" class="bg-amber-500 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctMenunggu }}%"></div>
            </div>
        </div>

        <!-- Card 3: Disetujui (Lolos Verifikasi ESDM) -->
        <div class="bg-white p-5 rounded-3xl border border-emerald-200/80 shadow-2xs hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-emerald-700 uppercase tracking-wider block mb-1">Tahap 3 • Lolos Verifikasi</span>
                    <h3 id="admin-kpi-disetujui" class="text-3xl font-black text-emerald-950 tracking-tight">{{ number_format($stats['disetujui']) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition border border-emerald-200">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>

            @php
                $pctDisetujui = $stats['total'] > 0 ? round(($stats['disetujui'] / $stats['total']) * 100, 1) : 0;
            @endphp
            <div class="mt-4 pt-3 border-t border-emerald-100 flex items-center justify-between text-[11px]">
                <span class="font-bold text-emerald-800 flex items-center gap-1">
                    <i class="fa-solid fa-shield-halved text-emerald-500 text-[10px]"></i> Berkas Valid & Disetujui
                </span>
                <span id="admin-pct-disetujui" class="px-2 py-0.5 bg-emerald-100 text-emerald-900 font-extrabold rounded-md text-[10px]">{{ $pctDisetujui }}%</span>
            </div>
            <div class="w-full bg-emerald-100/70 h-1.5 rounded-full mt-2 overflow-hidden">
                <div id="admin-bar-disetujui" class="bg-emerald-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctDisetujui }}%"></div>
            </div>
        </div>

        <!-- Card 4: Ditolak / Perlu Perbaikan -->
        <div class="bg-white p-5 rounded-3xl border border-rose-200/80 shadow-2xs hover:shadow-md transition group">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-[11px] font-extrabold text-rose-700 uppercase tracking-wider block mb-1">Tahap Revisi • Ditolak</span>
                    <h3 id="admin-kpi-ditolak" class="text-3xl font-black text-rose-950 tracking-tight">{{ number_format($stats['ditolak']) }}</h3>
                </div>
                <div class="w-11 h-11 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-lg font-bold group-hover:scale-110 transition border border-rose-200">
                    <i class="fa-solid fa-circle-xmark"></i>
                </div>
            </div>

            @php
                $pctDitolak = $stats['total'] > 0 ? round(($stats['ditolak'] / $stats['total']) * 100, 1) : 0;
            @endphp
            <div class="mt-4 pt-3 border-t border-rose-100 flex items-center justify-between text-[11px]">
                <span class="font-bold text-rose-800 flex items-center gap-1">
                    <i class="fa-solid fa-triangle-exclamation text-rose-500 text-[10px]"></i> Perlu Perbaikan Berkas
                </span>
                <span id="admin-pct-ditolak" class="px-2 py-0.5 bg-rose-100 text-rose-900 font-extrabold rounded-md text-[10px]">{{ $pctDitolak }}%</span>
            </div>
            <div class="w-full bg-rose-100/70 h-1.5 rounded-full mt-2 overflow-hidden">
                <div id="admin-bar-ditolak" class="bg-rose-600 h-1.5 rounded-full transition-all duration-500" style="width: {{ $pctDitolak }}%"></div>
            </div>
        </div>

    </div>

    <!-- SEKSI GRAFIK & ANALITIK (BAR CHART & DONUT CHART) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Bar Chart: Distribusi Wilayah & Permohonan per Kabupaten -->
        <div class="lg:col-span-8 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 pb-3 border-b border-slate-100">
                <div>
                    <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                        <i class="fa-solid fa-chart-column text-blue-600"></i>
                        Permohonan per Kabupaten
                    </h2>
                    <p class="text-xs text-slate-500 font-medium">Rekapitulasi volume berkas pengajuan BPBL di wilayah kerja Dinas ESDM.</p>
                </div>
                <span class="px-3 py-1 bg-slate-100 text-slate-700 text-[11px] font-bold rounded-xl self-start sm:self-auto">
                    <i class="fa-solid fa-location-dot text-blue-600 mr-1"></i> {{ count($chartKabupaten) }} Wilayah
                </span>
            </div>

            <div id="barChartKabupaten" class="w-full min-h-75"></div>
        </div>

        <!-- Donut Chart: Ringkasan Proporsi Tahapan Verifikasi -->
        <div class="lg:col-span-4 bg-white p-6 rounded-3xl border border-slate-200/80 shadow-2xs space-y-4 flex flex-col justify-between">
            <div class="pb-3 border-b border-slate-100">
                <h2 class="text-base font-extrabold text-slate-900 flex items-center gap-2">
                    <i class="fa-solid fa-chart-pie text-emerald-600"></i>
                    Proporsi Tahapan Verifikasi
                </h2>
                <p class="text-xs text-slate-500 font-medium">Persentase alur verifikasi berkas pengajuan.</p>
            </div>

            <div id="donutChartStatus" class="w-full flex items-center justify-center min-h-62.5"></div>

            <div class="grid grid-cols-2 gap-2 text-[11px] font-bold pt-2 border-t border-slate-100">
                <div class="p-2 bg-slate-50 rounded-xl flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                    <span class="text-slate-700">Masuk (Warga)</span>
                </div>
                <div class="p-2 bg-amber-50 rounded-xl flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                    <span class="text-amber-900">Review ESDM</span>
                </div>
                <div class="p-2 bg-emerald-50 rounded-xl flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                    <span class="text-emerald-900">Lolos ESDM</span>
                </div>
                <div class="p-2 bg-rose-50 rounded-xl flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span>
                    <span class="text-rose-900">Revisi/Ditolak</span>
                </div>
            </div>
        </div>

    </div>

    <!-- QUICK NAV BANNER TO DATA LIST MENU -->
    <div class="bg-linear-to-r from-slate-900 via-slate-800 to-blue-950 p-6 rounded-3xl text-white shadow-xl flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="space-y-1 text-center sm:text-left">
            <div class="inline-flex items-center gap-2 px-2.5 py-0.5 bg-amber-500 text-slate-950 text-[10px] font-black uppercase rounded-md mb-1">
                <i class="fa-solid fa-folder-open"></i> Kelola Berkas
            </div>
            <h3 class="text-base font-extrabold text-white">Kelola & Filter Detail Data Permohonan Warga</h3>
            <p class="text-xs text-slate-300 font-medium">Buka menu sidebar "Daftar Data Permohonan" untuk melihat filter wilayah, melakukan import Excel, ekspor laporan, dan cetak PDF.</p>
        </div>
        <a href="{{ route('admin.datalist') }}" class="px-5 py-3 bg-amber-500 hover:bg-amber-400 text-slate-950 font-extrabold text-xs rounded-2xl shadow-lg transition shrink-0 flex items-center gap-2">
            <i class="fa-solid fa-table-list"></i>
            <span>Buka Menu Data List</span>
            <i class="fa-solid fa-arrow-right text-[10px]"></i>
        </a>
    </div>

</div>

<script>
    const chartKabupatenRaw = @json($chartKabupaten ?? []);
    const chartStatusRaw = @json($chartStatus ?? []);

    document.addEventListener("DOMContentLoaded", function () {
        // 1. Inisialisasi ApexCharts: Bar Chart Kabupaten
        const kabLabels = Object.keys(chartKabupatenRaw);
        const kabValues = Object.values(chartKabupatenRaw);

        const optionsBar = {
            series: [{
                name: 'Jumlah Permohonan',
                data: kabValues.length > 0 ? kabValues : [0]
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: { show: false },
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            colors: ['#2563eb'],
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    columnWidth: '45%',
                    distributed: false,
                    dataLabels: { position: 'top' }
                }
            },
            dataLabels: {
                enabled: true,
                offsetY: -20,
                style: {
                    fontSize: '11px',
                    colors: ["#1e293b"],
                    fontWeight: 700
                }
            },
            xaxis: {
                categories: kabLabels.length > 0 ? kabLabels : ['Data Kosong'],
                axisBorder: { show: false },
                axisTicks: { show: false },
                labels: {
                    style: {
                        fontSize: '11px',
                        fontWeight: 600,
                        colors: '#64748b'
                    }
                }
            },
            yaxis: {
                labels: {
                    style: {
                        fontSize: '11px',
                        colors: '#64748b'
                    }
                }
            },
            grid: {
                borderColor: '#f1f5f9',
                strokeDashArray: 4
            },
            tooltip: {
                theme: 'light',
                y: {
                    formatter: function (val) {
                        return val + " Permohonan Warga";
                    }
                }
            }
        };

        const chartBar = new ApexCharts(document.querySelector("#barChartKabupaten"), optionsBar);
        chartBar.render();

        // 2. Inisialisasi ApexCharts: Donut Chart Status Verifikasi
        const statusValues = [
            chartStatusRaw.terkirim || 0,
            chartStatusRaw.disetujui_desa || 0,
            chartStatusRaw.lolos_verifikasi_pusat || 0,
            chartStatusRaw.ditolak || 0
        ];

        const optionsDonut = {
            series: statusValues.some(v => v > 0) ? statusValues : [1, 0, 0, 0],
            chart: {
                type: 'donut',
                height: 260,
                fontFamily: 'Plus Jakarta Sans, sans-serif'
            },
            labels: ['Masuk (Warga)', 'Menunggu ESDM', 'Lolos ESDM', 'Revisi/Ditolak'],
            colors: ['#3b82f6', '#f59e0b', '#10b981', '#f43f5e'],
            legend: { show: false },
            dataLabels: { enabled: false },
            plotOptions: {
                pie: {
                    donut: {
                        size: '72%',
                        labels: {
                            show: true,
                            total: {
                                show: true,
                                label: 'Total Berkas',
                                fontSize: '12px',
                                fontWeight: 700,
                                color: '#64748b',
                                formatter: function (w) {
                                    return {{ $stats['total'] }};
                                }
                            }
                        }
                    }
                }
            },
            stroke: { width: 2, colors: ['#ffffff'] }
        };

        const chartDonut = new ApexCharts(document.querySelector("#donutChartStatus"), optionsDonut);
        chartDonut.render();

        function fetchAdminKpiStats() {
            fetch("{{ route('api.kpi.stats') }}")
                .then(res => res.json())
                .then(data => {
                    const elTotal = document.getElementById('admin-kpi-total');
                    if (elTotal) elTotal.innerText = new Intl.NumberFormat('id-ID').format(data.total_warga);
                    const elMenunggu = document.getElementById('admin-kpi-menunggu');
                    if (elMenunggu) elMenunggu.innerText = new Intl.NumberFormat('id-ID').format(data.total_menunggu);
                    const elDisetujui = document.getElementById('admin-kpi-disetujui');
                    if (elDisetujui) elDisetujui.innerText = new Intl.NumberFormat('id-ID').format(data.total_disetujui);
                    const elDitolak = document.getElementById('admin-kpi-ditolak');
                    if (elDitolak) elDitolak.innerText = new Intl.NumberFormat('id-ID').format(data.total_ditolak);

                    const total = data.total_warga > 0 ? data.total_warga : 1;
                    const pctM = ((data.total_menunggu / total) * 100).toFixed(1);
                    const pctD = ((data.total_disetujui / total) * 100).toFixed(1);
                    const pctR = ((data.total_ditolak / total) * 100).toFixed(1);

                    const elPctM = document.getElementById('admin-pct-menunggu');
                    if (elPctM) elPctM.innerText = `${pctM}%`;
                    const elBarM = document.getElementById('admin-bar-menunggu');
                    if (elBarM) elBarM.style.width = `${pctM}%`;

                    const elPctD = document.getElementById('admin-pct-disetujui');
                    if (elPctD) elPctD.innerText = `${pctD}%`;
                    const elBarD = document.getElementById('admin-bar-disetujui');
                    if (elBarD) elBarD.style.width = `${pctD}%`;

                    const elPctR = document.getElementById('admin-pct-ditolak');
                    if (elPctR) elPctR.innerText = `${pctR}%`;
                    const elBarR = document.getElementById('admin-bar-ditolak');
                    if (elBarR) elBarR.style.width = `${pctR}%`;
                })
                .catch(err => console.log(err));
        }

        setInterval(fetchAdminKpiStats, 10000);
        window.addEventListener('focus', fetchAdminKpiStats);
    });
</script>
@endsection
