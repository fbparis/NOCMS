<?php
NOCMS::import(dirname(__FILE__) . '/../stuff/session.php');

if (@$_POST['custom_text']) {
	$_SESSION['text'] = trim(strip_tags(stripslashes($_POST['custom_text'])));
}

NOCMS::template('with-sessions');
$metaTitle = "Demo with session management";
?>
<p>This is a demo page with ajax loaded dynamic content and some session management.</p>
<p>Click <a href="<?php echo NOCMS::uri(); ?>">here</a> to refresh the page.</p>
<hr>
<p>Your IP address is <span id="dyn_ip_address">...</span>.</p>
<p>Your session datas are :</p>
<pre id="dyn_session_datas">...</pre>
<hr>
<form action="<?php echo NOCMS::uri(); ?>" method="post">
	<fieldset>
		<legend>Update your session</legend>
		<label for="custom_text">Custom text : </label>
		<input type="text" name="custom_text" id="custom_text" value=""> <button type="submit">Update session</button>
	</fieldset>
</form>
