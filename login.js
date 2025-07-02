// Notification System - Functional Approach
let notificationContainer = null
let notifications = []

// Initialize notification system
function initNotificationSystem() {
  notificationContainer = document.getElementById("notificationContainer")
  notifications = []
}

// Create notification element
function createNotification(type, title, message, duration) {
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
    <button class="notification-close" onclick="removeNotification(this.parentElement)">
        <i class="fas fa-times"></i>
    </button>
  `

  return notification
}

// Show notification
function showNotification(type, title, message, duration = 5000) {
  if (!notificationContainer) {
    initNotificationSystem()
  }

  const notification = createNotification(type, title, message, duration)
  notificationContainer.appendChild(notification)
  notifications.push(notification)

  // Trigger animation
  setTimeout(() => {
    notification.classList.add("show")
  }, 100)

  // Auto remove
  if (duration > 0) {
    setTimeout(() => {
      removeNotification(notification)
    }, duration)
  }

  return notification
}

// Remove notification
function removeNotification(notification) {
  if (!notification || !notification.parentElement) return

  notification.classList.add("hide")
  setTimeout(() => {
    if (notification.parentElement) {
      notification.parentElement.removeChild(notification)
    }
    const index = notifications.indexOf(notification)
    if (index > -1) {
      notifications.splice(index, 1)
    }
  }, 400)
}

// Notification helper functions
function showSuccess(title, message, duration = 5000) {
  return showNotification("success", title, message, duration)
}

function showError(title, message, duration = 7000) {
  return showNotification("error", title, message, duration)
}

function showWarning(title, message, duration = 6000) {
  return showNotification("warning", title, message, duration)
}

function showInfo(title, message, duration = 5000) {
  return showNotification("info", title, message, duration)
}

function clearAllNotifications() {
  notifications.forEach((notification) => {
    removeNotification(notification)
  })
}

// Create global notificationSystem object for compatibility
const notificationSystem = {
  success: showSuccess,
  error: showError,
  warning: showWarning,
  info: showInfo,
  clear: clearAllNotifications,
}

// DOM Elements
let loginForm = null
let loginButton = null
let loadingOverlay = null
let usernameInput = null
let passwordInput = null

// Initialize DOM elements
function initDOMElements() {
  loginForm = document.getElementById("loginForm")
  loginButton = document.querySelector(".btn-login")
  loadingOverlay = document.getElementById("loadingOverlay")
  usernameInput = document.getElementById("username")
  passwordInput = document.getElementById("password")
}

// Toggle password visibility
function togglePassword() {
  const passwordField = document.getElementById("password")
  const toggleIcon = document.getElementById("passwordToggleIcon")

  if (!passwordField || !toggleIcon) return

  if (passwordField.type === "password") {
    passwordField.type = "text"
    toggleIcon.className = "fas fa-eye-slash"
  } else {
    passwordField.type = "password"
    toggleIcon.className = "fas fa-eye"
  }
}

// Validate form inputs
function validateForm() {
  if (!usernameInput || !passwordInput || !loginButton) return

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

// Validate email format
function isValidEmail(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

// Validate input fields
function validateInputs(username, password) {
  if (!username) {
    showWarning("Validation Error", "Please enter your username or email")
    if (usernameInput) usernameInput.focus()
    return false
  }

  if (!password) {
    showWarning("Validation Error", "Please enter your password")
    if (passwordInput) passwordInput.focus()
    return false
  }

  if (password.length < 6) {
    showWarning("Validation Error", "Password must be at least 6 characters long")
    if (passwordInput) passwordInput.focus()
    return false
  }

  // Validate email format if it looks like an email
  if (username.includes("@") && !isValidEmail(username)) {
    showWarning("Validation Error", "Please enter a valid email address")
    if (usernameInput) usernameInput.focus()
    return false
  }

  return true
}

// Show loading state
function showLoading() {
  if (loginButton) {
    loginButton.classList.add("loading")
  }
  if (loadingOverlay) {
    loadingOverlay.classList.add("active")
  }

  // Disable form inputs
  if (usernameInput) usernameInput.disabled = true
  if (passwordInput) passwordInput.disabled = true
}

// Hide loading state
function hideLoading() {
  if (loginButton) {
    loginButton.classList.remove("loading")
  }
  if (loadingOverlay) {
    loadingOverlay.classList.remove("active")
  }

  // Enable form inputs
  if (usernameInput) usernameInput.disabled = false
  if (passwordInput) passwordInput.disabled = false
}

// Handle successful login
function handleLoginSuccess(data) {
  showSuccess("Login Successful!", `Welcome back, ${data.user.username}!`)

  // Check if password reset is required
  if (data.password_reset_required) {
    showWarning("Password Change Required", "You must change your password before continuing.")
    // Redirect to change password page
    setTimeout(() => {
      window.location.href = "change-password.html"
    }, 2000)
  } else {
    // Redirect to main dashboard
    setTimeout(() => {
      window.location.href = "main.html"
    }, 2000)
  }
}

// Handle login error
function handleLoginError(data) {
  showError("Login Failed", data.message || "Invalid credentials")

  // Clear password field
  if (passwordInput) {
    passwordInput.value = ""
    passwordInput.focus()
  }
}

// Handle network error
function handleNetworkError(error) {
  console.error("Login error:", error)
  showError("Connection Error", "Unable to connect to server. Please try again.")
}

// Submit login request
function submitLoginRequest(username, password, remember) {
  const formData = new FormData()
  formData.append("username", username)
  formData.append("password", password)
  formData.append("remember", remember)

  return fetch("auth/login_process.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }
      return response.json()
    })
    .then((data) => {
      hideLoading()

      if (data.success) {
        handleLoginSuccess(data)
      } else {
        handleLoginError(data)
      }
    })
    .catch((error) => {
      hideLoading()
      handleNetworkError(error)
    })
}

// Handle login form submission
function handleLogin(e) {
  e.preventDefault()

  if (!usernameInput || !passwordInput) {
    showError("Form Error", "Form elements not found. Please refresh the page.")
    return
  }

  const username = usernameInput.value.trim()
  const password = passwordInput.value.trim()
  const remember = document.getElementById("remember")?.checked || false

  // Validate inputs
  if (!validateInputs(username, password)) {
    return
  }

  // Show loading state
  showLoading()

  // Submit login request
  submitLoginRequest(username, password, remember)
}

// Handle enter key press
function handleEnterKey(e) {
  if (e.key === "Enter" && !loginButton?.classList.contains("loading")) {
    if (e.target === usernameInput && passwordInput) {
      e.preventDefault()
      passwordInput.focus()
    } else if (e.target === passwordInput) {
      e.preventDefault()
      handleLogin(e)
    }
  }
}

// Add input validation styling
function addInputValidation() {
  const inputs = [usernameInput, passwordInput].filter(Boolean)

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

// Add focus/blur effects
function addInputEffects() {
  const inputs = [usernameInput, passwordInput].filter(Boolean)

  inputs.forEach((input) => {
    input.addEventListener("focus", function () {
      this.parentElement.classList.add("focused")
    })

    input.addEventListener("blur", function () {
      if (!this.value) {
        this.parentElement.classList.remove("focused")
      }
    })
  })
}

// Show forgot password info
function showForgotPassword() {
  window.location.href = "forgot-password.html"
}

// Show register info
function showRegisterInfo() {
  showInfo(
    "Account Registration",
    "New accounts must be created by the system administrator. Please contact IT support.",
  )
}

// Initialize event listeners
function initEventListeners() {
  // Form submit handler
  if (loginForm) {
    loginForm.addEventListener("submit", handleLogin)
  }

  // Input validation
  if (usernameInput) {
    usernameInput.addEventListener("input", validateForm)
    usernameInput.addEventListener("keypress", handleEnterKey)
  }

  if (passwordInput) {
    passwordInput.addEventListener("input", validateForm)
    passwordInput.addEventListener("keypress", handleEnterKey)
  }

  // Global enter key handler
  document.addEventListener("keypress", (e) => {
    if (e.key === "Enter" && !loginButton?.classList.contains("loading")) {
      handleLogin(e)
    }
  })
}

// Initialize page
function initializePage() {
  // Initialize notification system
  initNotificationSystem()

  // Initialize DOM elements
  initDOMElements()

  // Focus on username input
  if (usernameInput) {
    usernameInput.focus()
  }

  // Initialize event listeners
  initEventListeners()

  // Add input validation and effects
  addInputValidation()
  addInputEffects()

  // Initial form validation
  validateForm()

  // Show welcome notification
  setTimeout(() => {
    showInfo("Welcome!", "Please enter your credentials to continue")
  }, 1000)
}

// Check if user is already logged in
function checkExistingSession() {
  fetch("auth/get_user_info.php")
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        // User is already logged in, redirect to main page
        showInfo("Already Logged In", "Redirecting to dashboard...")
        setTimeout(() => {
          window.location.href = "main.html"
        }, 1500)
      }
    })
    .catch((error) => {
      // Ignore errors, user is not logged in
      console.log("No existing session found")
    })
}

// DOM Content Loaded
document.addEventListener("DOMContentLoaded", () => {
  // Check for existing session first
  checkExistingSession()

  // Initialize page
  initializePage()

  // Show demo credentials in console
  setTimeout(() => {
    console.log("Demo Login Credentials:")
    console.log("Username: admin")
    console.log("Password: admin123")
  }, 2000)
})

// Global functions for HTML onclick handlers
window.togglePassword = togglePassword
window.showForgotPassword = showForgotPassword
window.showRegisterInfo = showRegisterInfo
window.removeNotification = removeNotification

// Export functions for debugging
window.loginDebug = {
  showLoading,
  hideLoading,
  validateForm,
  handleLogin,
  clearAllNotifications,
  showSuccess,
  showError,
  showWarning,
  showInfo,
}