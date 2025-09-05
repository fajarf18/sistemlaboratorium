<!DOCTYPE html>
<html>
<head>
    <title>Notifikasi Pengembalian Barang</title>
</head>
<body style="font-family: Arial, sans-serif;">
    <h2>Pengajuan Pengembalian Barang</h2>
    <p>Halo Admin,</p>
    <p>Seorang pengguna telah mengajukan pengembalian untuk peminjaman dengan detail sebagai berikut:</p>
    
    <p>
        <strong>ID Peminjaman:</strong> #{{ $peminjaman->id }} <br>
        <strong>Nama Peminjam:</strong> {{ $peminjaman->user->nama }} <br>
        <strong>NIM:</strong> {{ $peminjaman->user->nim }} <br>
        <strong>Status Saat Ini:</strong> {{ $peminjaman->status }}
    </p>
    
    <p>Pengguna ini ingin mengembalikan barang yang telah dipinjam. Silakan periksa di panel admin untuk melanjutkan prosesnya.</p>
    <p>Terima kasih.</p>
</body>
</html>