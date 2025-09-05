<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Pesanan Baru</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Pesanan Peminjaman Baru Telah Dibuat</h2>
    <p>Halo Admin,</p>
    <p>Ada pesanan peminjaman baru yang perlu dikonfirmasi. Berikut adalah rinciannya:</p>
    
    <p>
        <strong>ID Peminjaman:</strong> #{{ $peminjaman->id }} <br>
        <strong>Nama Peminjam:</strong> {{ $peminjaman->user->nama }} <br>
        <strong>NIM:</strong> {{ $peminjaman->user->nim }} <br>
        <strong>Tanggal Pinjam:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_pinjam)->format('d F Y') }} <br>
        <strong>Tanggal Kembali:</strong> {{ \Carbon\Carbon::parse($peminjaman->tanggal_kembali)->format('d F Y') }} <br>
        <strong>Status:</strong> {{ $peminjaman->status }}
    </p>
    
    <h3>Detail Barang:</h3>
    <table border="1" cellpadding="10" cellspacing="0" style="width: 100%;">
        <thead>
            <tr>
                <th>Nama Barang</th>
                <th>Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($peminjaman->detailPeminjaman as $detail)
            <tr>
                <td>{{ $detail->barang->nama_barang }}</td>
                <td>{{ $detail->jumlah }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <p>Silakan login ke panel admin untuk melakukan konfirmasi pesanan.</p>
    <p>Terima kasih.</p>
</body>
</html>