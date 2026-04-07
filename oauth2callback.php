<?php
session_start();

require_once 'vendor/autoload.php';
require_once 'models/User.php';
require_once 'database/db.php';

$client = new Google_Client();
$client->setClientId(getenv('GOOGLE_CLIENT_ID') ?: 'YOUR_CLIENT_ID');
$client->setClientSecret(getenv('GOOGLE_CLIENT_SECRET') ?: 'YOUR_CLIENT_SECRET');
$client->setRedirectUri(getenv('GOOGLE_REDIRECT_URI') ?: 'http://localhost/capstone-auth-app/oauth2callback.php');
$client->addScope('email');
$client->addScope('profile');

$error = '';
$user_model = new User($conn);

if (isset($_GET['code'])) {
    try {
        $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            unset($_SESSION['access_token']);
            header('Location: index.php?action=login&error=' . urlencode('Access token expired'));
            exit;
        }

        $_SESSION['access_token'] = $client->getAccessToken();

        $oauth2 = new Google_Service_Oauth2($client);
        $userInfo = $oauth2->userinfo->get();

        $email = $userInfo->email;
        $firstname = $userInfo->given_name ?: 'User';
        $lastname = $userInfo->family_name ?: '';
        $googleId = $userInfo->id;
        $accessTokenJson = json_encode($token);

        $existingUser = $user_model->findByEmail($email);

        if ($existingUser) {
            if ($existingUser['email_verified'] == 0) {
                $user_model->conn->prepare("UPDATE users SET email_verified = 1 WHERE email = ?")
                    ->execute([$email]);
            }

            if (!$existingUser['oauth_provider']) {
                $user_model->conn->prepare(
                    "UPDATE users SET oauth_provider = ?, oauth_id = ?, oauth_access_token = ? WHERE email = ?"
                )->execute(['google', $googleId, $accessTokenJson, $email]);
            }

            $user = $user_model->findByEmail($email);
        } else {
            $randomPassword = bin2hex(random_bytes(16));
            $hashPassword = password_hash($randomPassword, PASSWORD_DEFAULT);

            $stmt = $user_model->conn->prepare(
                "INSERT INTO users (firstname, lastname, email, password, email_verified, oauth_provider, oauth_id, oauth_access_token)
                 VALUES (?, ?, ?, ?, 1, 'google', ?, ?)"
            );
            $stmt->execute([$firstname, $lastname, $email, $hashPassword, $googleId, $accessTokenJson]);

            $user = $user_model->findByEmail($email);
        }

        session_regenerate_id(true);
        $_SESSION['user'] = $user;
        $_SESSION['oauth_user'] = true;

        header('Location: index.php?action=home');
        exit;

    } catch (Exception $e) {
        $error = 'OAuth authentication failed: ' . $e->getMessage();
        header('Location: index.php?action=login&error=' . urlencode($error));
        exit;
    }
}

if (isset($_GET['error'])) {
    $error = $_GET['error'];
    header('Location: index.php?action=login&error=' . urlencode($error));
    exit;
}

header('Location: index.php?action=login');
exit;
?>
