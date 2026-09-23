<?php

$pageTitle = 'Users';
$activeNav = 'users';

require_once __DIR__ . '/partials/header.php';

requirePermission('users.view');

$pdo = getPDO();

$studentRoleId = 0;
$studentRoleStmt = $pdo->query("SELECT id FROM roles WHERE role_name = 'Student' LIMIT 1");
$srId = $studentRoleStmt->fetchColumn();
$studentRoleId = $srId === false ? 0 : (int) $srId;

$errors = array();
$createData = array(
    'username' => '',
    'full_name' => '',
    'email' => '',
    'role_id' => 0,
    'is_active' => 1,
);

function validateUserInput($username, $fullName, $email, $roleId, $password)
{
    $errors = array();
    if ($username === '' || !preg_match('/^[a-zA-Z0-9_.-]{3,50}$/', $username)) {
        $errors[] = 'Username must be 3-50 characters using letters, numbers, dots, dashes or underscores.';
    }
    if ($fullName === '' || strlen($fullName) > 100) {
        $errors[] = 'Full name is required (max 100 characters).';
    }
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'A valid email address is required.';
    }
    if ($password !== '' && strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters.';
    }
    if ($roleId <= 0) {
        $errors[] = 'A role must be selected.';
    }
    return $errors;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : '';

    if ($action === 'create' && hasPermission('users.create')) {
        $createData['username'] = trim(isset($_POST['username']) ? $_POST['username'] : '');
        $createData['full_name'] = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
        $createData['email'] = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $createData['role_id'] = (int) (isset($_POST['role_id']) ? $_POST['role_id'] : 0);
        $createData['is_active'] = isset($_POST['is_active']) ? 1 : 0;
        $password = isset($_POST['password']) ? $_POST['password'] : '';

        $studentData = array(
            'student_id' => trim(isset($_POST['student_id']) ? $_POST['student_id'] : ''),
            'department' => trim(isset($_POST['department']) ? $_POST['department'] : ''),
            'level' => trim(isset($_POST['level']) ? $_POST['level'] : ''),
            'phone' => trim(isset($_POST['phone']) ? $_POST['phone'] : ''),
        );

        $errors = validateUserInput($createData['username'], $createData['full_name'], $createData['email'], $createData['role_id'], $password);

        if ($createData['role_id'] === $studentRoleId) {
            if ($studentData['student_id'] === '') {
                $errors[] = 'A student ID is required for student accounts.';
            }
            if ($studentData['department'] === '') {
                $errors[] = 'A department is required for student accounts.';
            }
            if ($studentData['level'] === '') {
                $errors[] = 'A level is required for student accounts.';
            }
        }

        if (empty($errors) && $studentData['student_id'] !== '') {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE student_id = :s');
            $stmt->execute([':s' => $studentData['student_id']]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'That student ID is already in use.';
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare('SELECT COUNT(*) FROM users WHERE username = :u OR email = :e');
            $stmt->execute([':u' => $createData['username'], ':e' => $createData['email']]);
            if ((int) $stmt->fetchColumn() > 0) {
                $errors[] = 'That username or email is already in use.';
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'INSERT INTO users (username, email, full_name, password_hash, role_id, is_active)
                 VALUES (:u, :e, :f, :p, :r, :a)'
            );
            $stmt->execute([
                ':u' => $createData['username'],
                ':e' => $createData['email'],
                ':f' => $createData['full_name'],
                ':p' => password_hash($password, PASSWORD_BCRYPT),
                ':r' => $createData['role_id'],
                ':a' => $createData['is_active'],
            ]);
            $newUserId = (int) $pdo->lastInsertId();

            if ($createData['role_id'] === $studentRoleId) {
                $nameParts = explode(' ', $createData['full_name'], 2);
                $stuStmt = $pdo->prepare(
                    'INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
                     VALUES (:uid, :sid, :fn, :ln, :em, :dp, :lv, :ph)'
                );
                $stuStmt->execute([
                    ':uid' => $newUserId,
                    ':sid' => $studentData['student_id'],
                    ':fn' => $nameParts[0],
                    ':ln' => isset($nameParts[1]) ? $nameParts[1] : '',
                    ':em' => $createData['email'],
                    ':dp' => $studentData['department'],
                    ':lv' => $studentData['level'],
                    ':ph' => $studentData['phone'],
                ]);
            }

            $_SESSION['flash_success'] = 'User "' . $createData['username'] . '" created.';
            header('Location: users.php');
            exit;
        }
    }

    if ($action === 'update' && hasPermission('users.edit')) {
        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        $stmt = $pdo->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        if ($user === false) {
            $_SESSION['flash_error'] = 'User not found.';
            header('Location: users.php');
            exit;
        }

        $canManage = hasPermission('users.manage');
        $fullName = trim(isset($_POST['full_name']) ? $_POST['full_name'] : '');
        $email = trim(isset($_POST['email']) ? $_POST['email'] : '');
        $roleId = $canManage ? (int) (isset($_POST['role_id']) ? $_POST['role_id'] : 0) : (int) $user['role_id'];
        $isActive = $canManage ? (isset($_POST['is_active']) ? 1 : 0) : (int) $user['is_active'];
        $password = $canManage ? (isset($_POST['password']) ? $_POST['password'] : '') : '';

        $studentData = array(
            'student_id' => trim(isset($_POST['student_id']) ? $_POST['student_id'] : ''),
            'department' => trim(isset($_POST['department']) ? $_POST['department'] : ''),
            'level' => trim(isset($_POST['level']) ? $_POST['level'] : ''),
            'phone' => trim(isset($_POST['phone']) ? $_POST['phone'] : ''),
        );

        $errors = validateUserInput($user['username'], $fullName, $email, $roleId, $password);

        if ($roleId === $studentRoleId) {
            if ($studentData['student_id'] === '') {
                $errors[] = 'A student ID is required for student accounts.';
            }
            if ($studentData['department'] === '') {
                $errors[] = 'A department is required for student accounts.';
            }
            if ($studentData['level'] === '') {
                $errors[] = 'A level is required for student accounts.';
            }
        }

        if (empty($errors) && $studentData['student_id'] !== '') {
            $stmt3 = $pdo->prepare('SELECT COUNT(*) FROM students WHERE student_id = :s AND user_id <> :uid');
            $stmt3->execute([':s' => $studentData['student_id'], ':uid' => $id]);
            if ((int) $stmt3->fetchColumn() > 0) {
                $errors[] = 'Another student already uses that student ID.';
            }
        }

        if (empty($errors)) {
            $stmt2 = $pdo->prepare('SELECT COUNT(*) FROM users WHERE email = :e AND id <> :id');
            $stmt2->execute([':e' => $email, ':id' => $id]);
            if ((int) $stmt2->fetchColumn() > 0) {
                $errors[] = 'Another account already uses that email address.';
            }
        }

        if (empty($errors)) {
            $stmt = $pdo->prepare(
                'UPDATE users SET full_name = :f, email = :e, role_id = :r, is_active = :a WHERE id = :id'
            );
            $stmt->execute([
                ':f' => $fullName,
                ':e' => $email,
                ':r' => $roleId,
                ':a' => $isActive,
                ':id' => $id,
            ]);

            if ($roleId === $studentRoleId) {
                $nameParts = explode(' ', $fullName, 2);
                $firstName = $nameParts[0];
                $lastName = isset($nameParts[1]) ? $nameParts[1] : '';
                $countStmt = $pdo->prepare('SELECT COUNT(*) FROM students WHERE user_id = :uid');
                $countStmt->execute([':uid' => $id]);
                $hasStudent = (int) $countStmt->fetchColumn() > 0;
                if ($hasStudent) {
                    $stuStmt = $pdo->prepare(
                        'UPDATE students SET student_id = :sid, first_name = :fn, last_name = :ln,
                                email = :em, department = :dp, level = :lv, phone = :ph
                          WHERE user_id = :uid'
                    );
                } else {
                    $stuStmt = $pdo->prepare(
                        'INSERT INTO students (user_id, student_id, first_name, last_name, email, department, level, phone)
                         VALUES (:uid, :sid, :fn, :ln, :em, :dp, :lv, :ph)'
                    );
                }
                $stuStmt->execute([
                    ':uid' => $id,
                    ':sid' => $studentData['student_id'],
                    ':fn' => $firstName,
                    ':ln' => $lastName,
                    ':em' => $email,
                    ':dp' => $studentData['department'],
                    ':lv' => $studentData['level'],
                    ':ph' => $studentData['phone'],
                ]);
            } else {
                $delStmt = $pdo->prepare('DELETE FROM students WHERE user_id = :uid');
                $delStmt->execute([':uid' => $id]);
            }

            if ($password !== '') {
                $stmt = $pdo->prepare('UPDATE users SET password_hash = :p WHERE id = :id');
                $stmt->execute([':p' => password_hash($password, PASSWORD_BCRYPT), ':id' => $id]);
            }
            $_SESSION['flash_success'] = 'User "' . $user['username'] . '" updated.';
            header('Location: users.php');
            exit;
        }
    }

    if ($action === 'toggle' && hasPermission('users.manage')) {
        $id = (int) (isset($_POST['id']) ? $_POST['id'] : 0);
        if ($id === (int) $_SESSION['user_id']) {
            $_SESSION['flash_error'] = 'You cannot deactivate your own account.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET is_active = 1 - is_active WHERE id = :id');
            $stmt->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'User status updated.';
        }
        header('Location: users.php');
        exit;
    }
}

