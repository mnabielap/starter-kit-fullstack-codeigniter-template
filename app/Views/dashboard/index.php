<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">Dashboard</h4>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-xl-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title mb-0">Welcome</h4>
            </div>
            <div class="card-body">
                <p class="text-muted">This is the CodeIgniter 4 Fullstack Starter Kit.</p>
                <div class="alert alert-info">
                    <strong>Current User ID:</strong> <span id="user-id-display">Loading...</span>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const user = API.getUser();
    if (user) {
        document.getElementById('user-id-display').innerText = user.sub;
    }
</script>
<?= $this->endSection() ?>