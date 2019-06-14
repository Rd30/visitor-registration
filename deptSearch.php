<?php
/*To search for the department in the "department" table of "it" db */

$db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");
include_once "/var/www/lib/php/spg_utils.php";

$dept_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_it"]);
if ($dept_conn->connect_error) {
  echo "Failed to connect to MySQL: (" . $dept_conn->connect_error . ") " . $dept_conn->connect_error;
  die("Connection failed: " . $dept_conn->connect_error);
}

if(isset($_REQUEST['term'])){
  // Prepare a select statement
  $sql = "SELECT name, contact_person, email, phone FROM department WHERE name LIKE ?";
  if($stmt = mysqli_prepare($dept_conn, $sql)){
      // Bind variables to the prepared statement as parameters
      mysqli_stmt_bind_param($stmt, "s", $param_term);
      // Set parameters
      $param_term = '%'.$_REQUEST['term'].'%';
      // Attempt to execute the prepared statement
      if(mysqli_stmt_execute($stmt)){
          $result = mysqli_stmt_get_result($stmt);
          // Check number of rows in the result set
          if(mysqli_num_rows($result) > 0){
              // Fetch result rows as an associative array
              while($row = mysqli_fetch_array($result, MYSQLI_ASSOC)){
                  echo "<p tabindex=\"-1\">" .$row["name"]. " (Contact Person : ".$row["contact_person"].")</p>";
              }
          } else{
              echo "<p>No matches found</p>";
          }
      } else{
          echo "ERROR: Could not able to execute $sql. " . mysqli_error($names_conn);
      }
  }
  // Close statement
  mysqli_stmt_close($stmt);
}

?>
