<?php

$isLoad = isset($_GET['load']);
$perPage = 30;

function getLogs($pdo, $search, $outcome, $offset, $limit)
{
    $where = array();
    $params = array();

    if ($search !== '') {
        $where[] = '(l.username_attempted LIKE :q1 OR COALESCE(u.full_name, \'\') LIKE :q2 OR COALESCE(u.email, \'\') LIKE :q3)';
        $params[':q1'] = '%' . $search . '%';
        $params[':q2'] = '%' . $search . '%';
        $params[':q3'] = '%' . $search . '%';
    }
    if ($outcome === 'success') {
        $where[] = 'l.success = 1';
    } elseif ($outcome === 'failed') {
        $where[] = 'l.success = 0';
    }

    $whereSql = empty($where) ? '' : ' WHERE ' . implode(' AND ', $where);

    $totalStmt = $pdo->prepare('SELECT COUNT(*) FROM login_logs l LEFT JOIN users u ON u.id = l.user_id' . $whereSql);
    $totalStmt->execute($params);
    $total = (int) $totalStmt->fetchColumn();

    $stmt = $pdo->prepare(
        'SELECT l.id, l.username_attempted, l.ip_address, l.user_agent, l.success, l.logged_at,
                u.username AS user_username, u.full_name, u.email
           FROM login_logs l
           LEFT JOIN users u ON u.id = l.user_id'
        . $whereSql .
        ' ORDER BY l.logged_at DESC, l.id DESC
          LIMIT :limit OFFSET :offset'
    );
    foreach ($params as $key => $val) {
        $stmt->bindValue($key, $val);
    }
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();

    return array('total' => $total, 'rows' => $stmt->fetchAll());
}

function logRowHtml($log)
{
    $success = (int) $log['success'] === 1;
    $badge = $success ? 'bg-success' : 'bg-danger';
    $label = $success ? 'Success' : 'Failed';

    $assoc = $log['user_username']
        ? htmlspecialchars($log['user_username']) . ' <span class="text-muted small">(' . htmlspecialchars($log['full_name']) . ')</span>'
        : '<span class="text-muted">&mdash;</span>';

    return '<tr>'
        . '<td class="text-muted small"><div class="fw-semibold" style="color: var(--text);">' . htmlspecialchars($log['logged_at']) . '</div></td>'
        . '<td>' . htmlspecialchars($log['username_attempted']) . '</td>'
        . '<td>' . $assoc . '</td>'
        . '<td class="text-muted small">' . htmlspecialchars($log['ip_address']) . '</td>'
        . '<td class="text-muted small" style="max-width: 240px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">' . htmlspecialchars($log['user_agent']) . '</td>'
        . '<td><span class="badge ' . $badge . '">' . $label . '</span></td>'
        . '</tr>';
}

if ($isLoad) {
    require_once __DIR__ . '/../includes/auth_check.php';
    require_once __DIR__ . '/../includes/permission_check.php';
    require_once __DIR__ . '/../config/database.php';

    requirePermission('logs.view');

    $pdo = getPDO();
    $search = trim(isset($_GET['q']) ? $_GET['q'] : '');
    $outcome = isset($_GET['outcome']) ? $_GET['outcome'] : '';
    $offset = isset($_GET['offset']) ? max(0, (int) $_GET['offset']) : 0;

    $data = getLogs($pdo, $search, $outcome, $offset, $perPage);

    $html = '';
    foreach ($data['rows'] as $log) {
        $html .= logRowHtml($log);
    }

    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(array(
        'html' => $html,
        'count' => count($data['rows']),
        'remaining' => max(0, $data['total'] - ($offset + count($data['rows']))),
    ));
    exit;
}

$pageTitle = 'Authentication Logs';
$activeNav = 'logs';

require_once __DIR__ . '/partials/header.php';

requirePermission('logs.view');

$pdo = getPDO();

$search = trim(isset($_GET['q']) ? $_GET['q'] : '');
$outcome = isset($_GET['outcome']) ? $_GET['outcome'] : '';

$data = getLogs($pdo, $search, $outcome, 0, $perPage);
$total = (int) $data['total'];
$logs = $data['rows'];

