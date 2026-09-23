(function () {
  'use strict';

  // ---------- Responsive sidebar drawer ----------
  (function () {
    var wrapper = document.querySelector('.app-wrapper');
    if (!wrapper) return;

    var toggleBtn = document.querySelector('.sidebar-toggle');
    var closeBtn = document.querySelector('.sidebar-close');
    var backdrop = document.querySelector('.sidebar-backdrop');

    function open() {
      wrapper.classList.add('sidebar-open');
      document.body.style.overflow = 'hidden';
    }

    function close() {
      wrapper.classList.remove('sidebar-open');
      document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', open);
    if (closeBtn) closeBtn.addEventListener('click', close);
    if (backdrop) backdrop.addEventListener('click', close);

    var navLinks = wrapper.querySelectorAll('.side-nav .nav-link');
    for (var i = 0; i < navLinks.length; i++) {
      navLinks[i].addEventListener('click', close);
    }
  })();

  // ---------- Edit user modal ----------
  var editModal = document.getElementById('editUserModal');
  if (editModal) {
    editModal.addEventListener('show.bs.modal', function (event) {
      var btn = event.relatedTarget;
      var form = editModal.querySelector('form');
      var canManage = btn.getAttribute('data-can-manage') === '1';

      document.getElementById('e_id').value = btn.getAttribute('data-id') || '';
      document.getElementById('e_username').value = btn.getAttribute('data-fullname') || '';
      document.getElementById('e_full_name').value = btn.getAttribute('data-fullname') || '';
      document.getElementById('e_email').value = btn.getAttribute('data-email') || '';

      var roleWrap = editModal.querySelector('#e_role_wrap');
      var activeWrap = editModal.querySelector('#e_active_wrap');
      var passWrap = editModal.querySelector('#e_password_wrap');

      if (canManage) {
        document.getElementById('e_role_id').value = btn.getAttribute('data-role') || '';
        document.getElementById('e_is_active').checked = btn.getAttribute('data-active') === '1';
        document.getElementById('e_password').value = '';
        roleWrap.classList.remove('d-none');
        activeWrap.classList.remove('d-none');
        passWrap.classList.remove('d-none');
      } else {
        roleWrap.classList.add('d-none');
        activeWrap.classList.add('d-none');
        passWrap.classList.add('d-none');
      }

      var errBox = editModal.querySelector('#e_error');
      if (errBox) {
        errBox.classList.add('d-none');
        errBox.textContent = '';
      }

      var roleSelEdit = editModal.querySelector('#e_role_id');
      var stuFieldsEdit = editModal.querySelector('#e_student_fields');
      var studentRoleId = roleSelEdit.getAttribute('data-student-role');
      var isStudent = btn.getAttribute('data-is-student') === '1';

      document.getElementById('e_student_id').value = btn.getAttribute('data-student-id') || '';
      document.getElementById('e_department').value = btn.getAttribute('data-department') || '';
      document.getElementById('e_level').value = btn.getAttribute('data-level') || '';
      document.getElementById('e_phone').value = btn.getAttribute('data-phone') || '';

      function syncEditStudent() {
        stuFieldsEdit.classList.toggle('d-none', roleSelEdit.value !== studentRoleId);
      }
      roleSelEdit.onchange = syncEditStudent;
      syncEditStudent();

      if (!isStudent) {
        document.getElementById('e_student_id').value = '';
        document.getElementById('e_department').value = '';
        document.getElementById('e_level').value = '';
        document.getElementById('e_phone').value = '';
      }
    });
  }

  // ---------- Toggle student fields in create-user modal ----------
  var createRoleSel = document.getElementById('c_role_id');
  if (createRoleSel) {
    var createStuFields = document.getElementById('c_student_fields');
    var createStudentRoleId = createRoleSel.getAttribute('data-student-role');
    createRoleSel.addEventListener('change', function () {
      createStuFields.classList.toggle('d-none', createRoleSel.value !== createStudentRoleId);
    });
  }

  // ---------- Toggle user (activate/deactivate) modal ----------
  var toggleModal = document.getElementById('toggleUserModal');
  if (toggleModal) {
    toggleModal.addEventListener('show.bs.modal', function (event) {
      var btn = event.relatedTarget;
      var will = btn.getAttribute('data-will');
      var activating = will === 'activate';

      document.getElementById('t_id').value = btn.getAttribute('data-id') || '';
      document.getElementById('t_message').textContent =
        'Do you want to ' + will + ' ' + btn.getAttribute('data-name') + '?';

      var confirmBtn = document.getElementById('t_confirm');
      var icon = document.getElementById('t_icon');
      if (activating) {
        confirmBtn.className = 'btn btn-success';
        confirmBtn.textContent = 'Activate';
        icon.className = 'stat-icon bg-success text-success';
      } else {
        confirmBtn.className = 'btn btn-warning';
        confirmBtn.textContent = 'Deactivate';
        icon.className = 'stat-icon bg-warning text-warning';
      }
    });
  }

  // ---------- Role create/edit modal ----------
  var roleModal = document.getElementById('roleModal');
  if (roleModal) {
    roleModal.addEventListener('show.bs.modal', function (event) {
      var btn = event.relatedTarget;
      var form = roleModal.querySelector('form');
      var isEdit = btn.hasAttribute('data-id');

      form.reset();
      var boxes = roleModal.querySelectorAll('.role-perm');
      var title = roleModal.querySelector('#roleModalTitle');

      if (isEdit) {
        document.getElementById('r_action').value = 'update';
        document.getElementById('r_id').value = btn.getAttribute('data-id');
        document.getElementById('r_name').value = btn.getAttribute('data-name') || '';
        document.getElementById('r_description').value = btn.getAttribute('data-description') || '';
        title.textContent = 'Edit Role';

        var pids = (btn.getAttribute('data-pids') || '').split(',').filter(function (v) { return v !== ''; });
        for (var i = 0; i < boxes.length; i++) {
          boxes[i].checked = pids.indexOf(boxes[i].value) !== -1;
        }
      } else {
        document.getElementById('r_action').value = 'create';
        document.getElementById('r_id').value = '0';
        title.textContent = 'New Role';
      }
    });
  }

  // ---------- Delete role modal ----------
  var delRoleModal = document.getElementById('deleteRoleModal');
  if (delRoleModal) {
    delRoleModal.addEventListener('show.bs.modal', function (event) {
      var btn = event.relatedTarget;
      document.getElementById('dr_id').value = btn.getAttribute('data-id') || '';
      document.getElementById('dr_message').textContent =
        'Delete role "' + btn.getAttribute('data-name') + '"? This cannot be undone.';
    });
  }
})();