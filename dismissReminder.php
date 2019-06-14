<?php
    include_once "/var/www/lib/php/spg_utils.php";
    require "/var/www/lib/php/PHPMailer/PHPMailerAutoload.php";
    $db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");

    // Create connection to "local"->"visitor_registration" db.
    $visitor_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_visitor"]);
    if ($visitor_conn->connect_error) {
      echo "Failed to connect to MySQL from dismissReminder: (" . $visitor_conn->connect_error . ") " . $visitor_conn->connect_error;
      die("Connection failed from dismissReminder: " . $visitor_conn->connect_error);
    }

    // Create connection to "it" db for the "names" table .
    $names_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_it"]);
    if ($names_conn->connect_error) {
      echo "Failed to connect to MySQL: (" . $names_conn->connect_error . ") " . $names_conn->connect_error;
      die("Connection failed: " . $names_conn->connect_error);
    }

    $get_visitor_not_dismissed_sql = "SELECT * FROM visitor WHERE dismissed = 'No'";
    $visitor_not_dismissed_parsed =  parseQueryResponse(sendQuery($visitor_conn, $get_visitor_not_dismissed_sql));

    if (sizeof($visitor_not_dismissed_parsed) > 0){
      foreach($visitor_not_dismissed_parsed as $dismissResult){
        $id = $dismissResult['id'];
        $name = $dismissResult['name'];
        $company = $dismissResult['company'];
        $type = $dismissResult['type'];
        $phone = $dismissResult['phone'];
        $host = $dismissResult['host'];
        $startTime = date("m/d/Y H:i", strtotime($dismissResult['start_time']));
        $endTime = $dismissResult['end_time'];

        $get_host_email = "SELECT Email FROM names WHERE User = '" . $host . "'";
        $host_email_parsed = parseQueryResponse(sendQuery($names_conn, $get_host_email));
        $hostEmail = $host_email_parsed[0]['Email'];
        //echo " id : ".$id." name : ".$name." company : ".$company." type : ".$type." phone : ".$phone." host : ".$host." start_time : ".$startTime."  hostEmail : ".$hostEmail."<br>";

        // Subject and body of the e-mail to be sent to the host.
        $subject = sprintf("Reminder : Dismiss the visitor - %s", $name);
        $body = sprintf(
          "<html>".
          "<body style=\"font-family:Lato-Bold;font-size: 18px;\"> ".
           "<p>Hello %s,</p> ".
           "<p>This is a friendly reminder to dismiss the following visitor if they have left the site already. Please ignore if the visitor is still on-site.</p> ".
            "<table style=\"background:#FFFFFF;color:#000000;border-radius: .4em;overflow: hidden;\"> " .
               "<tbody> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Name:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align:left;line-height:2.5;text-transform:uppercase;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Company:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;text-transform:uppercase;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Phone:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Visitor Type:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;text-transform: uppercase;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Visitor ID:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">Start Time:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;\">%s</td> ".
                "</tr> ".
                "<tr style=\"border-top: 1px solid #ddd;border-bottom: 1px solid #ddd;border-color: #46637f;\"> " .
                  "<th style=\"font-family:Lato-Bold;font-size: 20px;color:#FF4500;line-height: 2.5;text-transform: uppercase;\">End Time:</th> " .
                  "<td style=\"font-family:Lato-Regular;font-size: 20px;padding: 1em !important;text-align: left;line-height:2.5;text-transform: uppercase;\"><a target=\"_blank\" href=\"http://nd-force.entegris.com/visitorRegistration/dismissVisitor.php?id=%s\">Click here to dismiss the visitor.</a></td> ".
                "</tr> ".
              "</tbody> ".
             "</table> ".
            "<p>Thank You.</p> ".
          "</body> ".
        "</html> ",
         $host, $name, $company, $phone, $type, $id, $startTime, $id
        );
        mailHost($hostEmail, $subject, $body); // email host for the reminder.
      }
    }else{
      echo "No visitors to dismiss.";
    }

    mysqli_close($visitor_conn);
    mysqli_close($names_conn);

    function mailHost($to, $subject, $body) {
      $mail = new PHPMailer;
      $mail->isSMTP();
      $mail->Host = 'relay-west.entegris.com';
	  $mail->SMTPOptions= array(
		'ssl'=> array(
			'verify_peer'=>false,
			'verify_peer_name'=>false,
			'allow_self_signed'=>true
		)
	  );
	  $mail->SMTPSecure = "tls";
	  $mail->Port = 25;	  
	  $mail->setFrom("donotreply@entegris.com", "Dismiss Visitor Reminder");
      $mail->addAddress($to);
	  $mail->addCC("SPG.IT@entegris.com");	  	
      $mail->isHTML(true);
      $mail->Subject = $subject;
      $mail->Body = $body;

      if (!$mail->send()) {
        echo "<script language='javascript'>console.log('Failed to send an email to host.');</script>";
        error_log('Message could not be sent.');
        error_log('Mailer Error: ' . $mail->ErrorInfo);
      }
    }
?>
