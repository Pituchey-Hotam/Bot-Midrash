<html dir="rtl">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <style>
        * {
            font-family: Heebo;
        }
    </style>
</head>

<?php
session_start();

require("include/config.php");

function isLogged()
{
    return isset($_SESSION['id']);
}

$request = substr($_SERVER['REQUEST_URI'], strlen(basename(__DIR__)) + 1);

if (isLogged()) {
    require 'include/navbar.php';
}

switch ($request) {
    case '/':
    case '':
    case '/home':
        if (isLogged()) {
            require 'pages/home.php';
        } else {
            header('Location: login');
        }
        break;
    case '/login':
        require 'pages/login.php';
        break;
    case '/logout':
        session_destroy();
        header("Location: login");
        break;
    default:
        http_response_code(404);
        require 'pages/404.php';
        break;
}
?>

</html>