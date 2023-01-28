<?php
// web/index.php
require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing;

$request = Request::createFromGlobals();
$routes = include __DIR__.'/../src/app.php';

$context = new Routing\RequestContext();
$context->fromRequest($request);
$matcher = new Routing\Matcher\UrlMatcher($routes, $context);

try {

    require_once __DIR__ . '/../config/bootstrap.php';
    session_start();
    if (isset($_SESSION['username'])){
        $username = $_SESSION['username'];
        $user_role_id = $_SESSION['user_role_id'];
        extract($matcher->match($request->getPathInfo()), EXTR_SKIP);
        ob_start();
        include sprintf(__DIR__.'/../templates/%s.php', $_route);
    } else {
        include '../templates/formLogin.php';
    }
    $response = new Response(ob_get_clean());

} catch (Routing\Exception\ResourceNotFoundException $exception) {
    $response = new Response('Not Found', 404);
} catch (Exception $exception) {
    $response = new Response('An error occurred', 500);
}

$response->send();

// https://symfony.com/doc/5.4/create_framework/templating.html
