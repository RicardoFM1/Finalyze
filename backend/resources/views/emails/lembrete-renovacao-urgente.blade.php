<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>URGENTE: Assinatura Expira Amanhã</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color:
            background-color:
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background:
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg,
            color:
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .urgent-badge {
            background:
            color: white;
            padding: 5px 15px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: bold;
            display: inline-block;
            margin-bottom: 10px;
        }

        .content {
            padding: 30px;
        }

        .highlight-box {
            background:
            border: 2px solid
            border-left: 6px solid
            padding: 20px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .button {
            display: inline-block;
            background:
            color:
            padding: 15px 40px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            font-size: 18px;
            margin: 20px 0;
            text-align: center;
            box-shadow: 0 4px 6px rgba(255, 68, 68, 0.3);
        }

        .button:hover {
            background:
        }

        .footer {
            background:
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color:
        }

        .info-row {
            margin: 10px 0;
            font-size: 16px;
        }

        .info-label {
            font-weight: bold;
            color:
        }

        .timer {
            font-size: 32px;
            font-weight: bold;
            color:
            text-align: center;
            margin: 20px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <span class="urgent-badge">🚨 URGENTE</span>
            <h1>Sua Assinatura Expira Amanhã!</h1>
        </div>

        <div class="content">
            <p>Olá, <strong>{{ $usuario->nome }}</strong>!</p>

            <p style="font-size: 18px; color:
                ⚠️ Sua assinatura do Finalyze expira em menos de 24 horas!
            </p>

            <div class="timer">
                ⏱️ {{ $horasRestantes }}h restantes
            </div>

            <div class="highlight-box">
                <div class="info-row">
                    <span class="info-label">Plano:</span> {{ $plano->nome }}
                </div>
                <div class="info-row">
                    <span class="info-label">Expira em:</span> {{ $assinatura->termina_em->format('d/m/Y H:i') }}
                </div>
            </div>

            <p style="font-size: 16px;">
                <strong>Não perca acesso aos seus dados e funcionalidades!</strong>
            </p>

            <p>Renove agora para continuar gerenciando suas finanças sem interrupções:</p>

            <center>
                <a href="{{ config('app.frontend_url') }}/planos" class="button">
                    🚀 RENOVAR AGORA
                </a>
            </center>

            <p style="margin-top: 30px; padding: 15px; background:
                💡 <strong>Dica:</strong> Após a expiração, você perderá acesso a todas as funcionalidades premium do Finalyze.
            </p>
        </div>

        <div class="footer">
            <p><strong>Finalyze</strong> - Sua gestão financeira simplificada</p>
            <p>Este é um e-mail automático. Se você já renovou, por favor ignore.</p>
        </div>
    </div>
</body>

</html>