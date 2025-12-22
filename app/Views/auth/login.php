<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form id="loginForm">
    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" placeholder="admin@example.com" required value="admin@example.com">
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-control" id="password" placeholder="password123" required value="password123">
    </div>

    <div class="mt-4">
        <button class="btn btn-primary w-100" type="submit">Sign In</button>
    </div>
    
    <div class="mt-3 text-center">
        <a href="<?= base_url('register') ?>" class="text-muted">Create Account</a> | 
        <a href="<?= base_url('forgot-password') ?>" class="text-muted">Forgot Password?</a>
    </div>
    
    <div id="alertMessage" class="mt-3"></div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('loginForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const alertBox = document.getElementById('alertMessage');

        alertBox.innerHTML = '';

        try {
            const response = await API.fetch('/auth/login', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            const data = await response.json();

            if (response.ok) {
                API.saveTokens(data.data.tokens);
                window.location.href = API.baseUrl + '/'; 
            } else {
                alertBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        } catch (error) {
            alertBox.innerHTML = `<div class="alert alert-danger">Connection error</div>`;
        }
    });
</script>
<?= $this->endSection() ?>