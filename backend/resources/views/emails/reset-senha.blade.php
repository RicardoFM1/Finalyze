<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: 'Inter', sans-serif;
            line-height: 1.6;
            color:
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .container {
            background-color:
            border-radius: 16px;
            padding: 40px;
            text-align: center;
        }

        .header {
            margin-bottom: 30px;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
            color:
        }

        .code {
            font-size: 48px;
            font-weight: bold;
            color:
            letter-spacing: 10px;
            margin: 30px 0;
            padding: 20px;
            background:
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            display: inline-block;
        }

        .footer {
            margin-top: 40px;
            font-size: 12px;
            color:
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <div class="logo">Finalyze</div>
        </div>

        <h2>{{ __('Hello, :name!', ['name' => $usuario->nome]) }}</h2>
        <p>{{ __('Use the code below to reset your password. This code is valid for 1 hour:') }}</p>

        <div class="code">{{ $codigo }}</div>

        <p>{{ __('If you did not request a password reset, please ignore this e-mail.') }}</p>

        <div class="footer">
            &copy; {{ date('Y') }} Finalyze Finance. {{ __('All rights reserved.') }}
        </div>
    </div>
</body>

</html>