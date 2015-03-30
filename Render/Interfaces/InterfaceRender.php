<?php
namespace Render\Render\Interfaces;

/**
 *
 * @author breno douglas <bdouglasans@gmail.com>
 * @package Render template
 */
interface InterfaceRender
{
    
    public function content();
    public static function render($page, array $vars = array(), $layout = true);
    public function appendScript($path);
    public function appendStyle($path);
    public function getStyles();
    public function getScripts();
    public function createContent($action);
    public function addPartial($page, array $vars = array());
    
}