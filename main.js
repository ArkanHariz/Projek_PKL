const locationsData = []
const locationPartsData = []
const usersData = []

function addLocationToTable(name, description) {
  const tableBody = document.getElementById("location-table-body")
  if (!tableBody) return

  // Hapus baris placeholder jika ada
  if (tableBody.children.length === 1 && tableBody.children[0].cells[0].colSpan === 3) {
    tableBody.innerHTML = ""
  }

  const newRow = document.createElement("tr")
  newRow.innerHTML = `
        <td>${tableBody.children.length + 1}</td>
        <td>${name}</td>
        <td>${description}</td>
    `
  tableBody.appendChild(newRow)
}

const content = {
  dashboard: `
        <div class="dashboard-container fade-in">
            <div class="welcome-card">
                <div class="welcome-content">
                    <h1><i class="fas fa-tachometer-alt"></i> Dashboard</h1>
                    <p class="lead">Welcome to Dover Chemical CMMS System</p>
                    <p>Manage your equipment, locations, parts, and users efficiently.</p>
                </div>
                <div class="welcome-stats">
                    <div class="stat-card">
                        <i class="fas fa-cogs"></i>
                        <div>
                            <h3>Equipment</h3>
                            <p>Manage Assets</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-map-marker-alt"></i>
                        <div>
                            <h3>Locations</h3>
                            <p>Track Places</p>
                        </div>
                    </div>
                    <div class="stat-card">
                        <i class="fas fa-puzzle-piece"></i>
                        <div>
                            <h3>Parts</h3>
                            <p>Inventory Control</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `,

  "equipment-create": `
        <div class="form-container fade-in">
            <h2><i class="fas fa-plus-circle"></i> Create Equipment</h2>
            <form id="equipment-form" action="equipment/insert_equipment.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="nama_equipment" class="form-label">Equipment Name</label>
                        <input type="text" class="form-control" id="nama_equipment" name="nama_equipment" required placeholder="Enter equipment name" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_id" class="form-label">Equipment Location</label>
                        <select class="form-select" id="location_id" name="location_id" required>
                            <option value="">-- Choose Equipment Location --</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status" required>
                            <option value="">-- Choose Status --</option>
                            <option value="Aktif">Active</option>
                            <option value="Tidak Aktif">Inactive</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="keterangan" class="form-label">Notes</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional notes (optional)"></textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Equipment
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    `,

  "equipment-view": `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-list"></i> Equipment List</h2>
            </div>
            <iframe src="equipment/view_equipment.php" width="100%" height="600px" class="rounded shadow"></iframe>
        </div>
    `,

  "locations-create": `
        <div class="form-container fade-in">
            <h2><i class="fas fa-plus-circle"></i> Create Location</h2>
            <form id="location-form" action="locations/insert_location.php" method="POST">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="locationName" class="form-label">Location Name</label>
                        <input type="text" class="form-control" id="locationName" name="nama_location" required placeholder="Enter location name" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="description" class="form-label">Description</label>
                    <textarea class="form-control" id="description" name="keterangan" rows="4" placeholder="Location description (optional)"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Location
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    `,

  "locations-view": `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-list"></i> Locations List</h2>
            </div>
            <iframe src="locations/view_locations.php" width="100%" height="600px" class="rounded shadow"></iframe>
        </div>
    `,

  "parts-create": `
        <div class="form-container fade-in">
            <h2><i class="fas fa-plus-circle"></i> Create Part</h2>
            <form id="parts-form" action="parts/insert_parts.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="partName" class="form-label">Part Name</label>
                        <input type="text" class="form-control" id="partName" name="partName" required placeholder="Enter part name" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="location_part_id" class="form-label">Parts Location</label>
                        <select class="form-select" id="location_part_id" name="location_part_id" required>
                            <option value="">-- Choose Parts Location --</option>
                        </select>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="equipmentLocation" class="form-label">Equipment - Location</label>
                        <select class="form-select" id="equipmentLocation" name="equipmentLocation" required>
                            <option value="">-- Choose Equipment - Location --</option>
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="keterangan" class="form-label">Notes</label>
                        <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional notes (optional)"></textarea>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Part
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    `,

  "parts-view": `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-list"></i> Parts List</h2>
            </div>
            <iframe src="parts/view_parts.php" width="100%" height="600px" class="rounded shadow"></iframe>
        </div>
    `,

  "locations-parts-create": `
        <div class="form-container fade-in">
            <h2><i class="fas fa-plus-circle"></i> Create Location Parts</h2>
            <form id="location-parts-form" action="parts/insert_location_parts.php" method="POST">
                <div class="row">
                    <div class="col-md-8 mb-3">
                        <label for="locationspartsName" class="form-label">Parts Location Name</label>
                        <input type="text" class="form-control" id="locationspartsName" name="nama_location_part" required placeholder="Enter parts location name" />
                    </div>
                </div>
                <div class="mb-3">
                    <label for="descriptionlocationsParts" class="form-label">Description</label>
                    <textarea class="form-control" id="descriptionlocationsParts" name="keterangan" rows="4" placeholder="Location description (optional)"></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Location Parts
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    `,

  "locations-parts-view": `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-list"></i> Location Parts List</h2>
            </div>
            <iframe src="parts/view_location_parts.php" width="100%" height="600px" class="rounded shadow"></iframe>
        </div>
    `,

  "admin-create-user": `
        <div class="form-container fade-in">
            <h2><i class="fas fa-user-plus"></i> Create User</h2>
            <form id="create-user-form" action="users/insert_users.php" method="POST">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="username" class="form-label">Username</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="Enter username" />
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email address" />
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password" />
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="verifyPassword" class="form-label">Verify Password</label>
                        <div class="input-group">
                            <input type="password" class="form-control" id="verifyPassword" name="verifyPassword" required placeholder="Confirm password" />
                            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('verifyPassword')">
                                <i class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="role" class="form-label">User Role</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="">-- Select Role --</option>
                            <option value="Admin">Admin (All Permissions)</option>
                            <option value="Dispatch">Dispatch (Create Only)</option>
                            <option value="Technician">Technician (Create or Close Only)</option>
                            <option value="Viewer">Viewer (View Only)</option>
                        </select>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-user-plus"></i> Create User
                    </button>
                    <button type="reset" class="btn btn-outline-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
            <div id="user-message" class="mt-3"></div>
        </div>
    `,

  "admin-view-user": `
        <div class="fade-in">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h2><i class="fas fa-users"></i> User Management</h2>
            </div>
            <iframe src="users/view_users.php" width="100%" height="600px" class="rounded shadow"></iframe>
        </div>
    `,

  logout: `
        <div class="form-container fade-in text-center">
            <div class="mb-4">
                <i class="fas fa-sign-out-alt fa-4x text-muted mb-3"></i>
                <h2>Logout Successful</h2>
                <p class="lead">You have been logged out successfully.</p>
                <p>Thank you for using Dover Chemical CMMS System!</p>
            </div>
            <button class="btn btn-primary" onclick="window.location.reload()">
                <i class="fas fa-sign-in-alt"></i> Login Again
            </button>
        </div>
    `,
}

