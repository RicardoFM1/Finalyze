<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembrete de Renovação</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #ffffff;
            padding: 30px;
            text-align: center;
        }

        .header h1 {
            margin: 0;
            font-size: 28px;
        }

        .content {
            padding: 30px;
        }

        .highlight-box {
            background: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 20px 0;
            border-radius: 4px;
        }

        .button {
            display: inline-block;
            background: #667eea;
            color: #ffffff;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin: 20px 0;
            text-align: center;
        }

        .button:hover {
            background: #5568d3;
        }

        .footer {
            background: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }

        .info-row {
            margin: 10px 0;
        }

        .info-label {
            font-weight: bold;
            color: #667eea;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>⏰ Lembrete de Renovação</h1>
        </div>

        <div class="content">
            <p>Olá, <strong>{{ $usuario->nome }}</strong>!</p>

            <p>Este é um lembrete amigável de que sua assinatura do <strong>Finalyze</strong> está chegando ao fim.</p>

            <div class="highlight-box">
                <div class="info-row">
                    <span class="info-label">Plano:</span> {{ $plano->nome }}
                </div>
                <div class="info-row">
                    <span class="info-label">Expira em:</span> {{ $assinatura->termina_em->format('d/m/Y H:i') }}
                </div>
                <div class="info-row">
                    <span class="info-label">Dias restantes:</span> <strong>{{ $diasRestantes }} {{ $diasRestantes == 1 ? 'dia' : 'dias' }}</strong>
                </div>
            </div>

            <p>Para continuar aproveitando todos os benefícios do Finalyze, renove sua assinatura agora:</p>

            <center>
                <a href="{{ config('app.frontend_url') }}/planos" class="button">
                    Renovar Agora →
                </a>
            </center>

            <p style="margin-top: 30px; font-size: 14px; color: #666;">
                Se você já renovou, por favor desconsidere este e-mail.
            </p>
        </div>

        <div class="footer">
            <p><strong>Finalyze</strong> - Sua gestão financeira simplificada</p>
            <p>Se você não deseja receber estes lembretes, entre em contato conosco.</p>
        </div>
    </div>
</body>

</html>