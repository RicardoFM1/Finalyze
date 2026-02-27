<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color:
        }

        .container {
            max-width: 600px;
            margin: 20px auto;
            padding: 20px;
            border: 1px solid
            border-radius: 10px;
        }

        .header {
            background:
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
            color:
            text-align: center;
            padding: 20px;
        }

        .btn {
            display: inline-block;
            padding: 12px 24px;
            background:
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
            <h1>{{ __('Collaboration Invite') }}</h1>
        </div>
        <div class="content">
            <p>{{ __('Hello!') }}</p>
            <p><strong>{{ $owner->nome }}</strong> {{ __('has invited you to collaborate on their Finalyze Finance account.') }}</p>
            <p>{{ __('As a collaborator, you can view and edit transactions, goals, and the agenda of this workspace.') }}</p>
            <p>{{ __('To start, simply access your account (or create one if you do not have one yet).') }}</p>
            <p style="color:
            <a href="https://finalyze-6gw9.onrender.com/#/login" class="btn">{{ __('Access Finalyze') }}</a>
        </div>
        <div class="footer">
            <p>{{ __('Finalyze - Intelligent financial control.') }}</p>
        </div>
    </div>
</body>

</html>