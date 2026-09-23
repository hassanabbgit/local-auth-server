<?php

require_once __DIR__ . '/../includes/auth_check.php';
require_once __DIR__ . '/../includes/permission_check.php';
require_once __DIR__ . '/../config/database.php';

requirePermission('profile.edit');

$pdo = getPDO();
$userId = (int) $_SESSION['user_id'];

$initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1));
$spacePos = strpos(trim($_SESSION['full_name']), ' ');
if ($spacePos !== false) {
    $initials = strtoupper(substr(trim($_SESSION['full_name']), 0, 1) . substr(trim($_SESSION['full_name']), $spacePos + 1, 1));
}

$perPage = 20;
$page = max(1, (int) (isset($_GET['p']) ? $_GET['p'] : 1));
$outcome = isset($_GET['outcome']) ? $_GET['outcome'] : '';
$ip = trim(isset($_GET['ip']) ? $_GET['ip'] : '');

$where = 'user_id = :id';
$params = array(':id' => $userId);

if ($outcome === 'success') {
    $where .= ' AND success = 1';
} elseif ($outcome === 'failed') {
    $where .= ' AND success = 0';
}
if ($ip !== '') {
    $where .= ' AND ip_address LIKE :ip';
    $params[':ip'] = '%' . $ip . '%';
}

$totalStmt = $pdo->prepare('SELECT COUNT(*) FROM login_logs WHERE ' . $where);
$totalStmt->execute($params);
$total = (int) $totalStmt->fetchColumn();
$totalPages = max(1, (int) ceil($total / $perPage));
if ($page > $totalPages) {
    $page = $totalPages;
}
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare(
    'SELECT l.username_attempted, l.ip_address, l.user_agent, l.success, l.logged_at
       FROM login_logs l
      WHERE ' . $where . '
      ORDER BY l.logged_at DESC, l.id DESC
      LIMIT :limit OFFSET :offset'
);
foreach ($params as $key => $val) {
    $stmt->bindValue($key, $val);
}
$stmt->bindValue(':limit', $perPage, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$logs = $stmt->fetchAll();

$qs = array();
if ($outcome !== '') {
    $qs[] = 'outcome=' . urlencode($outcome);
}
if ($ip !== '') {
    $qs[] = 'ip=' . urlencode($ip);
}
$qs = implode('&', $qs);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Activity | Local Auth Server</title>
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
        <a class="nav-link" href="profile.php">
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
          My Profile
        </a>
        <a class="nav-link active" href="activity.php">
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

      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">My Activity</h1>
          <p class="text-muted small mb-0">Every sign-in attempt on your account, successful or not.</p>
        </div>
        <a href="index.php" class="btn btn-outline-primary btn-sm">Back to Dashboard</a>
      </div>

      <div class="card mb-4">
        <div class="card-body py-3">
          <form action="activity.php" method="GET" class="row g-2 align-items-end">
            <div class="col-md-5">
              <label for="ip" class="form-label">IP Address</label>
              <input type="text" class="form-control" id="ip" name="ip"
                     placeholder="e.g. 192.168.137.20" value="<?= htmlspecialchars($ip) ?>">
            </div>
            <div class="col-md-3">
              <label for="outcome" class="form-label">Outcome</label>
              <select class="form-select" id="outcome" name="outcome">
                <option value="">All outcomes</option>
                <option value="success" <?= $outcome === 'success' ? 'selected' : '' ?>>Successful</option>
                <option value="failed" <?= $outcome === 'failed' ? 'selected' : '' ?>>Failed</option>
              </select>
            </div>
            <div class="col-6 col-md-auto">
              <button type="submit" class="btn btn-primary w-100">Apply</button>
            </div>
            <div class="col-6 col-md-auto">
              <a href="activity.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card d-none d-lg-block">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>User</th>
                <th>IP Address</th>
                <th>Time</th>
                <th>User Agent</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($logs as $log): ?>
                <tr>
                  <td>
                    <div class="fw-semibold"><?= htmlspecialchars($log['username_attempted']) ?></div>
                  </td>
                  <td class="text-muted small"><?= htmlspecialchars($log['ip_address']) ?></td>
                  <td class="text-muted small"><?= htmlspecialchars($log['logged_at']) ?></td>
                  <td class="text-muted small" style="max-width: 260px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                    <?= htmlspecialchars($log['user_agent']) ?>
                  </td>
                  <td>
                    <?php if ((int) $log['success'] === 1): ?>
                      <span class="badge bg-success">Successful</span>
                    <?php else: ?>
                      <span class="badge bg-danger">Failed</span>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
              <?php if (empty($logs)): ?>
                <tr>
                  <td colspan="5" class="text-center text-muted py-4">No activity found.</td>
                </tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="d-lg-none">
        <?php foreach ($logs as $log): ?>
          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-start mb-2">
                <span class="fw-semibold"><?= htmlspecialchars($log['username_attempted']) ?></span>
                <?php if ((int) $log['success'] === 1): ?>
                  <span class="badge bg-success">Successful</span>
                <?php else: ?>
                  <span class="badge bg-danger">Failed</span>
                <?php endif; ?>
              </div>
              <div class="small text-muted mb-1">
                IP: <strong style="color: var(--text);"><?= htmlspecialchars($log['ip_address']) ?></strong>
              </div>
              <div class="small text-muted"><?= htmlspecialchars($log['logged_at']) ?></div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($logs)): ?>
          <div class="card">
            <div class="card-body text-center text-muted py-4">No activity found.</div>
          </div>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <nav class="mt-4">
          <ul class="pagination pagination-sm justify-content-center mb-0">
            <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
              <a class="page-link" href="activity.php?p=<?= max(1, $page - 1) ?><?= $qs !== '' ? '&' . $qs : '' ?>">Prev</a>
            </li>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
              <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                <a class="page-link" href="activity.php?p=<?= $i ?><?= $qs !== '' ? '&' . $qs : '' ?>"><?= $i ?></a>
              </li>
            <?php endfor; ?>
            <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
              <a class="page-link" href="activity.php?p=<?= min($totalPages, $page + 1) ?><?= $qs !== '' ? '&' . $qs : '' ?>">Next</a>
            </li>
          </ul>
        </nav>
      <?php endif; ?>
    </main>
  </div>

  <?php require_once __DIR__ . '/../includes/logout_modal.php'; ?>
<script src="../assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="../assets/js/script.js"></script>
</body>
</html>
