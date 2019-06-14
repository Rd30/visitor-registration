<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Visitor Registration</title>
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
	<!-- Font-awesome -->
    <link href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">
    <link rel="stylesheet" href="css/visitorRegistration.css">
  </head>
  <body class="container">
    <?php
      if (isset($_GET["success"]) && $_GET["success"] = "true") {
     ?>
     <div class="container registrationDone" id="registration-done">
       <div id="success-response" class="alert alert-success successResponse">
         <img src="img\2-ENTG-logo-horiz-2color.png" href="http://nd-force.entegris.com/visitorRegistration/" width="350" class="-inline-block" alt="Entegris-SAES">
         <p>Thank you <strong class="title-case"><?php echo $_GET["name"];?></strong> for entering your information.</p>
         <p>Please use the telephone to call <strong><?php echo $_GET["host"];?></strong> at <strong><?php echo $_GET["phone"];?></strong> to let them know that you are here.</p>
		 <a href="http://nd-force.entegris.com/visitorRegistration/" type="button" id="new-reg" class="btn btn-lg btn-primary new-reg-btn">New Registration</a>
		 <button id="print-badge-btn" type="button" class="btn btn-success success modalBtn col-md-6" onclick="printDiv()" hidden>Print Badge</button>
       </div>
	   <!--Start:Print badge-->
       <div class="printSection label" id="print-section">
         <h3 style="text-align:left;margin: 0px;"><?php echo $_GET["name"];?></h3>
         <h4 style="text-align:left;margin: 0px;"><?php echo $_GET["company"];?></h4>
         <hr>
         <div style="width: 100%;">
           <div style="float:left; width: 60%">
             <div style="float:left;text-align:left;margin: 0px; width: 20%">Host:</div><div style="float:right;text-align:left;margin: 0px; width: 80%">&nbsp;<?php echo $_GET["host"];?></div>
             <div style="float:left;text-align:left;margin: 0px; width: 20%">Id:</div><div style="float:right;text-align:left;margin: 0px; width: 80%">&nbsp;<?php echo $_GET["id"];?></div>
             <div style="float:left;text-align:left;margin: 0px; width: 20%">Type:</div><div style="float:right;text-align:left;margin: 0px; width: 80%">&nbsp;<?php echo $_GET["type"];?></div>
             <div style="float:left;text-align:left;margin: 0px; width: 20%">Time:</div><div style="float:right;text-align:left;margin: 0px; width: 80%">&nbsp;<?php echo $_GET["startTime"];?></div>
           </div>
           <div style="float:right;width: 40%">
              <img src="img\3-ENTG-logo-stacked-black.png" width="125px" height:"125px"  class="-inline-block" alt="Entegris-SAES">
           </div>
         </div>
       </div>
       <iframe class="iframePrintSection label" id="iframe-print-section" style="width:4.0in;height:2.13in;" hidden></iframe>
	   <!--End:Print badge-->
       <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
       <script>
			var iframeBody  = $("#iframe-print-section").contents().find("body");
			var appendContent = iframeBody.append($('#print-section'));
			$("#iframe-print-section").get(0).contentWindow.print();

			//Timer to reset the page to landing page.
			var timer = setTimeout(function() {
				window.location='http://nd-force.entegris.com/visitorRegistration/'
			 }, 30000);
       </script>
     </div>
     <?php
       }else{
		 // To get all the hosts(employees) names.
         include_once "/var/www/lib/php/spg_utils.php";
		 $db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");
         // Create connection to "it" db for the "names" table .
         $names_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_it"]);
         if ($names_conn->connect_error) {
           echo "Failed to connect to MySQL: (" . $names_conn->connect_error . ") " . $names_conn->connect_error;
           die("Connection failed: " . $names_conn->connect_error);
         }
         $get_all_hosts = "SELECT User FROM names ORDER BY User ASC";
         $get_all_hosts_parsed = parseQueryResponse(sendQuery($names_conn, $get_all_hosts));
         mysqli_close($names_conn);
     ?>
	<script type="text/javascript">var getAllHosts =<?php echo json_encode($get_all_hosts_parsed); ?>;</script> <!--pass all the hosts(employees) visitorRegistration.js-->
    <div class="container primary-div">
      <div id="landing" class="land-div">
        <h1>Visitor Registration</h1>
        <img src="img\2-ENTG-logo-horiz-2color.png" class="mx-auto d-block" alt="Entegris-SAES"><br>
        <button type="button" class="btn btn-lg btn-primary startBtn" id="startBtn">CLICK HERE TO START</button>
        <h2>All visitors to Entegris are required to register</h2>
      </div>
      <div id="pri-form" class="container form-div">
        <!--Navigation bar used for logo and the title-->
        <nav class="navbar navbar-light regFormNav" id="pri-form-nav" style="background-color: white;">
            <div class="col-md-4">
              <a class="navbar-brand" href=""><img src="img\2-ENTG-logo-horiz-2color.png" width="316" height="78" class="d-inline-block align-top" alt=""></a>
            </div>
            <div class="col-md-8"><h2>Visitor Registration</h2></div>
        </nav>
        <!--Message box -->
        <div id="msg-box" class="msgBox"></div>
        <!--Start : Visitor Registration form-->
        <form id="visitreg-form" name="visitorRegForm">
          <div class="form-group row">
            <label for="inputName" class="col-md-2 col-form-label-lg">Name:</label>
            <div class="col-md-10">
              <input type="text" name="inputName" class="form-control title-case" id="inputName" placeholder="Enter your full name" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputCompany" class="col-md-2 col-form-label-lg">Company:</label>
            <div class="col-md-10">
              <input type="text" name="inputCompany" class="form-control title-case" id="inputCompany" placeholder="Enter your company's name" autocomplete="off">
            </div>
          </div>
          <div class="form-group row" id="hostNameDiv">
            <label for="inputHost" class="col-md-2 col-form-label-lg">Host Name:</label>
            <div class="search-box col-md-10">
			  <span class="fa fa-search"></span>
              <input name="inputHostSearch" class="form-control title-case" type="text" id="inputHostSearch" placeholder="Search your host name..." autocomplete="off">
              <div class="result"></div>
            </div>
          </div>
          <div class="form-group row" id="deptNameDiv">
            <label for="inputHost" class="col-md-2 col-form-label-lg">Department:</label>
            <div class="dept-search-box col-md-10">
			  <span class="fa fa-search"></span>
              <input name="inputDeptSearch" class="form-control title-case" type="text" id="inputDeptSearch" placeholder="Search the name of the department you are visiting..." autocomplete="off">
              <div class="deptResult"></div>
            </div>
          </div>
          <div class="form-group row check-remember" id="checkRemember">
            <div class="col-md-2"></div>
            <div class="col-md-10 form-check">
              <input type="checkbox" name="checkRem" class="form-check-input" id="checkNoHost">
              <label class="form-check-label chkRemLabel" for="checkRem">Check here if you do not remember the host name. You can search by the department.</label>
            </div>
          </div>
          <div class="form-group row">
            <label for="inputPhone" class="col-md-2 col-form-label-lg">Phone:</label>
            <div class="col-md-10">
              <input name="inputPhone" type="text" class="form-control" id="inputPhone" minLength="7" placeholder="Enter your cellphone number" autocomplete="off">
            </div>
          </div>
          <div class="form-group row">
            <label for="inputVisitorType" class="col-md-2 col-form-label-lg">Visitor Type:</label>
            <div class="col-md-10">
              <select name="selectVisitorType" class="required custom-select mr-sm-2" id="sel-visitor-type" style="color:#b31a1b">
                  <option value="">Select your visitor type</option>
                  <option value="Contractor">Contractor</option>
                  <option value="Vendor">Vendor</option>
				  <option value="Customer">Customer</option>
				  <option value="Interview">Interview</option>
                  <option value="Other">Other</option>
              </select>
            </div>
          </div>
          <div class="form-group row">
              <label for="" class="col-md-2 col-form-label-lg"></label>
              <div class="btn-group col-md-10 reg-btn-grp">
                <div class="col-md-6"><a href="http://nd-force.entegris.com/visitorRegistration/" id="register-cancel" name="registerCancel" class="btn btn-primary">Cancel</a></div>
                <div class="col-md-6"><button type="button" class="btn btn-primary registerBtn" id="register-btn" data-toggle="modal" data-target="#confirm-reg">Register</button></div>
              </div>
          </div>
       </form>
	   <!--End : Visitor Registration form-->
	   <!--Start : Modal alert to display errors-->
       <div class="modal fade" id="alert-modal" role="dialog">
         <div class="modal-dialog modal-lg">
           <div class="modal-content">
             <div class="modal-body exclamation alert alert-danger text-center" id="error-msg"role="alert"></div>
             <div class="modal-footer">
               <button type="button" class="btn btn-default modalBtn" data-dismiss="modal">Close</button>
             </div>
           </div>
         </div>
       </div>
	   <!--End : Modal alert to display errors-->
       <!--Start : Modal dialog to Confirm Visitor Registration-->
       <div class="modal fade" id="confirm-reg" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
		<div id="loader"></div>
         <div class="modal-dialog modal-lg">
           <div class="modal-content">
             <form name="confirmRegistration" id="confirm-reg-form"> <!--action="visitorRegistration.php" method="post"-->
               <div class="modal-header">
                    <h4>Confirm Registration</h4>
               </div>
               <div class="modal-body">
                  <div class="alert alert-info modalAlert">
                    <strong>Please confirm your information. If something needs to be changed, please press the cancel button.</strong>
                  </div>
                  <div>
                    <div class="form-group row">
                      <label for="nameVal" class="col-md-5 col-form-label">Name:</label>
                      <input type="text" class="col-md-7 form-control-plaintext title-case" id="nameVal" name="name" required readonly>
                    </div>
                    <div class="form-group row">
                      <label for="companyVal" class="col-md-5 col-form-label">Company:</label>
                      <input type="text" class="col-md-7 form-control-plaintext title-case" id="companyVal" name="company" required readonly>
                    </div>
                    <div class="form-group row">
                      <label for="hostVal" class="col-md-5 col-form-label" id="deptLabelModal">Department:</label>
                      <label for="hostVal" class="col-md-5 col-form-label" id="hostLabelModal">Host Name:</label>
                      <input type="text" readonly class="col-md-7 form-control-plaintext title-case" id="hostNameVal" name="host_name" required readonly>
                    </div>
                    <div class="form-group row">
                      <label for="phoneVal" class="col-md-5 col-form-label">Phone:</label>
                      <input type="text" class="col-md-7 form-control-plaintext" id="phoneVal" name="phone" required readonly>
                    </div>
                    <div class="form-group row">
                      <label for="visitorTypeVal" class="col-md-5 col-form-label">Visitor Type:</label>
                      <input type="text" class="col-md-7 form-control-plaintext" id="visitorTypeVal" name="visitor_type" required readonly>
                    </div>
                  </div>
                  <!-- <iframe src="" id="myiframe" name="myiframe"></iframe> -->
                  <div class="test"></div>
                </div>
                <div class="modal-footer">
                  <button type="button" class="btn btn-default modalBtn col-md-6" data-dismiss="modal">Cancel</button>
                  <button id="confirm-btn" type="submit" class="btn btn-success success modalBtn col-md-6">Confirm</button>
                </div>
              </form>
            </div>
         </div>
       </div>
	   <!--End : Bootstrap modal dialog to Confirm Registration-->
      </div>
        <?php } ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.js" integrity="sha256-2Kok7MbOyxpgUVvAk/HJ2jigOSYS2auK4Pfzbm7uH60=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/jquery.validate.min.js"></script>
    <script src="https://cdn.jsdelivr.net/jquery.validation/1.16.0/additional-methods.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
    <!--<script src="lib/jquery-ui-1.12.1.custom/jquery-ui.min.js"></script> -->
    <script src="visitorRegistration.js"></script>
  </body>
</html>
