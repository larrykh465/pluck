<?php
require_once 'data/modules/albums/functions.php';

require_once 'data/inc/lib/SmartImage.class.php';

function albums_page_admin_list() {
	global $lang_albums, $lang_albums5, $lang_albums6, $lang_albums15, $lang_kop13, $lang_updown5, $var1;

	if (isset($var1))
		include (MODULE_SETTINGS.'/'.$var1.'.php');
	else
		$album_name = '';

	$module_page_admin[] = array(
		'func'  => 'albums',
		'title' => $lang_albums
	);
	$module_page_admin[] = array(
		'func'  => 'editalbum',
		'title' => $lang_albums6.' - '.$album_name
	);
	$module_page_admin[] = array(
		'func'  => 'deletealbum',
		'title' => $lang_albums5
	);
	$module_page_admin[] = array(
		'func'  => 'editimage',
		'title' => $lang_albums15
	);
	$module_page_admin[] = array(
		'func'  => 'deleteimage',
		'title' => $lang_kop13
	);
	$module_page_admin[] = array(
		'func'  => 'imageup',
		'title' => $lang_updown5
	);
	$module_page_admin[] = array(
		'func'  => 'imagedown',
		'title' => $lang_updown5
	);
	return $module_page_admin;
}

function albums_page_admin_albums() {
	global $cont1, $lang, $lang_albums1, $lang_albums2, $lang_albums3, $lang_albums4, $lang_albums19, $lang_albums16;
	?>
		<p>
			<strong><?php echo $lang_albums1; ?></strong>
		</p>
		<span class="kop2"><?php echo $lang_albums2; ?></span>
		<br />
		<?php
		read_albums(MODULE_SETTINGS);
		?>
			<br /><br />
			<label class="kop2" for="cont1"><?php echo $lang_albums3; ?></label>
			<br />
			<form method="post" action="">
				<span class="kop4"><?php echo $lang_albums4; ?></span>
				<br />
				<input name="cont1" id="cont1" type="text" />
				<input type="submit" name="submit" value="<?php echo $lang['general']['save']; ?>" />
			</form>
		<?php
		//When form is submitted.
		if (isset($_POST['submit'])) {
			if (isset($cont1) && file_exists(MODULE_SETTINGS.'/'.$cont1))
				show_error($lang_albums19, 1);

			elseif (isset($cont1)) {
				//The pretty album name.
				$album_name = sanitize($cont1);

				//Make the album url safe to use.
				$cont1 = seo_url($cont1);

				//Create and chmod directories.
				mkdir(MODULE_SETTINGS.'/'.$cont1, 0777);
				mkdir(MODULE_SETTINGS.'/'.$cont1.'/thumb', 0777);

				//Create album file.
				save_file(MODULE_SETTINGS.'/'.$cont1.'.php','<?php $album_name = \''.$album_name.'\'; ?>');

				redirect('?module=albums', 0);
			}
		}
	?>
	<p>
		<a href="?action=modules">&lt;&lt;&lt; <?php echo $lang['general']['back']; ?></a>
	</p>
<?php
}

