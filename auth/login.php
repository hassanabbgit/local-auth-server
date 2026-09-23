<?php

session_start();

if (isset($_SESSION['user_id'])) {
    $dashboard = ($_SESSION['role_id'] === 1) ? '../admin/index.php' : '../user/index.php';
    header('Location: ' . $dashboard);
    exit;
}

$error = isset($_SESSION['login_error']) ? $_SESSION['login_error'] : '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login | Network Access Portal</title>
  <link rel="stylesheet" href="../assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="auth-page">
  <main class="auth-container position-relative">
    <span class="ambient-blob blob-1"></span>
    <span class="ambient-blob blob-2"></span>
    <span class="ambient-blob blob-3"></span>

    <div class="auth-card auth-card-enter position-relative">
      <div class="p-4 p-md-5">
        <div class="text-center mb-4 auth-item" style="--delay: .05s;">
          <span class="brand-dot brand-logo" style="width: 52px; height: 52px; font-size: 1.3rem;">LA</span>
          <h1 class="h4 mt-3 mb-1">Network Access Portal</h1>
          <p class="text-muted small mb-0">Sign in with your network account</p>
        </div>

        <?php if ($error): ?>
          <div class="alert alert-danger py-2 auth-item" style="--delay: .12s;" role="alert">
            <?= htmlspecialchars($error) ?>
          </div>
        <?php endif; ?>

        <form action="authenticate.php" method="POST" novalidate>
          <div class="mb-3 auth-item" style="--delay: .18s;">
            <label for="username" class="form-label">Username</label>
            <div class="input-wrap">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
              <input type="text" class="form-control" id="username" name="username"
                     placeholder="jdoe" required autofocus autocomplete="username">
            </div>
          </div>
          <div class="mb-4 auth-item" style="--delay: .26s;">
            <label for="password" class="form-label">Password</label>
            <div class="input-wrap">
              <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
              <input type="password" class="form-control has-toggle" id="password" name="password"
                     placeholder="&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;&#8226;" required autocomplete="current-password">
              <button type="button" class="pw-toggle" id="pwToggle" aria-label="Show password" tabindex="-1">
                <svg id="iconEye" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                <svg id="iconEyeOff" width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
              </button>
            </div>
          </div>
          <button type="submit" class="btn btn-primary w-100 py-2 auth-item" style="--delay: .34s;">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 0.4rem;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
            Login
          </button>
        </form>

        <p class="text-muted small text-center mt-4 mb-0 auth-item" style="--delay: .42s;">
          <a href="../index.php">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 0.2rem;"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
            Back to home
          </a>
        </p>
      </div>
    </div>
  </main>

  <script>
    var wrap = document.querySelector('.input-wrap');
    if (wrap && document.getElementById('pwToggle')) {
      document.getElementById('pwToggle').addEventListener('click', function () {
        var input = document.getElementById('password');
        var show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        this.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        document.getElementById('iconEye').style.display = show ? 'none' : 'block';
        document.getElementById('iconEyeOff').style.display = show ? 'block' : 'none';
      });
    }
  </script>
</body>
</html>