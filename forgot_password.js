// Forgot Password Functionality - Simplified Version (No password display)
let currentUser = null

document.addEventListener("DOMContentLoaded", () => {
  // Focus on identifier input
  const identifierInput = document.getElementById("identifier")
  if (identifierInput) {
    identifierInput.focus()
  }

  // Add form submit handler
  const forgotPasswordForm = document.getElementById("forgotPasswordForm")
  if (forgotPasswordForm) {
    forgotPasswordForm.addEventListener("submit", (e) => {
      e.preventDefault()
      searchAccount()
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

  // Initialize tooltips for copy buttons
  initializeCopyButtonTooltips()
})

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
 * Generate new temporary password
 */
function generateNewPassword() {
  if (!currentUser || !currentUser.id) {
    alert("Error: User information not found. Please try again.")
    return
  }

  showForgotPasswordLoading("Generating new password...")

  const formData = new FormData()
  formData.append("action", "generate_new")
  formData.append("user_id", currentUser.id)

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
        // Show step 3 with new password
        document.getElementById("final-username").value = currentUser.username
        document.getElementById("final-password").value = data.new_password
        showStep3()
        alert("New Password Generated! A new temporary password has been created for your account.")
      } else {
        alert("Generation Failed: " + (data.message || "Failed to generate new password."))
      }
    })
    .catch((error) => {
      hideForgotPasswordLoading()
      console.error("Generation error:", error)
      alert("Connection Error: Unable to generate new password. Please try again.")
    })
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

  // Reset form
  const form = document.getElementById("forgotPasswordForm")
  if (form) {
    form.reset()
  }

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
  generateNewPassword,
  toggleFinalPassword,
  copyToClipboard,
  resetForm,
}