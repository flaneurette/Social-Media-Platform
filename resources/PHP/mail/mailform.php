<?php

// Error reporting (for testing only).
ini_set('display_errors', 1); 

// Optional headers to consider.
header("X-Frame-Options: DENY"); 
header("X-XSS-Protection: 1; mode=block"); 
header("Strict-Transport-Security: max-age=30");
header("Referrer-Policy: same-origin");

// Start our session.
session_start();

// Include our class, optional: make it required.
include("class.SecureMail.php");
	
	if(isset($_POST['token']))  {
			// A token was provided through $_POST data. Check if it is the same as our session token.
			if($_POST['token'] === $_SESSION['token']) {
				// The submitted token appears to be similar as the session token we set. Obtain $_POST data.   
				$parameters = array( 
					'to' => 'info@yourdomain.tld',
					'name' => $_POST['name'],
					'email' => $_POST['email'],				
					'subject' => $_POST['subject'],
					'body' => $_POST['body']
				);
				// Proceed to check the $_POST data.
				$checkForm = new \security\forms\SecureMail($parameters);
				// Check the script timer to see how much time was spent.
				$spent_time = $checkForm->getTime();
				if($spent_time == TRUE) {
					// Enough time has been spent, proceed scanning the $_POST data.
					$scan = $checkForm->fullScan();
						// Did the scan found something?
						if($scan != FALSE) {
							// The class decided the $_POST data was correct. 
							// Start sending the mail.
							$checkForm->sendmail();
							// Show a message.
							$checkForm->sessionmessage('Mail sent!'); 
							} else {
							// The class found something, we cannot send the mail.
							$checkForm->sessionmessage('Mail not sent.');
						}
				}
				
			} 

		// Show all session messages.
		$checkStatus = new \security\forms\SecureMail();
		$checkStatus->showmessage();
		// Destroy the session to finish.
		$checkStatus->destroysession();
	} 

// Setup new secure mail form.
$setup = new \security\forms\SecureMail();
// Clear any previous sessions messages.
$setup->clearmessages();
// Create a secure token.
$token = $setup->getToken();
// Place the token inside a server-side session.
$_SESSION['token'] = $token;
// Create some time to track how long a user takes to complete the form.
$time  = $setup->setTime();
// Try to detect a Robot on this form. If found, do you want to show a Captcha?
$robot = $setup->detectrobot();
if($robot == TRUE) {
	// YOUR OWN CAPTCHA CODE HERE.
	// echo "Prove to us you are not a robot.";
}
		
?>

<h2>Secure mail form.</h2>
<p>Test form.</p>
<form action="" method="post">
<input type="hidden" name="token" value="<?php echo $token;?>">
			<label for="name">Name:</label><br>
				<input type="text" name="name" value="Jane Doe">
				<p><!-- message --></p>
			<label for="email">E-mail:</label><br>
				<input type="text" name="email" value="jane.doe@website.com">
				<p><!-- message --></p>
			<label for="subject">Subject:</label><br>			
				<input type="text" name="subject" value="Test">
				<p><!-- message --></p>
			<label for="body">Message:</label><br>
				<textarea name="body" rows="10" cols="40">Is it working? Hope so! -JD.</textarea>
				<p><!-- message --></p>
  <input type="submit" name="submit" value="Submit">
</form>
