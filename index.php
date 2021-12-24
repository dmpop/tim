<?php
    // Theme (light, dark, sepia)
    $theme = "dark";
    // Footer
    $footer = "Read the <a href='https://dmpop.gumroad.com/l/linux-photography'>Linux Photography</a> book.";
?>

<!DOCTYPE html>
<html lang="en" data-theme="<?php echo $theme ?>">
<!-- Author: Dmitri Popov, dmpop@linux.com
	 License: GPLv3 https://www.gnu.org/licenses/gpl-3.0.txt -->

<head>
	<meta charset="utf-8">
	<title>Tim</title>
	<link rel="shortcut icon" href="favicon.png" />
	<link rel="stylesheet" href="css/classless.css">
	<link rel="stylesheet" href="css/themes.css">
	<meta name="viewport" content="width=device-width, initial-scale=1">
</head>

<body>
	<?php
	$max_upload = (int)(ini_get('upload_max_filesize'));
	$max_post = (int)(ini_get('post_max_size'));
	$memory_limit = (int)(ini_get('memory_limit'));
	$upload_mb = min($max_upload, $max_post, $memory_limit);
	if (!file_exists('upload')) {
		mkdir('upload', 0777, true);
	}
	if (!file_exists('result')) {
		mkdir('result', 0777, true);
	}
	?>
	<div style="text-align: center;">
		<img style="display: inline; height: 2.5em; vertical-align: middle;" src="favicon.svg" alt="logo" />
		<h1 class="text-center" style="display: inline; margin-left: 0.19em; vertical-align: middle; letter-spacing: 3px; margin-top: 0em; color: #f6a159ff;">Tim</h1>
		<p style="color: lightgray; margin-bottom: 1.5em;">Current upload limit is <u><?php echo $upload_mb; ?>MB</u></p>
	</div>
	
	<div class="card">

		<form style="margin-top: 1em;" action=" " method="POST" enctype="multipart/form-data">
			<label for="fileToUpload">Select JPEG file:</label>
			<input style="margin-bottom: 1.5em; margin-top: 0.5em;" type="file" name="fileToUpload" id="fileToUpload">
			<label for="quality">Select quality:</label>
			<select name="quality">
				<option value="low">Low</option>
				<option value="medium" selected>Medium</option>
				<option value="high">High</option>
				<option value="veryhigh">Very high</option>
			</select>
			<input type="checkbox" name="keep" value="ok"> Keep files
			<button style="margin-bottom: 1.5em;" type="submit" name="recompress">Recompress</button>
		</form>

		<details>
			<summary style="letter-spacing: 1px; color: #f6a159ff;">Help</summary>
			<ol>
				<li>
					Select the desired JPEG file using the <kbd>Browse</kbd> button.
				</li>
				<li>
					Select the desired recompression quality using the <strong>Quality</strong> drop-down list.
				</li>
				<li>
					
					If you want to save the uploaded and resulting files on the server, enable the <strong>Keep files</strong> option.
				</li>
				<li>
					Press the <kbd>Recompress</kbd> button.
				</li>
				<li>
					Download the resulting file.
				</li>
			</ol>
			<p class="text-center">Tim stands for <strong style="color: #f6a159ff;">T</strong>iny <strong style="color: #f6a159ff;">IM</strong>age.</p>
		</details>
	</div>
	<p class="text-center"><?php echo $footer ?></p>

	<?php
	if (isset($_POST["recompress"])) {
		$target_file = "upload/" . basename($_FILES["fileToUpload"]["name"]);
		$upload_ok = 1;
		$image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
		$check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
		if ($check !== false) {
			$upload_ok = 1;
		} else {
			$upload_ok = 0;
		}
		if ($image_file_type != "jpg" && $image_file_type != "jpeg" && $image_file_type != "JPG" && $image_file_type != "JPEG") {
			$upload_ok = 0;
		}
		if ($upload_ok == 0) {
			echo "<script>";
			echo 'alert("Something went wrong. Make sure you upload a JPEG file that does not exceed the upload limit.")';
			echo "</script>";
		} else {
			if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
				$f = 'upload/' . $_FILES["fileToUpload"]["name"];
				$q = $_POST['quality'];
				$r = "recompressed_" . $q . "_"  . basename($_FILES["fileToUpload"]["name"]);
				shell_exec("./jpeg-recompress --quality " . $q . " " . $f . " " . $r);
				ob_start();
				while (ob_get_status()) {
					ob_end_clean();
				}
				header('Content-type: image/jpeg');
				header('Content-Disposition: attachment; filename="' . $r . '"');
				readfile($r);
				if (isset($_POST['keep'])) {
					rename($r, "result/" . $r);
				} else {
					unlink($f);
					unlink($r);
				}
			} else {
				echo "<script>";
				echo 'alert("Error uploading the file.")';
				echo "</script>";
			}
		}
	}
	?>

</body>

</html>