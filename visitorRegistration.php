<?php
  include_once "/var/www/lib/php/spg_utils.php";  
  $db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");

  $name = ucwords($_POST['name']);
  $company = ucwords($_POST['company']);
  $host = $_POST['host_name'];
  $phone = $_POST['phone'];
  $type = $_POST['visitor_type'];
  $id = '';
  $startTime = '';
  $dismissed = 'No';

  // Check if the $host value contains Department Name. If so, trim to save just the host name.
  if (strpos($host, 'Contact Person') !== false) {
    $host = strstr($host, ':');
    $host = ltrim($host, ":");
    $host = rtrim($host, ")");
    $host = trim($host);
  }

  // Create connection to "local"->"visitor_registration" db.
  $visitor_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_visitor"]);
  if ($visitor_conn->connect_error) {
    echo "Failed to connect to MySQL: (" . $visitor_conn->connect_error . ") " . $visitor_conn->connect_error;
    die("Connection failed: " . $visitor_conn->connect_error);
  }

  // Query to insert visitor data into the visitor table in local and in denali.
  $visitor_insert_sql = "INSERT INTO visitor (id, name, company, type, phone, host, start_time, end_time, dismissed)
  VALUES (DEFAULT, '$name', '$company', '$type', '$phone', '$host', NOW(), NULL, '$dismissed')";

  // Insert visitor data and acknowledge the visitor.
  if (sendQuery($visitor_conn,$visitor_insert_sql) === TRUE) {
    $id = $visitor_conn->insert_id; // Fetch the id of last inserted  record.

    //Fetch the start time of the visitor.
    $get_visitor_data_sql = "SELECT start_time FROM visitor WHERE id = '" . $id . "'";
    $visitor_data_parsed = parseQueryResponse(sendQuery($visitor_conn, $get_visitor_data_sql));
    if (sizeof($visitor_data_parsed) > 0 && isset($visitor_data_parsed[0])){
      $startTime = date("m/d/Y H:i", strtotime($visitor_data_parsed[0]['start_time'])); //Change format from timestamp to  "m/d/Y H:i".
      mysqli_close($visitor_conn); //Close the visitor_registration db connection in local.
    }else{
        echo 'Failed to fetch the start time record';
    }
  }else {
      echo "Error: " . $visitor_insert_sql . "<br>" . $visitor_conn->error;
  }

  // Create connection to "denali"->"visitor_registration" db.
  //$denali_visitor_conn = new mysqli($db_config_data["m_denali_host"], $db_config_data["m_denali_user"], $db_config_data["m_denali_password"], $db_config_data["m_denali_db_visitor"]);
  //if ($denali_visitor_conn->connect_error) {
    //echo "Failed to connect to Denali's MySQL : (" . $denali_visitor_conn->connect_error . ") " . $denali_visitor_conn->connect_error;
    //die("Connection failed for Denali: " . $denali_visitor_conn->connect_error);
  //}

  // Insert visitor data into denali's visitor table.
  //if (sendQuery($denali_visitor_conn,$visitor_insert_sql) === TRUE) {
    //$denali_id = $denali_visitor_conn->insert_id; // Fetch the id of last inserted record.
    //mysqli_close($denali_visitor_insert_sql);
  //}else {
    //  echo "Error: " . $visitor_insert_sql . "<br>" . $denali_visitor_conn->error;
  //}

  // Create connection to "it" db for the "names" table .
  $names_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_it"]);
  if ($names_conn->connect_error) {
    echo "Failed to connect to MySQL: (" . $names_conn->connect_error . ") " . $names_conn->connect_error;
    die("Connection failed: " . $names_conn->connect_error);
  }

  // Query to fetch host's email and phone.
  $get_host_details_sql = "SELECT Email, Phone FROM names WHERE User = '" . $host . "'";
  $host_details_parsed = parseQueryResponse(sendQuery($names_conn, $get_host_details_sql));
  
  if (sizeof($host_details_parsed) > 0 && isset($host_details_parsed[0])) {
    if (($hostEmail = $host_details_parsed[0]['Email']) && ($hostPhone = $host_details_parsed[0]['Phone'])) {
	  //send the response back to the ajax. 	
	  echo json_encode(array("success"=>true, "host"=>$host, "phone"=>$host_details_parsed[0]['Phone'], "name"=>$name, "id"=>$id, "type"=>$type, "startTime"=>$startTime, "company"=>$company));	      
      mysqli_close($names_conn);
    }else{
      echo "Failed to fetch host's e-mail and phone.";
    }
  }

  // Email Id, subject and body for the e-mail to be sent to the host.
  $to = $hostEmail;
  $subject = sprintf("You have a visitor : %s", $name);
  $body = sprintf(
    "<html>".
    "<body style=\"font-family:Lato-Bold;font-size: 18px;\"> ".
     "<p>Hello %s,</p> ".
     "<p>You have a visitor who has just checked in at the lobby in Building 2 and is here to see you.</p> ".
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
	   "<p><strong>NOTE: Please dismiss the visitor as soon as they leave.</strong></p> ".
      "<p>Thank You.</p> ".	  
    "</body> ".
  "</html> ",
   $host, $name, $company, $phone, $type, $id, $startTime, $id
  );
  
  //Session variables for handleEmailHost.php
  session_start();
  $_SESSION['hostEmail'] = $to;
  $_SESSION['emailSubject'] = $subject;
  $_SESSION['emailBody'] = $body;
?>
