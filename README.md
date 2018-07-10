Simply - Simple flexible PHP template engine
=============================

Installation
------------
1  Add next into `composer.json` `require`
```
"pfilsx/simply" : "*"
```
2 Add next into `composer.json` `repositories` 
```
{
    "type": "git",
    "url": "https://github.com/pfilsx/simply.git"
}
```

Configuration
-----
You can use next settings:
1. `templatesDirectory` - path to your templates directory(default: `'views'`)
2. `globalVariables` - array with global variables for yours templates(optional, default: empty array. Can be extended by `$simply->assign()`)

Usage
-----
```php
$simply = new Simply(array('templatesDirectory' => 'templates'));
$simply->assign('title', 'Заголовок'); // добавление глобальной переменной
$simply->display('index', array('text' => 'Текст')); // отображение 
```

Methods
-----
1. `display(string $view, array $params = []) : void` - render template from file.
2. `render(string $view, array $params = []) : string` - compile template from file into string variable.
3. `renderString(string $content, array $params = []) : string` - render template from string content.
4. `displayString(string $content, array $params = []) : void` - compile template from string content into string variable.
5. `assign(string $name, mixed $value) : void` - add variable into global variables array.
6. `encode(string $content) : string` - escape html characters.
