// Forgot Password Functionality - Simplified Version (No password display)
let currentUser = null

document.addEventListener("DOMContentLoaded", () => {
  // Focus on identifier input
  const identifierInput = document.getElementById("identifier")
  if (identifierInput) {
    identifierInput.focus()
  }

  // Add form submit handler for forgot password
  const forgotPasswordForm = document.getElementById("forgotPasswordForm")
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", (e) => {
      e.preventDefault()
      searchAccount()
    })
  }

  // Add form submit handler for reset password
  const resetPasswordForm = document.getElementById("resetPasswordForm")
  if (resetPasswordForm) {
    resetPasswordForm.addEventListener("submit", (e) => {
      e.preventDefault()
      resetPassword()
    })
  }

  // Add enter key handler for identifier input
  if (identifierInput) {
    identifierInput.addEventListener("keypress", (e) => {
      if (e.key === "Enter") {
        e.preventDefault()
        searchAccount()
      }
    })
  }

  // Add real-time password validation
  addPasswordValidation()

  // Initialize tooltips for copy buttons
  initializeCopyButtonTooltips()
})

/**
 * Add real-time password validation
 */
function addPasswordValidation() {
  const newPasswordInput = document.getElementById("new-password")
  const confirmPasswordInput = document.getElementById("confirm-password")

  if (newPasswordInput && confirmPasswordInput) {
    // Validate password strength
    newPasswordInput.addEventListener("input", function () {
      validatePasswordStrength(this.value)
    })

    // Validate password match
    confirmPasswordInput.addEventListener("input", function () {
      validatePasswordMatch(newPasswordInput.value, this.value)
    })

    newPasswordInput.addEventListener("input", function () {
      if (confirmPasswordInput.value) {
        validatePasswordMatch(this.value, confirmPasswordInput.value)
      }
    })
  }
}

/**
 * Validate password strength
 */
function validatePasswordStrength(password) {
  const newPasswordInput = document.getElementById("new-password")

  if (password.length === 0) {
    newPasswordInput.classList.remove("valid", "invalid")
    return
  }

  if (password.length >= 6) {
    newPasswordInput.classList.add("valid")
    newPasswordInput.classList.remove("invalid")
  } else {
    newPasswordInput.classList.add("invalid")
    newPasswordInput.classList.remove("valid")
  }
}

/**
 * Validate password match
 */
function validatePasswordMatch(password, confirmPassword) {
  const confirmPasswordInput = document.getElementById("confirm-password")

  if (confirmPassword.length === 0) {
    confirmPasswordInput.classList.remove("valid", "invalid")
    return
  }

  if (password === confirmPassword && password.length >= 6) {
    confirmPasswordInput.classList.add("valid")
    confirmPasswordInput.classList.remove("invalid")
  } else {
    confirmPasswordInput.classList.add("invalid")
    confirmPasswordInput.classList.remove("valid")
  }
}

/**
 * Search for user account
 */
