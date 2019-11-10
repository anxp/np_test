<?php

if (isset($_GET['error'])) {
    $message = urldecode($_GET['error']);
    $class = 'error';
} else {
    $message = isset($_GET['notification']) ? urldecode($_GET['notification']) : '';
    $class = 'notification';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>Date calculator</title>
	<link rel="stylesheet" type="text/css" href="css/style.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="js/script.js"></script>
</head>
<body>

	<section class="header">
        <form action="formhandler.php" method="post">
            <input type="hidden" name="requesttype" value="sync">
		<div class="for-input">
			<input id="date_range_input" type="text" name="daterange_txt">
		</div>

		<div class="for-button">
			<button type="submit" name="post_btn" value="pb">POST</button>
			<button id="ajax_btn" type="button" name="ajax_btn" value="ab">AJAX</button>
		</div>
		<div id="user_notifications_block" class="<?php echo $class ?>">
			<p><?php echo $message ?></p>
		</div>
        </form>
	</section>

</body>
</html>