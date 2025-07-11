const locationsData = []
const locationPartsData = []
const usersData = []

// Content templates
const content = {
  dashboard: `
    <div class="fade-in">
      <div class="dashboard-container">
        <div class="welcome-section">
          <div class="welcome-content">
            <h1>Welcome to CMMS Dashboard</h1>
            <p>Computerized Maintenance Management System for Dover Chemical</p>
          </div>
          <div class="welcome-illustration">
            <div class="illustration-bg">
              <i class="fas fa-industry"></i>
            </div>
          </div>
        </div>
      </div>
    </div>
  `,

  "equipment-create": `
    <div class="fade-in">
      <div class="form-container">
        <h4>
          <i class="fas fa-plus-circle text-primary equipment"></i>
          Create Equipment
        </h4>
        <form id="equipment-form" action="equipment/insert_equipment.php" method="POST">
          <div class="row g-4">
            <div class="col-md-6">
              <label for="nama_equipment" class="form-label">Equipment Name</label>
              <input type="text" class="form-control" id="nama_equipment" name="nama_equipment" required placeholder="Enter equipment name" />
            </div>
            <div class="col-md-6">
              <label for="location_id" class="form-label">Equipment Locations</label>
              <select class="form-select" id="location_id" name="location_id" required>
                <option value="">-- Choose Equipment Locations --</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="status" class="form-label">Status</label>
              <select class="form-select" id="status" name="status" required>
                <option value="">-- Choose Status --</option>
                <option value="Aktif">Active</option>
                <option value="Tidak Aktif">Inactive</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="keterangan" class="form-label">Notes</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional notes (optional)"></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-save me-2"></i> Save Equipment
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-2"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  `,

  "equipment-view": `
    <div class="fade-in">
      <div class="iframe-container">
        <h4>
          <i class="fas fa-list text-primary equipment"></i>
          Equipment List
        </h4>
        <iframe src="equipment/view_equipment.php" width="100%" height="600px"></iframe>
      </div>
    </div>
  `,

  "locations-create": `
    <div class="fade-in">
      <div class="form-container">
        <h4>
          <i class="fas fa-plus-circle text-success locations"></i>
          Create Locations
        </h4>
        <form id="location-form">
          <div class="row g-4">
            <div class="col-md-8">
              <label for="locationName" class="form-label">Locations Name</label>
              <input type="text" class="form-control" id="locationName" name="nama_location" required placeholder="Enter locations name" />
            </div>
            <div class="col-12">
              <label for="description" class="form-label">Description</label>
              <textarea class="form-control" id="description" name="keterangan" rows="4" placeholder="Locations description (optional)"></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-success">
                  <i class="fas fa-save me-2"></i> Save Location
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-2"></i> Reset
                </button>
              </div>
            </div>
          </div>
          <div id="location-message" class="mt-3"></div>
        </form>
      </div>
    </div>
  `,

  "locations-view": `
    <div class="fade-in">
      <div class="iframe-container">
        <h4>
          <i class="fas fa-list text-success locations"></i>
          Locations List
        </h4>
        <iframe src="locations/view_locations.php" width="100%" height="600px"></iframe>
      </div>
    </div>
  `,

  "parts-create": `
    <div class="fade-in">
      <div class="form-container">
        <h4>
          <i class="fas fa-plus-circle text-info"></i>
          Create Parts
        </h4>
        <form id="parts-form" action="parts/insert_parts.php" method="POST">
          <div class="row g-4">
            <div class="col-md-6">
              <label for="partName" class="form-label">Parts Name</label>
              <input type="text" class="form-control" id="partName" name="partName" required placeholder="Enter parts name" />
            </div>
            <div class="col-md-6">
              <label for="location_part_id" class="form-label">Parts Locations</label>
              <select class="form-select" id="location_part_id" name="location_part_id" required>
                <option value="">-- Choose Parts Location --</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="equipmentLocation" class="form-label">Equipment - Locations</label>
              <select class="form-select" id="equipmentLocation" name="equipmentLocation" required>
                <option value="">-- Choose Equipment - Locations --</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="keterangan" class="form-label">Notes</label>
              <textarea class="form-control" id="keterangan" name="keterangan" rows="3" placeholder="Additional notes (optional)"></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-warning">
                  <i class="fas fa-save me-2"></i> Save Part
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-2"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  `,

  "parts-view": `
    <div class="fade-in">
      <div class="iframe-container">
        <h4>
          <i class="fas fa-list text-info"></i>
          Parts List
        </h4>
        <iframe src="parts/view_parts.php" width="100%" height="600px"></iframe>
      </div>
    </div>
  `,

  "locations-parts-create": `
    <div class="fade-in">
      <div class="form-container">
        <h4>
          <i class="fas fa-plus-circle text-info locations-parts"></i>
          Create Locations Parts
        </h4>
        <form id="location-parts-form" action="parts/insert_location_parts.php" method="POST">
          <div class="row g-4">
            <div class="col-md-8">
              <label for="locationspartsName" class="form-label">Parts Locations Name</label>
              <input type="text" class="form-control" id="locationspartsName" name="nama_location_part" required placeholder="Enter parts location name" />
            </div>
            <div class="col-12">
              <label for="descriptionlocationsParts" class="form-label">Description</label>
              <textarea class="form-control" id="descriptionlocationsParts" name="keterangan" rows="4" placeholder="Location description (optional)"></textarea>
            </div>
            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-info">
                  <i class="fas fa-save me-2"></i> Save Location Parts
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-2"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  `,

  "locations-parts-view": `
    <div class="fade-in">
      <div class="iframe-container">
        <h4>
          <i class="fas fa-list text-info locations-parts"></i>
          Locations Parts List
        </h4>
        <iframe src="parts/view_location_parts.php" width="100%" height="600px"></iframe>
      </div>
    </div>
  `,

  "admin-create-user": `
    <div class="fade-in">
      <div class="form-container">
        <h4>
          <i class="fas fa-user-plus text-primary users"></i>
          Create Users
        </h4>
        <form id="create-user-form" action="users/insert_users.php" method="POST">
          <div class="row g-4">
            <div class="col-md-6">
              <label for="username" class="form-label">Username</label>
              <input type="text" class="form-control" id="username" name="username" required placeholder="Enter username" />
            </div>
            <div class="col-md-6">
              <label for="email" class="form-label">Email Address</label>
              <input type="email" class="form-control" id="email" name="email" required placeholder="Enter email address" />
            </div>
            <div class="col-md-6">
              <label for="password" class="form-label">Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="password" name="password" required placeholder="Enter password" />
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('password')">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <label for="verifyPassword" class="form-label">Verify Password</label>
              <div class="input-group">
                <input type="password" class="form-control" id="verifyPassword" name="verifyPassword" required placeholder="Confirm password" />
                <button type="button" class="btn btn-outline-secondary" onclick="togglePassword('verifyPassword')">
                  <i class="fas fa-eye"></i>
                </button>
              </div>
            </div>
            <div class="col-md-6">
              <label for="role" class="form-label">Users Role</label>
              <select class="form-select" id="role" name="role" required>
                <option value="">-- Select Role --</option>
                <option value="Admin">Admin (All Permissions)</option>
                <option value="Dispatch">Dispatch (Create Only)</option>
                <option value="Technician">Technician (Create or Close Only)</option>
                <option value="Viewer">Viewer (View Only)</option>
              </select>
            </div>
            <div class="col-12">
              <div class="d-flex gap-3">
                <button type="submit" class="btn btn-primary">
                  <i class="fas fa-user-plus me-2"></i> Create User
                </button>
                <button type="reset" class="btn btn-outline-secondary">
                  <i class="fas fa-undo me-2"></i> Reset
                </button>
              </div>
            </div>
          </div>
        </form>
        <div id="user-message" class="mt-3"></div>
      </div>
    </div>
  `,

  "admin-view-user": `
    <div class="fade-in">
      <div class="iframe-container">
        <h4>
          <i class="fas fa-users text-primary users"></i>
          Users Management
        </h4>
        <iframe src="users/view_users.php" width="100%" height="600px"></iframe>
      </div>
    </div>
  `,

  logout: `
    <div class="fade-in">
      <div class="form-container text-center">
        <div class="mb-5">
          <div class="action-icon users-bg mx-auto mb-4" style="width: 100px; height: 100px; font-size: 3rem;">
            <i class="fas fa-sign-out-alt"></i>
          </div>
          <h2 class="mb-3">Logging Out...</h2>
          <p class="lead text-muted mb-2">Please wait while we log you out safely.</p>
        </div>
      </div>
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

// Initialize app
document.addEventListener("DOMContentLoaded", () => {
  // Mobile sidebar toggle
  const mobileSidebarToggle = document.getElementById("mobileSidebarToggle")
  const sidebarToggle = document.getElementById("sidebarToggle")
  const sidebar = document.getElementById("sidebar")
  const mobileOverlay = document.getElementById("mobileOverlay")

  function toggleSidebar() {
    sidebar.classList.toggle("active")
    mobileOverlay.classList.toggle("active")
  }

  function closeSidebar() {
    sidebar.classList.remove("active")
    mobileOverlay.classList.remove("active")
  }

  if (mobileSidebarToggle) {
    mobileSidebarToggle.addEventListener("click", toggleSidebar)
  }

  if (sidebarToggle) {
    sidebarToggle.addEventListener("click", closeSidebar)
  }

  if (mobileOverlay) {
    mobileOverlay.addEventListener("click", closeSidebar)
  }

  // Navigation click handlers
  document.querySelectorAll("[data-page]").forEach((link) => {
    link.addEventListener("click", function (e) {
      e.preventDefault()

      const page = this.getAttribute("data-page")

      // Handle logout specially
      if (page === "logout") {
        handleLogout()
        return
      }

      // Remove active class from all menu items
      document.querySelectorAll(".menu-item").forEach((el) => el.classList.remove("active"))

      // Add active class to clicked item (if it's a menu item)
      if (this.classList.contains("menu-item")) {
        this.classList.add("active")
      }

      document.getElementById("page-content").innerHTML = content[page] || "<h2>Page Not Found</h2>"

      // Update breadcrumb
      updateBreadcrumb(page)

      // Initialize all forms with the new comprehensive function
      setTimeout(() => {
        initializeAllForms()
      }, 100)

      // Close mobile sidebar
      closeSidebar()
    })
  })
})

function handleLogout() {
  // Show logout content
  document.getElementById("page-content").innerHTML = content.logout
  updateBreadcrumb("logout")

  // Perform logout
  fetch("auth/logout.php", {
    method: "POST",
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        showToast("Logged out successfully", "success")
        setTimeout(() => {
          window.location.href = "login.html"
        }, 2000)
      } else {
        showToast("Logout failed", "danger")
      }
    })
    .catch((error) => {
      console.error("Logout error:", error)
      // Force redirect even if logout request fails
      setTimeout(() => {
        window.location.href = "login.html"
      }, 2000)
    })
}

// Comprehensive form initialization function
function initializeAllForms() {
  const currentPage = document.querySelector(".menu-item.active")?.getAttribute("data-page")

  switch (currentPage) {
    case "locations-create":
      initializeLocationForm()
      break
    case "equipment-create":
      initializeEquipmentForm()
      break
    case "parts-create":
      initializePartsForm()
      break
    case "locations-parts-create":
      initializeLocationPartsForm()
      break
    case "admin-create-user":
      initializeUsersForm()
      break
  }
}

function initializeLocationForm() {
  const form = document.getElementById("location-form")
  if (!form) return

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const nama_location = document.getElementById("locationName").value.trim()
    const keterangan = document.getElementById("description").value.trim()

    if (!nama_location) {
      showToast("Nama lokasi wajib diisi.", "danger")
      return
    }

    const formData = new FormData()
    formData.append("nama_location", nama_location)
    formData.append("keterangan", keterangan)

    fetch("locations/insert_location.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.success) {
          showToast(result.message, "success")
          form.reset()
        } else {
          showToast(result.message, "danger")
        }
      })
      .catch((err) => {
        showToast("Terjadi kesalahan koneksi.", "danger")
        console.error(err)
      })
  })
}

function initializeEquipmentForm() {
  const form = document.getElementById("equipment-form")
  if (!form) return

  // Populate location dropdown
  populateLocationDropdown()

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const nama_equipment = document.getElementById("nama_equipment").value.trim()
    const location_id = document.getElementById("location_id").value
    const status = document.getElementById("status").value
    const keterangan = document.getElementById("keterangan").value.trim()

    if (!nama_equipment || !location_id || !status) {
      showToast("Nama equipment, lokasi, dan status wajib diisi.", "danger")
      return
    }

    const formData = new FormData()
    formData.append("nama_equipment", nama_equipment)
    formData.append("location_id", location_id)
    formData.append("status", status)
    formData.append("keterangan", keterangan)

    fetch("equipment/insert_equipment.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.success) {
          showToast(result.message, "success")
          form.reset()
        } else {
          showToast(result.message, "danger")
        }
      })
      .catch((err) => {
        showToast("Terjadi kesalahan koneksi.", "danger")
        console.error(err)
      })
  })
}

function initializePartsForm() {
  const form = document.getElementById("parts-form")
  if (!form) return

  // Populate dropdowns
  populatePartsLocationDropdown()
  populateEquipmentLocationDropdown()

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const partName = document.getElementById("partName").value.trim()
    const location_part_id = document.getElementById("location_part_id").value
    const equipmentLocation = document.getElementById("equipmentLocation").value
    const keterangan = document.getElementById("keterangan").value.trim()

    if (!partName || !location_part_id || !equipmentLocation) {
      showToast("Nama parts, lokasi parts, dan equipment wajib diisi.", "danger")
      return
    }

    const formData = new FormData()
    formData.append("partName", partName)
    formData.append("location_part_id", location_part_id)
    formData.append("equipmentLocation", equipmentLocation)
    formData.append("keterangan", keterangan)

    fetch("parts/insert_parts.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.success) {
          showToast(result.message, "success")
          form.reset()
        } else {
          showToast(result.message, "danger")
        }
      })
      .catch((err) => {
        showToast("Terjadi kesalahan koneksi.", "danger")
        console.error(err)
      })
  })
}

function initializeLocationPartsForm() {
  const form = document.getElementById("location-parts-form")
  if (!form) return

  form.addEventListener("submit", (e) => {
    e.preventDefault()

    const nama_location_part = document.getElementById("locationspartsName").value.trim()
    const keterangan = document.getElementById("descriptionlocationsParts").value.trim()

    if (!nama_location_part) {
      showToast("Nama lokasi parts wajib diisi.", "danger")
      return
    }

    const formData = new FormData()
    formData.append("nama_location_part", nama_location_part)
    formData.append("keterangan", keterangan)

    fetch("parts/insert_location_parts.php", {
      method: "POST",
      body: formData,
    })
      .then((res) => res.json())
      .then((result) => {
        if (result.success) {
          showToast(result.message, "success")
          form.reset()
        } else {
          showToast(result.message, "danger")
        }
      })
      .catch((err) => {
        showToast("Terjadi kesalahan koneksi.", "danger")
        console.error(err)
      })
  })
}

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
      showToast("Semua kolom wajib diisi.", "danger")
      return
    }

    if (password !== verifyPassword) {
      showToast("Password tidak cocok.", "danger")
      return
    }

    const formData = new FormData()
    formData.append("username", username)
    formData.append("email", email)
    formData.append("password", password)
    formData.append("role", role)

    fetch("users/insert_users.php", {
      method: "POST",
      body: formData,
    })
      .then((response) => response.json())
      .then((result) => {
        if (result.success) {
          showToast(result.message, "success")
          form.reset()
        } else {
          showToast(result.message, "danger")
        }
      })
      .catch((error) => {
        console.error("Error:", error)
        showToast("Terjadi kesalahan koneksi.", "danger")
      })
  })
}

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
        opt.textContent = `${item.nama_equipment} - ${item.nama_location}`
        select.appendChild(opt)
      })
    })
    .catch((error) => console.error("Gagal load equipment - location:", error))
}

function showToast(message, type = "success") {
  const toastContainer = document.getElementById("toast-container")
  const toastId = "toast-" + Date.now()

  const toast = document.createElement("div")
  toast.className = `toast align-items-center text-white bg-${type} border-0 show fade`
  toast.id = toastId
  toast.setAttribute("role", "alert")
  toast.setAttribute("aria-live", "assertive")
  toast.setAttribute("aria-atomic", "true")

  toast.innerHTML = `
    <div class="d-flex">
      <div class="toast-body">${message}</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" aria-label="Close"></button>
    </div>
  `

  toastContainer.appendChild(toast)

  // Auto-remove after fade out
  setTimeout(() => toast.remove(), 4000)
}

function updateMenuForRole(role) {
  const menuItems = document.querySelectorAll(".menu-item")

  if (role === "Viewer") {
    // Hide create/add menu items for viewers
    menuItems.forEach((item) => {
      const page = item.getAttribute("data-page")
      if (page && (page.includes("create") || page.includes("add"))) {
        item.style.display = "none"
      }
    })

    // Hide user management for viewers
    const userManagementSection = document.querySelector(".menu-section:last-of-type")
    if (userManagementSection) {
      userManagementSection.style.display = "none"
    }
  } else if (role === "Dispatch" || role === "Technician") {
    // Hide user management for Dispatch and Technician
    const userManagementSection = document.querySelector(".menu-section:last-of-type")
    if (userManagementSection) {
      userManagementSection.style.display = "none"
    }
  }
  // Admin role has access to everything, so no restrictions
}