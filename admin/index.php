<?php

$pageTitle = 'Dashboard';
$activeNav = 'dashboard';

require_once __DIR__ . '/partials/header.php';

$pdo = getPDO();
$userCount = (int) $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn();
$activeCount = (int) $pdo->query('SELECT COUNT(*) FROM users WHERE is_active = 1')->fetchColumn();
$roleCount = (int) $pdo->query('SELECT COUNT(*) FROM roles')->fetchColumn();
$logCount = (int) $pdo->query('SELECT COUNT(*) FROM login_logs')->fetchColumn();
$failCount = (int) $pdo->query('SELECT COUNT(*) FROM login_logs WHERE success = 0')->fetchColumn();

$recent = $pdo->query(
    'SELECT l.username_attempted, l.success, l.logged_at, u.username AS user_username
       FROM login_logs l
       LEFT JOIN users u ON u.id = l.user_id
      ORDER BY l.logged_at DESC, l.id DESC
      LIMIT 6'
)->fetchAll();
?>
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">Welcome back, <?= htmlspecialchars($_SESSION['full_name']) ?></h1>
          <p class="text-muted small mb-0">Here is what is happening across your network.</p>
        </div>
        <?php if (hasPermission('logs.view')): ?>
          <a href="logs.php" class="btn btn-outline-primary btn-sm">View all logs</a>
        <?php endif; ?>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <span class="stat-icon text-white" style="background: var(--gradient);">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= $userCount ?></div>
                <div class="stat-label">Total Users</div>
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
                <div class="stat-value"><?= $activeCount ?></div>
                <div class="stat-label">Active Accounts</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <span class="stat-icon bg-info text-info">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= $roleCount ?></div>
                <div class="stat-label">Roles</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-sm-6 col-xl-3">
          <div class="card card-hover h-100">
            <div class="card-body d-flex align-items-center gap-3">
              <span class="stat-icon bg-danger text-danger">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/><path d="M16 13H8"/><path d="M16 17H8"/><path d="M10 9H8"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= $failCount ?>/<?= $logCount ?></div>
                <div class="stat-label">Failed Logins</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row g-4">
        <?php if (hasPermission('logs.view')): ?>
          <div class="col-lg-7">
            <div class="card h-100">
              <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-3 px-4">
                <h5 class="mb-0">Recent Activity</h5>
                <a href="logs.php" class="small">View all</a>
              </div>
              <div class="card-body pt-0">
                <?php if (empty($recent)): ?>
                  <p class="text-muted small mb-0">No authentication activity yet.</p>
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
                          <div class="fw-semibold small"><?= htmlspecialchars($log['user_username'] ? $log['user_username'] : $log['username_attempted']) ?></div>
                          <div class="text-muted small"><?= htmlspecialchars($log['logged_at']) ?></div>
                        </div>
                        <span class="badge <?= (int) $log['success'] === 1 ? 'bg-success' : 'bg-danger' ?>">
                          <?= (int) $log['success'] === 1 ? 'Success' : 'Failed' ?>
                        </span>
                      </li>
                    <?php endforeach; ?>
                  </ul>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <div class="<?= hasPermission('logs.view') ? 'col-lg-5' : 'col-12' ?>">
          <div class="row g-3">
            <?php if (hasPermission('users.view')): ?>
              <div class="col-12">
                <div class="card card-hover h-100">
                  <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon text-white" style="background: var(--gradient);">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </span>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">Manage Users</div>
                      <div class="text-muted small">Create, edit and control account access.</div>
                    </div>
                    <a href="users.php" class="btn btn-sm btn-primary">Open</a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if (hasPermission('roles.manage')): ?>
              <div class="col-12">
                <div class="card card-hover h-100">
                  <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-info text-info">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8.5 4.5v8L12 22l-8.5-5.5v-8L12 2z"/><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                    </span>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">Roles &amp; Permissions</div>
                      <div class="text-muted small">Define roles and what they can do.</div>
                    </div>
                    <a href="roles.php" class="btn btn-sm btn-primary">Open</a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
            <?php if (hasPermission('logs.view')): ?>
              <div class="col-12">
                <div class="card card-hover h-100">
                  <div class="card-body d-flex align-items-center gap-3">
                    <span class="stat-icon bg-danger text-danger">
                      <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><path d="M14 2v6h6"/></svg>
                    </span>
                    <div class="flex-grow-1">
                      <div class="fw-semibold">Authentication Logs</div>
                      <div class="text-muted small">Review sign-in history.</div>
                    </div>
                    <a href="logs.php" class="btn btn-sm btn-primary">Open</a>
                  </div>
                </div>
              </div>
            <?php endif; ?>
          </div>
        </div>
      </div>

<?php require_once __DIR__ . '/partials/footer.php'; ?>