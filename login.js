// Notification System
class NotificationSystem {
  constructor() {
    this.container = document.getElementById("notificationContainer")
    this.notifications = []
  }

  show(type, title, message, duration = 5000) {
    const notification = this.createNotification(type, title, message, duration)
    this.container.appendChild(notification)
    this.notifications.push(notification)

    // Trigger animation
    setTimeout(() => {
      notification.classList.add("show")
    }, 100)

    // Auto remove
    if (duration > 0) {
      setTimeout(() => {
        this.remove(notification)
      }, duration)
    }

    return notification
  }

  createNotification(type, title, message, duration) {
    const notification = document.createElement("div")
    notification.className = `notification ${type}`

    const icons = {
      success: "fas fa-check-circle",
      error: "fas fa-times-circle",
      warning: "fas fa-exclamation-triangle",
      info: "fas fa-info-circle",
    }

    notification.innerHTML = `
            <div class="notification-icon">
                <i class="${icons[type]}"></i>
            </div>
            <div class="notification-content">
                <div class="notification-title">${title}</div>
                <div class="notification-message">${message}</div>
            </div>
            <button class="notification-close" onclick="notificationSystem.remove(this.parentElement)">
                <i class="fas fa-times"></i>
            </button>
        `

    return notification
  }

  remove(notification) {
    notification.classList.add("hide")
    setTimeout(() => {
      if (notification.parentElement) {
        notification.parentElement.removeChild(notification)
      }
      const index = this.notifications.indexOf(notification)
      if (index > -1) {
        this.notifications.splice(index, 1)
      }
    }, 400)
  }

  success(title, message, duration = 5000) {
    return this.show("success", title, message, duration)
  }

  error(title, message, duration = 7000) {
    return this.show("error", title, message, duration)
  }

  warning(title, message, duration = 6000) {
    return this.show("warning", title, message, duration)
  }

  info(title, message, duration = 5000) {
    return this.show("info", title, message, duration)
  }

  clear() {
    this.notifications.forEach((notification) => {
      this.remove(notification)
    })
  }
}

// Initialize notification system
const notificationSystem = new NotificationSystem()

// DOM Elements
const loginForm = document.getElementById("loginForm")
const loginButton = document.querySelector(".btn-login")
const loadingOverlay = document.getElementById("loadingOverlay")
const usernameInput = document.getElementById("username")
const passwordInput = document.getElementById("password")

// Initialize page
document.addEventListener("DOMContentLoaded", () => {
  // Focus on username input
  usernameInput.focus()

  // Add input event listeners for validation
  usernameInput.addEventListener("input", validateForm)
  passwordInput.addEventListener("input", validateForm)

  // Add form submit handler
  loginForm.addEventListener("submit", handleLogin)

  // Add enter key handler
  document.addEventListener("keypress", (e) => {
    if (e.key === "Enter" && !loginButton.classList.contains("loading")) {
      handleLogin(e)
    }
  })

  // Show welcome notification
  setTimeout(() => {
    notificationSystem.info("Welcome!", "Please enter your credentials to continue")
  }, 1000)
})

// Toggle password visibility
function togglePassword() {
  const passwordField = document.getElementById("password")
  const toggleIcon = document.getElementById("passwordToggleIcon")

  if (passwordField.type === "password") {
    passwordField.type = "text"
    toggleIcon.className = "fas fa-eye-slash"
  } else {
    passwordField.type = "password"
    toggleIcon.className = "fas fa-eye"
  }
}

// Validate form
function validateForm() {
  const username = usernameInput.value.trim()
  const password = passwordInput.value.trim()

  if (username && password) {
    loginButton.disabled = false
    loginButton.style.opacity = "1"
  } else {
    loginButton.disabled = true
    loginButton.style.opacity = "0.7"
  }
}

// Handle login
function handleLogin(e) {
  e.preventDefault()

  const username = usernameInput.value.trim()
  const password = passwordInput.value.trim()

  // Validation
  if (!username) {
    notificationSystem.warning("Validation Error", "Please enter your username or email")
    usernameInput.focus()
    return
  }

  if (!password) {
    notificationSystem.warning("Validation Error", "Please enter your password")
    passwordInput.focus()
    return
  }

  if (password.length < 6) {
    notificationSystem.warning("Validation Error", "Password must be at least 6 characters long")
    passwordInput.focus()
    return
  }

  // Show loading state
  showLoading()

  // Create form data
  const formData = new FormData()
  formData.append("username", username)
  formData.append("password", password)
  formData.append("remember", document.getElementById("remember").checked)

  // Submit login request
  fetch("auth/login_process.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => response.json())
    .then((data) => {
      hideLoading()

      if (data.success) {
        notificationSystem.success("Login Successful!", `Welcome back, ${data.user.username}!`)

        // Redirect after success
        setTimeout(() => {
          window.location.href = "main.html"
        }, 2000)
      } else {
        notificationSystem.error("Login Failed", data.message || "Invalid credentials")

        // Clear password field
        passwordInput.value = ""
        passwordInput.focus()
      }
    })
    .catch((error) => {
      hideLoading()
      console.error("Login error:", error)
      notificationSystem.error("Connection Error", "Unable to connect to server. Please try again.")
    })
}

// Show loading state
function showLoading() {
  loginButton.classList.add("loading")
  loadingOverlay.classList.add("active")

  // Disable form inputs
  usernameInput.disabled = true
  passwordInput.disabled = true
}

// Hide loading state
function hideLoading() {
  loginButton.classList.remove("loading")
  loadingOverlay.classList.remove("active")

  // Enable form inputs
  usernameInput.disabled = false
  passwordInput.disabled = false
}

// Show forgot password info
function showForgotPassword() {
  notificationSystem.info("Password Recovery", "Please contact your system administrator to reset your password.")
}

// Show register info
function showRegisterInfo() {
  notificationSystem.info(
    "Account Registration",
    "New accounts must be created by the system administrator. Please contact IT support.",
  )
}

// Input animations
usernameInput.addEventListener("focus", function () {
  this.parentElement.classList.add("focused")
})

usernameInput.addEventListener("blur", function () {
  if (!this.value) {
    this.parentElement.classList.remove("focused")
  }
})

passwordInput.addEventListener("focus", function () {
  this.parentElement.classList.add("focused")
})

passwordInput.addEventListener("blur", function () {
  if (!this.value) {
    this.parentElement.classList.remove("focused")
  }
})

// Prevent form submission on enter in input fields (handled by global enter handler)
usernameInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault()
    passwordInput.focus()
  }
})

passwordInput.addEventListener("keypress", (e) => {
  if (e.key === "Enter") {
    e.preventDefault()
    if (!loginButton.classList.contains("loading")) {
      handleLogin(e)
    }
  }
})

// Add input validation styling
function addInputValidation() {
  const inputs = [usernameInput, passwordInput]

  inputs.forEach((input) => {
    input.addEventListener("blur", function () {
      if (this.value.trim()) {
        this.classList.add("valid")
        this.classList.remove("invalid")
      } else if (this.hasAttribute("required")) {
        this.classList.add("invalid")
        this.classList.remove("valid")
      }
    })

    input.addEventListener("input", function () {
      this.classList.remove("invalid", "valid")
    })
  })
}

// Initialize input validation
addInputValidation()

// Demo login credentials info
setTimeout(() => {
  console.log("Demo Login Credentials:")
  console.log("Username: admin")
  console.log("Password: admin123")
}, 2000)