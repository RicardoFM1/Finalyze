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
            margin: 20px auto;
            padding: 20px;
            border: 1px solid #eee;
            border-radius: 10px;
        }

        .header {
            background: #673AB7;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }

        .content {
            padding: 30px;
            text-align: center;
        }

        .footer {
            font-size: 12px;
            color: #888;
            text-align: center;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background: #673AB7;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="header">
            <h1>Convite de Colaboração</h1>
        </div>
        <div class="content">
            <p>Olá!</p>
            <p><strong>{{ $owner->nome }}</strong> convidou você para colaborar na conta do <strong>Finalyze Finance</strong>.</p>
            <p>Como colaborador, você poderá visualizar e editar lançamentos, metas e agenda deste workspace.</p>
            <p>Para começar, basta acessar sua conta (ou criar uma se ainda não tiver) usando este e-mail.</p>
            <a href="https://finalyze-6gw9.onrender.com" class="btn">Acessar Finalyze</a>
        </div>
        <div class="footer">
            <p>Finalyze - Controle financeiro inteligente.</p>
        </div>
    </div>
</body>

</html>