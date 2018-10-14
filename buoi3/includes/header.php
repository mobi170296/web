<?php
	if(defined('M_RUNNING')){
?>
	<!DOCTYPE html>
	<html>
	<head>
		<meta charset="utf8"/>
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?php if(isset($CNF['TITLE'])) echo $CNF['TITLE']; ?></title>
		<?php
			if(isset($CNF['SCRIPTS']))
				foreach($CNF['SCRIPTS'] as $link){
					echo '<script type="text/javascript" src="'.$link.'"></script>';
				}
			if(isset($CNF['STYLES']))
				foreach($CNF['STYLES'] as $link){
					echo '<link rel="stylesheet" href="'.$link.'"/>';
				}
		?>
	</head>
	<body>
	<div id="container">
<?php
	}
?>