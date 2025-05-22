<?php
session_start();
require_once 'config/database.php';
require_once 'utils/validation.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

$applicant_id = $_GET['id'] ?? 0;
$errors = [];

if (!$applicant_id) {
    header('Location: admin.php');
    exit;
}

// Получение данных пользователя
$stmt = $conn->prepare("SELECT * FROM applicants WHERE id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$applicant = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$applicant) {
    header('Location: admin.php');
    exit;
}

// Получение текущих языков
$stmt = $conn->prepare("SELECT language_id FROM applicant_languages WHERE applicant_id = ?");
$stmt->bind_param("i", $applicant_id);
$stmt->execute();
$result = $stmt->get_result();
$current_languages = [];
while ($row = $result->fetch_assoc()) {
    $current_languages[] = $row['language_id'];
}
$stmt->close();

// Получение всех языков
$languages = [];
$result = $conn->query("SELECT id, name FROM programming_languages");
while ($row = $result->fetch_assoc()) {
    $languages[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $birthDate = trim($_POST['birth_date'] ?? '');
    $gender = $_POST['gender'] ?? '';
    $new_languages = $_POST['languages'] ?? [];
    $biography = trim($_POST['biography'] ?? '');
    $contractAccepted = isset($_POST['contractAccepted']);

    // Валидация
    if ($error = validateFullName($fullName)) {
        $errors['full_name'] = $error;
    }
    if ($error = validatePhone($phone)) {
        $errors['phone'] = $error;
    }
    if ($error = validateEmail($email)) {
        $errors['email'] = $error;
    }
    if ($error = validateBirthDate($birthDate)) {
        $errors['birth_date'] = $error;
    }
    if ($error = validateGender($gender)) {
        $errors['gender'] = $error;
    }
    if ($error = validateLanguages($new_languages, $conn)) {
        $errors['languages'] = $error;
    }
    if ($error = validateBiography($biography)) {
        $errors['biography'] = $error;
    }
    if ($error = validateContract($contractAccepted)) {
        $errors['contractAccepted'] = $error;
    }

    if (empty($errors)) {
        // Обновление данных пользователя
        $stmt = $conn->prepare("UPDATE applicants SET full_name = ?, phone = ?, email = ?, birth_date = ?, gender = ?, biography = ?, contract_accepted = ? WHERE id = ?");
        $contractAcceptedInt = $contractAccepted ? 1 : 0;
        $stmt->bind_param("ssssssii", $fullName, $phone, $email, $birthDate, $gender, $biography, $contractAcceptedInt, $applicant_id);
        $stmt->execute();
        $stmt->close();

        // Обновление языков
        $conn->query("DELETE FROM applicant_languages WHERE applicant_id = $applicant_id");
        $lang_stmt = $conn->prepare("INSERT INTO applicant_languages (applicant_id, language_id) VALUES (?, ?)");
        $lang_stmt->bind_param("ii", $applicant_id, $language_id);
        foreach ($new_languages as $language_id) {
            $lang_stmt->execute();
        }
        $lang_stmt->close();

        $conn->close();
        header('Location: admin.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Редактировать пользователя</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f9;
            margin: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        h1 {
            color: #333;
            text-align: center;
        }
        label {
            display: block;
            margin: 10px 0 5px;
        }
        input, select, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            box-sizing: border-box;
        }
        select[multiple] {
            height: 100px;
        }
        .error {
            color: red;
            font-size: 14px;
        }
        button {
            padding: 10px 20px;
            background: #4CAF50;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #45a049;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            color: #4CAF50;
            text-decoration: none;
        }
    </style>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h1 class="mb-4">Редактировать пользователя</h1>

    <!-- Сообщения об ошибках -->
    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $field => $error): ?>
                <p class="mb-1"><?php echo htmlspecialchars($error); ?></p>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="border p-4 bg-white shadow-sm rounded">
        <div class="mb-3">
            <label for="full_name" class="form-label">ФИО</label>
            <input type="text" class="form-control" id="full_name" name="full_name" 
                value="<?php echo htmlspecialchars($applicant['full_name']); ?>">
            <?php if (isset($errors['full_name'])): ?>
                <p class="error"><?php echo $errors['full_name']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="text" class="form-control" id="phone" name="phone" 
                value="<?php echo htmlspecialchars($applicant['phone']); ?>">
            <?php if (isset($errors['phone'])): ?>
                <p class="error"><?php echo $errors['phone']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email" 
                value="<?php echo htmlspecialchars($applicant['email']); ?>">
            <?php if (isset($errors['email'])): ?>
                <p class="error"><?php echo $errors['email']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="birth_date" class="form-label">Дата рождения</label>
            <input type="date" class="form-control" id="birth_date" name="birth_date" 
                value="<?php echo htmlspecialchars($applicant['birth_date']); ?>">
            <?php if (isset($errors['birth_date'])): ?>
                <p class="error"><?php echo $errors['birth_date']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label class="form-label">Пол</label><br>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="male" value="male" 
                    <?php echo $applicant['gender'] === 'male' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="male">Мужской</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="gender" id="female" value="female" 
                    <?php echo $applicant['gender'] === 'female' ? 'checked' : ''; ?>>
                <label class="form-check-label" for="female">Женский</label>
            </div>
            <?php if (isset($errors['gender'])): ?>
                <p class="error"><?php echo $errors['gender']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="languages" class="form-label">Языки программирования</label>
            <select id="languages" name="languages[]" class="form-select" multiple>
                <?php foreach ($languages as $lang): ?>
                    <option value="<?php echo $lang['id']; ?>" <?php echo in_array($lang['id'], $current_languages) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($lang['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['languages'])): ?>
                <p class="error"><?php echo $errors['languages']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3">
            <label for="biography" class="form-label">Биография</label>
            <textarea id="biography" name="biography" class="form-control" rows="4"><?php echo htmlspecialchars($applicant['biography']); ?></textarea>
            <?php if (isset($errors['biography'])): ?>
                <p class="error"><?php echo $errors['biography']; ?></p>
            <?php endif; ?>
        </div>

        <div class="mb-3 form-check">
            <input type="checkbox" class="form-check-input" id="contractAccepted" name="contractAccepted" 
                <?php echo $applicant['contract_accepted'] ? 'checked' : ''; ?>>
            <label class="form-check-label" for="contractAccepted">С контрактом ознакомлен(а)</label>
            <?php if (isset($errors['contractAccepted'])): ?>
                <p class="error"><?php echo $errors['contractAccepted']; ?></p>
            <?php endif; ?>
        </div>

        <button type="submit" class="btn btn-primary">Сохранить</button>
        <a href="admin.php" class="btn btn-secondary">Назад</a>
    </form>
</div>
</body>
</html>