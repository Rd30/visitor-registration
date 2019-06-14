<!DOCTYPE html>
<html lang="en-us">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=Edge" />
    <meta http-equiv="X-UA-Compatible" content="IE=9"/>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Visitors List</title>
    <!-- Bootstrap 4 -->
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.2.0/css/all.css" integrity="sha384-hWVjflwFxL6sNzntih27bfxkr27PmbbK/iSvJ+a4+0owXq79v+lsFkW54bOGbiDQ" crossorigin="anonymous">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" href="css\visitorsList.css">
  </head>
  <body class="container">
    <div class="container" id="vis-list-pri">

      <div class="container navbar-cont">
        <nav class="navbar navbar-expand-sm navbar-dark bg-dark" id="vis-list-nav">
            <div class="container-fluid" id="vis-lis-logo">
              <a class="navbar-brand" href=""><img src="img\2-ENTG-logo-horiz-2color.png" width="316" height="78" class="d-inline-block align-top" alt=""></a>
            </div>
            <div class="col-md-8 col-sm-8"><h2>Visitors List</h2></div>
        </nav>
      </div>
      <?php
        include_once 'C:\xampp\htdocs\visitorRegistration\lib\spg_utils.php';
        $db_config_data = parse_ini_file('C:\xampp\htdocs\visitorRegistration\config\visitorRegistration_db.ini');

        // Create connection to "denali"->"visitor_registration" db.
        $visitor_conn = new mysqli($db_config_data["m_denali_host"], $db_config_data["m_denali_user"], $db_config_data["m_denali_password"], $db_config_data["m_denali_db_visitor"]);
        if ($visitor_conn->connect_error) {
          echo "Failed to connect to MySQL from visitors list : (" . $visitor_conn->connect_error . ") " . $visitor_conn->connect_error;
          die("Connection failed for Denali from visitorlist: " . $visitor_conn->connect_error);
        }

        //Fetch the list of visitors for today.
        $get_visitor_list_sql = "SELECT name, host, phone, id, start_time FROM visitor WHERE DATE(`start_time`) = CURDATE() AND dismissed = 'No' ";
        $countRes = sendQuery($visitor_conn, $get_visitor_list_sql);
        $row_cnt = mysqli_num_rows($countRes); // count th number of records.

        $visitor_list_parsed = parseQueryResponse(sendQuery($visitor_conn, $get_visitor_list_sql));
      ?>
      <div class="container row info-grid">
        <div class="col-md-4 common date-grid">
        	<div class="row inner-date-grid">
        		<div class="col-md-4">
        			<i class="fa fa-calendar"> </i>
        		</div>
        		 <div class="col-md-8 market-update-left">
          		<h4>Date</h4>
          		<h3><?php echo date("m/d/Y");?></h3>
        	  </div>
        	  <div class="clearfix"></div>
        	</div>
        </div>
        <div class="col-md-4 common filler-grid"></div>
        <div class="col-md-4 common count-grid">
        	<div class="row inner-count-grid">
        		<div class="col-md-4">
        			<i class="fa fa-users"> </i>
        		</div>
        		 <div class="col-md-8 market-update-left">
          		<h4>Visitors Count</h4>
          		<h3><?php echo $row_cnt;?></h3>
        	  </div>
        	  <div class="clearfix"></div>
        	</div>
        </div>
      </div>
      <?php
        if($row_cnt > 0){
      ?>
      <div class="container">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th scope="col">NAME</th>
              <th scope="col">HOST</th>
              <th scope="col">PHONE</th>
              <th scope="col">ID</th>
            </tr>
          </thead>

          <tbody>
            <?php
              foreach($visitor_list_parsed as $myresults){
                echo "<tr>".
                       "<td>".$myresults['name']."</td>".
                       "<td>".$myresults['host']."</td>".
                       "<td>".$myresults['phone']."</td>".
                       "<td>".$myresults['id']."</td>".
                     "</tr>";
              }
              mysqli_close($visitor_conn);
            ?>
          </tbody>
        </table>
      </div>
      <?php
        } else{
      ?>
      <div class="alert alert-success tableAltAlert  text-center" role="alert"><strong>There are no visitors/All the visitors are checked out</strong></div>
      <?php
        }
      ?>
    </div>
  </body>
</html>
