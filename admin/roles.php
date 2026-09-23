<?php

$pageTitle = 'Roles';
$activeNav = 'roles';

require_once __DIR__ . '/partials/header.php';

requirePermission('roles.manage');

$pdo = getPDO();

$errors = array();
$roleForm = array(
    'id' => 0,
    'role_name' => '',
    'description' => '',
    'permissions' => array(),
);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';
    $canAssign = hasPermission('permissions.assign');

    if (($action === 'create' || $action === 'update') && $canAssign) {
        $roleForm['id'] = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $roleForm['role_name'] = trim(isset($_POST['role_name']) ? $_POST['role_name'] : '');
        $roleForm['description'] = trim(isset($_POST['description']) ? $_POST['description'] : '');
        $roleForm['permissions'] = isset($_POST['permissions']) ? array_map('intval', (array) $_POST['permissions']) : array();

        if ($roleForm['role_name'] === '' || strlen($roleForm['role_name']) > 50) {
            $errors[] = 'Role name is required (max 50 characters).';
        }
        if (strlen($roleForm['description']) > 255) {
            $errors[] = 'Description cannot exceed 255 characters.';
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM roles WHERE role_name = :n AND id <> :id');
            $stmt->execute([':n' => $roleForm['role_name'], ':id' => $roleForm['id']]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'A role with that name already exists.';
            }
        }

        if (empty($errors)) {
            try {
                $pdo->beginTransaction();
                $roleId = $roleForm['id'];
                if ($action === 'create') {
                    $stmt = $pdo->prepare('INSERT INTO roles (role_name, description) VALUES (:n, :d)');
                    $stmt->execute([':n' => $roleForm['role_name'], ':d' => $roleForm['description']]);
                    $roleId = (int) $pdo->lastInsertId();
                } else {
                    $stmt = $pdo->prepare('UPDATE roles SET role_name = :n, description = :d WHERE id = :id');
                    $stmt->execute([
                        ':n' => $roleForm['role_name'],
                        ':d' => $roleForm['description'],
                        ':id' => $roleId,
                    ]);
                    $stmt = $pdo->prepare('DELETE FROM role_permissions WHERE role_id = :id');
                    $stmt->execute([':id' => $roleId]);
                }
                $stmt = $pdo->prepare(
                    'INSERT INTO role_permissions (role_id, permission_id) VALUES (:r, :p)'
                );
                foreach ($roleForm['permissions'] as $permId) {
                    $stmt->execute([':r' => $roleId, ':p' => $permId]);
                }
                $pdo->commit();
                $_SESSION['flash_success'] = 'Role "' . $roleForm['role_name'] . '" saved.';
                header('Location: roles.php');
                exit;
            } catch (Exception $e) {
                $pdo->rollBack();
                $errors[] = 'Could not save the role. Please try again.';
            }
        }
    }

    if ($action === 'delete' && $canAssign) {
        $roleId = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        if ($roleId === 1) {
            $_SESSION['flash_error'] = 'The Administrator role cannot be deleted.';
        } else {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE role_id = :id');
            $stmt->execute([':id' => $roleId]);
            $inUse = (int) $stmt->fetchColumn();
            if ($inUse > 0) {
                $_SESSION['flash_error'] = 'Cannot delete this role: it is assigned to ' . $inUse . ' user(s).';
            } else {
                $stmt = $pdo->prepare('DELETE FROM roles WHERE id = :id');
                $stmt->execute([':id' => $roleId]);
                $_SESSION['flash_success'] = 'Role deleted.';
            }
        }
        header('Location: roles.php');
        exit;
    }
}

$roles = $pdo->query(
    'SELECT r.id, r.role_name, r.description,
            (SELECT COUNT(*) FROM users u WHERE u.role_id = r.id) AS user_count,
            (SELECT COUNT(*) FROM role_permissions rp WHERE rp.role_id = r.id) AS perm_count
       FROM roles r
      ORDER BY r.id ASC'
)->fetchAll();

$permissions = $pdo->query(
    'SELECT id, permission_name, description FROM permissions ORDER BY permission_name ASC'
)->fetchAll();

