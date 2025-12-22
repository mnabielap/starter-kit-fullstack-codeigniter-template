<header class="top-tagbar border-bottom bg-white mb-4">
    <div class="d-flex align-items-center justify-content-between px-4 py-2">
        <div>
            <!-- Mobile Toggle could go here -->
        </div>
        <div class="dropdown ms-sm-3">
            <button type="button" class="btn btn-light dropdown-toggle" id="page-header-user-dropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="d-flex align-items-center">
                    <span class="text-start ms-xl-2">
                        <span class="d-none d-xl-inline-block ms-1 fw-medium user-name-text">My Account</span>
                    </span>
                </span>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="javascript:void(0);" onclick="API.logout()">
                    <i class="bi bi-box-arrow-right text-muted fs-16 align-middle me-1"></i> 
                    <span class="align-middle">Logout</span>
                </a>
            </div>
        </div>
    </div>
</header>