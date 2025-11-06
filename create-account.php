<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Account - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="logo" onclick="window.location.href='index.php'">ARTISAN WOOD</div>
</header>

<main class="main-content">
    <div class="create-account-container">
        <button class="close-btn" onclick="window.location.href='index.php'">&times;</button>
        <h2>Create an account</h2>
        <p class="subtitle">Already have an account? <a href="signin.php">Sign in</a></p>
        <div id="error-message" class="error-message" style="display: none;"></div>

        <form id="create-account-form">
            <div class="form-row">
                <div class="form-group">
                    <input type="text" id="first-name" placeholder="First name" required>
                </div>
                <div class="form-group">
                    <input type="text" id="last-name" placeholder="Last name" required>
                </div>
            </div>
            <div class="form-group">
                <input type="email" id="email" placeholder="Email" required>
            </div>
            <div class="form-group">
                <input type="password" id="password" placeholder="Password (min. 6 characters)" required>
            </div>

            <p class="terms">
                By selecting <strong>Create personal account</strong>, you agree to our 
                <a href="#">User Agreement</a> and acknowledge reading our <a href="#">Privacy Notice</a>.
            </p>

            <button type="submit" class="btn btn-primary">Create personal account</button>
        </form>

        <div class="divider">or continue with</div>

        <div class="social-buttons">
            <button class="btn btn-secondary">Google</button>
            <button class="btn btn-secondary">Facebook</button>
        </div>
    </div>
</main>

<script type="module">
    import { initializeApp } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-app.js";
    import { getAuth, createUserWithEmailAndPassword, updateProfile } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

    // Fallback to demo config if not provided
    const firebaseConfig = typeof __firebase_config !== 'undefined' 
        ? JSON.parse(__firebase_config) 
        : { apiKey: "DEMO_API_KEY", authDomain: "DEMO_AUTH_DOMAIN", projectId: "DEMO_PROJECT_ID" };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    const createAccountForm = document.getElementById('create-account-form');
    const errorMessageDiv = document.getElementById('error-message');

    createAccountForm.addEventListener('submit', (e) => {
        e.preventDefault();

        const firstName = document.getElementById('first-name').value;
        const lastName = document.getElementById('last-name').value;
        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;
        const displayName = `${firstName} ${lastName}`.trim();
        errorMessageDiv.style.display = 'none';

        let userCredential;

        createUserWithEmailAndPassword(auth, email, password)
            .then((cred) => {
                userCredential = cred;
                // Update Firebase profile
                return updateProfile(userCredential.user, { displayName: displayName });
            })
            .then(() => {
                // Get the Firebase ID token
                return userCredential.user.getIdToken();
            })
            .then(token => {
                // Send the token and display name to the server
                return fetch('server-register.php', {
                    method: 'POST',
                    headers: {
                        'Authorization': 'Bearer ' + token,
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ displayName: displayName })
                });
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(err => { throw new Error(err.error || 'Server registration failed'); });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Redirect to the home page on successful registration
                    window.location.href = 'index.php';
                } else {
                    throw new Error(data.message || 'An unknown error occurred.');
                }
            })
            .catch((error) => {
                let message = "An error occurred. Please try again.";
                if (error.code === 'auth/email-already-in-use') {
                    message = 'This email is already registered.';
                } else if (error.code === 'auth/weak-password') {
                    message = 'Password is too weak. Please use at least 6 characters.';
                } else if (error.message) {
                    message = error.message;
                }
                errorMessageDiv.textContent = message;
                errorMessageDiv.style.display = 'block';
                console.error("Create account error:", error);
            });
    });
</script>

</body>
</html>
