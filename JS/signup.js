
document.getElementById("signupForm").addEventListener("submit", function(event) {
    // Collect form data
    const name = document.getElementById("name").value.trim();
    const email = document.getElementById("email").value.trim();
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirmPassword").value;
    const userType = document.getElementById("userType").value;
    
  
    // Error message elements
    const nameError = document.getElementById("nameError");
    const emailError = document.getElementById("emailError");
    const passwordError = document.getElementById("passwordError");
    const confirmPasswordError = document.getElementById("confirmPasswordError");
    const userTypeError = document.getElementById("userTypeError");
    const termsError = document.getElementById("termsError");
  
    // Clear previous error messages
    nameError.textContent = "";
    emailError.textContent = "";
    passwordError.textContent = "";
    confirmPasswordError.textContent = "";
    userTypeError.textContent = "";
    termsError.textContent = "";
  
    // Regular expressions for email and password validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    const passwordRegex = /^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?#&]{8,}$/;
  
    let isValid = true;
  
    // Validation checks
    if (name === "") {
        nameError.textContent = "Name is required.";
        isValid = false;
    }
  
    if (!emailRegex.test(email)) {
        emailError.textContent = "Please enter a valid email address.";
        isValid = false;
    }
  
    if (!passwordRegex.test(password)) {
        passwordError.textContent = "Password must be at least 8 characters, with a letter, a number, and a special character.";
        isValid = false;
    }
  
    if (password !== confirmPassword) {
        confirmPasswordError.textContent = "Passwords do not match.";
        isValid = false;
    }
  
    if (userType === "") {
        userTypeError.textContent = "Please select a user type.";
        isValid = false;
    }
    
    if (isValid) {
        // If everything is valid, allow the form to submit
        alert(`Welcome, ${name}! You have succesfully signed up as a ${userType === "farmer" ? "Farmer" : "Buyer"}.`);
        
        return true; // This line allows the form submission.
    } else {
        event.preventDefault(); // Prevent submission if there are errors
    }
  });