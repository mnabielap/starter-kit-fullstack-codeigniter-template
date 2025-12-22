<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Dashboard') ?> - Starter Kit</title>
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Custom CSS -->
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
</head>
<body>

    <div class="layout-wrapper">
        <!-- Sidebar -->
        <?= $this->include('partials/sidebar') ?>

        <div class="main-content">
            <!-- Header -->
            <?= $this->include('partials/header') ?>

            <div class="page-content">
                <div class="container-fluid">
                    <!-- Dynamic Content -->
                    <?= $this->renderSection('content') ?>
                </div>
            </div>
            
            <!-- Footer -->
            <?= $this->include('partials/footer') ?>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 (for nice alerts) -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        const BASE_URL = "<?= rtrim(base_url(), '/') ?>";
    </script>
    
    <!-- API Client (The Bridge between Web and API) -->
    <script src="<?= base_url('assets/js/api-client.js') ?>"></script>
    
    <!-- Custom Page Scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>