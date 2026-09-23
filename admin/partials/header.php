<?php

require_once __DIR__ . '/../../includes/auth_check.php';
require_once __DIR__ . '/../../includes/permission_check.php';
require_once __DIR__ . '/../../config/database.php';

if ($_SESSION['role_id'] !== 1) {
    $_SESSION['flash_error'] = 'You do not have permission to view that page.';
    header('Location: ../user/index.php');
    exit;
}

$pageTitle = isset($pageTitle) ? $pageTitle : 'Admin';
$activeNav = isset($activeNav) ? $activeNav : 'dashboard';

$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_success']);
$flashError = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_error']);

$initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1));
$spacePos = strpos(trim($_SESSION['full_name']), ' ');
if ($spacePos !== false) {
    $initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1) . substr(trim($_SESSION['full_name']), $spacePos + 1, 1));
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($pageTitle) ?> | Local Network Auth Server</title>
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
  <div class="app-wrapper">
    <aside class="app-sidebar">
      <div class="sidebar-header d-flex align-items-center justify-content-between">
        <a class="sidebar-brand" href="index.php">
          
          <span>Local Network Auth Server</span>
        </a>
        <button type="button" class="sidebar-close d-lg-none" aria-label="Close menu">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
      </div>

      <nav class="side-nav">
        <div class="side-nav-title">Menu</div>
        <a class="nav-link<?= $activeNav === 'dashboard' ? ' active' : '' ?>" href="index.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
          Dashboard
        </a>
        <?php if (hasPermission('users.view')): ?>
          <a class="nav-link<?= $activeNav === 'users' ? ' active' : '' ?>" href="users.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
            Users
          </a>
        <?php endif; ?>
        <?php if (hasPermission('roles.manage')): ?>
          <a class="nav-link<?= $activeNav === 'roles' ? ' active' : '' ?>" href="roles.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8.5 4.5v8L12 22l-8.5-5.5v-8L12 2z"/><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
            Roles
          </a>
        <?php endif; ?>
        <?php if (hasPermission('logs.view')): ?>
          <a class="nav-link<?= $activeNav === 'logs' ? ' active' : '' ?>" href="logs.php">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
            Authentication Logs
          </a>
        <?php endif; ?>
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
        Local Network Auth Server</span>
      </div>

      <?php if ($flashSuccess !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
          <?= htmlspecialchars($flashSuccess) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if ($flashError !== ''): ?>
        <div class="alert alert-danger alert-dismissible fade show py-2" role="alert">
          <?= htmlspecialchars($flashError) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>