<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/permission_check.php';
require_once __DIR__ . '/../config/database.php';

$flash = isset($_SESSION['flash_error']) ? $_SESSION['flash_error'] : '';
unset($_SESSION['flash_error']);

$flashSuccess = isset($_SESSION['flash_success']) ? $_SESSION['flash_success'] : '';
unset($_SESSION['flash_success']);

$initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1));
$spacePos = strpos(trim($_SESSION['full_name']), ' ');
if ($spacePos !== false) {
    $initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1) . substr(trim($_SESSION['full_name']), $spacePos + 1, 1));
}

$pdo = getPDO();
$userId = (int) $_SESSION['user_id'];

$stmt = $pdo->prepare(
    'SELECT u.username, u.full_name, u.email, u.created_at, u.last_login_at, r.role_name
       FROM users u
       JOIN roles r ON r.id = u.role_id
      WHERE u.id = :id
      LIMIT 1'
);
$stmt->execute([':id' => $userId]);
$account = $stmt->fetch();

$stats = $pdo->prepare(
    'SELECT COUNT(*) AS total,
            SUM(success = 1) AS ok,
            SUM(success = 0) AS bad
       FROM login_logs
      WHERE user_id = :id'
);
$stats->execute([':id' => $userId]);
$stats = $stats->fetch();

$recent = $pdo->prepare(
    'SELECT l.ip_address, l.success, l.logged_at, l.username_attempted
       FROM login_logs l
      WHERE l.user_id = :id
      ORDER BY l.logged_at DESC, l.id DESC
      LIMIT 6'
);
$recent->execute([':id' => $userId]);
$recent = $recent->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Dashboard | Local Network Auth Server</title>
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
        <a class="nav-link active" href="index.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="3" width="7" height="9" rx="1"/><rect x="14" y="3" width="7" height="5" rx="1"/><rect x="14" y="12" width="7" height="9" rx="1"/><rect x="3" y="16" width="7" height="5" rx="1"/></svg>
          Dashboard
        </a>
        <a class="nav-link" href="profile.php">
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
         Local Network Auth Server</span>
      </div>

      <?php if ($flashSuccess !== ''): ?>
        <div class="alert alert-success alert-dismissible fade show py-2" role="alert">
          <?= htmlspecialchars($flashSuccess) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>
      <?php if ($flash !== ''): ?>
        <div class="alert alert-warning alert-dismissible fade show py-2" role="alert">
          <?= htmlspecialchars($flash) ?>
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
      <?php endif; ?>

      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
          <p class="text-muted small mb-0">Here is your account at a glance.</p>
        </div>
        <a href="profile.php" class="btn btn-outline-primary btn-sm">Edit Profile</a>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
<span class="stat-icon text-white" style="background: var(--gradient);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $stats['total'] ?></div>
                <div class="stat-label">Total Sign-ins</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <span class="stat-icon bg-success text-success">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $stats['ok'] ?></div>
                <div class="stat-label">Successful</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <span class="stat-icon bg-danger text-danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $stats['bad'] ?></div>
                <div class="stat-label">Failed</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
<span class="stat-icon bg-info text-info">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/><path d="M12 7v5l4 2"/></svg>
              </span>
              <div>
                <div class="stat-label" style="font-size: 0.78rem;">Last Sign-in</div>
                <div class="small"><?= $account['last_login_at'] ? htmlspecialchars($account['last_login_at']) : '&mdash;' ?></div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <div class="col-lg-5">
          <div class="card h-100">
            <div class="card-body p-4">
              <div class="d-flex align-items-center gap-3 mb-3">
                <span class="avatar" style="width: 54px; height: 54px; font-size: 1.2rem;"><?= htmlspecialchars($initials) ?></span>
                <div>
                  <h2 class="h5 mb-0"><?= htmlspecialchars($_SESSION['full_name']) ?></h2>
                  <p class="text-muted small mb-0">@<?= htmlspecialchars($_SESSION['username']) ?></p>
                </div>
              </div>
              <hr class="opacity-25">
<?php if (isset($_SESSION['student_profile'])): ?>
                <?php $sp = $_SESSION['student_profile']; ?>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Role</span>
                  <span class="badge bg-success"><?= htmlspecialchars($_SESSION['role_name']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Student ID</span>
                  <span class="fw-semibold"><?= htmlspecialchars($sp['student_id']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Department</span>
                  <span><?= htmlspecialchars($sp['department']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Level</span>
                  <span><?= htmlspecialchars($sp['level']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Email</span>
                  <span><?= htmlspecialchars($sp['email']) ?></span>
                </p>
                <p class="small mb-3">
                  <span class="text-muted d-inline-block" style="width: 110px;">Phone</span>
                  <span><?= htmlspecialchars($sp['phone']) ?></span>
                </p>
              <?php else: ?>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Role</span>
                  <span class="badge bg-success"><?= htmlspecialchars($_SESSION['role_name']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Email</span>
                  <span><?= htmlspecialchars($_SESSION['email']) ?></span>
                </p>
                <p class="mb-2 small">
                  <span class="text-muted d-inline-block" style="width: 110px;">Status</span>
                  <span class="text-success">Active</span>
                </p>
                <p class="small mb-3">
                  <span class="text-muted d-inline-block" style="width: 110px;">Member since</span>
                  <span><?= htmlspecialchars(substr($account['created_at'], 0, 10)) ?></span>
                </p>
              <?php endif; ?>
              <div class="d-flex gap-2">
                <a href="profile.php" class="btn btn-sm btn-primary flex-fill">Edit Profile</a>
                <a href="activity.php" class="btn btn-sm btn-outline-primary flex-fill">View Activity</a>
              </div>
            </div>
          </div>
        </div>

        <div class="col-lg-7">
          <div class="card h-100">
            <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-4">
              <h2 class="h6 mb-0">Recent Sign-in Activity</h2>
              <a href="activity.php" class="small">View all</a>
            </div>
            <div class="card-body pt-0">
              <?php if (empty($recent)): ?>
                <p class="text-muted small mb-0">No sign-in activity yet.</p>
              <?php else: ?>
                <ul class="list-unstyled mb-0">
                  <?php foreach ($recent as $log): ?>
                    <li class="d-flex align-items-center gap-3 py-2 border-bottom border-secondary-subtle">
                      <span class="stat-icon <?= (int) $log['success'] === 1 ? 'bg-success text-success' : 'bg-danger text-danger' ?>" style="width: 34px; height: 34px;">
                        <?php if ((int) $log['success'] === 1): ?>
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
                        <?php else: ?>
                          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        <?php endif; ?>
                      </span>
                      <div class="flex-grow-1 min-w-0">
                        <div class="fw-semibold small">
                          <span class="text-white"><?= htmlspecialchars($log['username_attempted']) ?></span>
                          <span class="text-muted fw-normal">&middot; <?= htmlspecialchars($log['ip_address']) ?></span>
                        </div>
                        <div class="text-muted small"><?= htmlspecialchars($log['logged_at']) ?></div>
                      </div>
                      <span class="badge <?= (int) $log['success'] === 1 ? 'bg-success' : 'bg-danger' ?>">
                        <?= (int) $log['success'] === 1 ? 'Successful' : 'Failed' ?>
                      </span>
                    </li>
                  <?php endforeach; ?>
                </ul>
              <?php endif; ?>
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
