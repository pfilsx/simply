<?php
require_once '../src/Simply.php';

$simply = new Simply();

$action = isset($_GET['action']) ? $_GET['action'] : 'basic';
$simply->assign('html', '<i>encoded html</i>');
switch ($action) {
    case 'string':
        echo $simply->renderString('<?php $this->title = \'String template test\'; ?><html>
            <?= $this->render(\'header\') ?>
            <body>
                <h1><?= $title ?></h1> 
                <p>Basic template test rendered from string</p>
                <p>Encode test: <?= $this->encode($html) ?></p>
            </body></html>', array(
                'title' => 'String template'
        ));
        break;
    case 'basic':
    default:
    {
        $simply->display('index', array(
            'title' => 'Basic template'
        ));
        break;
    }
}
