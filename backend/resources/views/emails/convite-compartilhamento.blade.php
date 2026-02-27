<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color:
            max-width: 640px;
            margin: 0 auto;
            padding: 24px;
            background:
        }

        .card {
            background:
            border-radius: 12px;
            padding: 24px;
            border: 1px solid
        }

        .title {
            font-size: 20px;
            margin-bottom: 12px;
        }

        .btn {
            display: inline-block;
            padding: 12px 18px;
            background:
            color:
            text-decoration: none;
            border-radius: 8px;
            font-weight: 700;
            margin: 14px 0;
        }

        .meta {
            font-size: 12px;
            color:
            margin-top: 12px;
        }
    </style>
</head>

<body>
    <div class="card">
        <div class="title">Convite de compartilhamento</div>
        <p><strong>{{ $usuarioOrigem->nome }}</strong> convidou você para acessar a conta compartilhada no Finalyze.</p>

        @if (!empty($convite->mensagem))
        <p><strong>Mensagem:</strong> {{ $convite->mensagem }}</p>
        @endif

        <p>Clique no link abaixo para aceitar o convite:</p>
        <p>
            <a class="btn" href="{{ $linkConvite }}">Aceitar convite</a>
        </p>
        <p>Se o botão não funcionar, copie e cole este link no navegador:</p>
        <p>{{ $linkConvite }}</p>

        <p class="meta">Este link expira em {{ optional($convite->expira_em)->format('d/m/Y H:i') }}.</p>
    </div>
</body>

</html>