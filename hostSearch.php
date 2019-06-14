<?php
/*To search for the host in the "names" table of "it" db */

$db_config_data = parse_ini_file("/var/www/config/visitorRegistration_db.ini");
include_once "/var/www/lib/php/spg_utils.php";

$names_conn = new mysqli($db_config_data["m_host"], $db_config_data["m_user"], $db_config_data["m_password"], $db_config_data["m_db_it"]);
if ($names_conn->connect_error) {
  echo "Failed to connect to MySQL: (" . $names_conn->connect_error . ") " . $names_conn->connect_error;
  die("Connection failed: " . $names_conn->connect_error);
}

if(isset($_REQUEST['term'])){
  // Prepare a select statement
  $sql = "SELECT User FROM names WHERE User LIKE ?";
  if($stmt = mysqli_prepare($names_conn, $sql)){
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
                  echo "<p tabindex=\"-1\">" . $row["User"] . "</p>";
              }
          } else{
              echo "<p>No matches found</p>";
          }
      } else{
          echo "ERROR: Could not able to execute $sql. " . mysqli_error($names_conn);
      }
  }
  mysqli_stmt_close($stmt);
}

?>
