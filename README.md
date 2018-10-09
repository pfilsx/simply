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
2. `globalVariables` - array with global variables for yours templates(optional, default: `[]`. Can be extended by `$simply->assign()`)
3. `layout` - path to your layout from templates directory(optional, default: `null`)

Usage
-----
```php
$simply = new Simply(array('templatesDirectory' => 'templates', 'layout' => 'main'));
$simply->assign('title', 'Заголовок'); // add global variable
$simply->display('index', array('text' => 'Текст')); // display 
```
See [demo](https://github.com/pfilsx/simply/tree/master/demo) directory.

Methods
-----
1. `display(string $view, array $params = []) : void` - render template from file.
2. `render(string $view, array $params = []) : string` - compile template from file into string variable.
3. `renderString(string $content, array $params = []) : string` - render template from string content.
4. `displayString(string $content, array $params = []) : void` - compile template from string content into string variable.
5. `assign(string $name, mixed $value) : void` - add variable into global variables array.
6. `encode(string $content) : string` - escape html characters.
