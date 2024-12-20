<?php
class Router
{
    private $routes = [];

    public function add($path, $callback)
    {
        // Convert {parameter} syntax to regex group (?P<parameter>)
        $path = preg_replace('/\{([a-zA-Z0-9_]+)\}/', '(?P<\1>[a-zA-Z0-9_]+)', $path);
        $path = '#^' . $path . '$#';
        $this->routes[$path] = $callback;
    }

    public function dispatch()
    {
        $path = strtok($_SERVER['REQUEST_URI'], '?') ?: '/';

        foreach ($this->routes as $route => $callback) {
            if (preg_match($route, $path, $matches)) {
                // Filter to include only named parameters
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);

                // Ensure callback is called with the extracted parameters
                echo call_user_func_array($callback, $params);
                return;
            }
        }

        http_response_code(404);
        echo "Page non trouvée";
    }
}
