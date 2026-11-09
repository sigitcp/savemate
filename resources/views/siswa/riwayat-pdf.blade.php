<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Riwayat Transaksi - {{ $student->name }}</title>
    <style>
        body { font-family: sans-serif; font-size: 12px; color: #333; }
        .header { text-align: center; margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
        .info { margin-bottom: 15px; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; font-weight: bold; }
        .text-right { text-align: right; }
        .text-success { color: green; font-weight: bold; }
        .text-danger { color: red; font-weight: bold; }
        .badge { padding: 3px 6px; border-radius: 4px; font-size: 10px; }
        .bg-success { background-color: #d4edda; color: #155724; }
        .bg-danger { background-color: #f8d7da; color: #721c24; }
    </style>
</head>
<body>
    <div class="header">
        <h2>SAVE MATE - Smart Canteen</h2>
        <p>Laporan Riwayat Transaksi Siswa</p>
    </div>

    <div class="info">
        <strong>Nama:</strong> {{ $student->name }}<br>
        <strong>NIS:</strong> {{ $student->nis }} | <strong>Kelas:</strong> {{ $student->class }}<br>
        <strong>Periode:</strong> {{ \Carbon\Carbon::parse($startDate)->format('d M Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->format('d M Y') }}
    </div>

    <table>
        <thead>
            <tr>
                <th width="15%">Tanggal</th>
                <th width="15%">Jenis</th>
                <th width="50%">Keterangan</th>
                <th width="20%" class="text-right">Nominal</th>
            </tr>
        </thead>
        <tbody>
            @forelse($activities as $act)
            <tr>
                <td>
                    {{ $act->activity_type == 'purchase' ? $act->transaction_at->format('d/m/Y H:i') : $act->created_at->format('d/m/Y H:i') }}
                </td>
                <td>
                    @if($act->activity_type == 'topup')
                        <span class="badge bg-success">TOPUP</span>
                    @else
                        <span class="badge bg-danger">BELI</span>
                    @endif
                </td>
                <td>
                    @if($act->activity_type == 'topup')
                        Pengisian Saldo
                    @else
                        <strong>{{ $act->canteen->name }}</strong><br>
                        <small>
                        @foreach($act->items as $item)
                            {{ $item->menu->name }} (x{{ $item->qty }}){{ !$loop->last ? ',' : '' }}
                        @endforeach
                        </small>
                    @endif
                </td>
                <td class="text-right">
                    @if($act->activity_type == 'topup')
                        <span class="text-success">+ Rp {{ number_format($act->amount, 0, ',', '.') }}</span>
                    @else
                        <span class="text-danger">- Rp {{ number_format($act->total, 0, ',', '.') }}</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" style="text-align: center; padding: 20px;">Tidak ada data transaksi pada periode ini.</td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <div style="margin-top: 30px; text-align: right; font-size: 10px; color: #777;">
        Dicetak pada: {{ now()->format('d/m/Y H:i:s') }} WIB
    </div>
</body>
</html>