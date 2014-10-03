<?php

namespace Render\Render\Traits;

/**
 * Description of ViewHelper
 *
 * @author bdouglas
 */
trait ViewHelper
{

    public function generateUrl(array $string)
    {
        list($module, $controller, $action) = explode(":", current($string));
        $modules = Module::getModules();
        $url = '';
        foreach ($modules as $key => $value):
            $url .= $value == $module ? $key : '';
        endforeach;
        
        $url .= "/" . strtolower($controller) . "/" . strtolower($action);
        $params = end($string);
        if (is_array($params)) {
            foreach ($params as $key => $value) {
                $url .= "/" . $key . "/" . $value;
            }
        }
        return $url;
    }

    public function generateUrlPath($dados)
    {
        list($module, $controller, $action) = explode(":", $dados);
        $modules = Module::getModules();
        $url = "http://" . $_SERVER['HTTP_HOST'];

        foreach ($modules as $key => $value):
            $url .= $value == $module ? $key : '';
        endforeach;

        $url .= "/" . strtolower($controller) . "/" . strtolower($action);
        return $url;
    }
    
    public function registerHelpers()
    {
        $array = \App\Module::getHelpers();
        
        foreach ($array as $key => $value):
            $this->$key = clone ($value);
        endforeach;   
    }
}