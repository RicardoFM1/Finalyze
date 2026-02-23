<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
        }

        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #e1e1e1;
            border-radius: 12px;
        }

        .header {
            text-align: center;
            margin-bottom: 30px;
        }

        .logo {
            font-size: 28px;
            font-weight: bold;
            color: #1867c0;
        }

        .content {
            background: #f9f9f9;
            padding: 25px;
            border-radius: 8px;
            border-left: 5px solid #1867c0;
        }

        .title {
            font-size: 20px;
            font-weight: bold;
            color: #1867c0;
            margin-bottom: 10px;
        }

        .description {
            color: #555;
            font-size: 16px;
            margin-bottom: 20px;
            white-space: pre-wrap;
        }

        .footer {
            text-align: center;
            margin-top: 30px;
            font-size: 12px;
            color: #888;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background-color: #1867c0;
            color: white !important;
            text-decoration: none;
            border-radius: 8px;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Finalyze</div>
        </div>
        <div class="content">
            <div class="title">{{ $lembrete->icone }} {{ $lembrete->titulo }}</div>
            <div class="description">{{ $lembrete->descricao }}</div>

            <p>
                @if($lembrete->prazo)
                📅 <strong>{{ __('Date') }}:</strong> {{ \Carbon\Carbon::parse($lembrete->prazo)->format(__('d/m/Y')) }}<br>
                @endif
                @if($lembrete->hora)
                ⏰ <strong>{{ __('Time') }}:</strong> {{ \Carbon\Carbon::parse($lembrete->hora)->format('H:i') }}
                @endif
            </p>

            <a href="https://finalyze-6gw9.onrender.com/#/lembretes" class="btn">{{ __('View in Dashboard') }}</a>
        </div>
        <div class="footer">
            {{ __('This is an automated reminder from Finalyze.') }}<br>
            © {{ date('Y') }} Finalyze Finance. {{ __('All rights reserved.') }}
        </div>
    </div>
</body>

</html>