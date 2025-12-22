<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Auth') ?> - Starter Kit</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/css/style.css') ?>" rel="stylesheet">
    <style>
        body { background-color: #f3f3f9; }
        .auth-page-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .auth-card {
            background: #fff;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
    </style>
</head>
<body>

    <div class="auth-page-wrapper">
        <div class="auth-card">
            <div class="text-center mb-4">
                <h3 class="text-primary">Starter Kit</h3>
                <p class="text-muted">Sign in to continue.</p>
            </div>
            
            <?= $this->renderSection('content') ?>
            
        </div>
    </div>

    <script>
        const BASE_URL = "<?= rtrim(base_url(), '/') ?>";
    </script>

    <script src="<?= base_url('assets/js/api-client.js') ?>"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>