<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
            flex-direction: column;
        }
        .title {
            font-size: 2.5rem;
            font-weight: bold;
            margin-bottom: 2rem;
            color: #343a40;
        }
        .btn-container {
            display: flex;
            gap: 20px;
        }
        .btn-custom {
            padding: 15px 30px;
            font-size: 18px;
            font-weight: bold;
            border-radius: 8px;
            min-width: 200px;
        }
        .btn-form {
            background-color: #4CAF50;
            border-color: #4CAF50;
        }
        .btn-admin {
            background-color: #2196F3;
            border-color: #2196F3;
        }
        .btn-form:hover {
            background-color: #45a049;
            border-color: #45a049;
        }
        .btn-admin:hover {
            background-color: #0b7dda;
            border-color: #0b7dda;
        }
    </style>
</head>
<body>
    <h1 class="title">Задание №6</h1>
    <div class="btn-container">
        <a href="index.php" class="btn btn-primary btn-custom btn-form">Форма</a>
        <a href="admin_login.php" class="btn btn-primary btn-custom btn-admin">Админ панель</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>