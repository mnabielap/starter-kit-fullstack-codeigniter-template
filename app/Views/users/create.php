<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header"><h4>Create User</h4></div>
    <div class="card-body">
        <form id="createForm">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" id="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" id="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" id="password" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Role</label>
                <select id="role" class="form-select">
                    <option value="user">User</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <button class="btn btn-primary">Create</button>
            <div id="msg" class="mt-2"></div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    document.getElementById('createForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const body = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value,
            password: document.getElementById('password').value,
            role: document.getElementById('role').value
        };
        
        try {
            const res = await API.fetch('/users', {
                method: 'POST',
                body: JSON.stringify(body)
            });
            if (res.ok) window.location.href = API.baseUrl + '/users';
            else {
                const json = await res.json();
                document.getElementById('msg').innerHTML = `<div class="text-danger">${json.message}</div>`;
            }
        } catch(e) { alert('Error'); }
    });
</script>
<?= $this->endSection() ?>