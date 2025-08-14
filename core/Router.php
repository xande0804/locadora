<?php
class Router {
    public function dispatch() {
        $url = isset($_GET['url']) ? rtrim($_GET['url'], '/') : 'home/index';
        $url = filter_var($url, FILTER_SANITIZE_URL);
        $urlParts = explode('/', $url);

        $controllerName = !empty($urlParts[0]) ? ucfirst($urlParts[0]) . 'Controller' : 'HomeController';
        $methodName = isset($urlParts[1]) ? $urlParts[1] : 'index';
        $params = array_slice($urlParts, 2);

        if (file_exists('../app/controllers/' . $controllerName . '.php')) {
            $controller = new $controllerName;
            if (method_exists($controller, $methodName)) {
                call_user_func_array([$controller, $methodName], $params);
            } else {
                die("Método '{$methodName}' não encontrado no controller '{$controllerName}'.");
            }
        } else {
            die("Controller '{$controllerName}' não encontrado.");
        }
    }
}
?>