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
     * @var string
     */
    protected $templatesDirectory = 'views';
    /**
     * @var array
     */
    protected $globalVariables = [];

    /**
     * Simply constructor.
     * @param array $config
     * @throws Exception
     */
    public function __construct($config = [])
    {
        if (is_array($config) && !empty($config)){
            foreach ($config as $name => $value){
                $this->$name = $value;
            }
        }
        $this->templatesDirectory = $this->normalizePath($this->templatesDirectory);
        if (!is_dir($this->templatesDirectory)){
            throw new Exception("Указана несуществующая директория для шаблонов: '{$this->templatesDirectory}'");
        }
        $this->init();
    }

    /**
     * Используйте данный метод, если вам нужно выполнить какие-либо действия после инициализации объекта класса
     */
    protected function init(){

    }

    /**
     * Добавление переменной к глобальному массиву переменных
     * @param $name
     * @param $value
     */
    public function assign($name, $value){
        $this->globalVariables[$name] = $value;
    }

    /**
     * Компиляция шаблона из файла
     * @param $viewName - имя шаблона относительно базовой директории шаблонов
     * @param array $params - параметры для передачи в шаблон
     * @return string - результат компиляции шаблона
     * @throws Exception
     */
    public function render($viewName, $params = []){
        $fileName = $this->normalizePath($this->templatesDirectory.DIRECTORY_SEPARATOR.ltrim($viewName, '\\/'));
        if (substr($fileName, -4) != '.php'){
            $fileName = $fileName.'.php';
        }
        if (!is_file($fileName)){
            throw new Exception("Не найден файл шаблона: {$fileName}");
        }
        return $this->renderFile($fileName, $params);
    }

    /**
     * Рендеринг шаблона из файла
     * @param $viewName
     * @param array $params
     * @throws Exception
     */
    public function display($viewName, $params = []){
        echo $this->render($viewName, $params);
    }

    /**
     * Компиляция шаблона из строки(не рекомендуется к использованию по причинам безопасности)
     * @param $content
     * @param array $params
     * @return string
     * @throws Exception
     */
    public function renderString($content, $params = []){
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
     * Рендеринг шаблона из строки(не рекомендуется к использованию по причинам безопасности)
     * @param $content
     * @param array $params
     * @throws Exception
     */
    public function displayString($content, $params = []){
        echo $this->renderString($content, $params);
    }

    /**
     * Эскейп html символов
     * @param $content
     * @param bool $doubleEncode
     * @return string
     */
    public function encode($content, $doubleEncode = true){
        return htmlspecialchars($content, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode);
    }

    /**
     * @param $file
     * @param array $params
     * @return string
     * @throws Exception
     */
    protected function renderFile($file, $params = []){
        $obInitialLevel = ob_get_level();
        $params = array_merge($this->globalVariables, $params);
        $params['_renderedFile'] = $file;
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
     * Приведение пути к нормализованному виду
     * @param $path - путь
     * @param string $ds - разделитель
     * @return string
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