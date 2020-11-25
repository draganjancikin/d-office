<?php
// echo "autoload.php" . "<br>";

function myAutoLoader($class) {
    // echo $class;
    switch ($class) {
        case 'Admin':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Admin/' . $class . '.class.php';
            break;
        case 'Article':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Article/' . $class . '.class.php';
            break;
        case 'Connection':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Database/' . $class . '.class.php';
            break;
        case 'Client':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Client/' . $class . '.class.php';
            break;
        case 'Contact':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Client/' . $class . '.class.php';
            break;
        case 'Cutting':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Cutting/' . $class . '.class.php';
            break;
        case 'Database':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Database/' . $class . '.class.php';
            break;
        case 'Material':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Material/' . $class . '.class.php';
            break;
        case 'Order':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Order/' . $class . '.class.php';
            break;
        case 'Pidb':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Pidb/' . $class . '.class.php';
            break;
        case 'Project':
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/Project/' . $class . '.class.php';
            break;
        default:
            $path = $_SERVER['DOCUMENT_ROOT'] . '/../src/Controller/' . $class . '.class.php';
            break;
    }
    // echo "PATH: " . $path;
    include $path;
}

spl_autoload_register('myAutoLoader');
