<!doctype html>
<html lang="sv">
<head>
	<meta charset="utf-8"/>
	<title><?=$this->getTitle();?></title>

	<?php if (isset($_favicon)): ?><link rel="shortcut icon" href="<?=$_favicon?>"/><?php endif; ?>

	<?php if (isset($_stylesheets)): ?>

	<?php foreach ($_stylesheets as $style): ?>
	<link rel="stylesheet" type="text/css" href="<?=$style?>"/>
	<?php endforeach; ?>

	<?php endif; ?>

</head>
<body>

	<p><?= $content ?></p>
	
  	<?php if (isset($_jquery)): ?><script src="<?=$_jquery?>"></script><?php endif; ?>

	<?php if (isset($_javascript)): ?>
	<?php foreach($_javascript as $script): ?>
	<script src="<?=$script?>"></script>
	<?php endforeach; ?>
	<?php endif;?>

</body>
</html>
