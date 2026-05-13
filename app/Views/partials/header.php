<?php
$isAdmin = $isAdmin ?? false;
$isConnected = $isConnected ?? false;
// Get isGold from session if not explicitly passed
$isGold = $isGold ?? (session()->get('is_gold') === 1);
?>
<nav class="app-nav" id="app-nav">
    <div class="nav-logo">
        <a class="brand" href="<?= base_url('resultat') ?>">
            <img src="<?= base_url('images/logo.png') ?>" alt="BodyMetric">
        </a>
        <?php if ($isConnected): ?>
            <?php if ($isGold): ?>
                <span class="gold-badge-nav">GOLD</span>
            <?php else: ?>
                <a href="<?= base_url('gold') ?>" class="gold-link-nav">Passer Gold</a>
            <?php endif; ?>
        <?php else: ?>
            <span class="nav-status is-offline">Deconnecte</span>
        <?php endif; ?>
    </div>

    <button class="nav-toggle" id="nav-toggle" aria-label="Menu">
        <span></span>
        <span></span>
        <span></span>
    </button>

    <div class="nav-links" id="nav-links">
        <?php if ($isConnected): ?>
            <a href="<?= base_url('profil') ?>">Profil</a>
            <a href="<?= base_url('objectif') ?>">Objectif</a>
            <a href="<?= base_url('resultat') ?>">Suggestions</a>
            <a href="<?= base_url('mes-regimes') ?>">Mes Régimes</a>
            <a href="<?= base_url('portefeuille') ?>">Portefeuille</a>
            <?php if (!$isGold): ?>
                <a href="<?= base_url('gold') ?>" class="nav-gold">Devenir Gold</a>
            <?php endif; ?>
            <?php if ($isAdmin): ?>
                <div class="nav-divider"></div>
                <a href="<?= base_url('bo/dashboard') ?>" class="nav-admin">Dashboard BO</a>
            <?php endif; ?>
            <div class="nav-divider"></div>
            <a class="logout" href="<?= base_url('logout') ?>">Deconnexion</a>
        <?php else: ?>
            <a href="<?= base_url('connexion') ?>">Connexion</a>
            <a href="<?= base_url('inscription/step1') ?>" class="btn-register">Inscription</a>
        <?php endif; ?>
    </div>
</nav>

<div class="nav-overlay" id="nav-overlay"></div>

<script>
(function() {
    const toggle = document.getElementById('nav-toggle');
    const navLinks = document.getElementById('nav-links');
    const overlay = document.getElementById('nav-overlay');

    if (toggle && navLinks) {
        toggle.addEventListener('click', function() {
            toggle.classList.toggle('active');
            navLinks.classList.toggle('active');
            if (overlay) overlay.classList.toggle('active');
            document.body.style.overflow = navLinks.classList.contains('active') ? 'hidden' : '';
        });

        if (overlay) {
            overlay.addEventListener('click', function() {
                toggle.classList.remove('active');
                navLinks.classList.remove('active');
                overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        }

        // Close menu when clicking a link (mobile)
        navLinks.querySelectorAll('a').forEach(function(link) {
            link.addEventListener('click', function() {
                toggle.classList.remove('active');
                navLinks.classList.remove('active');
                if (overlay) overlay.classList.remove('active');
                document.body.style.overflow = '';
            });
        });
    }
})();
</script>
