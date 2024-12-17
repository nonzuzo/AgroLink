const form = document.getElementById("loginForm");

form.addEventListener("submit", async function (event) {
  event.preventDefault();

  // Clear previous error messages
  document.getElementById("emailError").textContent = "";
  document.getElementById("passwordError").textContent = "";

  // Get form values
  const email = document.getElementById("email").value.trim();
  const password = document.getElementById("password").value.trim();
  const userType = document.getElementById("userType").value.trim();

  let isValid = true;

  // Email validation
  const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!email) {
    document.getElementById("emailError").textContent = "Email is required.";
    isValid = false;
  } else if (!emailPattern.test(email)) {
    document.getElementById("emailError").textContent =
      "Please enter a valid email.";
    isValid = false;
  }

  // Password validation
  if (!password) {
    document.getElementById("passwordError").textContent =
      "Password is required.";
    isValid = false;
  }

  // Validate userType
  if (!userType) {
    alert("User type is required.");
    isValid = false;
  }

  if (isValid) {
    const myHeaders = new Headers();
    myHeaders.append("Content-Type", "application/json");

    try {
      const response = await fetch("../ACTIONS/login.php", {
        method: "POST",
        body: JSON.stringify({ email, password, userType }),
        headers: myHeaders,
      });

      let result;
      try {
        result = await response.json();
      } catch (error) {
        console.error("Failed to parse response as JSON", error);
        alert("Something went wrong. Please try again later.");
        return;
      }

      if (result.status === "success") {
        switch ((result.userType || "").toLowerCase()) {
          case "farmer":
            window.location.href = "../ACTIONS/farmer_dashboard.php";
            break;
          case "buyer":
            window.location.href = "../ACTIONS/buyer_dashboard.php";
            break;
          case "admin":
            window.location.href = "../ACTIONS/admin_dashboard.php";
            break;
          default:
            alert("Unknown user type. Please contact support.");
        }
      } else {
        alert(result.message || "Login failed. Please try again.");
      }
    } catch (error) {
      console.error("Unexpected error occurred", error);
      alert("Unexpected error occurred. Please try again.");
    }
  }
});
