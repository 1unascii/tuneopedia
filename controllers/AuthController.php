<?php

require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/User.php');

class AuthController {

    public function login() {
        session_start();
        $db = connect();
        if (!$db) {
            echo 'Database error';
            return;
        }

        $user = User::authenticate($db, $_POST['user_name'], $_POST['password']);

        if ($user) {
            session_regenerate_id(true);
            $_SESSION['Authenticated'] = true;
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_name'] = $user['user_name'];
            echo 'Login successful!';
        } else {
            echo 'Invalid username or password.';
        }
    }

    public function logout() {
        session_start();
        if (array_key_exists('Authenticated', $_SESSION)) {
            session_destroy();
            echo "You have been logged out";
        } else {
            $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\');
            $base = preg_replace('#/public$#', '', $base);
            header("Location: $base/");
        }
    }

    public function register() {
        session_start();
        $db = connect();
        if (!$db) {
            echo 'Database error';
            return;
        }

        $result = User::register(
            $db,
            $_POST['first_name'],
            $_POST['last_name'],
            $_POST['user_name'],
            $_POST['email'],
            $_POST['password']
        );

        if ($result['success']) {
            echo 'Thank you for signing up';
        } else {
            switch ($result['error']) {
                case 'email_taken':
                    echo 'The email you entered is already in use';
                    break;
                case 'username_taken':
                    echo 'The username you entered is already in use';
                    break;
                default:
                    echo 'There was an unexpected error';
                    break;
            }
        }
    }
}
