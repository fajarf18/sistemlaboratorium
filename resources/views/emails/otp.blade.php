<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode Verifikasi OTP Anda</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f4f4;
            color: #333;
            line-height: 1.6;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            padding-bottom: 20px;
            border-bottom: 1px solid #eeeeee;
        }
        .header h1 {
            color: #0056b3;
            margin: 0;
        }
        .content {
            padding: 20px 0;
        }
        .content p {
            font-size: 16px;
        }
        .otp-code {
            display: block;
            width: fit-content;
            margin: 25px auto;
            padding: 15px 30px;
            background-color: #e6f7ff;
            border: 1px dashed #91d5ff;
            border-radius: 8px;
            font-size: 36px;
            font-weight: bold;
            color: #0056b3;
            letter-spacing: 5px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 1px solid #eeeeee;
            font-size: 12px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Verifikasi Akun Anda</h1>
        </div>
        <div class="content">
            <p>Halo,</p>
            <p>Terima kasih telah mendaftar. Silakan gunakan kode verifikasi (OTP) di bawah ini untuk menyelesaikan proses registrasi Anda. Kode ini berlaku selama 10 menit.</p>
            
            <span class="otp-code">{{ $otp }}</span>
            
            <p>Jika Anda tidak merasa mendaftar, mohon abaikan email ini.</p>
            <p>Terima kasih!</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>