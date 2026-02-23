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
            <div class="title">{{ $meta->icone }} {{ $meta->titulo }}</div>
            <div class="description">{{ $meta->descricao }}</div>

            <p>
                <strong>{{ __('Status') }}:</strong> {{ $meta->status }}<br>
                @if($meta->prazo)
                📅 <strong>{{ __('Deadline') }}:</strong> {{ \Carbon\Carbon::parse($meta->prazo)->format(__('d/m/Y')) }}<br>
                @endif
                @if($meta->tipo === 'financeira')
                💰 <strong>{{ __('Progress') }}:</strong> {{ number_format($meta->valor_atual, 2, ',', '.') }} / {{ number_format($meta->valor_objetivo, 2, ',', '.') }}
                @else
                📈 <strong>{{ __('Progress') }}:</strong> {{ $meta->atual_quantidade }} / {{ $meta->meta_quantidade }} {{ $meta->unidade }}
                @endif
            </p>

            <a href="{{ config('app.url') }}/#/metas" class="btn">{{ __('View Goals') }}</a>
        </div>
        <div class="footer">
            {{ __('This is an automated reminder from Finalyze.') }}<br>
            © {{ date('Y') }} Finalyze Finance. {{ __('All rights reserved.') }}
        </div>
    </div>
</body>

</html>