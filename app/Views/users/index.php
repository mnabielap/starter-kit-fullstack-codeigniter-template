<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header border-0">
                <div class="d-flex align-items-center justify-content-between">
                    <h5 class="card-title mb-0">User Management</h5>
                    <a href="<?= base_url('users/create') ?>" class="btn btn-success btn-sm">
                        <i class="bi bi-plus-lg"></i> Create New User
                    </a>
                </div>
            </div>
            
            <!-- Filters -->
            <div class="card-body border border-dashed border-end-0 border-start-0">
                <div class="row g-3">
                    <div class="col-xxl-5 col-sm-6">
                        <input type="text" class="form-control" id="searchName" placeholder="Search by name, email, or role...">
                    </div>
                    <div class="col-xxl-2 col-sm-4">
                        <select class="form-select" id="filterRole">
                            <option value="">All Roles</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                    <div class="col-xxl-2 col-sm-4">
                        <select class="form-select" id="sortBy">
                            <option value="created_at:desc">Newest</option>
                            <option value="created_at:asc">Oldest</option>
                            <option value="name:asc">Name (A-Z)</option>
                        </select>
                    </div>
                    <div class="col-xxl-1 col-sm-4">
                        <button type="button" class="btn btn-primary w-100" onclick="resetPageAndLoad()">Filter</button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-nowrap align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Role</th>
                                <th>Created At</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr><td colspan="6" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="row align-items-center mt-4">
                    <div class="col-sm">
                        <div class="text-muted">
                            Showing <span id="pageStart">0</span> to <span id="pageEnd">0</span> of <span id="totalResults">0</span>
                        </div>
                    </div>
                    <div class="col-sm-auto">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <li class="page-item" id="prevBtn">
                                <a href="javascript:void(0);" class="page-link" onclick="changePage(-1)">Previous</a>
                            </li>
                            <li class="page-item active">
                                <a href="javascript:void(0);" class="page-link" id="currentPageDisplay">1</a>
                            </li>
                            <li class="page-item" id="nextBtn">
                                <a href="javascript:void(0);" class="page-link" onclick="changePage(1)">Next</a>
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    let currentPage = 1;
    const limit = 10;
    let totalPages = 1;

    function resetPageAndLoad() {
        currentPage = 1;
        loadUsers();
    }

    function changePage(delta) {
        if (currentPage + delta >= 1 && currentPage + delta <= totalPages) {
            currentPage += delta;
            loadUsers();
        }
    }

    async function loadUsers() {
        const search = document.getElementById('searchName').value;
        const role = document.getElementById('filterRole').value;
        const sortBy = document.getElementById('sortBy').value;

        const params = new URLSearchParams({
            page: currentPage,
            limit: limit,
            sortBy: sortBy,
            search: search,
            role: role
        });

        try {
            const response = await API.fetch(`/users?${params.toString()}`);
            const json = await response.json();

            if (response.ok) {
                const data = json.data;
                const tbody = document.getElementById('usersTableBody');
                tbody.innerHTML = '';

                // Update Pagination UI
                totalPages = data.totalPages;
                document.getElementById('totalResults').innerText = data.totalResults;
                document.getElementById('currentPageDisplay').innerText = data.page;
                
                const start = (data.page - 1) * data.limit + 1;
                const end = Math.min(start + data.limit - 1, data.totalResults);
                document.getElementById('pageStart').innerText = data.totalResults ? start : 0;
                document.getElementById('pageEnd').innerText = data.totalResults ? end : 0;

                // Disable buttons if at ends
                document.getElementById('prevBtn').classList.toggle('disabled', data.page <= 1);
                document.getElementById('nextBtn').classList.toggle('disabled', data.page >= totalPages);

                if (data.results.length === 0) {
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center">No users found</td></tr>';
                    return;
                }

                data.results.forEach(user => {
                    tbody.innerHTML += `
                        <tr>
                            <td>#${user.id}</td>
                            <td>${user.name}</td>
                            <td>${user.email}</td>
                            <td><span class="badge bg-${user.role === 'admin' ? 'danger' : 'info'}">${user.role}</span></td>
                            <td>${new Date(user.created_at).toLocaleDateString()}</td>
                            <td>
                                <a href="<?= base_url('users/edit?id=') ?>${user.id}" class="btn btn-sm btn-primary">Edit</a>
                                <button class="btn btn-sm btn-danger" onclick="deleteUser('${user.id}')">Delete</button>
                            </td>
                        </tr>
                    `;
                });
            } else {
                if(json.code === 403) {
                     document.querySelector('.card-body').innerHTML = '<div class="alert alert-warning">Access Denied: Admin role required.</div>';
                }
            }
        } catch (e) {
            console.error(e);
        }
    }

    async function deleteUser(id) {
        if(!confirm('Are you sure?')) return;
        try {
            const response = await API.fetch(`/users/${id}`, { method: 'DELETE' });
            if(response.ok) loadUsers();
            else alert('Failed to delete');
        } catch(e) { alert('Error'); }
    }

    // Load on start
    loadUsers();
</script>
<?= $this->endSection() ?>