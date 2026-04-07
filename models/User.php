<?php
require_once 'database/db.php';
require_once 'database/Mailer.php';
require_once 'database/TokenGenerator.php';

class User {
    public $conn;

    public function __construct($conn) {
        $this->conn = $conn;
    }

    public function emailExists($email) {
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function register($firstname, $lastname, $email, $password) {

        if ($this->emailExists($email)) {
            return "Email already exists!";
        }

        if (strlen($password) < 8) {
            return "Password must be at least 8 characters!";
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);
        $verificationToken = TokenGenerator::generate();
        $tokenExpiry = TokenGenerator::generateExpiryTime(24);

        $stmt = $this->conn->prepare(
            "INSERT INTO users (firstname, lastname, email, password, verification_token, verification_token_expire, email_verified)
             VALUES (?, ?, ?, ?, ?, ?, 0)"
        );

        $stmt->execute([$firstname, $lastname, $email, $hash, $verificationToken, $tokenExpiry]);
        
        $userId = $this->conn->lastInsertId();
        $this->sendVerificationEmail($email, $firstname, $verificationToken);

        return $userId;
    }

    public function login($email, $password) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return false;
        }

        if (!$user['email_verified'] || $user['email_verified'] == 0) {
            return 'email_not_verified';
        }

        if ($user['password'] && password_verify($password, $user['password'])) {
            return $user;
        }

        return false;
    }

    public function isEmailVerified($email) {
        $stmt = $this->conn->prepare("SELECT email_verified FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $result = $stmt->fetch();
        return $result && $result['email_verified'] == 1;
    }

    public function verifyEmail($token) {
        $stmt = $this->conn->prepare(
            "SELECT * FROM users WHERE verification_token = ? AND verification_token_expire > NOW()"
        );
        $stmt->execute([$token]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return 'invalid_token';
        }

        $updateStmt = $this->conn->prepare(
            "UPDATE users SET email_verified = 1, verification_token = NULL, verification_token_expire = NULL WHERE id = ?"
        );
        $updateStmt->execute([$user['id']]);

        return true;
    }

    public function resendVerificationEmail($email) {
        $stmt = $this->conn->prepare(
            "SELECT firstname, email_verified, verification_token FROM users WHERE email = ?"
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return 'user_not_found';
        }

        if ($user['email_verified']) {
            return 'already_verified';
        }

        $verificationToken = TokenGenerator::generate();
        $tokenExpiry = TokenGenerator::generateExpiryTime(24);

        $updateStmt = $this->conn->prepare(
            "UPDATE users SET verification_token = ?, verification_token_expire = ? WHERE email = ?"
        );
        $updateStmt->execute([$verificationToken, $tokenExpiry, $email]);

        $this->sendVerificationEmail($email, $user['firstname'], $verificationToken);
        return true;
    }

    private function sendVerificationEmail($email, $firstname, $token) {
        $verifyLink = $this->buildAppUrl() . '/index.php?action=verify&token=' . $token;
        $mailer = new Mailer();
        $mailSent = $mailer->sendVerificationEmail($email, $firstname, $verifyLink);

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $_SESSION['latest_verification_link'] = $verifyLink;
        $_SESSION['verification_email_sent'] = $mailSent;

        return $mailSent;
    }

    private function buildAppUrl() {
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'];
        $scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
        return $scheme . '://' . $host . ($scriptDir === '/' ? '' : $scriptDir);
    }

    public function findByEmail($email) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByGoogleId($googleId) {
        $stmt = $this->conn->prepare("SELECT * FROM users WHERE oauth_id = ?");
        $stmt->execute([$googleId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function googleLogin($googleData) {
        $googleId = $googleData['id'];
        $email = $googleData['email'];
        $firstname = $googleData['given_name'] ?? 'User';
        $lastname = $googleData['family_name'] ?? '';

        $user = $this->findByGoogleId($googleId);
        
        if ($user) {
            $this->updateGoogleToken($googleId, $googleData['access_token'] ?? null);
            return $user;
        }

        $existingUser = $this->findByEmail($email);
        
        if ($existingUser) {
            $this->linkGoogleAccount($existingUser['id'], $googleId, $googleData['access_token'] ?? null);
            return $this->findByEmail($email);
        }

        return $this->createGoogleUser($firstname, $lastname, $email, $googleId, $googleData['access_token'] ?? null);
    }

    private function createGoogleUser($firstname, $lastname, $email, $googleId, $accessToken) {
        $stmt = $this->conn->prepare(
            "INSERT INTO users (firstname, lastname, email, oauth_provider, oauth_id, oauth_access_token, email_verified)
             VALUES (?, ?, ?, 'google', ?, ?, 1)"
        );

        $stmt->execute([$firstname, $lastname, $email, $googleId, $accessToken]);
        
        return $this->findByEmail($email);
    }

    private function linkGoogleAccount($userId, $googleId, $accessToken) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET oauth_provider = ?, oauth_id = ?, oauth_access_token = ? WHERE id = ?"
        );
        $stmt->execute([$googleId, $accessToken, $userId]);
        return true;
    }

    private function updateGoogleToken($googleId, $accessToken) {
        $stmt = $this->conn->prepare(
            "UPDATE users SET oauth_access_token = ? WHERE oauth_id = ?"
        );
        $stmt->execute([$accessToken, $googleId]);
        return true;
    }
}
?>