// Update breadcrumb function
function updateBreadcrumb(page) {
  const breadcrumbMap = {
    dashboard: "Dashboard",
    "equipment-create": "Create Equipment",
    "equipment-view": "View Equipment",
    "locations-create": "Create Location",
    "locations-view": "View Locations",
    "parts-create": "Create Parts",
    "parts-view": "View Parts",
    "locations-parts-create": "Create Location Parts",
    "locations-parts-view": "View Location Parts",
    "admin-create-user": "Create User",
    "admin-view-user": "View Users",
    logout: "Logout",
  }

  const currentPageElement = document.getElementById("current-page")
  if (currentPageElement) {
    currentPageElement.textContent = breadcrumbMap[page] || "Unknown Page"
  }
}

// Navigation click handler with breadcrumb update
document.querySelectorAll("[data-page]").forEach((link) => {
  link.addEventListener("click", function (e) {
    e.preventDefault()
    document.querySelectorAll(".nav-link, .dropdown-item").forEach((el) => el.classList.remove("active"))
    this.classList.add("active")

    const page = this.getAttribute("data-page")
    document.getElementById("page-content").innerHTML = content[page] || "<h2>Not Found</h2>"

    // Update breadcrumb
    updateBreadcrumb(page)

    // Run additional scripts
    if (page === "admin-create-user") {
      initializeUsersForm()
    } else if (page === "equipment-create") {
      populateLocationDropdown()
    } else if (page === "parts-create") {
      populatePartsLocationDropdown()
      populateEquipmentLocationDropdown()
    }

    // Close all dropdowns
    document.querySelectorAll(".dropdown").forEach((drop) => drop.classList.remove("show"))

    // Close mobile menu
    const offcanvas = bootstrap.Offcanvas.getInstance(document.getElementById("sidebarOffcanvas"))
    if (offcanvas) {
      offcanvas.hide()
    }
  })
})

