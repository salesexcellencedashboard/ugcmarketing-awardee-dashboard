<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<style>
    body {
        background:
            linear-gradient(rgba(255, 255, 255, 0.55), rgba(255, 255, 255, 0.55)),
            url('<?= base_url('company-logo.jpg') ?>') center/cover no-repeat fixed !important;
    }

    .auth-card {
        background: rgba(255, 255, 255, 0.96);
        backdrop-filter: blur(2px);
        border-radius: 14px;
        box-shadow: 0 14px 34px rgba(0, 0, 0, 0.22);
    }
</style>

<div class="text-center mb-4">
    <div class="d-flex justify-content-center align-items-center gap-3">
        <img src="<?= base_url('Union-Galvasteel-Logo.png') ?>" alt="Union Galvasteel Logo" class="img-fluid" style="max-height: 60px; width: auto;">
        <img src="<?= base_url('phinmalogo.png') ?>" alt="PHINMA Logo" class="img-fluid" style="max-height: 60px; width: auto;">
    </div>
</div>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success"><?= esc(session()->getFlashdata('success')) ?></div>
<?php endif; ?>

<?php if (isset($validation)): ?>
    <div class="alert alert-danger">
        <?= $validation->listErrors() ?>
    </div>
<?php endif; ?>

<form method="post" action="<?= site_url('login') ?>">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="identity" class="form-label">Username or Email</label>
        <input type="text" class="form-control" id="identity" name="identity" value="<?= old('identity') ?>" required>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-ugc">Login</button>
    </div>

    <div class="text-center">
        <a href="<?= site_url('forgot-password') ?>" class="small ugc-link">Forgot Password?</a>
    </div>
</form>

<?= $this->endSection() ?>
