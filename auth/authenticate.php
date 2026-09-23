<?php

session_start();

require_once __DIR__ . '/../config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$username = trim(isset($_POST['username']) ? $_POST['username'] : '');
$password = isset($_POST['password']) ? $_POST['password'] : '';

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Please enter both username and password.';
    header('Location: login.php');
    exit;
}

$pdo = getPDO();

$logStmt = $pdo->prepare(
    'INSERT INTO login_logs (user_id, username_attempted, ip_address, user_agent, success)
     VALUES (:user_id, :username, :ip, :ua, :success)'
);

function recordLogin($pdo, $logStmt, $userId, $username, $success)
{
    $logStmt->execute([
        ':user_id' => $userId,
        ':username' => $username,
        ':ip' => isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '',
        ':ua' => isset($_SERVER['HTTP_USER_AGENT']) ? substr($_SERVER['HTTP_USER_AGENT'], 0, 255) : '',
        ':success' => $success ? 1 : 0,
    ]);
}

$stmt = $pdo->prepare(
    'SELECT u.id, u.username, u.full_name, u.email, u.password_hash,
            u.role_id, u.is_active, r.role_name
       FROM users u
       JOIN roles r ON r.id = u.role_id
      WHERE u.username = :username
      LIMIT 1'
);
$stmt->execute([':username' => $username]);
$user = $stmt->fetch();

if ($user === false) {
    recordLogin($pdo, $logStmt, null, $username, false);
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: login.php');
    exit;
}

if (!password_verify($password, $user['password_hash'])) {
    recordLogin($pdo, $logStmt, (int) $user['id'], $username, false);
    $_SESSION['login_error'] = 'Invalid username or password.';
    header('Location: login.php');
    exit;
}

if ((int) $user['is_active'] !== 1) {
    recordLogin($pdo, $logStmt, (int) $user['id'], $username, false);
    $_SESSION['login_error'] = 'This account has been deactivated. Contact an administrator.';
    header('Location: login.php');
    exit;
}

recordLogin($pdo, $logStmt, (int) $user['id'], $username, true);

session_regenerate_id(true);

$_SESSION['user_id'] = (int) $user['id'];
$_SESSION['username'] = $user['username'];
$_SESSION['full_name'] = $user['full_name'];
$_SESSION['email'] = $user['email'];
$_SESSION['role_id'] = (int) $user['role_id'];
$_SESSION['role_name'] = $user['role_name'];

$permStmt = $pdo->prepare(
    'SELECT p.permission_name
       FROM permissions p
       JOIN role_permissions rp ON rp.permission_id = p.id
      WHERE rp.role_id = :role_id'
);
$permStmt->execute([':role_id' => $_SESSION['role_id']]);
$_SESSION['permissions'] = $permStmt->fetchAll(PDO::FETCH_COLUMN);

$stuStmt = $pdo->prepare(
    'SELECT student_id, first_name, last_name, department, level, phone, email
       FROM students
      WHERE user_id = :user_id
      LIMIT 1'
);
$stuStmt->execute([':user_id' => $_SESSION['user_id']]);
$studentProfile = $stuStmt->fetch();
$_SESSION['student_profile'] = $studentProfile === false ? null : $studentProfile;

if (isset($_SESSION['redirect_after_login']) && $_SESSION['redirect_after_login'] !== '') {
    $dashboard = $_SESSION['redirect_after_login'];
    unset($_SESSION['redirect_after_login']);
} else {
    $dashboard = ($user['role_id'] === 1) ? '../admin/index.php' : '../user/index.php';
}
header('Location: ' . $dashboard);
exit;