function albums_page_admin_editalbum() {
	global $cont1, $cont2, $cont3, $lang, $lang_albums8, $lang_albums9, $lang_albums10, $lang_albums11, $lang_albums12, $lang_albums13, $lang_albums17, $var1;

	//Check if album exists
	if (file_exists(MODULE_SETTINGS.'/'.$var1)) {
		//Introduction text.
		?>
			<p>
				<strong><?php echo $lang_albums8; ?></strong>
			</p>
			<p>
				<span class="kop2"><?php echo $lang_albums10; ?></span>
				<br />
				<span class="kop4"><?php echo $lang_albums13; ?></span>
			</p>
			<form method="post" action="" enctype="multipart/form-data">
				<p>
					<label class="kop2" for="cont1"><?php echo $lang['general']['title']; ?></label>
					<br />
					<input name="cont1" id="cont1" type="text" />
				</p>
				<p>
					<label class="kop2" for="cont2"><?php echo $lang_albums11; ?></label>
					<br />
					<textarea cols="50" rows="5" name="cont2" id="cont2"></textarea>
				</p>
				<p>
					<input type="file" name="imagefile" id="imagefile" />
					<br />
					<label class="kop4" for="cont3"><?php echo $lang_albums12; ?></label>
					<input name="cont3" id="cont3" type="text" size="3" value="85" />
				</p>
				<input type="submit" name="submit" value="<?php echo $lang['general']['save']; ?>" />
			</form>
			<br />
		<?php
		//Edit images
		?>
		<span class="kop2"><?php echo $lang_albums9; ?></span>
		<br />
		<?php
		read_albumimages(MODULE_SETTINGS.'/'.$var1);
		//Let's process the image...
		if (isset($_POST['submit'])) {
			//Define some variables
			$imageme = $_FILES['imagefile']['name'];
			list($imageze, $ext) = explode('.', $imageme);
			$fullimage = MODULE_SETTINGS.'/'.$var1.'/'.$imageme;
			$thumbimage = MODULE_SETTINGS.'/'.$var1.'/thumb/'.$imageme;

			//First: Upload the image.
			//If file is pjpeg or jpeg: Accept.
			if ($_FILES['imagefile']['type'] == 'image/jpeg' || $_FILES['imagefile']['type'] == 'image/pjpeg') {

				//If we somehow can't copy the image, show an error.
				if (!copy($_FILES['imagefile']['tmp_name'], MODULE_SETTINGS.'/'.$var1.'/'.$_FILES['imagefile']['name'])) {
					show_error($lang['general']['upload_failed'], 1);
					include_once ('data/inc/footer.php');
					exit;
				}

				//If the extension is with capitals, we have to rename it...
				if ($ext != 'jpg') {
					rename($fullimage, 'data/settings/modules/albums/'.$var1.'/'.$imageze.'.jpg');
					$fullimage = 'data/settings/modules/albums/'.$var1.'/'.$imageze.'.jpg';
					$thumbimage = 'data/settings/modules/albums/'.$var1.'/thumb/'.$imageze.'.jpg';
				}

				//$images = read_dir_contents(MODULE_SETTINGS.'/'.$var1, 'files');

				//Define which filenames are already in use, and define what filename we should use.
				if (file_exists('data/settings/modules/albums/'.$var1.'/image1.jpg')) {
					$i = 2;
					$o = 3;
					while (file_exists('data/settings/modules/albums/'.$var1.'/image'.$i.'.jpg') || file_exists('data/settings/modules/albums/'.$var1.'/image'.$o.'.jpg')) {
						$i++;
						$o++;
					}
					$newfile = 'image'.$i;
				}
				else
					$newfile = 'image1';

				//Then rename the file and give it the right filename, also define new image-variables.
				rename($fullimage, 'data/settings/modules/albums/'.$var1.'/'.$newfile.'.jpg');
				$fullimage = 'data/settings/modules/albums/'.$var1.'/'.$newfile.'.jpg';
				$thumbimage = 'data/settings/modules/albums/'.$var1.'/thumb/'.$newfile.'.jpg';
				chmod($fullimage, 0777);
			}

			//Block images other then JPG.
			else {
				//FIXME: Maybe a better error message?
				show_error($lang['general']['upload_failed'], 1);
				include_once ('data/inc/footer.php');
				exit;
			}

			//Copy the image to the thumbdir.
			copy ($fullimage, $thumbimage) or die ('Error: Thumb copying failed!');

			//Compress the big image.
			$image_width = 640;
			$image = new SmartImage($fullimage, true);
			list($width, $height) = getimagesize($fullimage);
			$imgratio = $width / $height;

			if ($imgratio > 1) {
				$newwidth = $image_width;
				$newheight = $image_width / $imgratio;
			}

			else {
				$newheight = $image_width;
				$newwidth = $image_width * $imgratio;
			}

			$image->resize($newwidth, $newheight);
			$image->saveImage($fullimage, $cont3);
			$image->close();

			//Then make a thumb from the image.
			$thumb_width = 200;
			$thumb = new SmartImage($thumbimage, true);
			list($width, $height) = getimagesize($thumbimage);
			$imgratio = $width / $height;

			if ($imgratio > 1) {
				$newwidth = $thumb_width;
				$newheight = $thumb_width / $imgratio;
			}

			else {
				$newheight = $thumb_width;
				$newwidth = $thumb_width * $imgratio;
			}

			$thumb->resize($newwidth, $newheight);
			$thumb->saveImage($thumbimage, $cont3);
			$thumb->close();

			//Sanitize data.
			$cont1 = sanitize($cont1);
			$cont2 = sanitize($cont2);
			$cont2 = str_replace ("\n",'<br />', $cont2);

			//Compose the data.
			$data = '<?php'."\n"
			.'$name = \''.$cont1.'\';'."\n"
			.'$info = \''.$cont2.'\';'."\n"
			.'?>';

			//Then save the image information.
			save_file(MODULE_SETTINGS.'/'.$var1.'/'.$newfile.'.php', $data);

			//Redirect.
			redirect('?module=albums&page=editalbum&var1='.$var1, 0);
		}
	}
	?>
		<br />
		<p>
			<a href="?module=albums">&lt;&lt;&lt; <?php echo $lang['general']['back']; ?></a>
		</p>
	<?php
}

