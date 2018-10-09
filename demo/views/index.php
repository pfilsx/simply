<html>
    <?= $this->render('header', ['title' => 'Basic template']) ?>
    <body>
        <h1><?= $title ?></h1>
        <p>Basic template test rendered from file: <?= $_renderedFile ?></p>
        <p>Encode test: <?= $this->encode($html) ?></p>
    </body>
</html>

