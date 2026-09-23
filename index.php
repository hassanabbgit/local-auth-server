<?php

$dbOk = false;

try {
    require_once __DIR__ . '/config/database.php';
    getPDO();
    $dbOk = true;
} catch (Exception $e) {
    $dbOk = false;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Local Network Authentication Portal</title>
  <link rel="stylesheet" href="assets/vendor/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <nav class="landing-nav">
    <div class="landing-nav-inner">
      <a class="landing-brand" href="index.php">
        
        <span>Local Network Access Portal</span>
      </a>
      <div class="landing-nav-links d-none d-md-flex">
        <a href="#features">Features</a>
        <a href="#how-it-works">How it works</a>
        <a href="#security">Security</a>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="status-pill d-none d-sm-inline-flex">
          <span class="status-dot"></span>
          All systems operational
        </span>
        <a class="btn btn-primary btn-sm px-3" href="auth/login.php">
          <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px; margin-right: 0.3rem;"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
          Sign In
        </a>
      </div>
    </div>
  </nav>

  <main>
    <header class="landing-hero">
      <div class="container">
        <span class="hero-badge mb-4">
          <span class="brand-dot" style="width: 20px; height: 20px; font-size: 0.6rem;">LA</span>
          Unified sign-in for your local network
        </span>
        <h1 class="landing-title">One account for every<br><span class="text-gradient">service on your network</span></h1>
        <p class="landing-sub">
          A centralized authentication server your organisation can rely on. Users sign in once with role-based
          access, and every attempt is recorded in a tamper-evident audit log.
        </p>
        <div class="d-flex flex-wrap justify-content-center gap-3 mb-5">
          <a class="btn btn-primary px-4 py-2" href="auth/login.php">Sign in to your account</a>
          <a class="btn btn-outline-primary px-4 py-2" href="#features">Explore features</a>
        </div>
        <p class="<?= $dbOk ? 'text-success' : 'text-muted' ?> mb-0">
          <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="vertical-align: -2px;">
            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
          </svg>
          <?= $dbOk ? 'Authentication service is active and accepting sign-ins.' : 'Service status is being checked. Please try again shortly.' ?>
        </p>
      </div>
    </header>

    <section class="landing-section" id="features">
      <div class="container">
        <div class="text-center mb-5">
          <span class="hero-badge mb-3">Platform capabilities</span>
          <h2 class="landing-h2">Everything modern access control needs</h2>
          <p class="text-muted mx-auto" style="max-width: 620px;">
            Purpose-built for internal networks where security, accountability and simplicity matter.
          </p>
        </div>
        <div class="row g-4">
          <div class="col-md-6 col-lg-3">
            <div class="card feature-card h-100">
              <div class="card-body p-4">
                <span class="stat-icon text-white" style="background: var(--gradient);">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><path d="M10 17l5-5-5-5"/><path d="M15 12H3"/></svg>
                </span>
                <h3 class="h6 mt-3 mb-1">Centralized Sign-in</h3>
                <p class="text-muted small mb-0">One set of credentials for every internal service. Log in once, move everywhere.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card feature-card h-100">
              <div class="card-body p-4">
                <span class="stat-icon bg-info text-info">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="8.5" cy="7" r="4"/><path d="M20 8v6"/><path d="M23 11h-6"/></svg>
                </span>
                <h3 class="h6 mt-3 mb-1">Role-Based Access</h3>
                <p class="text-muted small mb-0">Administrators, staff and members get exactly the permissions they need — nothing more.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card feature-card h-100">
              <div class="card-body p-4">
                <span class="stat-icon bg-success text-success">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2"/><rect x="9" y="3" width="6" height="4" rx="1"/><path d="M9 12h6"/><path d="M9 16h6"/></svg>
                </span>
                <h3 class="h6 mt-3 mb-1">Full Audit Trail</h3>
                <p class="text-muted small mb-0">Every successful and failed attempt is recorded with user, IP address and time.</p>
              </div>
            </div>
          </div>
          <div class="col-md-6 col-lg-3">
            <div class="card feature-card h-100">
              <div class="card-body p-4">
                <span class="stat-icon bg-warning text-warning">
                  <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                </span>
                <h3 class="h6 mt-3 mb-1">Self-Service Profile</h3>
                <p class="text-muted small mb-0">Users review their own account, member details and personal sign-in history anytime.</p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-section landsection-alt" id="how-it-works">
      <div class="container">
        <div class="text-center mb-5">
          <span class="hero-badge mb-3">Simple workflow</span>
          <h2 class="landing-h2">How it works</h2>
        </div>
        <div class="row g-4">
          <div class="col-md-4">
            <div class="step-card h-100">
              <div class="step-number">1</div>
              <h3 class="h6 mt-3 mb-1">An administrator creates your account</h3>
              <p class="text-muted small mb-0">Your role decides what you can see and do across the portal.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="step-card h-100">
              <div class="step-number">2</div>
              <h3 class="h6 mt-3 mb-1">You sign in once</h3>
              <p class="text-muted small mb-0">Credentials are verified and your session is established securely.</p>
            </div>
          </div>
          <div class="col-md-4">
            <div class="step-card h-100">
              <div class="step-number">3</div>
              <h3 class="h6 mt-3 mb-1">Activity is logged</h3>
              <p class="text-muted small mb-0">Every sign-in — success or failure — is timestamped and attributed to an IP address.</p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <section class="landing-section" id="security">
      <div class="container">
        <div class="row align-items-center g-4">
          <div class="col-lg-5 text-center text-lg-start">
            <span class="stat-icon text-white" style="background: var(--gradient); width: 64px; height: 64px;">
              <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>
            </span>
            <h2 class="landing-h2 mt-3 mb-2">Built for accountability</h2>
            <p class="text-muted mb-4">
              Passwords are stored hashed, sessions are regenerated at login, and administrators can audit
              the whole history of who signed in, from where and when.
            </p>
            <a class="btn btn-outline-primary px-4" href="auth/login.php">Sign in to get started</a>
          </div>
          <div class="col-lg-7">
            <div class="row g-3">
              <div class="col-sm-6">
                <div class="card h-100">
                  <div class="card-body d-flex gap-3 p-3">
                    <span class="stat-icon bg-info text-info" style="width: 38px; height: 38px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                    </span>
                    <div>
                      <div class="fw-semibold small">Hashed credentials</div>
                      <div class="text-muted small">Passwords are never stored in plain text.</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card h-100">
                  <div class="card-body d-flex gap-3 p-3">
                    <span class="stat-icon bg-success text-success" style="width: 38px; height: 38px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg>
                    </span>
                    <div>
                      <div class="fw-semibold small">Per-role permissions</div>
                      <div class="text-muted small">Access is granted by role, not by guesswork.</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card h-100">
                  <div class="card-body d-flex gap-3 p-3">
                    <span class="stat-icon bg-warning text-warning" style="width: 38px; height: 38px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/></svg>
                    </span>
                    <div>
                      <div class="fw-semibold small">Session protection</div>
                      <div class="text-muted small">Session IDs rotate at every sign-in.</div>
                    </div>
                  </div>
                </div>
              </div>
              <div class="col-sm-6">
                <div class="card h-100">
                  <div class="card-body d-flex gap-3 p-3">
                    <span class="stat-icon bg-danger text-danger" style="width: 38px; height: 38px;">
                      <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 12h-4l-3 9L9 3l-3 9H2"/></svg>
                    </span>
                    <div>
                      <div class="fw-semibold small">Failed-attempt tracking</div>
                      <div class="text-muted small">Suspicious sign-ins are visible to admins.</div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <footer class="landing-footer">
    <div class="container d-flex flex-wrap justify-content-between align-items-center gap-3 py-4">
      <div class="d-flex align-items-center gap-2">
        <span class="brand-dot" style="width: 26px; height: 26px; font-size: 0.6rem;">LA</span>
        <span class="small text-muted">Local Network Access Portal</span>
      </div>
      <div class="d-flex gap-3 small">
        <a href="#features" class="text-muted text-decoration-none">Features</a>
        <a href="#how-it-works" class="text-muted text-decoration-none">How it works</a>
        <a href="#security" class="text-muted text-decoration-none">Security</a>
        <a href="auth/login.php" class="text-muted text-decoration-none">Sign In</a>
      </div>
      <div class="small text-muted">&copy; <?= date('Y') ?> Network Access Portal &middot; Internal use only</div>
    </div>
  </footer>

</body>
</html>