$students = $pdo->query(
    'SELECT s.user_id, s.student_id, s.department, s.level, s.phone
       FROM students s'
)->fetchAll();
$studentById = array();
foreach ($students as $s) {
    $studentById[(int) $s['user_id']] = $s;
}

$users = $pdo->query(
    'SELECT u.id, u.username, u.full_name, u.email, u.is_active, u.last_login_at,
            r.role_name, r.id AS role_id
       FROM users u
       JOIN roles r ON r.id = u.role_id
      ORDER BY u.id ASC'
)->fetchAll();

$roles = $pdo->query('SELECT id, role_name FROM roles ORDER BY role_name ASC')->fetchAll();
?>
      <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-4">
        <div>
          <h1 class="h3 mb-0">Users</h1>
          <p class="text-muted small mb-0">Manage accounts, roles and access status.</p>
        </div>
        <?php if (hasPermission('users.create')): ?>
          <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createUserModal">+ New User</button>
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

      <div class="card d-none d-lg-block">
        <div class="table-responsive">
          <table class="table table-hover align-middle mb-0">
            <thead>
              <tr>
                <th>User</th>
                <th>Email</th>
                <th>Role</th>
                <th>Dept / Level</th>
                <th>Status</th>
                <th>Last Login</th>
                <th class="text-end">Actions</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($users as $u): ?>
                <?php $stu = isset($studentById[(int) $u['id']]) ? $studentById[(int) $u['id']] : null; ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-2">
                      <span class="avatar avatar-sm text-white">
                        <?= htmlspecialchars(strtoupper(substr($u['full_name'], 0, 1))) ?>
                      </span>
                      <div>
                        <div class="fw-semibold"><?= htmlspecialchars($u['full_name']) ?></div>
                        <div class="text-muted small">@<?= htmlspecialchars($u['username']) ?></div>
                      </div>
                    </div>
                  </td>
                  <td class="text-muted"><?= htmlspecialchars($u['email']) ?></td>
                  <td><?= htmlspecialchars($u['role_name']) ?></td>
                  <td class="small">
                    <?php if ($stu): ?>
                      <div><?= htmlspecialchars($stu['department']) ?></div>
                      <div class="text-muted"><?= htmlspecialchars($stu['student_id']) ?> &middot; <?= htmlspecialchars($stu['level']) ?></div>
                    <?php else: ?>
                      <span class="text-muted">&mdash;</span>
                    <?php endif; ?>
                  </td>
                  <td>
                    <?php if ((int) $u['is_active'] === 1): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                  </td>
                  <td class="text-muted small">
                    <?= $u['last_login_at'] ? htmlspecialchars($u['last_login_at']) : 'Never' ?>
                  </td>
                  <td class="text-end">
                    <?php if (hasPermission('users.edit')): ?>
                      <button type="button" class="btn btn-sm btn-outline-primary btn-edit-user"
                              data-bs-toggle="modal" data-bs-target="#editUserModal"
                              data-id="<?= (int) $u['id'] ?>"
                              data-fullname="<?= htmlspecialchars($u['full_name']) ?>"
                              data-email="<?= htmlspecialchars($u['email']) ?>"
                              data-role="<?= (int) $u['role_id'] ?>"
                              data-active="<?= (int) $u['is_active'] ?>"
                              data-can-manage="<?= hasPermission('users.manage') ? 1 : 0 ?>"
                              data-is-student="<?= $stu ? 1 : 0 ?>"
                              data-student-id="<?= $stu ? htmlspecialchars($stu['student_id']) : '' ?>"
                              data-department="<?= $stu ? htmlspecialchars($stu['department']) : '' ?>"
                              data-level="<?= $stu ? htmlspecialchars($stu['level']) : '' ?>"
                              data-phone="<?= $stu ? htmlspecialchars($stu['phone']) : '' ?>">Edit</button>
                    <?php endif; ?>
                    <?php if (hasPermission('users.manage') && (int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                      <button type="button" class="btn btn-sm btn-outline-<?= (int) $u['is_active'] === 1 ? 'warning' : 'success' ?> btn-toggle-user"
                              data-bs-toggle="modal" data-bs-target="#toggleUserModal"
                              data-id="<?= (int) $u['id'] ?>"
                              data-name="<?= htmlspecialchars($u['full_name']) ?>"
                              data-will="<?= (int) $u['is_active'] === 1 ? 'deactivate' : 'activate' ?>">
                        <?= (int) $u['is_active'] === 1 ? 'Deactivate' : 'Activate' ?>
                      </button>
                    <?php endif; ?>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>

      <div class="d-lg-none">
        <?php foreach ($users as $u): ?>
          <?php $isActive = (int) $u['is_active'] === 1; ?>
          <?php $stu = isset($studentById[(int) $u['id']]) ? $studentById[(int) $u['id']] : null; ?>
          <div class="card mb-3">
            <div class="card-body">
              <div class="d-flex align-items-start gap-3">
                <span class="avatar text-white" style="width: 42px; height: 42px;">
                  <?= htmlspecialchars(strtoupper(substr($u['full_name'], 0, 1))) ?>
                </span>
                <div class="flex-grow-1 min-w-0">
                  <div class="fw-semibold"><?= htmlspecialchars($u['full_name']) ?></div>
                  <div class="text-muted small">@<?= htmlspecialchars($u['username']) ?></div>
                  <div class="mt-2">
                    <?php if ($isActive): ?>
                      <span class="badge bg-success">Active</span>
                    <?php else: ?>
                      <span class="badge bg-secondary">Inactive</span>
                    <?php endif; ?>
                    <span class="badge bg-info"><?= htmlspecialchars($u['role_name']) ?></span>
                  </div>
                </div>
              </div>
              <hr class="my-3 opacity-25">
              <?php if ($stu): ?>
                <div class="small text-muted mb-1">
                  <span style="color: var(--text);"><?= htmlspecialchars($stu['department']) ?></span>
                  &middot; <?= htmlspecialchars($stu['student_id']) ?> &middot; <?= htmlspecialchars($stu['level']) ?>
                </div>
              <?php endif; ?>
              <div class="small text-muted mb-2"><?= htmlspecialchars($u['email']) ?></div>
              <div class="small text-muted mb-3">
                Last login:
                <strong style="color: var(--text);"><?= $u['last_login_at'] ? htmlspecialchars($u['last_login_at']) : 'Never' ?></strong>
              </div>
              <div class="d-flex gap-2">
                <?php if (hasPermission('users.edit')): ?>
                  <button type="button" class="btn btn-sm btn-outline-primary btn-edit-user flex-fill"
                          data-bs-toggle="modal" data-bs-target="#editUserModal"
                          data-id="<?= (int) $u['id'] ?>"
                          data-fullname="<?= htmlspecialchars($u['full_name']) ?>"
                          data-email="<?= htmlspecialchars($u['email']) ?>"
                          data-role="<?= (int) $u['role_id'] ?>"
                          data-active="<?= (int) $u['is_active'] ?>"
                          data-can-manage="<?= hasPermission('users.manage') ? 1 : 0 ?>"
                          data-is-student="<?= $stu ? 1 : 0 ?>"
                          data-student-id="<?= $stu ? htmlspecialchars($stu['student_id']) : '' ?>"
                          data-department="<?= $stu ? htmlspecialchars($stu['department']) : '' ?>"
                          data-level="<?= $stu ? htmlspecialchars($stu['level']) : '' ?>"
                          data-phone="<?= $stu ? htmlspecialchars($stu['phone']) : '' ?>">Edit</button>
                <?php endif; ?>
                <?php if (hasPermission('users.manage') && (int) $u['id'] !== (int) $_SESSION['user_id']): ?>
                  <button type="button" class="btn btn-sm btn-outline-<?= $isActive ? 'warning' : 'success' ?> btn-toggle-user flex-fill"
                          data-bs-toggle="modal" data-bs-target="#toggleUserModal"
                          data-id="<?= (int) $u['id'] ?>"
                          data-name="<?= htmlspecialchars($u['full_name']) ?>"
                          data-will="<?= $isActive ? 'deactivate' : 'activate' ?>">
                    <?= $isActive ? 'Deactivate' : 'Activate' ?>
                  </button>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>

      <?php if (hasPermission('users.create')): ?>
        <div class="modal fade" id="createUserModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="users.php" method="POST" novalidate>
                <input type="hidden" name="action" value="create">
                <div class="modal-header">
                  <h5 class="modal-title">New User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="row g-3">
                    <div class="col-md-6">
                      <label for="c_username" class="form-label">Username</label>
                      <input type="text" class="form-control" id="c_username" name="username"
                             value="<?= htmlspecialchars($createData['username']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="c_full_name" class="form-label">Full Name</label>
                      <input type="text" class="form-control" id="c_full_name" name="full_name"
                             value="<?= htmlspecialchars($createData['full_name']) ?>" required>
                    </div>
                    <div class="col-12">
                      <label for="c_email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="c_email" name="email"
                             value="<?= htmlspecialchars($createData['email']) ?>" required>
                    </div>
                    <div class="col-md-6">
                      <label for="c_role_id" class="form-label">Role</label>
                      <select class="form-select" id="c_role_id" name="role_id" data-student-role="<?= $studentRoleId ?>" required>
                        <option value="">Select a role</option>
                        <?php foreach ($roles as $r): ?>
                          <option value="<?= (int) $r['id'] ?>" <?= $createData['role_id'] === (int) $r['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($r['role_name']) ?>
                          </option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-md-6">
                      <label for="c_password" class="form-label">Password</label>
                      <input type="password" class="form-control" id="c_password" name="password" required>
                      <div class="form-text">At least 8 characters.</div>
                    </div>
                    <div class="col-12 d-none" id="c_student_fields">
                      <hr class="opacity-25 my-1">
                      <div class="fw-semibold small mb-2">Student Details</div>
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label for="c_student_id" class="form-label">Student ID</label>
                          <input type="text" class="form-control" id="c_student_id" name="student_id"
                                 placeholder="e.g. CSC/2023/0042"
                                 value="<?= htmlspecialchars(isset($_POST['student_id']) ? $_POST['student_id'] : '') ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="c_department" class="form-label">Department</label>
                          <input type="text" class="form-control" id="c_department" name="department"
                                 placeholder="e.g. Computer Science"
                                 value="<?= htmlspecialchars(isset($_POST['department']) ? $_POST['department'] : '') ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="c_level" class="form-label">Level</label>
                          <input type="text" class="form-control" id="c_level" name="level"
                                 placeholder="e.g. 300 Level"
                                 value="<?= htmlspecialchars(isset($_POST['level']) ? $_POST['level'] : '') ?>">
                        </div>
                        <div class="col-md-6">
                          <label for="c_phone" class="form-label">Phone</label>
                          <input type="text" class="form-control" id="c_phone" name="phone"
                                 placeholder="e.g. +234 803 000 0000"
                                 value="<?= htmlspecialchars(isset($_POST['phone']) ? $_POST['phone'] : '') ?>">
                        </div>
                      </div>
                    </div>
                    <div class="col-12">
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="c_is_active" name="is_active" value="1"
                               checked>
                        <label class="form-check-label" for="c_is_active">Account active</label>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Create User</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (hasPermission('users.edit')): ?>
        <div class="modal fade" id="editUserModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <form action="users.php" method="POST" novalidate>
                <input type="hidden" name="action" value="update">
                <input type="hidden" name="id" id="e_id">
                <div class="modal-header">
                  <h5 class="modal-title">Edit User</h5>
                  <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                  <div class="alert alert-danger py-2 d-none" id="e_error"></div>
                  <div class="row g-3">
                    <div class="col-12">
                      <label class="form-label">Username</label>
                      <input type="text" class="form-control" id="e_username" disabled>
                    </div>
                    <div class="col-md-6">
                      <label for="e_full_name" class="form-label">Full Name</label>
                      <input type="text" class="form-control" id="e_full_name" name="full_name" required>
                    </div>
                    <div class="col-md-6">
                      <label for="e_email" class="form-label">Email</label>
                      <input type="email" class="form-control" id="e_email" name="email" required>
                    </div>
                    <div class="col-md-6 d-none" id="e_role_wrap">
                      <label for="e_role_id" class="form-label">Role</label>
                      <select class="form-select" id="e_role_id" name="role_id" data-student-role="<?= $studentRoleId ?>">
                        <?php foreach ($roles as $r): ?>
                          <option value="<?= (int) $r['id'] ?>"><?= htmlspecialchars($r['role_name']) ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="col-12 d-none" id="e_student_fields">
                      <hr class="opacity-25 my-1">
                      <div class="fw-semibold small mb-2">Student Details</div>
                      <div class="row g-3">
                        <div class="col-md-6">
                          <label for="e_student_id" class="form-label">Student ID</label>
                          <input type="text" class="form-control" id="e_student_id" name="student_id"
                                 placeholder="e.g. CSC/2023/0042">
                        </div>
                        <div class="col-md-6">
                          <label for="e_department" class="form-label">Department</label>
                          <input type="text" class="form-control" id="e_department" name="department">
                        </div>
                        <div class="col-md-6">
                          <label for="e_level" class="form-label">Level</label>
                          <input type="text" class="form-control" id="e_level" name="level">
                        </div>
                        <div class="col-md-6">
                          <label for="e_phone" class="form-label">Phone</label>
                          <input type="text" class="form-control" id="e_phone" name="phone">
                        </div>
                      </div>
                    </div>
                    <div class="col-md-6 d-none" id="e_active_wrap">
                      <label class="form-label d-block">&nbsp;</label>
                      <div class="form-check pt-2">
                        <input class="form-check-input" type="checkbox" id="e_is_active" name="is_active" value="1">
                        <label class="form-check-label" for="e_is_active">Account active</label>
                      </div>
                    </div>
                    <div class="col-12 d-none" id="e_password_wrap">
                      <label for="e_password" class="form-label">Reset Password</label>
                      <input type="password" class="form-control" id="e_password" name="password"
                             autocomplete="new-password">
                      <div class="form-text">Leave blank to keep the current password.</div>
                    </div>
                  </div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

      <?php if (hasPermission('users.manage')): ?>
        <div class="modal fade" id="toggleUserModal" tabindex="-1" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content">
              <form action="users.php" method="POST">
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" id="t_id">
                <div class="modal-body text-center pt-4 pb-3">
                  <div class="mb-3">
                    <span class="stat-icon bg-warning" id="t_icon">
                      <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><path d="M12 9v4"/><path d="M12 17h.01"/></svg>
                    </span>
                  </div>
                  <h5 class="modal-title mb-1">Confirm</h5>
                  <p class="text-muted small mb-0" id="t_message">Are you sure?</p>
                </div>
                <div class="modal-footer justify-content-center border-0 pt-0">
                  <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                  <button type="submit" class="btn btn-primary" id="t_confirm">Continue</button>
                </div>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>

<?php require_once __DIR__ . '/partials/footer.php'; ?>