$summary = $pdo->query('SELECT SUM(success = 1) AS ok, SUM(success = 0) AS bad, COUNT(*) AS total FROM login_logs')->fetch();
?>
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">Authentication Logs</h1>
          <p class="text-muted small mb-0">Track every successful and failed sign-in attempt.</p>
        </div>
      </div>

      <div class="row g-3 mb-4">
        <div class="col-md-4">
          <div class="card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
              <span class="stat-icon bg-success text-success">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><path d="M22 4L12 14.01l-3-3"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $summary['ok'] ?></div>
                <div class="stat-label">Successful</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
              <span class="stat-icon bg-danger text-danger">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $summary['bad'] ?></div>
                <div class="stat-label">Failed</div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card">
            <div class="card-body d-flex align-items-center gap-3 py-3">
              <span class="stat-icon text-white" style="background: var(--gradient);">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4"/><path d="M8 2v4"/><path d="M3 10h18"/></svg>
              </span>
              <div>
                <div class="stat-value"><?= (int) $summary['total'] ?></div>
                <div class="stat-label">Total Events</div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="card mb-4">
        <div class="card-body py-3">
          <form action="logs.php" method="GET" class="row g-2 align-items-end">
            <div class="col-md-6 col-lg-5">
              <label for="q" class="form-label">Search</label>
              <input type="text" class="form-control" id="q" name="q"
                     placeholder="Username, name or email"
                     value="<?= htmlspecialchars($search) ?>">
            </div>
            <div class="col-md-3">
              <label for="outcome" class="form-label">Outcome</label>
              <select class="form-select" id="outcome" name="outcome">
                <option value="">All outcomes</option>
                <option value="success" <?= $outcome === 'success' ? 'selected' : '' ?>>Successful</option>
                <option value="failed" <?= $outcome === 'failed' ? 'selected' : '' ?>>Failed</option>
              </select>
            </div>
            <div class="col-12 col-md-auto">
              <button type="submit" class="btn btn-primary w-100">Apply Filter</button>
            </div>
            <div class="col-12 col-md-auto">
              <a href="logs.php" class="btn btn-outline-secondary w-100">Reset</a>
            </div>
          </form>
        </div>
      </div>

      <div class="card">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>Time</th>
                <th>Username</th>
                <th>Associated User</th>
                <th>IP Address</th>
                <th>User Agent</th>
                <th>Outcome</th>
              </tr>
            </thead>
            <tbody id="logTbody">
              <?php if (empty($logs)): ?>
                <tr id="noRowsRow">
                  <td colspan="6" class="text-center text-muted py-4">No log entries match your filters.</td>
                </tr>
              <?php else: ?>
                <?php foreach ($logs as $log): ?>
                  <?= logRowHtml($log) ?>
                <?php endforeach; ?>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

      <?php if ($total > count($logs)): ?>
        <div class="text-center mt-4">
          <button type="button" class="btn btn-outline-primary" id="loadMoreBtn" data-loaded="<?= count($logs) ?>">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="me-1"><path d="M12 5v14"/><path d="M19 12l-7 7-7-7"/></svg>
            Load more (<span id="remainingCount"><?= $total - count($logs) ?></span> remaining)
          </button>
        </div>
      <?php endif; ?>

      <script>
        (function () {
          var btn = document.getElementById('loadMoreBtn');
          if (!btn) return;
          var loaded = parseInt(btn.getAttribute('data-loaded'), 10) || 0;
          var busy = false;

          btn.addEventListener('click', function () {
            if (busy) return;
            busy = true;
            btn.disabled = true;

            var url = new URL(window.location.href);
            url.searchParams.set('load', '1');
            url.searchParams.set('offset', loaded);

            fetch(url.toString(), { headers: { 'X-Requested-With': 'loadmore' } })
              .then(function (r) {
                if (!r.ok) throw new Error('Bad response');
                return r.json();
              })
              .then(function (data) {
                if (data.html) {
                  document.getElementById('logTbody').insertAdjacentHTML('beforeend', data.html);
                  var noRows = document.getElementById('noRowsRow');
                  if (noRows) noRows.remove();
                }
                loaded += data.count || 0;
                btn.setAttribute('data-loaded', loaded);
                var rem = document.getElementById('remainingCount');
                if (rem) rem.textContent = data.remaining;
                if (data.remaining <= 0) {
                  if (btn.parentNode) btn.parentNode.removeChild(btn);
                  return;
                }
              })
              .catch(function () {})
              .then(function () {
                busy = false;
                if (btn.parentNode) btn.disabled = false;
              });
          });
        })();
      </script>

<?php require_once __DIR__ . '/partials/footer.php'; ?>