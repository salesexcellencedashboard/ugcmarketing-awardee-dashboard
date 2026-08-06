<?= $this->extend('layouts/main') ?>
<?= $this->section('content') ?>

<style>
    .settings-container {
        max-width: 1200px;
        margin: 0 auto;
    }

    .settings-tabs {
        display: flex;
        gap: 0;
        border-bottom: 2px solid #e5e7eb;
        margin-bottom: 1.5rem;
    }

    .settings-tab {
        padding: 0.75rem 1.5rem;
        font-size: 0.9rem;
        font-weight: 700;
        color: #6b7280;
        background: none;
        border: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .settings-tab:hover {
        color: var(--ugc-green);
    }

    .settings-tab.active {
        color: var(--ugc-red);
        border-bottom-color: var(--ugc-red);
    }

    .settings-panel {
        display: none;
    }

    .settings-panel.active {
        display: block;
    }

    .settings-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 10px;
        box-shadow: 0 2px 7px rgba(15, 23, 42, 0.05);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
    }

    .settings-card h5 {
        font-size: 1.1rem;
        font-weight: 800;
        color: var(--ugc-green);
        margin-bottom: 1rem;
        text-transform: uppercase;
        letter-spacing: 0.3px;
    }

    .form-label-settings {
        font-size: 0.82rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 0.3rem;
    }

    .form-control-settings {
        font-size: 0.9rem;
        border: 1px solid #d9dee7;
        border-radius: 6px;
        padding: 0.5rem 0.75rem;
        width: 100%;
        margin-bottom: 0.8rem;
    }

    .form-control-settings:focus {
        outline: none;
        border-color: var(--ugc-green);
        box-shadow: 0 0 0 0.2rem rgba(10, 106, 59, 0.1);
    }

    .btn-settings {
        background: var(--ugc-green);
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 0.55rem 1.5rem;
        font-size: 0.85rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-settings:hover {
        background: var(--ugc-green-dark);
    }

    .btn-settings-danger {
        background: var(--ugc-red);
    }

    .btn-settings-danger:hover {
        background: #b71620;
    }

    .btn-settings-sm {
        padding: 0.35rem 0.85rem;
        font-size: 0.75rem;
    }

    .admin-table {
        width: 100%;
        font-size: 0.82rem;
    }

    .admin-table th {
        font-size: 0.72rem;
        text-transform: uppercase;
        color: #374151;
        border-bottom: 2px solid #e5e7eb;
        padding: 0.6rem 0.5rem;
        font-weight: 800;
        letter-spacing: 0.3px;
    }

    .admin-table td {
        padding: 0.5rem;
        border-bottom: 1px solid #f0f2f6;
        vertical-align: middle;
    }

    .badge-status {
        display: inline-block;
        padding: 0.2rem 0.6rem;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
    }

    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-inactive {
        background: #fee2e2;
        color: #991b1b;
    }

    .alert-settings {
        padding: 0.75rem 1rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .alert-settings-success {
        background: #d1fae5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .alert-settings-error {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }

    .profile-avatar {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        background: var(--ugc-green);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2.5rem;
        font-weight: 800;
        flex-shrink: 0;
        overflow: hidden;
        position: relative;
    }

    .profile-avatar img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
    }

    .profile-pic-upload {
        position: relative;
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        gap: 0.5rem;
    }

    .profile-pic-upload .upload-overlay {
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        background: rgba(0,0,0,0.6);
        color: #fff;
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.25rem 0.6rem;
        border-radius: 0 0 50px 50px;
        cursor: pointer;
        text-align: center;
        width: 100px;
        opacity: 0;
        transition: opacity 0.2s ease;
    }

    .profile-pic-upload:hover .upload-overlay {
        opacity: 1;
    }

    .profile-pic-upload input[type="file"] {
        display: none;
    }

    .text-muted-settings {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* Side-by-side layout for Admin Management */
    .admin-sidebar-layout {
        display: flex;
        gap: 1.5rem;
        align-items: flex-start;
    }

    .admin-sidebar-layout .admin-form-side {
        flex: 0 0 380px;
        min-width: 320px;
    }

    .admin-sidebar-layout .admin-table-side {
        flex: 1;
        min-width: 0;
        overflow: hidden;
    }

    @media (max-width: 992px) {
        .admin-sidebar-layout {
            flex-direction: column;
        }
        .admin-sidebar-layout .admin-form-side {
            flex: 0 0 auto;
            width: 100%;
            min-width: 0;
        }
        .admin-sidebar-layout .admin-table-side {
            width: 100%;
        }
    }
</style>

<div class="settings-container">
    <h4 style="color: var(--ugc-red); font-weight: 900; margin-bottom: 0.25rem; text-transform: uppercase; font-size: 1.4rem;">
        System Settings
    </h4>
    <p class="text-muted-settings" style="margin-bottom: 1.5rem;">
        Manage your profile credentials and admin accounts
    </p>

    <!-- Tabs -->
    <div class="settings-tabs">
        <button class="settings-tab active" onclick="switchSettingsTab('profile', this)">My Profile</button>
        <button class="settings-tab" onclick="switchSettingsTab('admins', this)">Admin Management</button>
    </div>

    <!-- Profile Panel -->
    <div class="settings-panel active" id="settingsProfilePanel">
        <?php if (session()->getFlashdata('profile_success')): ?>
            <div class="alert-settings alert-settings-success"><?= esc(session()->getFlashdata('profile_success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('profile_error')): ?>
            <div class="alert-settings alert-settings-error"><?= esc(session()->getFlashdata('profile_error')) ?></div>
        <?php endif; ?>
        <?php if ($profileErrors = session()->getFlashdata('profile_errors')): ?>
            <div class="alert-settings alert-settings-error">
                <?php if (is_array($profileErrors)): ?>
                    <?php foreach ($profileErrors as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= esc($profileErrors) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="settings-card">
            <div style="display: flex; align-items: center; gap: 1.5rem; margin-bottom: 1.5rem;">
                <form method="post" action="<?= site_url('settings/upload-profile-pic') ?>" enctype="multipart/form-data" id="profilePicForm">
                    <?= csrf_field() ?>
                    <div class="profile-pic-upload">
                        <div class="profile-avatar" id="profileAvatar">
                            <?php 
                            $profilePic = $user['profile_pic'] ?? null;
                            if ($profilePic): ?>
                                <img src="<?= base_url($profilePic) ?>" alt="Profile Picture">
                            <?php else: ?>
                                <?= strtoupper(substr(esc($user['fullname'] ?? session()->get('fullname') ?? 'A'), 0, 1)) ?>
                            <?php endif; ?>
                        </div>
                        <label class="upload-overlay" for="profilePicInput">Change Photo</label>
                        <input type="file" id="profilePicInput" name="profile_pic" accept="image/jpeg,image/png,image/gif,image/webp" onchange="document.getElementById('profilePicForm').submit();">
                    </div>
                </form>
                <div>
                    <h5 style="margin-bottom: 0.2rem;"><?= esc($user['fullname'] ?? session()->get('fullname') ?? 'Administrator') ?></h5>
                    <div class="text-muted-settings">
                        <span class="badge-status badge-active"><?= esc(ucfirst($user['role'] ?? session()->get('role') ?? 'admin')) ?></span>
                    </div>
                </div>
            </div>

            <form method="post" action="<?= site_url('settings/update-profile') ?>">
                <?= csrf_field() ?>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-settings">Full Name *</label>
                        <input type="text" class="form-control-settings" name="fullname" 
                               value="<?= esc(old('fullname', $user['fullname'] ?? session()->get('fullname') ?? '')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-settings">Username *</label>
                        <input type="text" class="form-control-settings" name="username" 
                               value="<?= esc(old('username', $user['username'] ?? session()->get('username') ?? '')) ?>" required>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-settings">Email *</label>
                        <input type="email" class="form-control-settings" name="email" 
                               value="<?= esc(old('email', $user['email'] ?? session()->get('email') ?? '')) ?>" required>
                    </div>
                </div>

                <hr style="margin: 1.2rem 0; border-color: #e5e7eb;">

                <h6 style="font-weight: 800; color: var(--ugc-green); margin-bottom: 0.8rem; text-transform: uppercase; font-size: 0.85rem;">
                    Change Password (leave blank to keep current)
                </h6>

                <div class="row">
                    <div class="col-md-6">
                        <label class="form-label-settings">New Password</label>
                        <input type="password" class="form-control-settings" name="password" placeholder="Enter new password">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label-settings">Confirm Password</label>
                        <input type="password" class="form-control-settings" name="confirm_password" placeholder="Confirm new password">
                    </div>
                </div>

                <div style="margin-top: 1rem;">
                    <button type="submit" class="btn-settings">Update Profile</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Admin Management Panel -->
    <div class="settings-panel" id="settingsAdminsPanel">
        <?php if (session()->getFlashdata('admin_success')): ?>
            <div class="alert-settings alert-settings-success"><?= esc(session()->getFlashdata('admin_success')) ?></div>
        <?php endif; ?>
        <?php if (session()->getFlashdata('admin_error')): ?>
            <div class="alert-settings alert-settings-error"><?= esc(session()->getFlashdata('admin_error')) ?></div>
        <?php endif; ?>
        <?php if ($adminErrors = session()->getFlashdata('admin_errors')): ?>
            <div class="alert-settings alert-settings-error">
                <?php if (is_array($adminErrors)): ?>
                    <?php foreach ($adminErrors as $error): ?>
                        <div><?= esc($error) ?></div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <?= esc($adminErrors) ?>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <div class="admin-sidebar-layout">
            <!-- Register New Admin -->
            <div class="settings-card admin-form-side" style="margin-bottom: 0;">
                <h5>Register New Admin</h5>
                <p class="text-muted-settings" style="margin-bottom: 1rem;">
                    Add a new marketing officer to access the system.
                </p>

                <form method="post" action="<?= site_url('settings/register-admin') ?>">
                    <?= csrf_field() ?>

                    <label class="form-label-settings">Full Name *</label>
                    <input type="text" class="form-control-settings" name="fullname" 
                           value="<?= esc(old('fullname')) ?>" required>

                    <label class="form-label-settings">Username *</label>
                    <input type="text" class="form-control-settings" name="username" 
                           value="<?= esc(old('username')) ?>" required>

                    <label class="form-label-settings">Email *</label>
                    <input type="email" class="form-control-settings" name="email" 
                           value="<?= esc(old('email')) ?>" required>

                    <label class="form-label-settings">Password *</label>
                    <input type="password" class="form-control-settings" name="password" required>

                    <label class="form-label-settings">Confirm Password *</label>
                    <input type="password" class="form-control-settings" name="confirm_password" required>

                    <div style="margin-top: 0.5rem;">
                        <button type="submit" class="btn-settings">Register Admin</button>
                    </div>
                </form>
            </div>

            <!-- Admin List -->
            <div class="settings-card admin-table-side" style="margin-bottom: 0;">
                <h5>Registered Administrators</h5>
                <p class="text-muted-settings" style="margin-bottom: 1rem;">
                    List of all admin accounts in the system.
                </p>

                <?php if (empty($admins)): ?>
                    <p class="text-muted-settings">No admin accounts found.</p>
                <?php else: ?>
                    <div style="overflow-x: auto; max-height: 500px; overflow-y: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Full Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Registered</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($admins as $admin): ?>
                                    <tr>
                                        <td><strong><?= esc($admin['fullname']) ?></strong></td>
                                        <td><?= esc($admin['username']) ?></td>
                                        <td><?= esc($admin['email']) ?></td>
                                        <td>
                                            <span class="badge-status <?= $admin['status'] === 'active' ? 'badge-active' : 'badge-inactive' ?>">
                                                <?= esc($admin['status']) ?>
                                            </span>
                                        </td>
                                        <td class="text-muted-settings"><?= esc(date('M d, Y', strtotime($admin['created_at'] ?? 'now'))) ?></td>
                                        <td>
                                            <?php if ((int)$admin['id'] !== (int)session()->get('user_id')): ?>
                                                <?php if ($admin['status'] === 'active'): ?>
                                                    <a href="<?= site_url('settings/deactivate-admin/' . $admin['id']) ?>" 
                                                       class="btn-settings btn-settings-danger btn-settings-sm"
                                                       onclick="return confirm('Deactivate this admin? They will lose access to the system.')">
                                                        Deactivate
                                                    </a>
                                                <?php else: ?>
                                                    <a href="<?= site_url('settings/activate-admin/' . $admin['id']) ?>" 
                                                       class="btn-settings btn-settings-sm">
                                                        Activate
                                                    </a>
                                                <?php endif; ?>
                                            <?php else: ?>
                                                <span class="text-muted-settings">(You)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<script>
function switchSettingsTab(tab, btn) {
    document.querySelectorAll('.settings-tab').forEach(function(t) {
        t.classList.remove('active');
    });
    btn.classList.add('active');

    document.querySelectorAll('.settings-panel').forEach(function(p) {
        p.classList.remove('active');
    });
    document.getElementById('settings' + tab.charAt(0).toUpperCase() + tab.slice(1) + 'Panel').classList.add('active');
}
</script>

<?= $this->endSection() ?>