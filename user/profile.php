<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/permission_check.php';
require_once __DIR__ . '/../config/database.php';

requirePermission('profile.edit');

$pdo = getPDO();

$userId = (int) $_SESSION['user_id'];
$stmt = $pdo->prepare('SELECT id, username, full_name, email FROM users WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $userId]);
$user = $stmt->fetch();

$errors = array();
$fullName = $user['full_name'];
$email = $user['email'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
    $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
    $currentPassword = isset($_POST['current_password']) ? $_POST['current_password'] : '';
    $newPassword = isset($_POST['new_password']) ? $_POST['new_password'] : '';

    if ($fullName === '' || strlen($fullName) > 100) {
        $errors[] = 'Full name is required (max 100 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($newPassword !== '') {
        if ($currentPassword === '') {
            $errors[] = 'Enter your current password to change it.';
        } elseif (strlen($newPassword) < 8) {
            $errors[] = 'New password must be at least 8 characters.';
        } else {
            $stmt = $pdo->prepare('SELECT password_hash FROM users WHERE id = :id');
            $stmt->execute([':id' => $userId]);
            $hash = $stmt->fetchColumn();
            if (!password_verify($currentPassword, $hash)) {
                $errors[] = 'Your current password is incorrect.';
            }
        }
    }

    if ($newPassword === '' && $currentPassword !== '') {
        $errors[] = 'Enter a new password to change it.';
    }

    if (empty($errors)) {
        $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e AND id <> :id');
        $stmt->execute([':e' => $email, ':id' => $userId]);
        if ((int) $stmt->fetchColumn() > 0) {
            $errors[] = 'Another account already uses that email address.';
        }
    }

if (empty($errors)) {
        $stmt = $pdo->prepare('UPDATE users SET full_name = :f, email = :e WHERE id = :id');
        $stmt->execute([':f' => $fullName, ':e' => $email, ':id' => $userId]);
        if (isset($_SESSION['student_profile'])) {
            $stmt = $pdo->prepare('UPDATE students SET email = :e WHERE user_id = :id');
            $stmt->execute([':e' => $email, ':id' => $userId]);
            $_SESSION['student_profile']['email'] = $email;
        }
        if ($newPassword !== '') {
            $stmt = $pdo->prepare('UPDATE users SET password_hash = :p WHERE id = :id');
            $stmt->execute([':p' => password_hash($newPassword, PASSWORD_BCRYPT), ':id' => $userId]);
        }
        $_SESSION['full_name'] = $fullName;
        $_SESSION['email'] = $email;
        $_SESSION['flash_success'] = 'Profile updated.';
        header('Location: profile.php');
        exit;
    }
}

$initials = strtoupper(substr(trim($fullName), 0, 1));
$spacePos = strpos(trim($fullName), ' ');
if ($spacePos !== false) {
    $initials = strtoupper(substr(trim($fullName), 0, 1) . substr(trim($fullName), $spacePos + 1, 1));
}

$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Profile | Local Auth Server</title>
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="app-wrapper">
    <aside class="app-sidebar">
      <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a class="sidebar-brand" href="index.php">
          <span class="brand-dot">LA</span>
          <span>Local Auth Server</span>
        </a>
        <button type="button" class="sidebar-close d-lg-none" aria-label="Close menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>
      <nav class="side-nav">
        <div class="side-nav-title">Menu</div>
        <a class="nav-link" href="index.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
          Dashboard
        </a>
        <a class="nav-link active" href="profile.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          My Profile
        </a>
        <a class="nav-link" href="activity.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
          My Activity
        </a>
      </nav>
      <div class="sidebar-user">
        <span class="avatar"><?= htmlspecialchars($initials) ?></span>
        <div class="me-auto">
          <div class="user-name"><?= htmlspecialchars($_SESSION['full_name']) ?></div>
          <div class="user-role"><?= htmlspecialchars($_SESSION['role_name']) ?></div>
        </div>
        <button type="button" class="btn-ghost" data-bs-toggle="modal" data-bs-target="#logoutModal" title="Logout">
<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
        </button>
      </div>
    </aside>

    <div class="sidebar-backdrop"></div>

    <main class="app-main">
      <div class="mobile-bar">
        <button type="button" class="sidebar-toggle" aria-label="Open menu">
          <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
        <span class="mobile-brand"><span class="brand-dot">LA</span> Local Auth Server</span>
      </div>

      <?php if ($flashSuccess !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
          <?= htmlspecialchars($flashSuccess) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger py-2">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="d-flex align-items-center gap-3 mb-4">
        <span class="avatar" style="width: 56px; height: 56px; font-size: 1.25rem;"><?= htmlspecialchars($initials) ?></span>
        <div>
          <h1 class="h3 mb-0">My Profile</h1>
<p class="text-muted small mb-0">@<?= htmlspecialchars($user['username']) ?> &middot; <?= htmlspecialchars($_SESSION['role_name']) ?></p>
        </div>
      </div>

      <?php if (isset($_SESSION['student_profile'])): ?>
        <?php $sp = $_SESSION['student_profile']; ?>
        <div class="row mb-4">
          <div class="col-lg-8 mx-auto">
            <div class="card">
              <div class="card-body p-4">
                <h5 class="mb-3 d-flex align-items-center gap-2">
                  <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: var(--accent);"><path d="M22 10v6M2 10l10-5 10 5-10 5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>
                  Student Profile
                </h5>
                <div class="row g-3">
                  <div class="col-sm-6">
                    <div class="text-muted small">Student ID</div>
                    <div class="fw-semibold"><?= htmlspecialchars($sp['student_id']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-muted small">Full Name</div>
                    <div class="fw-semibold"><?= htmlspecialchars($sp['first_name']) ?> <?= htmlspecialchars($sp['last_name']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-muted small">Department</div>
                    <div><?= htmlspecialchars($sp['department']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-muted small">Level</div>
                    <div><?= htmlspecialchars($sp['level']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-muted small">Email</div>
                    <div><?= htmlspecialchars($sp['email']) ?></div>
                  </div>
                  <div class="col-sm-6">
                    <div class="text-muted small">Phone</div>
                    <div><?= htmlspecialchars($sp['phone']) ?></div>
                  </div>
                </div>
                <p class="text-muted small mb-0 mt-3">Student records are managed by your administrator.</p>
              </div>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body p-4">
              <h5 class="mb-3">Account Details</h5>
              <form action="profile.php" method="POST" novalidate>
                <div class="mb-3">
                  <label for="full_name" class="form-label">Full Name</label>
                  <input type="text" class="form-control" id="full_name" name="full_name"
                         value="<?= htmlspecialchars($fullName) ?>" required>
                </div>
                <div class="mb-3">
                  <label for="email" class="form-label">Email</label>
                  <input type="email" class="form-control" id="email" name="email"
                         value="<?= htmlspecialchars($email) ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Save Details</button>
              </form>
            </div>
          </div>
        </div>
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body p-4">
              <h5 class="mb-3">Change Password</h5>
              <form action="profile.php" method="POST" novalidate>
                <input type="hidden" name="full_name" value="<?= htmlspecialchars($fullName) ?>">
                <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                <div class="mb-3">
                  <label for="current_password" class="form-label">Current Password</label>
                  <input type="password" class="form-control" id="current_password" name="current_password" autocomplete="current-password">
                </div>
                <div class="mb-3">
                  <label for="new_password" class="form-label">New Password</label>
                  <input type="password" class="form-control" id="new_password" name="new_password" autocomplete="new-password">
                  <div class="form-text">At least 8 characters. Leave blank to keep unchanged.</div>
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>

  <?php require_once __DIR__ . '/../includes/logout_modal.php'; ?>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>
