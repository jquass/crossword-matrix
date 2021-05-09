<?php
session_start();

if ('POST' === $_SERVER['REQUEST_METHOD']) {
    switch ($_POST['btnSubmit']) {
        case 'Login':
            if (authorizeUser($_POST['email'], $_POST['password'])) {
                $_SESSION['email'] = $_POST['email'];
            }
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        case 'Logout':
            session_destroy();
            header('Location: ' . $_SERVER['REQUEST_URI']);
            exit();
        default;
            break;
    }

}

if (empty($_SESSION) || !array_key_exists('email', $_SESSION) || !$_SESSION['email']) {
    die ('
<h1>Login</h1>
<form method="post" id="login">
    <label for="email"> EMAIL </label>
    <input type="text" name="email" width="21">
    <br>
    <label for="password"> PASSWORD </label>
    <input type="password" name="password" width="21">
    <br>
    <input type="submit" value="Login" name="btnSubmit">
</form>
    ');
}
