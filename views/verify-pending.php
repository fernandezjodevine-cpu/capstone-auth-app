<?php
if (isset($_SESSION['user'])) {
    header('Location: index.php?action=home');
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Check Email - UtangListo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="assets/css/style.css" rel="stylesheet">
</head>

<body class="auth-page">

<div class="auth-card shadow-sm" style="max-width: 500px; width: 100%;">
    <div class="card-body text-center">
        <div class="auth-logo">
            <img src="assets/images/logo.png" alt="UtangListo Logo" />
        </div>
        <h4 class="auth-header text-center">Registration Successful!</h4>
        <p class="auth-subtitle text-center">Verify your email to continue</p>
        <p>Check your inbox for a verification link. If you didn't receive it, click the button below to resend.</p>

        <?php if (!empty($message)): ?>
            <div class="alert <?php echo htmlspecialchars($alert_type ?: 'alert-info'); ?> mt-4">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="index.php?action=verify-pending" class="mt-4">
            <div class="mb-3">
                <label class="form-label">Email Address</label>
                <input class="form-control" type="email" name="email" placeholder="your@email.com" value="<?php echo htmlspecialchars($_POST['email'] ?? $_SESSION['pending_email'] ?? ''); ?>" required>
            </div>
            <button class="btn btn-primary w-100" type="submit">Resend Email</button>
        </form>

        <?php if (!empty($_SESSION['latest_verification_link'])): ?>
            <div class="alert alert-info mt-4">
                <p class="mb-2"><strong>Local verification link:</strong></p>
                <a href="<?php echo htmlspecialchars($_SESSION['latest_verification_link']); ?>" class="btn btn-sm btn-info" target="_blank">
                    Click Here to Verify Email
                </a>
                <p class="mt-2 mb-0"><small>
                    Or copy and paste this URL in your browser:<br/>
                    <code style="word-break: break-all; font-size: 11px;">
                        <?php echo htmlspecialchars($_SESSION['latest_verification_link']); ?>
                    </code>
                </small></p>
                <p class="mt-2 mb-0"><small>If email delivery is not configured, use this link to verify your account.</small></p>
            </div>
        <?php else: ?>
            <div class="alert alert-info mt-4">
                <small><strong>Tip:</strong> Check your spam folder if you don't see the email.</small>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="index.php?action=login" class="auth-link">Back to Login</a>
        </div>
    </div>
</div>

</body>
</html>
