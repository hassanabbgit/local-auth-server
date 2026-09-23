<div class="modal fade" id="logoutModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-sm">
    <div class="modal-content">
      <form action="../auth/logout.php" method="POST">
        <div class="modal-body text-center pt-4 pb-3">
          <div class="mb-3">
            <span class="stat-icon text-white" style="background: var(--gradient);">
              <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><path d="M16 17l5-5-5-5"/><path d="M21 12H9"/></svg>
            </span>
          </div>
          <h5 class="modal-title mb-1">Sign out</h5>
          <p class="text-muted small mb-0">Are you sure you want to end your session?</p>
        </div>
        <div class="modal-footer justify-content-center border-0 pt-0">
          <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="submit" class="btn btn-primary">Logout</button>
        </div>
      </form>
    </div>
  </div>
</div>