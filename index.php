<?php
session_start();
require_once 'config/google-config.php';
require_once 'controllers/AuthController.php';
require_once 'controllers/StoreController.php';

$auth = new AuthController();
$store = new StoreController();
$action = $_GET['action'] ?? 'login';
$error = "";
$success = "";

// GOOGLE LOGIN
if ($action == 'google-login') {
    $googleAuthUrl = $auth->getGoogleAuthUrl();
    header('Location: ' . $googleAuthUrl);
    exit;
}

// GOOGLE CALLBACK
if ($action == 'google-callback') {
    if (!isset($_GET['code'])) {
        $error = "Authorization failed.";
        include 'views/login.php';
    } else {
        $result = $auth->handleGoogleCallback($_GET['code'], $_GET['state'] ?? null);
        
        if ($result['success']) {
            session_regenerate_id(true);
            $_SESSION['user'] = $result['user'];
            header('Location: index.php?action=home');
            exit;
        } else {
            $error = $result['message'];
            include 'views/login.php';
        }
    }
}

// REGISTER
if ($action == 'register' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $auth->register($_POST);

    if (is_array($result) && $result['success']) {
        $_SESSION['pending_verification'] = true;
        $_SESSION['pending_email'] = $_POST['email'];
        header('Location: index.php?action=verify-pending');
        exit;
    } else {
        if (is_array($result)) {
            $error = $result['message'] ?? 'Registration failed';
        } else {
            $error = $result;
        }
        include 'views/register.php';
    }

// LOGIN
} elseif ($action == 'login' && $_SERVER['REQUEST_METHOD'] == 'POST') {
    $result = $auth->login($_POST);

    if (is_array($result) && isset($result['error'])) {
        if ($result['error'] === 'email_not_verified') {
            $_SESSION['pending_verification'] = true;
            $_SESSION['pending_email'] = $result['email'];
            header('Location: index.php?action=verify-pending');
            exit;
        }
    }

    if (is_array($result) && isset($result['id'])) {
        // Successfully logged in - result is the user array
        session_regenerate_id(true);
        $_SESSION['user'] = $result;
        header('Location: index.php?action=home');
        exit;
    } else {
        $error = "Invalid email or password!";
        include 'views/login.php';
    }

// VERIFY EMAIL LINK
} elseif ($action == 'verify') {
    if (!isset($_GET['token']) || empty($_GET['token'])) {
        $message = 'Invalid verification link.';
        $alert_type = 'alert-danger';
        include 'views/verify.php';
    } else {
        $result = $auth->verifyEmail($_GET['token']);
        if ($result['success']) {
            $message = $result['message'];
            $alert_type = 'alert-success';
        } else {
            $message = $result['message'];
            $alert_type = 'alert-warning';
        }
        include 'views/verify.php';
    }

// VERIFY PENDING PAGE
} elseif ($action == 'verify-pending') {
    if (!isset($_SESSION['pending_verification'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $message = '';
    $alert_type = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
        $resend_result = $auth->resendVerificationEmail($_POST['email']);
        $message = $resend_result['message'];
        $alert_type = $resend_result['success'] ? 'alert-success' : 'alert-warning';
    }

    include 'views/verify-pending.php';

// RESEND VERIFICATION
} elseif ($action == 'resend-verify') {
    if (!isset($_SESSION['pending_verification'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $message = '';
    $alert_type = '';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['email'])) {
        $resend_result = $auth->resendVerificationEmail($_POST['email']);
        $message = $resend_result['message'];
        $alert_type = $resend_result['success'] ? 'alert-success' : 'alert-warning';
    }

    include 'views/verify-pending.php';

// HOME
} elseif ($action == 'home') {
    if (isset($_SESSION['user'])) {
        $dashboard = $store->getDashboardData();
        include 'views/home.php';
    } else {
        header('Location: index.php?action=login');
    }

// SALES
} elseif ($action == 'sales') {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $products = $store->getProducts();
    $sales = $store->getSales();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $store->processPurchase($_POST);
        if ($result === true) {
            $success = 'Purchase saved successfully.';
        } else {
            $error = $result;
        }

        $products = $store->getProducts();
        $sales = $store->getSales();
    }

    include 'views/sales.php';

// INVENTORY
} elseif ($action == 'inventory') {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $products = $store->getProducts();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $store->restockProduct($_POST);
        if ($result === true) {
            $success = 'Product restocked successfully.';
        } else {
            $error = $result;
        }

        $products = $store->getProducts();
    }

    include 'views/inventory.php';

// CUSTOMERS
} elseif ($action == 'customers') {
    if (!isset($_SESSION['user'])) {
        header('Location: index.php?action=login');
        exit;
    }

    $customers = $store->getCustomers();

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $result = $store->payOutstanding($_POST);
        if ($result === true) {
            $success = 'Outstanding balance paid successfully.';
        } else {
            $error = $result;
        }

        $customers = $store->getCustomers();
    }

    include 'views/customers.php';

// LOGOUT
} elseif ($action == 'logout') {
    session_destroy();
    header('Location: index.php?action=login');

// SHOW REGISTER FORM
} elseif ($action == 'register') {
    include 'views/register.php';

} else {
    include 'views/login.php';
}
?>