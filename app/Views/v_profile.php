<?= $this->extend('layout'); ?>

<?= $this->section('content'); ?>

<h4>Profil Pengguna</h4>

<ul>
    <li><b>Username:</b> <?= $username; ?></li>
    <li><b>Role:</b> <?= $role; ?></li>
    <li><b>Email:</b> <?= $email; ?></li>
    <li><b>Waktu Login:</b> <?= $waktu_login; ?></li>
    <li><b>Status Login:</b> <?= $status; ?></li>
</ul>

<?= $this->endSection(); ?>