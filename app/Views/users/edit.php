<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="card">
    <div class="card-header"><h4>Edit User</h4></div>
    <div class="card-body">
        <form id="editForm">
            <input type="hidden" id="userId">
            <div class="mb-3">
                <label>Name</label>
                <input type="text" id="name" class="form-control">
            </div>
            <div class="mb-3">
                <label>Email</label>
                <input type="email" id="email" class="form-control">
            </div>
            <div class="mb-3">
                <label>Password (Leave blank to keep current)</label>
                <input type="password" id="password" class="form-control">
            </div>
            <button class="btn btn-primary">Update</button>
            <div id="msg" class="mt-2"></div>
        </form>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const params = new URLSearchParams(window.location.search);
    const id = params.get('id');

    async function loadUser() {
        if (!id) {
            alert('No ID provided');
            window.location.href = API.baseUrl + '/users';
            return;
        }

        const res = await API.fetch(`/users/${id}`);
        if(res.ok) {
            const u = await res.json();
            document.getElementById('userId').value = u.id;
            document.getElementById('name').value = u.name;
            document.getElementById('email').value = u.email;
        } else {
            alert('User not found');
            window.location.href = API.baseUrl + '/users';
        }
    }

    document.getElementById('editForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const body = {
            name: document.getElementById('name').value,
            email: document.getElementById('email').value
        };
        const pass = document.getElementById('password').value;
        if(pass) body.password = pass;

        try {
            const res = await API.fetch(`/users/${id}`, {
                method: 'PATCH',
                body: JSON.stringify(body)
            });
            const json = await res.json();

            if(res.ok) {
                window.location.href = API.baseUrl + '/users';
            } else {
                let errorMessage = json.message || 'Update failed';
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

    loadUser();
</script>
<?= $this->endSection() ?>