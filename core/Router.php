<?php
class Router
{
    private $routes = [];
    public function add($path, $callback)
    {
        $this->routes[$path] = $callback;
    }
    public function dispatch()
    {
        $path = strtok($_SERVER["REQUEST_URI"], "?") ?: "/";
        $callback = $this->routes[$path] ?? null;
        if (!$callback) {
            http_response_code(404);
            echo "Page non trouvée";
            return;
        }
        echo call_user_func($callback);
    }
}

