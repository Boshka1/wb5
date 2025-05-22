<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

require_once 'config/database.php';

// Получение данных пользователя
$stmt = $conn->prepare("SELECT full_name, phone, email, birth_date, gender, biography, contract_accepted FROM applicants WHERE id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();

if (!$data) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Получение языков
$stmt = $conn->prepare("SELECT language_id FROM applicant_languages WHERE applicant_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
$languages = [];
while ($row = $result->fetch_assoc()) {
    $languages[] = $row['language_id'];
}
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактирование данных</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div class="form-container">
        <h1>Редактирование данных</h1>
        <form action="save_edit.php" method="POST">
            <div class="form-group">
                <label for="fullName">ФИО:</label>
                <input type="text" id="fullName" name="full_name" value="<?php echo htmlspecialchars($data['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label for="phone">Телефон:</label>
                <input type="tel" id="phone" name="phone" value="<?php echo htmlspecialchars($data['phone']); ?>" required pattern="\d{10,15}">
            </div>

            <div class="form-group">
                <label for="email">E-mail:</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($data['email']); ?>" required>
            </div>

            <div class="form-group">
                <label for="birthDate">Дата рождения:</label>
                <input type="date" id="birthDate" name="birth_date" value="<?php echo htmlspecialchars($data['birth_date']); ?>" required>
            </div>

            <div class="form-group">
                <label>Пол:</label>
                <div class="radio-group">
                    <label><input type="radio" name="gender" value="male" <?php echo $data['gender'] === 'male' ? 'checked' : ''; ?>> Мужчина</label>
                    <label><input type="radio" name="gender" value="female" <?php echo $data['gender'] === 'female' ? 'checked' : ''; ?>> Женщина</label>
                </div>
            </div>

            <div class="form-group">
                <label for="languages">Любимый язык программирования:</label>
                <select name="languages[]" id="languages" multiple required>
                    <?php
                    $all_languages = [
                        1 => 'Pascal', 2 => 'C', 3 => 'C++', 4 => 'JavaScript', 5 => 'PHP',
                        6 => 'Python', 7 => 'Java', 8 => 'Haskell', 9 => 'Clojure', 10 => 'Prolog',
                        11 => 'Scala', 12 => 'Go'
                    ];
                    foreach ($all_languages as $value => $name):
                    ?>
                        <option value="<?php echo $value; ?>" <?php echo in_array($value, $languages) ? 'selected' : ''; ?>>
                            <?php echo $name; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="biography">Биография:</label>
                <textarea id="biography" name="biography" required><?php echo htmlspecialchars($data['biography']); ?></textarea>
            </div>

            <div class="form-group">
                <label for="contractAccepted">
                    <input type="checkbox" name="contractAccepted" id="contractAccepted" required <?php echo $data['contract_accepted'] ? 'checked' : ''; ?>> Я согласен с условиями контракта
                </label>
            </div>

            <button type="submit">Сохранить изменения</button>
        </form>
        <p><a href="logout.php">Выйти</a></p>
    </div>
</body>
</html>