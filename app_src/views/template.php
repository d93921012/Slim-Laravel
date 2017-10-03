<!DOCTYPE html>
<html>
<head>
    <title>Slim2 basic application</title>
</head>
<body>
<h1>My Slim2 basic app-frame</h1>
<a href="<?= url('home') ?>">Home</a> |
<a href="<?= url('session-write') ?>">Session write</a> |
<a href="<?= url('session-read') ?>">Session read</a> |
<a href="<?= url('cou_cur') ?>">Controll test</a>
<div>
<?php echo $content; ?>
</div>
</body>
</html>