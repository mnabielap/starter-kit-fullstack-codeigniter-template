<div class="app-menu bg-dark text-white">
    <div class="navbar-brand-box text-center py-3 border-bottom border-secondary">
        <h4 class="text-white m-0">TEMPLATE</h4>
    </div>
    <div id="scrollbar" class="p-3">
        <ul class="navbar-nav" id="navbar-nav">
            <li class="nav-item mb-2">
                <a class="nav-link text-white-50" href="<?= base_url('/') ?>">
                    <i class="bi bi-speedometer2 me-2"></i> <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item mb-2">
                <a class="nav-link text-white-50" href="<?= base_url('users') ?>">
                    <i class="bi bi-people me-2"></i> <span>Users</span>
                </a>
            </li>
            <li class="nav-item mt-4">
                <span class="text-uppercase text-secondary fs-12 fw-bold">Docs</span>
            </li>
            <li class="nav-item">
                <a class="nav-link text-white-50" href="<?= base_url('api/v1/docs') ?>" target="_blank">
                    <i class="bi bi-file-text me-2"></i> <span>API Swagger</span>
                </a>
            </li>
        </ul>
    </div>
</div>