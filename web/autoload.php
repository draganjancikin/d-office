<?php
// echo "autoload.php" . "<br>";

function myAutoLoader($class) {
    // echo $class;
    switch ($class) {
        case 'AdminController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'ArticleController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'ConnectionController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'ClientController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'ContactController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'CuttingController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'DatabaseController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'MaterialController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'OrderController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'PidbController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        case 'ProjectController':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
        default:
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.php';
            break;
    }
    // echo "PATH: " . $path . "<br>";
    include $path;
}

spl_autoload_register('myAutoLoader');
