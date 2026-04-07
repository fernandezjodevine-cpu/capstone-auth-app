<?php
require_once 'models/User.php';
require_once 'database/db.php';

class AuthController {
    private $user;

    public function __construct() {
        global $conn;
        $this->user = new User($conn);
    }

    public function register($data) {
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return "Invalid email format!";
        }

        $result = $this->user->register(
            $data['firstname'],
            $data['lastname'],
            $data['email'],
            $data['password']
        );

        if (is_numeric($result)) {
            return ['success' => true, 'message' => 'Registration successful! Check your email to verify your account.'];
        }

        return $result;
    }

    public function login($data) {
        $result = $this->user->login($data['email'], $data['password']);

        if ($result === 'email_not_verified') {
            return ['error' => 'email_not_verified', 'email' => $data['email']];
        }

        if ($result) {
            return $result;
        }

        return false;
    }

    public function verifyEmail($token) {
        $result = $this->user->verifyEmail($token);
        if ($result === true) {
            return ['success' => true, 'message' => 'Email verified successfully! You can now login.'];
        }
        return ['success' => false, 'message' => 'Verification token expired or invalid. Please request a new verification email.'];
    }

    public function resendVerificationEmail($email) {
        $result = $this->user->resendVerificationEmail($email);
        if ($result === true) {
            return ['success' => true, 'message' => 'Verification email sent! Check your inbox.'];
        } elseif ($result === 'user_not_found') {
            return ['success' => false, 'message' => 'User not found.'];
        } elseif ($result === 'already_verified') {
            return ['success' => false, 'message' => 'This email is already verified.'];
        }
        return ['success' => false, 'message' => 'An error occurred.'];
    }

    public function getGoogleAuthUrl() {
        require_once 'database/GoogleOAuth.php';
        
        $googleOAuth = new GoogleOAuth(
            GOOGLE_CLIENT_ID,
            GOOGLE_CLIENT_SECRET,
            GOOGLE_REDIRECT_URI
        );
        
        return $googleOAuth->getAuthorizationUrl();
    }

    public function handleGoogleCallback($code, $state) {
        require_once 'database/GoogleOAuth.php';
        
        $googleOAuth = new GoogleOAuth(
            GOOGLE_CLIENT_ID,
            GOOGLE_CLIENT_SECRET,
            GOOGLE_REDIRECT_URI
        );

        if (!$googleOAuth->verifyState($state)) {
            return ['success' => false, 'message' => 'Invalid state parameter.'];
        }

        $accessToken = $googleOAuth->getAccessToken($code);
        if (!is_array($accessToken) || empty($accessToken['access_token'])) {
            $errorMessage = 'Unknown error';
            if (is_array($accessToken)) {
                $errorMessage = $accessToken['error_description'] ?? $accessToken['error'] ?? json_encode($accessToken, JSON_UNESCAPED_SLASHES);
            }
            return ['success' => false, 'message' => 'Failed to get access token: ' . $errorMessage];
        }

        $googleData = $googleOAuth->getUserInfo($accessToken);
        if (!$googleData) {
            return ['success' => false, 'message' => 'Failed to get user info.'];
        }

        $googleData['access_token'] = $accessToken;
        $user = $this->user->googleLogin($googleData);
        
        if ($user) {
            return ['success' => true, 'user' => $user];
        }
        
        return ['success' => false, 'message' => 'Failed to login with Google.'];
    }
}
?>