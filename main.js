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
        <form id="equipment-form" action="equipment/insert_equipment.php" method="POST">
            <div class="mb-3">
                <label for="nama_equipment" class="form-label">Equipment Name</label>
                <input type="text" class="form-control" id="nama_equipment" name="nama_equipment" required />
            </div>

            <div class="mb-3">
                <label for="location_id" class="form-label">Equipment Locations</label>
                <select class="form-select" id="location_id" name="location_id" required>
                    <option value="">-- Choose Equipment Locations --</option>
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
    `,

    "equipment-view": `
        <h2>View Equipment</h2>
        <iframe src="equipment/view_equipment.php" width="100%" height="400px" frameborder="0"></iframe>
    `,
    
    // Create Location Form
    "locations-create": `
        <h2>Create Location</h2>
        <form id="location-form" action="locations/insert_location.php" method="POST">
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
        <iframe src="locations/view_locations.php" width="100%" height="400px" frameborder="0"></iframe>
    `,

    // Create Parts Form
    "parts-create": `
        <h2>Create Part</h2>
        <form id="parts-form" action="parts/insert_parts.php" method="POST">
            <div class="mb-3">
                <label for="partName" class="form-label">Parts Name</label>
                <input type="text" class="form-control" id="partName" name="partName" required />
            </div>

            <div class="mb-3">
                <label for="location_part_id" class="form-label">Parts Locations</label>
                <select class="form-select" id="location_part_id" name="location_part_id" required>
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
    `,

    "parts-view": `
        <h2>View Parts</h2>
        <iframe src="parts/view_parts.php" width="100%" height="400px" frameborder="0"></iframe>
    `,

    // Create Location Parts Form
    "locations-parts-create": `
        <h2>Create Location Parts</h2>
        <form id="location-parts-form" action="parts/insert_location_parts.php" method="POST">
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
        <iframe src="parts/view_location_parts.php" width="100%" height="400px" frameborder="0"></iframe>
    `,

    // Create Admin User Form
    "admin-create-user": `
        <h2>Create User</h2>
        <form id="create-user-form" action="users/insert_users.php" method="POST>
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
                <div class="input-group">
                    <input type="password" class="form-control" id="password" name="password" required />
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">Show</button>
                </div>
            </div>

            <div class="mb-3">
                <label for="verifyPassword" class="form-label">Verify Password</label>
                <div class="input-group">
                    <input type="password" class="form-control" id="verifyPassword" name="verifyPassword" required />
                    <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('verifyPassword')">Show</button>
                </div>
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
        <iframe src="users/view_users.php" width="100%" height="400px" frameborder="0"></iframe>
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
        } else if (page === 'equipment-create') {
            populateLocationDropdown();
        } else if (page === 'parts-create') {
            populatePartsLocationDropdown();
            populateEquipmentLocationDropdown();
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

        if (!username || !email || !password || !verifyPassword || !role) {
            document.getElementById('user-message').innerHTML =
                '<div class="alert alert-danger">Semua kolom wajib diisi.</div>';
            return;
        }

        if (password !== verifyPassword) {
            document.getElementById('user-message').innerHTML =
                '<div class="alert alert-danger">Password tidak cocok.</div>';
            return;
        }

        // Tidak menyimpan verifyPassword
        const formData = new FormData();
        formData.append("username", username);
        formData.append("email", email);
        formData.append("password", password);
        formData.append("role", role);

        fetch("users/insert_users.php", {
            method: "POST",
            body: formData
        })
        .then(response => response.text())
        .then(result => {
            document.getElementById('user-message').innerHTML =
                '<div class="alert alert-success">User berhasil dibuat.</div>';
            form.reset();
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('user-message').innerHTML =
                '<div class="alert alert-danger">Terjadi kesalahan.</div>';
        });
    });
}


function togglePassword(fieldId) {
    const input = document.getElementById(fieldId);
    const button = input.nextElementSibling;
    if (input.type === "password") {
        input.type = "text";
        button.textContent = "Hide";
    } else {
        input.type = "password";
        button.textContent = "Show";
    }
}


function populateLocationDropdown() {
    fetch('locations/get_locations.php')
        .then(response => response.json())
        .then(data => {
            const locationSelect = document.getElementById('location_id');
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


function populatePartsLocationDropdown() {
    fetch('parts/get_location_parts.php')
        .then(response => response.json())
        .then(data => {
            const locationSelect = document.getElementById('location_part_id');
            if (!locationSelect) return;

            locationSelect.innerHTML = '<option value="">-- Choose Parts Locations --</option>';
            data.forEach(loc => {
                const opt = document.createElement('option');
                opt.value = loc.id;
                opt.textContent = loc.nama_location_part;
                locationSelect.appendChild(opt);
            });
        })
        .catch(error => console.error('Gagal load location_parts:', error));
}


function populateEquipmentLocationDropdown() {
    fetch('parts/get_equipment_with_location.php')
        .then(response => response.json())
        .then(data => {
            const select = document.getElementById('equipmentLocation');
            if (!select) return;

            select.innerHTML = '<option value="">-- Choose Equipment - Locations --</option>';
            data.forEach(item => {
                const opt = document.createElement('option');
                opt.value = item.id;
                opt.textContent = `${item.nama_equipment} - ${item.nama_location}`; // Ganti ini
                select.appendChild(opt);
            });
        })
        .catch(error => console.error('Gagal load equipment - location:', error));
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