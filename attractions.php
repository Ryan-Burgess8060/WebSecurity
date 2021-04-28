<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8" />
	<title>Sleeping Eagles B &amp; B</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body>
	<main>
		<article>
			<form action="attractions.php" enctype="multipart/form-data" method="post">
				<label for="newImage">Upload an attraction image:</label>
				<input type="file" id="newImage" name="image">
				<input type="submit" value="Upload an image" id="sub" name="mit">
			</form>
			<?php
			if(isset($_POST['mit']))
			{ 
				if (!empty($_POST['image'])) {
				$file = "images/" . $_FILES["image"]["name"];

				if(move_uploaded_file($_FILES["image"]["tmp_name"], $file)) 
				{
					echo "<img src=".$file."  />";
				} 
				else 
					{
					echo "Error !!";
					}
				} else {
					echo "yeah empty sucks";
				}
			} 
			?>
			<br class="clear">
		</article>
	</main>
</body>
</html>