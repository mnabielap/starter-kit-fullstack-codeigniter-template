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
            const json = await res.json();

            if (res.ok) {
                window.location.href = API.baseUrl + '/users';
            } else {
                let errorMessage = json.message || 'Failed to create user';
                if (json.errors) {
                    errorMessage = typeof json.errors === 'object' 
                        ? Object.values(json.errors).join('<br>') 
                        : json.errors;
                }
                document.getElementById('msg').innerHTML = `<div class="text-danger">${errorMessage}</div>`;
            }
        } catch(e) { 
            document.getElementById('msg').innerHTML = `<div class="text-danger">Connection error</div>`;
        }
    });
</script>
<?= $this->endSection() ?>