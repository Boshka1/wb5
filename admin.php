<?php
session_start();
require_once 'config/database.php';

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Получение всех пользователей
$applicants = [];
$result = $conn->query("SELECT id, full_name, phone, email, birth_date, gender, biography, contract_accepted, login FROM applicants");
while ($row = $result->fetch_assoc()) {
    // Получение языков
    $stmt = $conn->prepare("SELECT pl.name FROM programming_languages pl JOIN applicant_languages al ON pl.id = al.language_id WHERE al.applicant_id = ?");
    $stmt->bind_param("i", $row['id']);
    $stmt->execute();
    $lang_result = $stmt->get_result();
    $languages = [];
    while ($lang_row = $lang_result->fetch_assoc()) {
        $languages[] = $lang_row['name'];
    }
    $stmt->close();
    $row['languages'] = implode(', ', $languages);
    $applicants[] = $row;
}

// Статистика по языкам
$stats = [];
$result = $conn->query("SELECT pl.name, COUNT(al.applicant_id) as count FROM programming_languages pl LEFT JOIN applicant_languages al ON pl.id = al.language_id GROUP BY pl.id, pl.name");
while ($row = $result->fetch_assoc()) {
    $stats[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Панель администратора</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f4f4f9;
            margin: 20px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            background: #fff;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background: #0d6efd;
            color: #fff;
        }
        tr:nth-child(even) {
            background: #f9f9f9;
        }
        a {
            color: #0d6efd;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
        .action {
            margin-right: 10px;
        }
        .stats {
            margin-top: 30px;
        }
        .logout {
            display: block;
            text-align: center;
            margin: 20px 0;
            padding: 10px;
            background: #0d6efd;
            color: #fff;
            border-radius: 4px;
            width: 200px;
            margin-left: auto;
            margin-right: auto;
        }
    </style>
</head>
<body>
    <h1>Панель администратора</h1>
    <a href="logout.php" class="logout">Выйти</a>
    <h2>Данные пользователей</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>ФИО</th>
            <th>Телефон</th>
            <th>Email</th>
            <th>Дата рождения</th>
            <th>Пол</th>
            <th>Языки</th>
            <th>Биография</th>
            <th>Контракт</th>
            <th>Логин</th>
            <th>Действия</th>
        </tr>
        <?php foreach ($applicants as $applicant): ?>
            <tr>
                <td><?php echo htmlspecialchars($applicant['id']); ?></td>
                <td><?php echo htmlspecialchars($applicant['full_name']); ?></td>
                <td><?php echo htmlspecialchars($applicant['phone']); ?></td>
                <td><?php echo htmlspecialchars($applicant['email']); ?></td>
                <td><?php echo htmlspecialchars($applicant['birth_date']); ?></td>
                <td><?php echo htmlspecialchars($applicant['gender']); ?></td>
                <td><?php echo htmlspecialchars($applicant['languages']); ?></td>
                <td><?php echo htmlspecialchars(substr($applicant['biography'], 0, 50)) . (strlen($applicant['biography']) > 50 ? '...' : ''); ?></td>
                <td><?php echo $applicant['contract_accepted'] ? 'Да' : 'Нет'; ?></td>
                <td><?php echo htmlspecialchars($applicant['login']); ?></td>
                <td>
                    <a href="admin_edit.php?id=<?php echo $applicant['id']; ?>" class="action">Редактировать</a>
                    <a href="admin_delete.php?id=<?php echo $applicant['id']; ?>" class="action" onclick="return confirm('Вы уверены, что хотите удалить?')">Удалить</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>

    <h2 class="stats">Статистика по языкам программирования</h2>
    <table>
        <tr>
            <th>Язык</th>
            <th>Количество пользователей</th>
        </tr>
        <?php foreach ($stats as $stat): ?>
            <tr>
                <td><?php echo htmlspecialchars($stat['name']); ?></td>
                <td><?php echo htmlspecialchars($stat['count']); ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>