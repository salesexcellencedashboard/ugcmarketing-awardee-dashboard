<?= $this->extend('layouts/auth') ?>
<?= $this->section('content') ?>

<div class="text-center mb-4">
    <h4 class="mb-1">Forgot Password</h4>
    <p class="text-muted mb-0">Enter your email to request password reset</p>
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

<form method="post" action="/forgot-password">
    <?= csrf_field() ?>

    <div class="mb-3">
        <label for="email" class="form-label">Registered Email</label>
        <input type="email" class="form-control" id="email" name="email" value="<?= old('email') ?>" required>
    </div>

    <div class="d-grid mb-3">
        <button type="submit" class="btn btn-primary">Submit</button>
    </div>

    <div class="text-center">
        <a href="/login" class="small">Back to Login</a>
    </div>
</form>

<?= $this->endSection() ?>
