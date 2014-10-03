<?php 
namespace Render\Render;

use Render\Render\Interfaces\InterfaceRender;

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
    
    public function __construct()
    {
        $this->scriptsCollection = new \ArrayIterator();
        $this->stylesCollection = new \ArrayIterator();
        //$this->registerHelpers();
    }
    
    public function __call($name, $arguments)
    {
        if(isset($this->$name)){
            $object = clone $this->$name;
            return $object();
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
        
        $fileLayout = self::$viewPath. "/" . self::$layout. ".phtml";
        if ($layout == true && file_exists($fileLayout)) {
            ob_start();
                require_once $fileLayout;
            $this->html = ob_get_clean();
            echo $this->html;
        } else {
            echo $this->content();
        }
	}
	
    public static function render($page, array $vars = array(), $layout = true)
    {   
		$render = new static();
        $render->renderTemplate($page, $vars, $layout);
    }
    
    private function extractVar(array $var)
    {
        foreach ($var as $key => $value):
            $this->$key = $value;
        endforeach;
    }
    
    public function createContent($action)
    {
        $file = self::$viewPath . '/' . $action . '.phtml';
        
        if(file_exists($file)) {
            ob_start();
                require_once $file;
            $this->content = ob_get_clean();
        } else {
            throw new \Exception("View file no exists for action");
        }
    }
    
    public static function setLayoutPath($path) 
    {
        self::$viewPath = $path;
    }
    
    public static function setupLayout($layout) 
    {
        self::$layout = $layout;
    }
    
    public function extendsLayout($layout) 
    {
        self::$layout = $layout;
    }
	
}