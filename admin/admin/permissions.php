<?php
require_once '../common/auth.php';
requirePageRole([ROLE_ADMIN]);
require_once '../common/layout.php';
renderHeader('Role Permissions');
?>
<div class="card">
    <select id="permissionRoleSelect"></select>
</div>
<div id="permissionList"></div>
<button onclick="savePermissions()">Save Permissions</button>

<script>
let allPermissions = [];

async function loadPermissionRoles() {
    const res = await apiGet('../api/admin/list-roles.php');
    if (res.status === 'success') {
        let html = '<option value="">Select Role</option>';
        res.data.roles.forEach(role => {
            html += `<option value="${role.id}">${role.name}</option>`;
        });
        document.getElementById('permissionRoleSelect').innerHTML = html;
    }
}

async function loadPermissions() {
    const roleId = document.getElementById('permissionRoleSelect').value;
    if (!roleId) return;
    const res = await apiGet('../api/admin/permissions.php?role_id=' + roleId);
    if (res.status === 'success') {
        allPermissions = res.data.permissions;
        let html = '';
        allPermissions.forEach(p => {
            html += `
                <div class="card">
                    <label>
                        <input type="checkbox" class="permCheck" value="${p.id}" ${p.assigned ? 'checked' : ''}>
                        ${p.name} (${p.key_name})
                    </label>
                </div>
            `;
        });
        document.getElementById('permissionList').innerHTML = html;
    }
}

async function savePermissions() {
    const roleId = document.getElementById('permissionRoleSelect').value;
    const ids = [...document.querySelectorAll('.permCheck:checked')].map(el => el.value);

    const fd = new FormData();
    fd.append('role_id', roleId);
    ids.forEach(id => fd.append('permission_ids[]', id));

    const res = await apiPost('../api/admin/save-role-permissions.php', fd);
    showAlert(res.message);
}

document.getElementById('permissionRoleSelect').addEventListener('change', loadPermissions);

loadPermissionRoles();
</script>
<?php renderFooter('admin'); ?>