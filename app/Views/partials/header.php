<?php $isAdmin = $isAdmin ?? false; ?>
<nav class="app-nav">
    <div class="nav-logo">
        <a class="brand" href="<?= base_url('/') ?>">
            <img src="<?= base_url('images/logo.png') ?>" alt="BodyMetric">
        </a>
    </div>
    <div class="nav-links">
        <a href="<?= base_url('profil') ?>">Profil</a>
        <a href="<?= base_url('portefeuille') ?>">Wallet</a>
        <?php if ($isAdmin): ?>
            <a href="<?= base_url('bo/codes') ?>">Admin</a>
            <a href="<?= base_url('bo/codes/form') ?>">Generer codes</a>
        <?php endif; ?>
        <a class="logout" href="<?= base_url('inscription/step1') ?>">Deconnexion</a>
    </div>
</nav>
