<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
ini_set('log_errors', 1);
ini_set('error_log', '/home/b/b918347x/public_html/php_errors.log');

session_start();

error_log("Session data: " . print_r($_SESSION, true));

$success = isset($_SESSION['success']) ? $_SESSION['success'] : false;
$generated_login = isset($_SESSION['generated_login']) ? $_SESSION['generated_login'] : null;
$generated_password = isset($_SESSION['generated_password']) ? $_SESSION['generated_password'] : null;

unset($_SESSION['success'], $_SESSION['generated_login'], $_SESSION['generated_password']);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Успешно!</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f9;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .container {
            background: #fff;
            padding: 40px 30px;
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        h1 {
            color: #0044d0;
            margin-bottom: 20px;
        }

        p {
            font-size: 18px;
            color: #555;
            margin-bottom: 15px;
        }

        .credentials {
            background: #f0f0f0;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        a {
            display: inline-block;
            padding: 12px 24px;
            background: #0044d0;
            color: #fff;
            text-decoration: none;
            border-radius: 8px;
            transition: background 0.3s;
        }

        a:hover {
            background: #0044d0;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Успешно!</h1>
        <p>Ваши данные были сохранены. Спасибо за отправку формы!</p>
        <?php if ($success && $generated_login && $generated_password): ?>
            <div class="credentials">
                <p><strong>Ваш логин:</strong> <?php echo htmlspecialchars($generated_login); ?></p>
                <p><strong>Ваш пароль:</strong> <?php echo htmlspecialchars($generated_password); ?></p>
                <p>Сохраните эти данные для входа и редактирования формы.</p>
            </div>
        <?php else: ?>
            <p style="color: red;">Ошибка: логин и пароль не получены. Проверьте логи.</p>
        <?php endif; ?>
        <a href="index.php">⬅ Вернуться назад</a>
        <a href="login.php">Войти для редактирования</a>
    </div>
</body>
</html>