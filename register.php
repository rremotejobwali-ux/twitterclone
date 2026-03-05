<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign up / Twitter Clone</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <h1 class="auth-title">Join X today.</h1>
            <form id="registerForm">
                <div class="form-group">
                    <input type="text" name="full_name" class="form-input" placeholder="Full Name" required>
                </div>
                <div class="form-group">
                    <input type="text" name="username" class="form-input" placeholder="Username" required>
                </div>
                <div class="form-group">
                    <input type="password" name="password" class="form-input" placeholder="Password" required>
                </div>
                <button type="submit" class="btn-primary btn-accent">Sign up</button>
            </form>
            <div id="error" style="color: var(--error-color); margin-top: 10px;"></div>
            <a href="login.php" class="auth-link">Have an account already? Log in</a>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            formData.append('action', 'register');

            try {
                const res = await fetch('actions/auth.php', {
                    method: 'POST',
                    body: formData
                });
                const data = await res.json();
                
                if (data.success) {
                    window.location.href = 'index.php';
                } else {
                    document.getElementById('error').textContent = data.message;
                }
            } catch (err) {
                console.error(err);
            }
        });
    </script>
</body>
</html>
