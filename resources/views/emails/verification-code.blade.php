<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Подтверждение регистрации</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #ffffff;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 2px solid #6366f1;
        }
        .header h1 {
            color: #6366f1;
            margin: 0;
        }
        .content {
            padding: 30px 20px;
            text-align: center;
        }
        .code {
            font-size: 36px;
            font-weight: bold;
            color: #6366f1;
            background-color: #f0f0ff;
            display: inline-block;
            padding: 15px 30px;
            border-radius: 10px;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            padding: 20px;
            font-size: 12px;
            color: #888888;
            border-top: 1px solid #eeeeee;
        }
        .warning {
            color: #ff3366;
            font-size: 12px;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Фотосфера</h1>
        </div>
        <div class="content">
            <h2>Здравствуйте, {{ $login ?? 'пользователь' }}!</h2>
            <p>Для завершения регистрации введите следующий код подтверждения:</p>
            <div class="code">{{ $code }}</div>
            <p>Код действителен в течение 15 минут.</p>
            <p class="warning">Если вы не регистрировались на нашем сайте, просто проигнорируйте это письмо.</p>
        </div>
        <div class="footer">
            <p>&copy; {{ date('Y') }} Фотосфера. Все права защищены.</p>
            <p>Это автоматическое сообщение, пожалуйста, не отвечайте на него.</p>
        </div>
    </div>
</body>
</html>