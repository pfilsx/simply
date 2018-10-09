<?php


namespace pfilsx\simply;
use Exception;
use ParseError;

/**
 * Package Simply
 */
class Simply
{
    /**
     * Path to directory with templates
     * @var string
     */
    protected $templatesDirectory = 'views';
    /**
     * Global variables array passed to all templates
     * @var array
     */
    protected $globalVariables = [];

    /**
     * Path to layout file(without .php)
     * Layout file must be in templates directory, path will be generated as $templatesDirectory.$layout
     * @var null|string
     */
    protected $layout = null;

    /**
     * Simply constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        $this->loadConfig($config);
        $this->templatesDirectory = $this->normalizePath($this->templatesDirectory);
        if (!is_dir($this->templatesDirectory)){
            throw new Exception("Templates directory does not exist: '{$this->templatesDirectory}'");
        }
        if (!empty($this->layout)){
            $this->layout = $this->templatesDirectory.DIRECTORY_SEPARATOR.ltrim($this->layout, '/\\');
            if (substr($this->layout, -4) != '.php'){
                $this->layout .= '.php';
            }
            if (!is_file($this->layout)){
                throw new Exception("Layout file does not exist: '{$this->layout}'");
            }
        }
        $this->init();
    }

    /**
     * Loads configuration
     * @param $config
     */
    protected function loadConfig($config){
        if (is_array($config) && !empty($config)){
            foreach ($config as $name => $value){
                if (property_exists($this, $name)){
                    $this->$name = $value;
                }
            }
        }
    }

    /**
     * Use this if you need to do some actions after construct
     */
    protected function init(){

    }

    /**
     * Adds variable to a global variables array
     * @param $name - variable name
     * @param $value - variable value
     * @see $globalVariables
     */
    public function assign($name, $value){
        $this->globalVariables[$name] = $value;
    }

    /**
     * Renders view file
     * @param $viewName - view path from $templatesDirectory
     * @param array $params - params
     * @return string - render result
     * @throws Exception
     */
    public function render($viewName, $params = []){
        $content = $this->renderPartial($viewName, $params);
        if (empty($this->layout)){
            return $content;
        }
        return $this->renderLayout($content, $params);
    }

    /**
     * Displays view file
     * @param $viewName - view path from $templatesDirectory
     * @param array $params - params
     * @throws Exception
     */
    public function display($viewName, $params = []){
        echo $this->render($viewName, $params);
    }

    /**
     * Renders view file without layout
     * @param $viewName - view path from $templatesDirectory
     * @param array $params - params
     * @return string - render result
     * @throws Exception
     */
    public function renderPartial($viewName, $params = []){
        $fileName = $this->normalizePath($this->templatesDirectory.DIRECTORY_SEPARATOR.ltrim($viewName, '\\/'));
        if (substr($fileName, -4) != '.php'){
            $fileName = $fileName.'.php';
        }
        if (!is_file($fileName)){
            throw new Exception("View file does not exist: {$fileName}");
        }
        return $this->renderFile($fileName, $params);
    }
    /**
     * Рендеринг шаблона из файла
     * @param $viewName
     * @param array $params
     * @throws Exception
     * @see renderPartial()
     */
    public function displayPartial($viewName, $params = []){
        echo $this->renderPartial($viewName, $params);
    }
    /**
     * Renders view from string(not recommended cause by security reasons)
     * @param $content - string representation of view
     * @param array $params - params
     * @return string - render result
     * @throws Exception
     */
    public function renderString($content, $params = []){
        $content = $this->renderStringPartial($content, $params);
        if (empty($this->layout)){
            return $content;
        }
        return $this->renderLayout($content, $params);
    }
    /**
     * Displays view from string without layout(not recommended cause by security reasons)
     * @param $content - string representation of view
     * @param array $params - params
     * @throws Exception
     */
    public function displayString($content, $params){
        echo $this->renderString($content, $params);
    }

    /**
     * Renders view from string without layout(not recommended cause by security reasons)
     * @param $content - string representation of view
     * @param array $params - params
     * @return string - render result
     * @throws Exception
     */
    public function renderStringPartial($content, $params = []){
        $obInitialLevel = ob_get_level();
        $params = array_merge($this->globalVariables, $params);
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);

        $phpVer = (int)(substr(phpversion(),0,1));
        if ($phpVer >= 7)
        {
            try {
                eval("?> $content");
                return ob_get_clean();
            }
            catch (ParseError $ex){
                while (ob_get_level() > $obInitialLevel) {
                    if (!@ob_end_clean()) {
                        ob_clean();
                    }
                }
                throw new Exception($ex->getMessage());
            }
            catch (Exception $ex){
                while (ob_get_level() > $obInitialLevel) {
                    if (!@ob_end_clean()) {
                        ob_clean();
                    }
                }
                throw $ex;
            }
        } else {
            $result = @eval("?> $content");
            if ($result === false){
                throw new Exception('Ошибка в синтаксисе шаблона');
            }
            return ob_get_clean();
        }
    }

    /**
     * Displays view from string without layout(not recommended cause by security reasons)
     * @param $content - string representation of view
     * @param array $params - params
     * @throws Exception
     */
    public function displayStringPartial($content, $params = []){
        echo $this->renderStringPartial($content, $params);
    }

    /**
     * Encodes string
     * @param $content
     * @param bool $doubleEncode
     * @return string
     */
    public function encode($content, $doubleEncode = true){
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * Renders layout file
     * @param $content - view content
     * @param $params - params
     * @return string - render result
     * @throws Exception
     */
    protected function renderLayout($content, $params){
        return $this->renderFile($this->layout, array_merge(
            $params, ['_content_' => $content]
        ));
    }

    /**
     * Renders php file
     * @param $file - path to file
     * @param array $params - params
     * @return string - render result
     * @throws Exception
     */
    protected function renderFile($file, $params = []){
        $obInitialLevel = ob_get_level();
        $params = array_merge($this->globalVariables, $params);
        $params['_renderedFile'] = $file;
        $params['_layout'] = $this->layout;
        ob_start();
        ob_implicit_flush(false);
        extract($params, EXTR_OVERWRITE);
        try {
            require $file;
            return ob_get_clean();
        }
        catch (Exception $ex){
            while (ob_get_level() > $obInitialLevel) {
                if (!@ob_end_clean()) {
                    ob_clean();
                }
            }
            throw $ex;
        }
    }

    /**
     * @param $path - path
     * @param string $ds - separator
     * @return string - normalized path
     */
    protected function normalizePath($path, $ds = DIRECTORY_SEPARATOR)
    {
        $path = strtr($path, '/\\', $ds . $ds);
        $path = rtrim($path, $ds);
        if (strpos($ds . $path, "{$ds}.") === false && strpos($path, "{$ds}{$ds}") === false) {
            return $path;
        }
        if (strpos($path, "{$ds}{$ds}") === 0 && $ds == '\\') {
            $parts = [$ds];
        } else {
            $parts = [];
        }
        foreach (explode($ds, $path) as $part) {
            if ($part === '..' && !empty($parts) && end($parts) !== '..') {
                array_pop($parts);
            } elseif ($part === '.' || $part === '' && !empty($parts)) {
                continue;
            } else {
                $parts[] = $part;
            }
        }
        $path = implode($ds, $parts);
        return $path === '' ? '.' : $path;
    }

}