function albums_page_admin_deletealbum() {
	global $var1;

	//Check if an album was defined, and if the album exists
	if (isset($var1) && file_exists(MODULE_SETTINGS.'/'.$var1)) {
		recursive_remove_directory(MODULE_SETTINGS.'/'.$var1);
		unlink(MODULE_SETTINGS.'/'.$var1.'.php');
	}

	redirect('?module=albums', 0);
}

function albums_page_admin_editimage() {
	global $cont1, $cont2, $lang, $lang_albums11, $var1, $var2;

	//Check if an image was defined, and if the image exists.
	if (isset($var2) && file_exists('data/settings/modules/albums/'.$var1.'/'.$var2.'.php')) {
		//Include the image-information.
		include_once ('data/settings/modules/albums/'.$var1.'/'.$var2.'.php');

		//Replace html-breaks by real ones.
		$info = str_replace('<br />', "\n", $info);
		?>
		<br />
		<form name="form1" method="post" action="">
			<p>
				<label class="kop2" for="cont1"><?php echo $lang['general']['title']; ?></label>
				<br />
				<input name="cont1" id="cont1" type="text" value="<?php echo $name; ?>" />
			</p>
			<p>
				<label class="kop2" for="cont2"><?php echo $lang_albums11; ?></label>
				<br />
				<textarea cols="50" rows="5" name="cont2" id="cont2"><?php echo $info; ?></textarea>
			</p>
			<input type="submit" name="submit" value="<?php echo $lang['general']['save']; ?>" />
			<input type="button" value="<?php echo $lang['general']['cancel']; ?>" onclick="javascript: window.location='?module=albums&amp;page=editalbum&amp;var1=<?php echo $var1; ?>';" />
		</form>
		<?php
		//When the information is posted:
		if (isset($_POST['submit'])) {
			//Sanitize data.
			$cont1 = sanitize($cont1);
			$cont2 = sanitize($cont2);
			$cont2 = str_replace ("\n",'<br />', $cont2);

			//Then save the imageinformation
			$data = '<?php'."\n"
			.'$name = \''.$cont1.'\';'."\n"
			.'$info = \''.$cont2.'\';'."\n"
			.'?>';

			save_file(MODULE_SETTINGS.'/'.$var1.'/'.$var2.'.php', $data);

			redirect('?module=albums&page=editalbum&var1='.$var1, 0);
		}
	}
}

function albums_page_admin_deleteimage() {
	global $var1, $var2;
	//Check if an image was defined, and if the image exists
	if (isset($var1) && isset($var2) && file_exists('data/settings/modules/albums/'.$var1.'/'.$var2.'.php') && file_exists('data/settings/modules/albums/'.$var1.'/thumb/'.$var2.'.jpg')) {
		unlink('data/settings/modules/albums/'.$var1.'/'.$var2.'.php');
		unlink('data/settings/modules/albums/'.$var1.'/'.$var2.'.jpg');
		unlink('data/settings/modules/albums/'.$var1.'/thumb/'.$var2.'.jpg');
	}

	redirect('?module=albums&page=editalbum&var1='.$var1, 0);
}

