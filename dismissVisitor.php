<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Dismiss Visitor</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/css/bootstrap-datetimepicker.min.css" />
	<!-- Font-awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/visitorRegistration.css">
  </head>
  <body class="dismiss-visitor-body">
    <div id="dismissVisitor" class="container dismiss-visitor-div">
      <?php
        if(isset($_GET['id']) && !empty($_GET['id'])){
          $id = $_GET['id'];

          include_once "/var/www/lib/php/spg_utils.php";
          $db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");

          // Create connection to "local"->"visitor_registration" db.
          $visitor_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_visitor"]);
          if ($visitor_conn->connect_error) {
            echo "Failed to connect to MySQL from dismiss visitor: (" . $visitor_conn->connect_error . ") " . $visitor_conn->connect_error;
            die("Connection failed from dismiss visitor: " . $visitor_conn->connect_error);
          }

          // Query to fetch the visitor data for the given id.
          $get_visitor_details_sql = "SELECT * FROM visitor WHERE id = '" . $id . "'";
          $visitor_details_parsed = parseQueryResponse(sendQuery($visitor_conn, $get_visitor_details_sql));

          $visitorName = NULL;
          $visitorCompany = NULL;
          $visitorType = NULL;
          $visitorPhone = NULL;
          $visitorStartTime = NULL;
		  $visitorEndTime = NULL;
          $visitorDismissed = NULL;

          if (sizeof($visitor_details_parsed) > 0 && isset($visitor_details_parsed[0])) {
            if (($visitorName = $visitor_details_parsed[0]['name']) && ($visitorCompany = $visitor_details_parsed[0]['company']) &&
                  ($visitorType = $visitor_details_parsed[0]['type']) && ($visitorPhone = $visitor_details_parsed[0]['phone']) &&
                  ($visitorStartTime = $visitor_details_parsed[0]['start_time']) && ($visitorEndTime = $visitor_details_parsed[0]['end_time']) &&
                  ($visitorDismissed = $visitor_details_parsed[0]['dismissed'])){
                      $visitorStartTime = date("m/d/Y H:i", strtotime($visitor_details_parsed[0]['start_time'])); //Change from timestamp format  into  "m/d/Y H:i".
                      $visitorEndTime = date("m/d/Y H:i", strtotime($visitor_details_parsed[0]['end_time']));     //Change from timestamp format  into  "m/d/Y H:i".
            }else{
					 echo "<script language='text/javascript'>console.log('Failed to fetch visitor details.');</script>";
            }
          }
        } else {
          echo "Visitor/Dismiss ID is empty";
        }
        mysqli_close($visitor_conn);
      ?>
      <nav class="navbar navbar-light dismissVisNav col-md-offset-1" id="dismiss-visitor-nav">
        <div class="" id="dismiss-visitor-logo">
          <a class="navbar-brand" href="https://www.entegris.com"><img src="img\2-ENTG-logo-horiz-2color.png" style="max-width:100%; width:12rem;height:4rem; display: block;"; class="img-responsive" alt="Entegris-logo"></a>
        </div>
        <div class="dismiss-visitor-title" id="dismiss-visitor-title">
          <h2 class="idIcon">Dismiss Visitor</h2>
        </div>
      </nav>
      <!--Message box -->
      <div class="col-md-offset-3" style="width:49%;"><div id="dismiss-msg-box" class="dismissMsgBox"></div></div>
      <form class="form-horizontal dismissVisitorForm" method="post" action="" id="dismiss-visitor" name="dismissVisitorForm">
        <div class="form-group form-group-lg">
          <label for="nameVal" class="col-md-offset-1 col-md-2 control-label">Name:</label>
          <div class="col-md-6">
            <input type="text" class="form-control" id="visitorName" name="visitorName" value="<?php echo htmlspecialchars($visitorName); ?>" readonly>
          </div>
        </div>
        <div class="form-group form-group-lg">
          <label for="companyVal" class="col-md-offset-1 col-md-2 control-label">Company:</label>
          <div class="col-md-6">
            <input type="text" class="form-control" id="visitorCompany" name="visitorCompany" value="<?php echo htmlspecialchars($visitorCompany); ?>" readonly>
          </div>
        </div>
        <div class="form-group form-group-lg ">
          <label for="phoneVal" class="col-md-offset-1 col-md-2 control-label">Phone:</label>
          <div class="col-md-6">
            <input type="text" class="form-control" id="visitorPhone" name="visitorPhone" value="<?php echo htmlspecialchars($visitorPhone); ?>" readonly>
          </div>
        </div>
        <div class="form-group form-group-lg ">
          <label for="visitorTypeVal" class="col-md-offset-1 col-md-2 control-label">Type:</label>
          <div class="col-md-6">
            <input type="text" class="form-control" id="visitorType" name="visitorType" value="<?php echo htmlspecialchars($visitorType); ?>" readonly>
          </div>
        </div>
        <div class="form-group form-group-lg">
          <label for="visitorStartTime" class=" col-md-offset-1 col-md-2 control-label">Start Time:</label>
          <div class="col-md-6">
            <input type="text" class="form-control" id="visitorStartTime" name="visitorStartTime" value="<?php echo htmlspecialchars($visitorStartTime); ?>" readonly>
          </div>
        </div>
        <div class="form-group form-group-lg">
          <label for="visitorEndTime" class="col-md-offset-1 col-md-2 control-label">End Time:</label>
          <div class="col-md-6">
            <div class="input-group" id="visitorEndGroup">
              <?php
                if($visitorDismissed == 'Yes'){
              ?>
                  <input type="text" id="visitorEndTime" name="visitorEndTime" class="form-control date" placeholder="Click here to input the visitor's end-time"  value="<?php echo $visitorEndTime ?>" />
              <?php
                }else{
              ?>
                  <input type="text" id="visitorEndTime" name="visitorEndTime" class="form-control date" placeholder="Click here to input the visitor's end-time"  value="<?php echo isset($_POST['visitorEndTime']) ? $_POST['visitorEndTime'] : '' ?>" <?php echo isset($_POST["dismissVisitor"]) ? "disabled" : "";?> />
              <?php
                }
              ?>
              <span class="input-group-addon" id="endTimeIcon"><span class="glyphicon glyphicon-calendar"></span></span>
            </div>
          </div>
        </div>
        <div class="form-group form-group-lg" hidden>
          <div class="col-md-offset-3 col-md-6">
            <input type="text" class="form-control" id="visitorDismissed" name="visitorDismissed" value="<?php echo htmlspecialchars($visitorDismissed); ?>">
          </div>
        </div>
        <div class="form-group form-group-lg">
          <div class="col-md-offset-3 col-md-6">
            <button type="submit" name="dismissVisitor" class="btn btn-default dismissBtn" id="dismissVisitorBtn" <?php echo isset($_POST["dismissVisitor"]) ? "disabled" : "";?>>Dismiss Visitor</button>
          </div>
        </div>
     </form>
     <?php
        // Update the "end-time" and "dismissed" column values on the click of a "Dismiss Visitor" button.
        if(isset($_POST['dismissVisitor'])){
          $endTime = $_POST['visitorEndTime'];
          $endTimeFormatted =  date("Y-m-d H:i:s",strtotime($endTime));

          $db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");

          // Create connection to "local"->"visitor_registration" db.
          $visitor_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_visitor"]);
          if ($visitor_conn->connect_error) {
            echo "Failed to connect to MySQL from dismiss visitor: (" . $visitor_conn->connect_error . ") " . $visitor_conn->connect_error;
            die("Connection failed from dismiss visitor: " . $visitor_conn->connect_error);
          }

          // Query to update visitor data into the visitor table in local and in denali.
          $update_endTime_sql =  "UPDATE visitor SET end_time = '".$endTimeFormatted."', dismissed = 'Yes' WHERE id = '". $id ."'";

          // Update visitor data in local and acknowledge the visitor.
          if (sendQuery($visitor_conn,$update_endTime_sql) === TRUE) {
            echo "<div class=\"checkIcon alert alert-success dismissAlert text-center col-md-offset-3 col-md-6\" id='flash-msg'><strong>Thank You. You have dismissed the visitor successfully.</strong></div>";
            mysqli_close($visitor_conn);
          }else {
              echo "Error: " . $update_endTime_sql . "<br>" . $visitor_conn->error;
          }

          // Create connection to "denali"->"visitor_registration" db.
          //$denali_visitor_conn = new mysqli($db_config_data["m_denali_host"], $db_config_data["m_denali_user"], $db_config_data["m_denali_password"], $db_config_data["m_denali_db_visitor"]);
          //if ($denali_visitor_conn->connect_error) {
            //echo "Failed to connect to Denali's MySQL : (" . $denali_visitor_conn->connect_error . ") " . $denali_visitor_conn->connect_error;
            //die("Connection failed for Denali: " . $denali_visitor_conn->connect_error);
         // }

          //Update visitor data in denali.
          //if (sendQuery($denali_visitor_conn,$update_endTime_sql) === TRUE) {
            //mysqli_close($denali_visitor_conn);
          //}else {
            //  echo "Error: " . $update_endTime_sql . "<br>" . $denali_visitor_conn->error;
          //}
        }
     ?>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://momentjs.com/downloads/moment.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.43/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script type="text/javascript">
      $(function () {
        $('#dismissVisitorBtn').attr('disabled', 'disabled'); 

        // disable the datetime picker and dismiss visitor button if the visitor has been dismissed already.
        if($('#visitorDismissed').val() == 'Yes'){
          $('#visitorEndTime').prop('readonly', true);
          $('#dismiss-msg-box').addClass('exclamation alert alert-info text-center').css('font-weight','bold');
          $('#dismiss-msg-box').text('Visitor has been dismissed already.');
          $('#visitorEndTime').datetimepicker('disable');
        }

        $('.dismissAlert').css('font-weight','bold');

        $('#visitorEndTime').on("click", function(e) {
          if(($('#visitorEndTime').val().length) > 15){
            $('#dismissVisitorBtn').removeAttr('disabled');
          }
        });

        $('#visitorEndTime').datetimepicker({
          format: 'MM/DD/YYYY HH:mm',
        });
		
		//jQuery custom validation method to validate dateTime.
        $.validator.addMethod("dateTime", function(value, element) {          
          var formats = ["MM/DD/YYYY", "MM/DD/YYYY HH:mm"]; // Validate date and datetime.          
          return moment(value, formats, true).isValid(); // Validate the date and return.
        }, "Please enter a valid date and time");

        // jQuery validators.
        $('#dismiss-visitor').validate({
          rules: {
            visitorEndTime:{
              required: true,
              minlength: 15,
              dateTime: true,
            },
          },
          messages: {
            visitorEndTime: "Error: Please enter a valid date and time.",
          },
          errorElement : 'div',
          errorLabelContainer: '.dismissMsgBox',
          errorClass: 'exclamation alert alert-danger text-center',
          highlight:function(element, errorClass, validClass) {
      			$(element).parents('.form-group').addClass('has-error');
      		},
      		unhighlight: function(element, errorClass, validClass) {
      			$(element).parents('.controls').removeClass('has-error');
      			$(element).parents('.form-group').addClass('has-success');
      		}
        });
      });
    </script>
  </body>
</html>
