const locationsData = [];
const locationPartsData = [];
const usersData = [];

function addLocationToTable(name, description) {
    const tableBody = document.getElementById('location-table-body');
    if (!tableBody) return;

    // Hapus baris placeholder jika ada
    if (tableBody.children.length === 1 && tableBody.children[0].cells[0].colSpan === 3) {
    tableBody.innerHTML = '';
    }

    const newRow = document.createElement('tr');
    newRow.innerHTML = `
    <td>${tableBody.children.length + 1}</td>
    <td>${name}</td>
    <td>${description}</td>
    `;
    tableBody.appendChild(newRow);
}

const content = {
    "dashboard": `<h2>Dashboard</h2><p>Welcome to the Dashboard!</p>`,

    // Create Equipment Form
    "equipment-create": `
        <h2>Create Equipment</h2>
        <form id="equipment-form">
            <div class="mb-3">
                <label for="equipmentName" class="form-label">Equipment Name</label>
                <input type="text" class="form-control" id="equipmentName" name="equipmentName" required />
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Locations</label>
                <select class="form-select" id="location" name="location" required>
                    <option value="">-- Choose Locations --</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status" required>
                    <option value="">-- Choose Status --</option>
                    <option value="Aktif">Aktif</option>
                    <option value="Tidak Aktif">Tidak Aktif</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Note</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <div id="equipment-message" class="mt-3"></div>

        <script>
            document.getElementById('equipment-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const name = document.getElementById('equipmentName').value.trim();
                const location = document.getElementById('location').value;
                const status = document.getElementById('status').value;
                const keterangan = document.getElementById('keterangan').value.trim();

                if (name && location && status) {
                    document.getElementById('equipment-message').innerHTML =
                        '<div class="alert alert-success">Equipment "' + name + '" berhasil disimpan.</div>';
                    this.reset();
                } else {
                    document.getElementById('equipment-message').innerHTML =
                        '<div class="alert alert-danger">Nama, Lokasi, dan Status wajib diisi.</div>';
                }
            });
        <\/script>
    `,

    "equipment-view": `<h2>View Equipment</h2><p>List of all equipment.</p>`,
    
    // Create Location Form
    "locations-create": `
        <h2>Create Location</h2>
        <form id="location-form" action="insert_location.php" method="POST">
            <div class="mb-3">
                <label for="locationName" class="form-label">Locations Name</label>
                <input type="text" class="form-control" id="locationName" name="nama_location" required />
            </div>
            <div class="mb-3">
                <label for="description" class="form-label">Note</label>
                <textarea class="form-control" id="description" name="keterangan" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    `,

    // View Locations
    "locations-view": `
        <h2>View Locations</h2>
        <iframe src="view_locations.php" width="100%" height="400px" frameborder="0"></iframe>
    `,

    // Create Parts Form
    "parts-create": `
        <h2>Create Part</h2>
        <form id="parts-form">
            <div class="mb-3">
                <label for="partName" class="form-label">Parts Name</label>
                <input type="text" class="form-control" id="partName" name="partName" required />
            </div>

            <div class="mb-3">
                <label for="location" class="form-label">Parts Locations</label>
                <select class="form-select" id="location" name="location" required>
                    <option value="">-- Choose Parts Locations --</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="equipmentLocation" class="form-label">Equipment - Location</label>
                <select class="form-select" id="equipmentLocation" name="equipmentLocation" required>
                    <option value="">-- Choose Equipment - Locations --</option>
                </select>
            </div>

            <div class="mb-3">
                <label for="keterangan" class="form-label">Note</label>
                <textarea class="form-control" id="keterangan" name="keterangan" rows="3"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
        <div id="parts-message" class="mt-3"></div>

        <script>
            document.getElementById('parts-form').addEventListener('submit', function (e) {
                e.preventDefault();

                const partName = document.getElementById('partName').value.trim();
                const location = document.getElementById('location').value;
                const equipmentLocation = document.getElementById('equipmentLocation').value;
                const keterangan = document.getElementById('keterangan').value.trim();

                if (partName && location && equipmentLocation) {
                    document.getElementById('parts-message').innerHTML =
                    '<div class="alert alert-success">Part "' + partName + '" berhasil disimpan.</div>';
                    this.reset();
                } else {
                    document.getElementById('parts-message').innerHTML =
                    '<div class="alert alert-danger">Semua field wajib diisi.</div>';
                }
            });
        <\/script>
    `,

    "parts-view": `<h2>View Parts</h2><p>List of all parts.</p>`,

    // Create Location Parts Form
    "locations-parts-create": `
        <h2>Create Location Parts</h2>
        <form id="location-parts-form" action="insert_location_parts.php" method="POST">
            <div class="mb-3">
                <label for="locationspartsName" class="form-label">Parts Locations Name</label>
                <input type="text" class="form-control" id="locationspartsName" name="nama_location_part" required />
            </div>
            <div class="mb-3">
                <label for="descriptionlocationsParts" class="form-label">Note</label>
                <textarea class="form-control" id="descriptionlocationsParts" name="keterangan" rows="3"></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Simpan</button>
        </form>
    `,
    
    // View Location Parts
    "locations-parts-view": `
        <h2>View Location Parts</h2>
        <iframe src="view_location_parts.php" width="100%" height="400px" frameborder="0"></iframe>
    `,

    // Create Admin User Form
    "admin-create-user": `
        <h2>Create User</h2>
        <form id="create-user-form">
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required />
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Gmail</label>
                <input type="email" class="form-control" id="email" name="email" required />
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required />
            </div>
            <div class="mb-3">
                <label for="verifyPassword" class="form-label">Verify Password</label>
                <input type="password" class="form-control" id="verifyPassword" name="verifyPassword" required />
            </div>
            <div class="mb-3">
            <label for="role" class="form-label">User Role</label>
            <select class="form-select" id="role" name="role" required>
                <option value="">-- Pilih Role --</option>
                <option value="Admin">Admin (All Role)</option>
                <option value="Dispatch">Dispatch (Create Only)</option>
                <option value="Technician">Tech (Create or Close Only)</option>
                <option value="Viewer">Work Order (View Only)</option>
            </select>
            </div>
            <button type="submit" class="btn btn-primary">Create User</button>
        </form>
        <div id="user-message" class="mt-3"></div>
    `,

    "admin-view-user": `
        <h2>View User</h2>
        <p>List of all user.</p>
        <table class="table table-striped table-bordered">
            <thead class="table-primary">
                <tr>
                    <th scope="col">Number</th>
                    <th scope="col">Username</th>
                    <th scope="col">Gmail</th>
                    <th scope="col">Role</th>
                    <th scope="col">Actions</th>
                </tr>
            </thead>
            <tbody id="user-table-body">
                <tr>
                    <td colspan="5" class="text-center">Belum ada data user.</td>
                </tr>
            </tbody>
        </table>
    `,

    "logout": `<h2>Logout</h2><p>You have been logged out. Thank you!</p>`
};