function initializeUsersForm() {
  const form = document.getElementById("create-user-form")
  if (!form) return

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const username = document.getElementById("username").value.trim()
    const email = document.getElementById("email").value.trim()
    const password = document.getElementById("password").value
    const verifyPassword = document.getElementById("verifyPassword").value
    const role = document.getElementById("role").value

    if (!username || !email || !password || !verifyPassword || !role) {
      document.getElementById("user-message").innerHTML =
        '<div class="alert alert-danger">Semua kolom wajib diisi.</div>'
      return
    }

    if (password !== verifyPassword) {
      document.getElementById("user-message").innerHTML = '<div class="alert alert-danger">Password tidak cocok.</div>'
      return
    }

    // Tidak menyimpan verifyPassword
    const formData = new FormData()
    formData.append("username", username)
    formData.append("email", email)
    formData.append("password", password)
    formData.append("role", role)

    fetch("users/insert_users.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.text())
      .then((result) => {
        document.getElementById("user-message").innerHTML =
          '<div class="alert alert-success">User berhasil dibuat.</div>'
        form.reset()
      })
      .catch((error) => {
        console.error("Error:", error)
        document.getElementById("user-message").innerHTML = '<div class="alert alert-danger">Terjadi kesalahan.</div>'
      })
  })
}

// Update toggle password function to change icon
function togglePassword(fieldId) {
  const input = document.getElementById(fieldId)
  const button = input.nextElementSibling
  const icon = button.querySelector("i")

  if (input.type === "password") {
    input.type = "text"
    icon.className = "fas fa-eye-slash"
  } else {
    input.type = "password"
    icon.className = "fas fa-eye"
  }
}

function populateLocationDropdown() {
  fetch("locations/get_locations.php")
    .then((response) => response.json())
    .then((data) => {
      const locationSelect = document.getElementById("location_id")
      locationSelect.innerHTML = '<option value="">-- Choose Locations --</option>'
      data.forEach((location) => {
        const option = document.createElement("option")
        option.value = location.id
        option.textContent = location.nama_location
        locationSelect.appendChild(option)
      })
    })
    .catch((error) => console.error("Error loading locations:", error))
}

function populatePartsLocationDropdown() {
  fetch("parts/get_location_parts.php")
    .then((response) => response.json())
    .then((data) => {
      const locationSelect = document.getElementById("location_part_id")
      if (!locationSelect) return

      locationSelect.innerHTML = '<option value="">-- Choose Parts Locations --</option>'
      data.forEach((loc) => {
        const opt = document.createElement("option")
        opt.value = loc.id
        opt.textContent = loc.nama_location_part
        locationSelect.appendChild(opt)
      })
    })
    .catch((error) => console.error("Gagal load location_parts:", error))
}

function populateEquipmentLocationDropdown() {
  fetch("parts/get_equipment_with_location.php")
    .then((response) => response.json())
    .then((data) => {
      const select = document.getElementById("equipmentLocation")
      if (!select) return

      select.innerHTML = '<option value="">-- Choose Equipment - Locations --</option>'
      data.forEach((item) => {
        const opt = document.createElement("option")
        opt.value = item.id
        opt.textContent = `${item.nama_equipment} - ${item.nama_location}` // Ganti ini
        select.appendChild(opt)
      })
    })
    .catch((error) => console.error("Gagal load equipment - location:", error))
}

// Handle dropdown toggle
document.querySelectorAll(".sidebar .dropdown-toggle").forEach((toggle) => {
  toggle.addEventListener("click", function (e) {
    e.preventDefault()
    const parent = this.closest(".dropdown")
    const isShown = parent.classList.contains("show")
    document.querySelectorAll(".sidebar .dropdown").forEach((d) => d.classList.remove("show"))
    if (!isShown) parent.classList.add("show")
  })
})

// Collapse dropdown on item click
document.querySelectorAll(".sidebar .dropdown-menu .dropdown-item").forEach((item) => {
  item.addEventListener("click", function () {
    this.closest(".dropdown").classList.remove("show")

    // Auto-close sidebar on mobile
    const sidebarMenu = document.getElementById("sidebarMenu")
    const bsCollapse =
      bootstrap.Collapse.getInstance(sidebarMenu) || new bootstrap.Collapse(sidebarMenu, { toggle: false })
    if (window.innerWidth < 768 && sidebarMenu.classList.contains("show")) {
      bsCollapse.hide()
    }
  })
})