<?php

namespace App\Core;

use App\Controller\HomeController;
use Exception;

/**
 * Router class.
 *
 * @author Dragan Jancikin <dragan.jancikin@gmail.com>
 */
class Router {
  private $routes = [];

  /**
   * Add a route.
   *
   * @param $method
   *   The HTTP method.
   * @param $route
   *   The route pattern.
   * @param $action
   *   The controller action.
   *
   * @return void
   */
  public function add($method, $route, $action) {
    $method = strtoupper($method);
    $this->routes[] = ['method' => $method, 'route' => $route, 'action' => $action];
  }

  /**
   * Dispatch a route.
   *
   * @param $uri
   *   The request URI.
   * @param $httpMethod
   *   The HTTP method.
   *
   * @return void
   * @throws Exception
   */
  public function dispatch($uri, $httpMethod) {
    // Normalize HTTP method.
    $httpMethod = strtoupper($httpMethod);

    // Separate the path and query string.
    $uriParts = explode('?', $uri, 2);
    $path = $uriParts[0];
    $queryString = $uriParts[1] ?? '';

    // Parse query string into an array.
    parse_str($queryString, $queryParams);

    foreach ($this->routes as $routeInfo) {
      $route = $routeInfo['route'];
      $method = $routeInfo['method'];
      $action = $routeInfo['action'];

      if ($this->match($route, $path, $params) && $httpMethod === $method) {
        // Merge route parameters and query parameters
        $params = array_merge($params, $queryParams);
        $this->executeAction($action, $params);
        return;
      }
    }
    throw new Exception("Route not found or method not allowed");
  }


  /**
   * Match URI to a route.
   *
   * @param $route
   *   The route pattern.
   * @param $uri
   *   The request URI.
   * @param $params
   *   The parameters array.
   *
   * @return bool
   *   TRUE if the route matches the URI, FALSE otherwise.
   */
  private function match($route, $uri, &$params) {
    // Convert dynamic placeholders in the route to regex patterns.
    $routePattern = preg_replace('/{(\w+)}/', '([^/]+)', $route);
    $routePattern = '#^' . $routePattern . '$#';

    // Check if the URI matches the pattern.
    if (preg_match($routePattern, $uri, $matches)) {
      // The first match is the full URI, we discard it.
      array_shift($matches);
      // Fill the parameters array with dynamic parameters.
      preg_match_all('/{(\w+)}/', $route, $paramNames);
      $params = array_combine($paramNames[1], $matches);
      return true;
    }

    return false;
  }

  /**
   * Execute the controller action.
   *
   * @param $action
   *   The controller action.
   * @param $params
   *   The parameters array.
   *
   * @return void
   * @throws Exception
   */
  private function executeAction($action, $params) {
    list($controller, $method) = explode('@', $action);

    $namespace = '\App\Controller';
    $fullyQualifiedName = $namespace . '\\' . $controller;

    $controller = new $fullyQualifiedName();
    if (!method_exists($controller, $method)) {
      throw new Exception("Method $method not found in controller $controller");
    }

    // Call the method with parameters.
    call_user_func_array([$controller, $method], $params);
  }

}
