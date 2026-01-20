<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifikasi Pengembalian Barang</title>
</head>
<body style="margin:0; padding:0; background-color:#f8fafc; font-family:'Segoe UI', Arial, sans-serif; color:#0f172a;">
    <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; padding:24px 0;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" width="640" style="background:#ffffff; border-radius:12px; box-shadow:0 10px 30px rgba(15, 23, 42, 0.08); overflow:hidden;">
                    <tr>
                        <td style="padding:20px 24px; background:linear-gradient(135deg,#2563eb,#0ea5e9); color:#fff;">
                            <p style="margin:0; font-size:13px; letter-spacing:1px; text-transform:uppercase; opacity:0.9;">Pengembalian Barang</p>
                            <h1 style="margin:6px 0 0; font-size:24px; font-weight:700;">#{{ $peminjaman->id }}</h1>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px;">
                            <p style="margin:0 0 12px; font-size:15px;">Halo Admin,</p>
                            <p style="margin:0 0 20px; font-size:15px; color:#334155;">Ada pengajuan pengembalian barang.</p>

                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; margin-bottom:20px;">
                                <tr>
                                    <td style="padding:10px; background:#f1f5f9; border-radius:10px;">
                                        <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="font-size:14px; color:#0f172a;">
                                            <tr>
                                                <td style="padding:6px 0; width:160px; color:#475569;">Nama Peminjam</td>
                                                <td style="padding:6px 0; font-weight:600;">{{ $peminjaman->user->nama }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#475569;">NIM/NIP</td>
                                                <td style="padding:6px 0; font-weight:600;">{{ $peminjaman->user->nim ?? '-' }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#475569;">Status</td>
                                                <td style="padding:6px 0; font-weight:600;">{{ $peminjaman->status }}</td>
                                            </tr>
                                            <tr>
                                                <td style="padding:6px 0; color:#475569;">Tanggal Kembali (input)</td>
                                                <td style="padding:6px 0; font-weight:600;">
                                                    {{ $peminjaman->tanggal_kembali ? \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->translatedFormat('d M Y') : '-' }}
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>

                            <h3 style="margin:0 0 10px; font-size:16px; color:#0f172a;">Detail Barang</h3>
                            <table role="presentation" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; font-size:14px; color:#0f172a;">
                                <thead>
                                    <tr style="background:#2563eb; color:#fff;">
                                        <th align="left" style="padding:10px 12px; font-weight:700;">Nama Barang</th>
                                        <th align="center" style="padding:10px 12px; font-weight:700;">Jumlah</th>
                                        <th align="center" style="padding:10px 12px; font-weight:700;">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($peminjaman->detailPeminjaman as $detail)
                                    <tr style="background:#f8fafc;">
                                        <td style="padding:10px 12px; border-bottom:1px solid #e2e8f0;">{{ $detail->barang->nama_barang }}</td>
                                        <td align="center" style="padding:10px 12px; border-bottom:1px solid #e2e8f0;">
                                            {{ strtolower($detail->barang->tipe) === 'habis pakai' ? $detail->jumlah . ' (pinjam)' : $detail->peminjamanUnits->count() }}
                                        </td>
                                        <td align="center" style="padding:10px 12px; border-bottom:1px solid #e2e8f0;">
                                            @if(strtolower($detail->barang->tipe) === 'habis pakai')
                                                @php
                                                    $terpakai = $detail->jumlah_rusak ?? 0;
                                                    $kembali = max(0, ($detail->jumlah ?? 0) - $terpakai);
                                                @endphp
                                                Pinjam {{ $detail->jumlah }}, Kembali {{ $kembali }}, Terpakai {{ $terpakai }}
                                            @else
                                                @php
                                                    $baik = $detail->peminjamanUnits->where('status_pengembalian','dikembalikan')->count();
                                                    $rusak = $detail->peminjamanUnits->whereIn('status_pengembalian',['rusak_ringan','rusak_berat'])->count();
                                                @endphp
                                                Baik {{ $baik }}, Rusak {{ $rusak }}
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <div style="margin-top:24px;">
                                <a href="{{ url('/admin/konfirmasi') }}" style="display:inline-block; background:#2563eb; color:#fff; padding:12px 18px; border-radius:10px; text-decoration:none; font-weight:600;">Buka Panel Konfirmasi</a>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:16px 24px; text-align:center; font-size:12px; color:#94a3b8;">
                            Email ini dikirim otomatis oleh Sistem Laboratorium.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