$permIdsByRole = array();
$stmt = $pdo->query('SELECT role_id, permission_id FROM role_permissions');
foreach ($stmt->fetchAll() as $row) {
    $permIdsByRole[(int) $row['role_id']][] = (int) $row['permission_id'];
}
?>
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">Roles &amp; Permissions</h1>
          <p class="text-muted small mb-0">Define roles and the actions each role is allowed to perform.</p>
        </div>
        <?php if (hasPermission('permissions.assign')): ?>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#roleModal">+ New Role</button>
        <?php endif; ?>
      </div>

      <?php if (!empty($errors)): ?>
        <div class="alert alert-danger py-2">
          <ul class="mb-0">
            <?php foreach ($errors as $e): ?>
              <li><?= htmlspecialchars($e) ?></li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endif; ?>

      <div class="row g-4">
        <?php foreach ($roles as $r): ?>
          <div class="col-md-6 col-xl-4">
            <div class="card card-hover h-100">
              <div class="card-body p-4 d-flex flex-column">
                <div class="d-flex justify-content-between align-items-start mb-2">
                  <div class="d-flex align-items-center gap-2">
                    <span class="stat-icon text-white" style="background: var(--gradient);">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2l8.5 4.5v8L12 22l-8.5-5.5v-8L12 2z"/><path d="M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/></svg>
                    </span>
                    <h5 class="card-title mb-0"><?= htmlspecialchars($r['role_name']) ?></h5>
                  </div>
                  <?php if ((int) $r['id'] === 1): ?>
                    <span class="badge bg-success">Built-in</span>
                  <?php endif; ?>
                </div>
                <p class="text-muted small mb-3">
                  <?= $r['description'] !== '' ? htmlspecialchars($r['description']) : 'No description provided.' ?>
                </p>
                <div class="d-flex gap-4 mb-4">
                  <div>
                    <div class="fw-bold"><?= (int) $r['user_count'] ?></div>
                    <div class="text-muted small">Users</div>
                  </div>
                  <div>
                    <div class="fw-bold"><?= (int) $r['perm_count'] ?></div>
                    <div class="text-muted small">Permissions</div>
                  </div>
                </div>
                <?php if (hasPermission('permissions.assign')): ?>
                  <div class="mt-auto">
                    <button type="button" class="btn btn-sm btn-outline-primary me-2 btn-edit-role"
                            data-bs-toggle="modal" data-bs-target="#roleModal"
                            data-id="<?= (int) $r['id'] ?>"
                            data-name="<?= htmlspecialchars($r['role_name']) ?>"
                            data-description="<?= htmlspecialchars($r['description']) ?>"
                            data-pids="<?= implode(',', isset($permIdsByRole[(int) $r['id']]) ? $permIdsByRole[(int) $r['id']] : array()) ?>">
                      Edit
                    </button>
                    <?php if ((int) $r['id'] !== 1): ?>
                      <button type="button" class="btn btn-sm btn-outline-danger btn-delete-role"
                              data-bs-toggle="modal" data-bs-target="#deleteRoleModal"
                              data-id="<?= (int) $r['id'] ?>"
                              data-name="<?= htmlspecialchars($r['role_name']) ?>">
                        Delete
                      </button>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (hasPermission('permissions.assign')): ?>
        <div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="roles.php" method="POST" novalidate>
                <input type="hidden" name="action" id="r_action" value="create">
                <input type="hidden" name="id" id="r_id" value="0">
                <div class="modal-header">
                  <h5 class="modal-title" id="roleModalTitle">New Role</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-12">
                      <label for="r_name" class="form-label">Role Name</label>
                      <input type="text" class="form-control" id="r_name" name="role_name" required>
                    </div>
                    <div class="col-12">
                      <label for="r_description" class="form-label">Description</label>
                      <textarea class="form-control" id="r_description" name="description" rows="2"
                                maxlength="255"></textarea>
                    </div>
                    <div class="col-12">
                      <label class="form-label d-block">Permissions</label>
                      <?php foreach ($permissions as $p): ?>
                        <div class="form-check">
                          <input class="form-check-input role-perm" type="checkbox"
                                 id="perm-<?= (int) $p['id'] ?>" name="permissions[]"
                                 value="<?= (int) $p['id'] ?>">
                          <label class="form-check-label" for="perm-<?= (int) $p['id'] ?>">
                            <code><?= htmlspecialchars($p['permission_name']) ?></code>
                            <span class="text-muted small">— <?= htmlspecialchars($p['description']) ?></span>
                          </label>
                        </div>
                      <?php endforeach; ?>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
              </form>
            </div>
          </div>
        </div>

        <div class="modal fade" id="deleteRoleModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
              <form action="roles.php" method="POST">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" id="dr_id">
                <div class="modal-body text-center pt-4 pb-3">
                  <div class="mb-3">
                    <span class="stat-icon bg-danger text-danger">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h18"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                    </span>
                  </div>
                  <h5 class="modal-title mb-1">Delete Role</h5>
                  <p class="text-muted small mb-0" id="dr_message">This cannot be undone.</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-danger">Delete Role</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>