function albums_page_admin_imageup() {
	global $lang, $lang_updown6, $var1, $var2;

	//Check if images exist.
	if (file_exists('data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg') && file_exists('data/settings/modules/albums/'.$var2.'/'.$var1.'.php') && file_exists('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg')) {
		//We can't higher image1, so we have to check:
		if ($var1 == 'image1') {
			echo $lang_updown6;
			redirect('?module=albums&page=editalbum&var1='.$var2, 2);
			include_once ('data/inc/footer.php');
			exit;
		}

		//Determine the imagenumber.
		list($filename, $pagenumber) = explode('e', $var1);
		$higherpagenumber = $pagenumber - 1;

		//First make temporary files.
		rename('data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.jpg');
		rename('data/settings/modules/albums/'.$var2.'/'.$var1.'.php', 'data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.php');
		rename('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.$var1.TEMP.'.jpg');

		//Then make the higher images one higher.
		rename('data/settings/modules/albums/'.$var2.'/'.NAME.$higherpagenumber.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg');
		rename('data/settings/modules/albums/'.$var2.'/'.NAME.$higherpagenumber.'.php', 'data/settings/modules/albums/'.$var2.'/'.$var1.'.php');
		rename('data/settings/modules/albums/'.$var2.'/thumb/'.NAME.$higherpagenumber.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg');

		//Finally, give the temp-files its final name.
		rename ('data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.NAME.$higherpagenumber.'.jpg');
		rename ('data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.php', 'data/settings/modules/albums/'.$var2.'/'.NAME.$higherpagenumber.'.php');
		rename ('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.TEMP.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.NAME.$higherpagenumber.'.jpg');

		//Show message.
		echo $lang['general']['changing_rank'];
	}

	//Redirect.
	redirect('?module=albums&page=editalbum&var1='.$var2, 0);
}

function albums_page_admin_imagedown() {
	global $lang, $lang_updown7, $var1, $var2;

	//Check if images exist.
	if (file_exists('data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg') && file_exists('data/settings/modules/albums/'.$var2.'/'.$var1.'.php') && file_exists('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg')) {
		//Determine the imagenumber
		list($filename, $pagenumber) = explode('e', $var1);
		$lowerpagenumber = $pagenumber + 1;

		//We can't lower the last image, so we have to check:
		if (!file_exists('data/settings/modules/albums/'.$var2.'/'.NAME.$lowerpagenumber.'.jpg')) {
			echo $lang_updown7;
			redirect('?module=albums&page=editalbum&var1='.$var2, 2);
			include_once ('data/inc/footer.php');
			exit;
		}

		//First make temporary files.
		rename('data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.jpg');
		rename('data/settings/modules/albums/'.$var2.'/'.$var1.'.php', 'data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.php');
		rename('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.$var1.TEMP.'.jpg');

		//Then make the higher images one higher.
		rename('data/settings/modules/albums/'.$var2.'/'.NAME.$lowerpagenumber.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.$var1.'.jpg');
		rename('data/settings/modules/albums/'.$var2.'/'.NAME.$lowerpagenumber.'.php', 'data/settings/modules/albums/'.$var2.'/'.$var1.'.php');
		rename('data/settings/modules/albums/'.$var2.'/thumb/'.NAME.$lowerpagenumber.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.$var1.'.jpg');

		//Finally, give the temp-files its final name.
		rename ('data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.jpg', 'data/settings/modules/albums/'.$var2.'/'.NAME.$lowerpagenumber.'.jpg');
		rename ('data/settings/modules/albums/'.$var2.'/'.$var1.TEMP.'.php', 'data/settings/modules/albums/'.$var2.'/'.NAME.$lowerpagenumber.'.php');
		rename ('data/settings/modules/albums/'.$var2.'/thumb/'.$var1.TEMP.'.jpg', 'data/settings/modules/albums/'.$var2.'/thumb/'.NAME.$lowerpagenumber.'.jpg');

		//Show message.
		echo $lang['general']['changing_rank'];
	}

	//Redirect.
	redirect('?module=albums&page=editalbum&var1='.$var2, 0);
}
?>