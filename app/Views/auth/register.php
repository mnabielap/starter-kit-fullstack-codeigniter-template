<?= $this->extend('layouts/auth') ?>

<?= $this->section('content') ?>
<form id="registerForm">
    <div class="mb-3">
        <label for="name" class="form-label">Full Name</label>
        <input type="text" class="form-control" id="name" required>
    </div>

    <div class="mb-3">
        <label for="email" class="form-label">Email</label>
        <input type="email" class="form-control" id="email" required>
    </div>

    <div class="mb-3">
        <label class="form-label" for="password">Password</label>
        <input type="password" class="form-control" id="password" required>
        <div class="form-text">Min 8 chars</div>
    </div>

    <div class="mt-4">
        <button class="btn btn-success w-100" type="submit">Register</button>
    </div>
    
    <div class="mt-3 text-center">
        <a href="<?= base_url('login') ?>" class="text-muted">Back to Login</a>
    </div>

    <div id="alertMessage" class="mt-3"></div>
</form>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('registerForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const alertBox = document.getElementById('alertMessage');
        const name = document.getElementById('name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await API.fetch('/auth/register', {
                method: 'POST',
                body: JSON.stringify({ name, email, password })
            });
            const data = await response.json();

            if (response.ok) {
                API.saveTokens(data.data.tokens);
                window.location.href = API.baseUrl + '/';
            } else {
                alertBox.innerHTML = `<div class="alert alert-danger">${data.message}</div>`;
            }
        } catch (error) {
            alertBox.innerHTML = `<div class="alert alert-danger">Error</div>`;
        }
    });
</script>
<?= $this->endSection() ?>