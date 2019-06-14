<?php
  require "/var/www/lib/php/PHPMailer/PHPMailerAutoload.php";

  //Retrieve session variables from visitorRegistration.php
  session_start();
  $to = $_SESSION['hostEmail'];
  $subject = $_SESSION['emailSubject'];
  $body = $_SESSION['emailBody'];

  if(isset($_POST['sendEmail'])){
    mailHost($to, $body, $subject);
  }

  function mailHost($to, $body, $subject){
    $mail = new PHPMailer;
	//$mail->SMTPDebug = 4;
    $mail->isSMTP();	    
	$mail->Host = 'relay-west.entegris.com';
	//$mail->Host = "172.16.14.250";
	$mail->SMTPOptions= array(
	          'ssl'=> array(
			  'verify_peer'=>false,
			  'verify_peer_name'=>false,
			  'allow_self_signed'=>true
		)
	);
	$mail->SMTPSecure = "tls";
	$mail->Port = 25;     // TCP port to connect to
	//$mail->SMTPAuth = false;  // Enable SMTP authentication
	//$mail->Username = 'Entegris.relay@entegris.com';
	//$mail->Password = 'pr8Tr8#t';	
	$mail->addAddress($to);
	//$mail->setFrom("spg_admin@saes-group.com", "Entegris Visitor Alert");
	$mail->setFrom("donotreply@entegris.com", "Visitor Alert");    
	$mail->addCC("SPG.IT@entegris.com");	
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body = $body;

    if (!$mail->send()){         
      error_log('E-mailing Error : ' . $mail->ErrorInfo);
    }

	session_destroy(); //Destroy the session once the e-mail is sent.
  }
?>
