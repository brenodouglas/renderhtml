<?php
namespace Render\Render;

use Render\Render\Interfaces\InterfaceRender;
use Render\Render\Interfaces\ViewHelperInterface;

/**
 * 
 * @author Breno Douglas <bdouglasans@gmail.com>
 */
class Render implements InterfaceRender
{

    private $scriptsCollection;
    private $stylesCollection;
    private $content;
    private $html;
    private static $viewPath;
    private static $layout;
    private static $viewsHelpers = new \ArrayIterator();

    public function __construct()
    {
        $this->scriptsCollection = new \ArrayIterator();
        $this->stylesCollection = new \ArrayIterator();
        //$this->registerHelpers();
    }

    public function __call($name, $arguments)
    {
        if (self::$viewsHelpers->offsetExists($name)) {
            $object = self::$viewsHelpers->offsetGet($name);

            if($object instanceof ViewHelperInterface)
                return $object->run();
        }
    }

    public function appendScript($path)
    {
        $this->scriptsCollection->append($path);
    }

    public function appendStyle($path)
    {
        $this->stylesCollection->append($path);
    }

    public function content()
    {
        return $this->content;
    }

    public function getScripts()
    {
        $html = "";
        while ($this->scriptsCollection->valid()) :
            $html .= "<script type='text/javascript' src='" . $this->scriptsCollection->current() . "'></script>\n";
            $this->scriptsCollection->next();
        endwhile;

        return $html;
    }

    public function getStyles()
    {
        $html = "";
        while ($this->stylesCollection->valid()) :
            $html .= "<link rel='stylesheet' type='text/css' href='" . $this->stylesCollection->current() . "' />\n";
            $this->stylesCollection->next();
        endwhile;

        return $html;
    }

    public function renderTemplate($page, array $vars = array(), $layout = true)
    {
        $this->extractVar($vars);
        $this->createContent($page);
        
        $page = explode(":", $page)[0];
        
        $fileLayout = self::$viewPath[$page] . "/" . self::$layout . ".phtml";
        if ($layout == true && file_exists($fileLayout)) {
            ob_start();
            require_once $fileLayout;
            $this->html = ob_get_clean();
            return $this->html;
        } else {
            return $this->content();
        }
    }

    public static function render($page, array $vars = array(), $layout = true)
    {
        $render = new static();
        echo $render->renderTemplate($page, $vars, $layout);
    }

    public static function getHtmlTemplate($page, array $vars = array(), $layout = false)
    {
        $render = new static();
        return $render->renderTemplate($page, $vars, $layout);
    }

    private function extractVar(array $var)
    {
        foreach ($var as $key => $value):
            $this->$key = $value;
        endforeach;
    }
    
    public function addPartial($page, array $vars = array())
    {
        $html = $this->createContentPartial($page);
        echo $html;
    }
    
    private function createContentPartial($action)
    {   
        $actions = explode(":", $action);
        
        $file = self::$viewPath[$actions[0]] . '/partials/' . $actions[1] . '.phtml';

        if (file_exists($file)) {
            ob_start();
            require_once $file;
            $content = ob_get_clean();
            return $content;
        } else {
            throw new \Exception("Partifal file no exists for action: {$file}");
        }
    }
    
    public function createContent($action)
    {   
        $actions = explode(":", $action);
        
        $file = self::$viewPath[$actions[0]] . '/' . $actions[1] . '.phtml';

        if (file_exists($file)) {
            ob_start();
            require_once $file;
            $this->content = ob_get_clean();
        } else {
            throw new \Exception("View file no exists for action");
        }
    }

    public static function setLayoutPath($alias, $path)
    {
        self::$viewPath[ucfirst($alias)] = $path;
    }

    public static function setupLayout($layout)
    {
        self::$layout = $layout;
    }

    public static function registerViewHelper($alias, $classNamespace)
    {
        self::$viewsHelpers->offsetSet($alias, new $classNamespace);
    }

    public function extendsLayout($layout)
    {
        self::$layout = $layout;
    }

}
