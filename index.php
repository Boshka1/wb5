<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
// Получаем ошибки из сессии
$errors = $_SESSION['errors'] ?? [];
// Очищаем ошибки после использования
$_SESSION['errors'] = [];
?>

<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Форма заявки</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4">Форма заявки</h1>

    <!-- Сообщения об ошибках -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $field => $error): ?>
                <p class="mb-1"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form action="process_form.php" method="POST" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label for="full_name" class="form-label">ФИО</label>
            <input type="text" class="form-control" id="full_name" name="full_name"
                placeholder="Введите ваше полное имя" required
                value="<?php echo isset($_COOKIE['full_name']) ? htmlspecialchars($_COOKIE['full_name']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="tel" class="form-control" id="phone" name="phone"
                placeholder="8(900)917 33-77" pattern="^\+?\d{10,15}$" required
                value="<?php echo isset($_COOKIE['phone']) ? htmlspecialchars($_COOKIE['phone']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                placeholder="example@domain.com" required
                value="<?php echo isset($_COOKIE['email']) ? htmlspecialchars($_COOKIE['email']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label for="dob" class="form-label">Дата рождения</label>
            <input type="date" class="form-control" id="dob" name="dob" required
                value="<?php echo isset($_COOKIE['dob']) ? htmlspecialchars($_COOKIE['dob']) : ''; ?>">
        </div>

        <div class="mb-3">
            <label class="form-label">Пол</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" required
                    <?php if (isset($_COOKIE['gender']) && $_COOKIE['gender'] === 'female') echo 'checked'; ?>>
                <label class="form-check-label" for="female">Женский</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male"
                    <?php if (isset($_COOKIE['gender']) && $_COOKIE['gender'] === 'male') echo 'checked'; ?>>
                <label class="form-check-label" for="male">Мужской</label>
            </div>
        </div>

        <div class="mb-3">
            <label for="languages" class="form-label">Любимые языки программирования</label>
            <select id="languages" name="languages[]" class="form-select" multiple required>
                <?php
                $options = ['Pascal', 'C', 'C++', 'JavaScript', 'PHP', 'Python', 'Java', 'Haskell', 'Clojure', 'Prolog', 'Scala', 'Go'];
                $selected = isset($_COOKIE['languages']) ? json_decode($_COOKIE['languages'], true) : [];
                foreach ($options as $lang) {
                    $isSelected = in_array($lang, $selected) ? 'selected' : '';
                    echo "<option value=\"$lang\" $isSelected>$lang</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="bio" class="form-label">Биография</label>
            <textarea id="bio" name="bio" class="form-control" rows="4" required><?php
                echo isset($_COOKIE['bio']) ? htmlspecialchars($_COOKIE['bio']) : '';
            ?></textarea>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="contract" name="contract" required
                <?php if (isset($_COOKIE['contract'])) echo 'checked'; ?>>
            <label class="form-check-label" for="contract">С контрактом ознакомлен(а)</label>
        </div>

        <button type="submit" class="btn btn-primary">Отправить</button>
    </form>
    <p class="mt-3"><a href="login.php">Войти для редактирования данных</a></p>
</div>
</body>
</html>