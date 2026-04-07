<!DOCTYPE html>
<html>
<head>
<title>Login - UtangListo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="auth-page">

<div class="auth-card shadow-sm" style="max-width: 400px; width: 100%;">
    <div class="card-body">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="UtangListo Logo" />
        </div>
        <h4 class="auth-header text-center">UtangListo</h4>
        <p class="auth-subtitle text-center">Sari-Sari Store Management</p>

        <?php if(!empty($error)) echo "<div class='alert alert-danger'>$error</div>"; ?>

        <form method="POST" action="index.php?action=login">
            <input class="form-control mb-3" type="email" name="email" placeholder="Email" required>
            <input class="form-control mb-3" type="password" name="password" placeholder="Password" required>
            <button class="btn btn-primary w-100">Login</button>
        </form>

        <div class="divider my-3">
            <span>OR</span>
        </div>

        <a href="index.php?action=google-login" class="btn btn-google w-100 mb-3">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display: inline; margin-right: 8px;">
                <circle cx="12" cy="12" r="1"></circle>
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2z"></path>
            </svg>
            Continue with Google
        </a>

        <div class="text-center mt-3">
            <a href="index.php?action=register" class="auth-link">Create Account</a>
        </div>
    </div>
</div>

</body>
</html>