// Navigation click handler
document.querySelectorAll('[data-page]').forEach(link => {
    link.addEventListener('click', function (e) {
    e.preventDefault();
    document.querySelectorAll('.nav-link, .dropdown-item').forEach(el => el.classList.remove('active'));
    this.classList.add('active');

    const page = this.getAttribute('data-page');
    document.getElementById('page-content').innerHTML = content[page] || "<h2>Not Found</h2>";

    // Jalankan script tambahan
    if (page === 'admin-create-user') {
        initializeUsersForm();
    } else if (page === 'admin-view-user') {
        populateUsersTable();
    } else if (page === 'equipment-create') {
        populateLocationDropdown();
    }

    document.querySelectorAll('.dropdown').forEach(drop => drop.classList.remove('show'));
    });
});


function initializeUsersForm() {
    const form = document.getElementById('create-user-form');
    if (!form) return;

    form.addEventListener('submit', function (e) {
    e.preventDefault();

    const username = document.getElementById('username').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value;
    const verifyPassword = document.getElementById('verifyPassword').value;
    const role = document.getElementById('role').value;

    if (username && email && password && verifyPassword && role) {
        const existing = JSON.parse(localStorage.getItem('usersData') || '[]');
        existing.push({ username, email, role });
        localStorage.setItem('usersData', JSON.stringify(existing));

        document.getElementById('user-message').innerHTML =
        '<div class="alert alert-success">User "' + username + '" berhasil.</div>';
        this.reset();
    } else {
        document.getElementById('user-message').innerHTML =
        '<div class="alert alert-danger">Semua kolom wajib diisi.</div>';
    }
    });
}


function populateUsersTable() {
    const tableBody = document.getElementById('user-table-body');
    if (!tableBody) return;

    const savedData = JSON.parse(localStorage.getItem('usersData') || '[]');

    tableBody.innerHTML = '';

    if (savedData.length === 0) {
    tableBody.innerHTML = '<tr><td colspan="5" class="text-center">Belum ada data user.</td></tr>';
    return;
    }

    savedData.forEach((user, index) => {
    const row = document.createElement('tr');
    row.innerHTML = `
        <td>${index + 1}</td>
        <td>${user.username}</td>
        <td>${user.email}</td>
        <td>${user.role}</td>
        <td>
            <button class="btn btn-sm btn-warning me-1" onclick="editUser(${index})">Edit</button>
            <button class="btn btn-sm btn-danger" onclick="deleteUser(${index})">Delete</button>
        </td>
    `;
    tableBody.appendChild(row);
    });
}


function populateLocationDropdown() {
    fetch('get_locations.php')
        .then(response => response.json())
        .then(data => {
            const locationSelect = document.getElementById('location');
            locationSelect.innerHTML = '<option value="">-- Choose Locations --</option>';
            data.forEach(location => {
                const option = document.createElement('option');
                option.value = location.id;
                option.textContent = location.nama_location;
                locationSelect.appendChild(option);
            });
        })
        .catch(error => console.error('Error loading locations:', error));
}


// Handle dropdown toggle
document.querySelectorAll('.sidebar .dropdown-toggle').forEach(toggle => {
    toggle.addEventListener('click', function (e) {
    e.preventDefault();
    const parent = this.closest('.dropdown');
    const isShown = parent.classList.contains('show');
    document.querySelectorAll('.sidebar .dropdown').forEach(d => d.classList.remove('show'));
    if (!isShown) parent.classList.add('show');
    });
});

// Collapse dropdown on item click
document.querySelectorAll('.sidebar .dropdown-menu .dropdown-item').forEach(item => {
    item.addEventListener('click', function () {
    this.closest('.dropdown').classList.remove('show');

    // Auto-close sidebar on mobile
    const sidebarMenu = document.getElementById('sidebarMenu');
    const bsCollapse = bootstrap.Collapse.getInstance(sidebarMenu) || new bootstrap.Collapse(sidebarMenu, { toggle: false });
    if (window.innerWidth < 768 && sidebarMenu.classList.contains('show')) {
        bsCollapse.hide();
    }
    });
});