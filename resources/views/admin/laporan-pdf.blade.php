<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Panen PATANI — {{ $periodeLabel }}</title>
    <style>
        /* ── Reset & Base ───────────────────────────────────────────────── */
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            color: #1f2937;
            background: #f9fafb;
        }

        /* ── Container ──────────────────────────────────────────────────── */
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }

        /* ── Header ─────────────────────────────────────────────────────── */
        .header {
            background: linear-gradient(135deg, #16a34a, #15803d);
            color: white;
            padding: 20px 24px;
            border-radius: 8px;
            margin-bottom: 16px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h1  { font-size: 20px; font-weight: bold; }
        .header p   { font-size: 12px; opacity: 0.85; margin-top: 4px; }
        .header .logo { font-size: 36px; }

        /* ── Stats Bar ───────────────────────────────────────────────────── */
        .stats-bar {
            display: grid;
            grid-template-columns: repeat(5, 1fr);
            gap: 10px;
            margin-bottom: 16px;
        }
        .stat-card {
            background: #f0fdf4;
            border: 1px solid #bbf7d0;
            border-radius: 6px;
            padding: 10px 12px;
            text-align: center;
        }
        .stat-label { font-size: 9px; color: #6b7280; text-transform: uppercase; letter-spacing: 0.3px; }
        .stat-value { font-size: 18px; font-weight: bold; color: #15803d; margin: 4px 0 2px; }
        .stat-unit  { font-size: 9px; color: #9ca3af; }

        /* ── Section Title ───────────────────────────────────────────────── */
        .section-title {
            background: #15803d;
            color: white;
            font-size: 10px;
            font-weight: bold;
            padding: 6px 14px;
            border-radius: 4px;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ── Table ───────────────────────────────────────────────────────── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        thead tr { background: #16a34a; color: white; }
        thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
            border: 1px solid #15803d;
        }
        thead th.right  { text-align: right; }
        thead th.center { text-align: center; }

        tbody tr:nth-child(even) { background: #f0fdf4; }
        tbody td {
            padding: 5px 8px;
            border: 1px solid #d1fae5;
            vertical-align: middle;
        }
        tbody td.right  { text-align: right; }
        tbody td.center { text-align: center; }
        tbody td.bold   { font-weight: bold; }

        tfoot tr { background: #dcfce7; font-weight: bold; }
        tfoot td { padding: 6px 8px; border: 1px solid #86efac; }
        tfoot td.right { text-align: right; }

        /* ── Kualitas Badge ──────────────────────────────────────────────── */
        .badge {
            display: inline-block; padding: 2px 7px;
            border-radius: 10px; font-size: 8px; font-weight: bold;
        }
        .badge-gabah-basah  { background: #dbeafe; color: #1d4ed8; }
        .badge-gabah-kering { background: #fef9c3; color: #854d0e; }
        .badge-beras        { background: #d1fae5; color: #15803d; }
        .badge-baik         { background: #d1fae5; color: #15803d; }
        .badge-sedang       { background: #fef9c3; color: #854d0e; }
        .badge-kurang       { background: #fee2e2; color: #b91c1c; }

        /* ── Empty State ─────────────────────────────────────────────────── */
        .empty {
            text-align: center;
            padding: 40px;
            color: #9ca3af;
            font-style: italic;
        }

        /* ── Footer ──────────────────────────────────────────────────────── */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 2px solid #d1fae5;
            display: flex;
            justify-content: space-between;
            font-size: 9px;
            color: #9ca3af;
        }

        /* ── Tombol Aksi (hanya tampil di layar, hilang saat print) ─────── */
        .action-bar {
            display: flex;
            gap: 10px;
            justify-content: flex-end;
            margin-bottom: 16px;
        }
        .btn {
            padding: 8px 18px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            text-decoration: none;
        }
        .btn-print  { background: #16a34a; color: white; }
        .btn-close  { background: #e5e7eb; color: #374151; }
        .btn:hover  { opacity: 0.9; }

        /* ── PRINT STYLES ────────────────────────────────────────────────── */
        @media print {
            body    { background: white; font-size: 9px; }
            .action-bar { display: none !important; }
            .container  { padding: 0; max-width: 100%; }
            .header     { border-radius: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            thead tr    { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            .stat-card  { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
            table       { page-break-inside: auto; }
            tr          { page-break-inside: avoid; page-break-after: auto; }

            @page {
                size: A4 landscape;
                margin: 10mm 12mm;
            }
        }
    </style>
</head>
<body>

<div class="container">

    {{-- Tombol aksi (hilang saat print) --}}
    <div class="action-bar">
        <a href="{{ url()->previous() }}" class="btn btn-close">
            ← Kembali
        </a>
        <button onclick="window.print()" class="btn btn-print">
             Cetak / Simpan PDF
        </button>
    </div>

    {{-- Header --}}
    <div class="header">
        <div>
            <h1>🌾 LAPORAN PRODUKSI PANEN — PATANI</h1>
            <p>Periode: {{ $periodeLabel }}</p>
            <p style="font-size:10px; opacity:0.7; margin-top:2px;">
                Dicetak: {{ now()->format('d/m/Y H:i') }} WIB
            </p>
        </div>
        <div class="logo"></div>
    </div>

    {{-- Stats Bar --}}
    <div class="stats-bar">
        <div class="stat-card">
            <div class="stat-label">Total Produksi</div>
            <div class="stat-value">{{ number_format($totalProduksiKg / 1000, 2, ',', '.') }}</div>
            <div class="stat-unit">Ton</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Pendapatan</div>
            <div class="stat-value" style="font-size:13px;">
                Rp {{ number_format($totalPendapatan / 1000000, 1, ',', '.') }}
            </div>
            <div class="stat-unit">Juta Rupiah</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Jumlah Record</div>
            <div class="stat-value">{{ count($dataPanen) }}</div>
            <div class="stat-unit">Record Panen</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Petani</div>
            <div class="stat-value">{{ $totalPetani }}</div>
            <div class="stat-unit">Orang Terdaftar</div>
        </div>
        <div class="stat-card">
            <div class="stat-label">Total Lahan</div>
            <div class="stat-value">{{ number_format($totalLuas, 1, ',', '.') }}</div>
            <div class="stat-unit">Hektar</div>
        </div>
    </div>

    {{-- Tabel Data Panen --}}
    <div class="section-title">Detail Data Panen</div>

    @if(count($dataPanen) === 0)
        <div class="empty">
            Tidak ada data panen untuk periode ini.
        </div>
    @else
        <table>
            <thead>
                <tr>
                    <th class="center" style="width:30px">No</th>
                    <th style="width:65px">Tanggal</th>
                    <th>Nama Petani</th>
                    <th>Desa</th>
                    <th>Nama Sawah</th>
                    <th class="right" style="width:45px">Luas (Ha)</th>
                    <th class="right" style="width:70px">Hasil (Kg)</th>
                    <th class="right" style="width:65px">Hasil (Ton)</th>
                    <th class="center" style="width:80px">Kualitas</th>
                    <th class="right" style="width:100px">Total Pendapatan</th>
                </tr>
            </thead>
            <tbody>
                @foreach($dataPanen as $i => $panen)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $panen['tanggal'] }}</td>
                    <td class="bold">{{ $panen['nama_petani'] }}</td>
                    <td>{{ $panen['desa'] }}</td>
                    <td>{{ $panen['nama_sawah'] }}</td>
                    <td class="right">{{ number_format($panen['luas'], 2) }}</td>
                    <td class="right">{{ $panen['hasil_kg'] }}</td>
                    <td class="right bold" style="color:#15803d;">{{ $panen['hasil_ton'] }}</td>
                    <td class="center">
                        @php $k = strtolower(str_replace(' ', '-', $panen['kualitas'])); @endphp
                        <span class="badge badge-{{ $k }}">{{ $panen['kualitas'] }}</span>
                    </td>
                    <td class="right">{{ $panen['total_pendapatan'] }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="6" class="bold">TOTAL</td>
                    <td class="right bold">{{ number_format($totalProduksiKg, 0, ',', '.') }} Kg</td>
                    <td class="right bold" style="color:#15803d;">{{ number_format($totalProduksiKg / 1000, 2, ',', '.') }} Ton</td>
                    <td></td>
                    <td class="right bold">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                </tr>
            </tfoot>
        </table>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <span>PATANI — Sistem Informasi Pertanian Digital</span>
        <span>Data bersumber dari input petani terdaftar. Dokumen ini dibuat otomatis oleh sistem.</span>
        <span>{{ now()->format('d/m/Y H:i') }} WIB</span>
    </div>

</div>

{{-- Auto trigger print dialog saat halaman dibuka --}}
<script>
    // Tunggu semua konten selesai render baru buka dialog print
    window.addEventListener('load', function () {
        // Tunda 500ms agar render selesai sempurna
        setTimeout(function () {
            window.print();
        }, 500);
    });
</script>

</body>
</html>