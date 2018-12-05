<?php
require_once __DIR__.'/../src/Simply.php';
require_once __DIR__.'/../src/Html.php';

$simply = new \pfilsx\simply\Simply(['layout' => 'layout/layout']);

$action = isset($_GET['action']) ? $_GET['action'] : 'basic';
$simply->assign('html', '<i>encoded html</i>');
switch ($action) {
    case 'string':
        $simply->assign('title', 'String template');
        echo $simply->renderString('<h1><?= $title ?></h1> 
                <p>Basic template test rendered from string</p>
                <p>Encode test: <?= $this->encode($html) ?></p>');
        break;
    case 'basic':
    default:
        {
            $simply->assign('title', 'Basic template');
            $simply->display('layout/content');
            break;
        }
}