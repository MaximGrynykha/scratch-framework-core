<?php

namespace Ismaxim\ScratchFrameworkCore;

use Ismaxim\ScratchFrameworkCore\middlewares\BaseMiddleware;

class Controller
{
    public string $layout = 'main';
    public string $action = '';

    /**
     * @var \app\core\middlewares\BaseMiddleware[]
     */
    protected array $middlewares = [];

    protected function render(string $view, array $params = [])
    {
        return Application::$app->view->renderView($view, $params);
    }

    protected function setLayout(string $layout)
    {
        $this->layout = $layout;
    }

    protected function registerMiddleware(BaseMiddleware $middleware)
    {   
        $this->middlewares[] = $middleware;
    }

    public function getMiddlewares()
    {
        return $this->middlewares;
    }
}