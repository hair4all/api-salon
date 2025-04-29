<?php

namespace App\Routing;

use Illuminate\Routing\ResourceRegistrar;

class CustomResourceRegistrar extends ResourceRegistrar
{
    protected function addResourceShow($name, $base, $controller, $options)
    {
        $uri    = $this->getResourceUri($name) . '/show/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'show', $options);
        return $this->router->get($uri, $action);
    }

    protected function addResourceUpdate($name, $base, $controller, $options)
    {
        $uri    = $this->getResourceUri($name) . '/edit/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'update', $options);
        return $this->router->put($uri, $action);
    }

    protected function addResourceDestroy($name, $base, $controller, $options)
    {
        $uri    = $this->getResourceUri($name) . '/delete/{' . $base . '}';
        $action = $this->getResourceAction($name, $controller, 'destroy', $options);
        return $this->router->delete($uri, $action);
    }
}
