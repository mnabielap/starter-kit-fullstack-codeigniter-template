<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form id="resetForm">
    <div class="text-center mb-4">
        <p class="text-muted">Create a new password.</p>
    </div>

    <div class="mb-3">
        <label for="password" class="form-label">New Password</label>
        <input type="password" class="form-control" id="password" required>
        <div class="form-text">Must be at least 8 characters.</div>
    </div>

    <div class="mt-4">
        <button class="btn btn-primary w-100" type="submit">Reset Password</button>
    </div>
    
    <div id="alertMessage" class="mt-3"></div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Extract Token from URL Query Params (?token=...)
    const urlParams = new URLSearchParams(window.location.search);
    const token = urlParams.get('token');

    if (!token) {
        document.getElementById('alertMessage').innerHTML = '<div class="alert alert-danger">Invalid Link: Token missing.</div>';
        document.querySelector('button').disabled = true;
    }

    document.getElementById('resetForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const password = document.getElementById('password').value;
        const alertBox = document.getElementById('alertMessage');

        try {
            const response = await API.fetch(`/auth/reset-password?token=${token}`, {
                method: 'POST',
                body: JSON.stringify({ password })
            });

            if (response.ok) {
                alertBox.innerHTML = `<div class="alert alert-success">Password reset successful! Redirecting to login...</div>`;
                setTimeout(() => {
                    window.location.href = API.baseUrl + '/login';
                }, 2000);
            } else {
                const data = await response.json();
                alertBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        } catch (error) {
            alertBox.innerHTML = `<div class="alert alert-danger">An error occurred</div>`;
        }
    });
</script>
<?= $this->endSection() ?>