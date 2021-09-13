<?php

namespace Ismaxim\ScratchFrameworkCore;

class View
{
    public string $title = '';

    public function renderView(string $view, array $params = [])
    {
        $viewContent = $this->renderOnlyView($view, $params);
        $layoutContent = $this->layoutContent();
        
        return str_replace('{{ content }}', $viewContent, $layoutContent);
    }

    /* public function renderContent($viewContent)
    {
        $layoutContent = $this->layoutContent();
        return str_replace('{{ content }}', $viewContent, $layoutContent);
    } */

    protected function layoutContent()
    {
        $layout = Application::$app->layout;

        if (Application::$app->controller) {
            $layout = Application::$app->controller->layout;
        }

        ob_start();
        include_once Application::$ROOT_DIR."/views/layouts/$layout.php";
        return ob_get_clean();
    }

    protected function renderOnlyView(string $view, array $params = [])
    {
        ob_start();
        extract($params);
        include_once Application::$ROOT_DIR."/views/$view.php";
        return ob_get_clean();
    }
}