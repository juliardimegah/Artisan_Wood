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

            <button type="submit" class="btn btn-primary">Continue</button>
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
    import { getAuth, createUserWithEmailAndPassword, updateProfile, signInAnonymously, signInWithCustomToken } from "https://www.gstatic.com/firebasejs/11.6.1/firebase-auth.js";

    const firebaseConfig = typeof __firebase_config !== 'undefined' 
        ? JSON.parse(__firebase_config) 
        : { apiKey: "DEMO_API_KEY", authDomain: "DEMO_AUTH_DOMAIN", projectId: "DEMO_PROJECT_ID" };

    const app = initializeApp(firebaseConfig);
    const auth = getAuth(app);

    if (typeof __initial_auth_token !== 'undefined' && __initial_auth_token) {
        signInWithCustomToken(auth, __initial_auth_token).catch(error => {
            console.error("Custom token sign-in error:", error);
            signInAnonymously(auth);
        });
    } else {
        signInAnonymously(auth);
    }

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

        createUserWithEmailAndPassword(auth, email, password)
            .then((userCredential) => {
                const user = userCredential.user;
                return updateProfile(user, { displayName: displayName });
            })
            .then(() => {
                window.location.href = 'index.php';
            })
            .catch((error) => {
                let message = "Terjadi kesalahan. Silakan coba lagi.";
                if (error.code === 'auth/email-already-in-use') {
                    message = 'Email ini sudah terdaftar.';
                } else if (error.code === 'auth/weak-password') {
                    message = 'Password terlalu lemah. Gunakan minimal 6 karakter.';
                }
                errorMessageDiv.textContent = message;
                errorMessageDiv.style.display = 'block';
                console.error("Create account error:", error);
            });
    });
</script>

</body>
</html>
