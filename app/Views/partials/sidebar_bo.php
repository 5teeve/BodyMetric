<?php
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
$isActive = function ($path) use ($currentUri) {
    return strpos($currentUri, $path) !== false ? 'active' : '';
};
?>
<aside class="sidebar-bo" id="sidebar-bo">
    <div class="sidebar-header">
        <div class="sidebar-brand">
            <span class="brand-icon">⚙️</span>
            <span class="brand-text">Back-Office</span>
        </div>
        <button class="sidebar-toggle" id="sidebar-toggle" aria-label="Toggle sidebar">
            <span></span>
            <span></span>
            <span></span>
        </button>
    </div>

    <nav class="sidebar-nav">
        <ul class="nav-list">
            <li class="nav-item">
                <a href="<?= base_url('bo/dashboard') ?>" class="nav-link <?= $isActive('/bo/dashboard') ?>">
                    <span class="nav-icon">📊</span>
                    <span class="nav-text">Dashboard</span>
                </a>
            </li>

            <li class="nav-divider"></li>

            <li class="nav-header">Gestion</li>

            <li class="nav-item">
                <a href="<?= base_url('bo/regimes') ?>" class="nav-link <?= $isActive('/bo/regimes') ?>">
                    <span class="nav-icon">🍽️</span>
                    <span class="nav-text">Régimes</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('bo/activites') ?>" class="nav-link <?= $isActive('/bo/activites') ?>">
                    <span class="nav-icon">🏃</span>
                    <span class="nav-text">Activités</span>
                </a>
            </li>

            <li class="nav-item">
                <a href="<?= base_url('bo/codes') ?>" class="nav-link <?= $isActive('/bo/codes') ?>">
                    <span class="nav-icon">🎫</span>
                    <span class="nav-text">Codes</span>
                </a>
            </li>

            <li class="nav-divider"></li>

            <li class="nav-header">Configuration</li>

            <li class="nav-item">
                <a href="<?= base_url('bo/parametres') ?>" class="nav-link <?= $isActive('/bo/parametres') ?>">
                    <span class="nav-icon">⚙️</span>
                    <span class="nav-text">Paramètres</span>
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="<?= base_url('/') ?>" class="btn-back">
            <span class="nav-icon">←</span>
            <span class="nav-text">Retour au site</span>
        </a>
    </div>
</aside>

<style>
.sidebar-bo {
    width: 260px;
    height: 100vh;
    background: #0f172a;
    color: #e2e8f0;
    position: fixed;
    left: 0;
    top: 0;
    z-index: 100;
    display: flex;
    flex-direction: column;
    transition: transform 0.3s ease;
}

.sidebar-header {
    padding: 1.5rem;
    border-bottom: 1px solid rgba(148, 163, 184, 0.1);
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-weight: 700;
    font-size: 1.125rem;
}

.brand-icon {
    font-size: 1.25rem;
}

.sidebar-toggle {
    display: none;
    flex-direction: column;
    gap: 4px;
    background: none;
    border: none;
    cursor: pointer;
    padding: 0.5rem;
}

.sidebar-toggle span {
    display: block;
    width: 24px;
    height: 2px;
    background: #e2e8f0;
    border-radius: 2px;
    transition: all 0.3s;
}

.sidebar-nav {
    flex: 1;
    padding: 1rem 0;
    overflow-y: auto;
}

.nav-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.nav-item {
    margin: 0.25rem 0;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    color: #94a3b8;
    text-decoration: none;
    transition: all 0.2s;
    border-left: 3px solid transparent;
}

.nav-link:hover,
.nav-link.active {
    color: #e2e8f0;
    background: rgba(34, 197, 94, 0.1);
    border-left-color: #22c55e;
}

.nav-icon {
    font-size: 1.25rem;
    width: 24px;
    text-align: center;
}

.nav-text {
    font-size: 0.9375rem;
}

.nav-divider {
    height: 1px;
    background: rgba(148, 163, 184, 0.1);
    margin: 1rem 1.5rem;
}

.nav-header {
    padding: 0.5rem 1.5rem;
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: #64748b;
    font-weight: 600;
}

.sidebar-footer {
    padding: 1rem 1.5rem;
    border-top: 1px solid rgba(148, 163, 184, 0.1);
}

.btn-back {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    color: #94a3b8;
    text-decoration: none;
    padding: 0.75rem;
    border-radius: 8px;
    transition: all 0.2s;
}

.btn-back:hover {
    color: #e2e8f0;
    background: rgba(148, 163, 184, 0.1);
}

/* Responsive */
@media (max-width: 768px) {
    .sidebar-bo {
        transform: translateX(-100%);
    }

    .sidebar-bo.open {
        transform: translateX(0);
    }

    .sidebar-toggle {
        display: flex;
    }
}
</style>

<script>
document.getElementById('sidebar-toggle')?.addEventListener('click', function() {
    document.getElementById('sidebar-bo').classList.toggle('open');
});
</script>