function searchAccount() {
  const identifier = document.getElementById("identifier").value.trim()

  console.log("Search account called with identifier:", identifier)

  if (!identifier) {
    alert("Please enter your username or email")
    document.getElementById("identifier").focus()
    return
  }

  if (identifier.includes("@") && !isValidEmailForgot(identifier)) {
    alert("Please enter a valid email address")
    document.getElementById("identifier").focus()
    return
  }

  showForgotPasswordLoading("Searching for your account...")

  const formData = new FormData()
  formData.append("identifier", identifier)
  formData.append("action", "search")

  console.log("Sending request to auth/forgot_password.php")

  fetch("auth/forgot_password.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      console.log("Response status:", response.status)

      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`)
      }

      return response.text()
    })
    .then((text) => {
      console.log("Raw response:", text)

      try {
        const data = JSON.parse(text)
        console.log("Parsed response data:", data)

        hideForgotPasswordLoading()

        if (data.success) {
          currentUser = data.user
          showStep2()
          alert("Account Found! Your account has been located successfully.")
        } else {
          alert("Account Not Found: " + (data.message || "No account found with that username or email."))
          document.getElementById("identifier").focus()
        }
      } catch (parseError) {
        console.error("JSON parse error:", parseError)
        console.error("Raw response that failed to parse:", text)
        hideForgotPasswordLoading()
        alert("Server Error: Invalid response from server. Please try again.")
      }
    })
    .catch((error) => {
      console.error("Fetch error:", error)
      hideForgotPasswordLoading()
      alert("Connection Error: Unable to search for account. Please check your connection and try again.")
    })
}

/**
 * Show step 2 - Account found, ready to generate password
 */
function showStep2() {
  document.getElementById("step1").style.display = "none"
  document.getElementById("step2").style.display = "block"
  document.getElementById("step3").style.display = "none"

  // Populate user info
  document.getElementById("display-username").value = currentUser.username
  document.getElementById("display-email").value = currentUser.email
}

/**
 * Show step 3 - New password generated
 */
function showStep3() {
  document.getElementById("step1").style.display = "none"
  document.getElementById("step2").style.display = "none"
  document.getElementById("step3").style.display = "block"
}

/**
 * Reset password with user input
 */
function resetPassword() {
  if (!currentUser || !currentUser.id) {
    alert("Error: User information not found. Please try again.")
    return
  }

  const newPassword = document.getElementById("new-password").value
  const confirmPassword = document.getElementById("confirm-password").value

  // Validate passwords
  if (!newPassword || !confirmPassword) {
    alert("Please fill in both password fields.")
    document.getElementById("new-password").focus()
    return
  }

  if (newPassword.length < 6) {
    alert("Password must be at least 6 characters long.")
    document.getElementById("new-password").focus()
    return
  }

  if (newPassword !== confirmPassword) {
    alert("Passwords do not match. Please check and try again.")
    document.getElementById("confirm-password").focus()
    return
  }

  // Show loading
  showForgotPasswordLoading("Resetting your password...")

  const formData = new FormData()
  formData.append("action", "reset_password")
  formData.append("user_id", currentUser.id)
  formData.append("new_password", newPassword)
  formData.append("confirm_password", confirmPassword)

  fetch("auth/forgot_password.php", {
    method: "POST",
    body: formData,
  })
    .then((response) => {
      if (!response.ok) {
        throw new Error("Network response was not ok")
      }
      return response.json()
    })
    .then((data) => {
      hideForgotPasswordLoading()

      if (data.success) {
        // Show step 3 with success message
        document.getElementById("final-username").value = data.username || currentUser.username
        showStep3()
        alert(
          "Password Reset Successful! Your password has been reset successfully. You can now login with your new password.",
        )
      } else {
        alert("Reset Failed: " + (data.message || "Failed to reset password."))
      }
    })
    .catch((error) => {
      hideForgotPasswordLoading()
      console.error("Reset error:", error)
      alert("Connection Error: Unable to reset password. Please try again.")
    })
}

/**
 * Toggle new password visibility
 */
function toggleNewPassword() {
  const passwordField = document.getElementById("new-password")
  const toggleIcon = document.getElementById("newPasswordIcon")

  if (passwordField.type === "password") {
    passwordField.type = "text"
    toggleIcon.className = "fas fa-eye-slash"
  } else {
    passwordField.type = "password"
    toggleIcon.className = "fas fa-eye"
  }
}

/**
 * Toggle confirm password visibility
 */
function toggleConfirmPassword() {
  const passwordField = document.getElementById("confirm-password")
  const toggleIcon = document.getElementById("confirmPasswordIcon")

  if (passwordField.type === "password") {
    passwordField.type = "text"
    toggleIcon.className = "fas fa-eye-slash"
  } else {
    passwordField.type = "password"
    toggleIcon.className = "fas fa-eye"
  }
}

/**
 * Toggle password visibility in final step
 */
function toggleFinalPassword() {
  const passwordField = document.getElementById("final-password")
  const toggleIcon = document.getElementById("finalPasswordIcon")

  if (passwordField.type === "password") {
    passwordField.type = "text"
    toggleIcon.className = "fas fa-eye-slash"
  } else {
    passwordField.type = "password"
    toggleIcon.className = "fas fa-eye"
  }
}

/**
 * Copy text to clipboard
 */
function copyToClipboard(elementId) {
  const element = document.getElementById(elementId)
  if (!element) {
    alert("Error: Unable to copy. Element not found.")
    return
  }

  // Create a temporary input to copy from
  const tempInput = document.createElement("input")
  tempInput.value = element.value
  document.body.appendChild(tempInput)
  tempInput.select()

  try {
    const successful = document.execCommand("copy")
    document.body.removeChild(tempInput)

    if (successful) {
      // Visual feedback
      const copyBtn = event.target.closest(".copy-btn")
      if (copyBtn) {
        const originalHTML = copyBtn.innerHTML
        copyBtn.innerHTML = '<i class="fas fa-check"></i>'
        copyBtn.classList.add("copied")
        copyBtn.style.color = "#28a745"

        setTimeout(() => {
          copyBtn.innerHTML = originalHTML
          copyBtn.classList.remove("copied")
          copyBtn.style.color = ""
        }, 2000)
      }

      const fieldType = elementId.includes("username") ? "Username" : "Password"
      alert(`Copied! ${fieldType} copied to clipboard successfully.`)
    } else {
      throw new Error("Copy command failed")
    }
  } catch (err) {
    document.body.removeChild(tempInput)
    console.error("Copy failed:", err)

    // Fallback: show the value in a prompt for manual copy
    const fieldType = elementId.includes("username") ? "Username" : "Password"
    prompt(`Please copy this ${fieldType.toLowerCase()} manually:`, element.value)
  }
}

/**
 * Reset form to initial state
 */
function resetForm() {
  // Show step 1
  document.getElementById("step1").style.display = "block"
  document.getElementById("step2").style.display = "none"
  document.getElementById("step3").style.display = "none"

  // Reset forms
  const forgotForm = document.getElementById("forgotPasswordForm")
  const resetForm = document.getElementById("resetPasswordForm")

  if (forgotForm) {
    forgotForm.reset()
  }

  if (resetForm) {
    resetForm.reset()
  }

  // Clear validation classes
  const inputs = document.querySelectorAll(".form-control")
  inputs.forEach((input) => {
    input.classList.remove("valid", "invalid")
  })

  // Clear user data
  currentUser = null

  // Focus on identifier input
  const identifierInput = document.getElementById("identifier")
  if (identifierInput) {
    identifierInput.focus()
  }
}

/**
 * Show loading state
 */
function showForgotPasswordLoading(message = "Loading...") {
  const loginButton = document.querySelector(".btn-login")
  const loadingOverlay = document.getElementById("loadingOverlay")
  const loadingText = loadingOverlay ? loadingOverlay.querySelector("p") : null

  if (loginButton) {
    loginButton.classList.add("loading")
  }

  if (loadingOverlay) {
    loadingOverlay.classList.add("active")
  }

  if (loadingText) {
    loadingText.textContent = message
  }
}

/**
 * Hide loading state
 */
function hideForgotPasswordLoading() {
  const loginButton = document.querySelector(".btn-login")
  const loadingOverlay = document.getElementById("loadingOverlay")

  if (loginButton) {
    loginButton.classList.remove("loading")
  }

  if (loadingOverlay) {
    loadingOverlay.classList.remove("active")
  }
}

/**
 * Validate email format
 */
function isValidEmailForgot(email) {
  const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/
  return emailRegex.test(email)
}

/**
 * Initialize copy button tooltips
 */
function initializeCopyButtonTooltips() {
  const copyButtons = document.querySelectorAll(".copy-btn")
  copyButtons.forEach((btn) => {
    btn.setAttribute("aria-label", "Copy to clipboard")
    btn.setAttribute("title", "Copy to clipboard")
  })
}

// Export functions for global access
window.forgotPassword = {
  searchAccount,
  resetPassword,
  toggleNewPassword,
  toggleConfirmPassword,
  copyToClipboard,
  resetForm,
}