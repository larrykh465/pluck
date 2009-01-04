<?php
/*
 * This file is part of pluck, the easy content management system
 * Copyright (c) somp (www.somp.nl)
 * http://www.pluck-cms.org

 * Pluck is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.

 * See docs/COPYING for the complete license.
*/

//Make sure the file isn't accessed directly.
if (!strpos($_SERVER['SCRIPT_FILENAME'], 'index.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'admin.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'install.php') && !strpos($_SERVER['SCRIPT_FILENAME'], 'login.php')) {
	//Give out an "Access denied!" error.
	echo 'Access denied!';
	//Block all other code.
	exit();
}

//Page
if ($_GET['cat'] == 'page' && file_exists('data/trash/pages/'.$_GET['var']))
	unlink('data/trash/pages/'.$_GET['var']);

//Image
if ($_GET['cat'] == 'image' && file_exists('data/trash/images/'.$_GET['var']))
	unlink('data/trash/images/'.$_GET['var']);

//Redirect
redirect('?action=trashcan', 0);
?>