<?php include "../inc/dbinfo.inc"; ?>
<html>
  <head>
      <style>
          .center-flex {
              display: flex;
              justify-content: center;
              align-items: center;
          }

          .center-table {
              display: flex;
              justify-content: center;
              align-items: space-evenly;
          }	

          .full-width {
              width: 100%;
          }

          .table-container {
              width: 80vw;
          }

          .flex-space-between {
              display: flex;
              align-items: center;
              justify-content: space-between;
          }

          .input-age {
              width: 10em;
          }

          .radio-choices {
              display: flex;
              justify-content: space-between;
              width: 10em;
          }

          .radio-squares {
              width: 100%;
              display: flex;
              align-items: center;
              justify-content: space-evenly;
          }

          input {
              margin: 1% 0;
              border: 1px solid;
              padding: 0.5%;
          }

          tr {
              margin: 1% 0;
          }

          input[type="submit"]:hover {
              background-color: gray;
              color: white;
              border: 1px solid black;
              cursor: pointer;
          }

          input[type='radio'] {
              box-sizing: border-box;
              appearance: none;
              background: white;
              outline: 2px solid #333;
              border: 3px solid white;
              width: 16px;
              height: 16px;
            }
            
          input[type='radio']:checked {
              background: green;
              border: 3px solid green;
          }

          table {
              width: 80%;
          }

          .small-head {
            width: 13%;
          }

          .big-head {
              width: 30.5%;
          }

      </style>
  </head>
  <body>
    <div class="center-flex">
        <h1>TRAVEL REGISTRY</h1>
    </div>

    <?php

      $connection = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD);

      if (mysqli_connect_errno()) echo "Failed to connect to MySQL: " . mysqli_connect_error();

      $database = mysqli_select_db($connection, DB_DATABASE);

      VerifyEmployeesTable($connection, DB_DATABASE);

      $employee_name = htmlentities($_POST['NAME']);
      $employee_address = htmlentities($_POST['ADDRESS']);
      $employee_immigrant = $_POST['IMMIGRANT'] == "1" ? 1 : 0;
      $employee_age = intval($_POST['AGE']);

      if (strlen($employee_name) || strlen($employee_address)) {
        AddEmployee($connection, $employee_name, $employee_address, $employee_immigrant, $employee_age);
      }
    ?>

    <div class="center-flex">
      <form action="<?PHP echo $_SERVER['SCRIPT_NAME'] ?>" method="POST">
        <table class="table-container" border="0">
            <tr>
                <td>NAME</td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="NAME" maxlength="45" class="full-width" />
                </td>
            </tr>
            <tr>
                <td>ADDRESS</td>
            </tr>
            <tr>
                <td>
                    <input type="text" name="ADDRESS" maxlength="90" class="full-width" />
                </td>
            </tr>
            <tr class="flex-space-between">
                <td>IMMIGRANT</td>
                <td class="radio-choices">
                    <div class="radio-squares">
                        <input type="radio" name="IMMIGRANT" value="1" id="yes" checked> <label for="yes">YES</label>
                    </div>
                    <div class="radio-squares">
                        <input type="radio" name="IMMIGRANT" value="0" id="no"> <label for="no">NO</label>
                    </div>
                </td>                      
            </tr>
            <tr class="flex-space-between">
                <td>AGE</td>
                <td>
                    <input type="number" name="AGE" min="0" max="99" class="input-age">
                </td>
            </tr>
            <td>
                <input type="submit" value="Add Data" class="full-width" />
            </td>
        </table>
      </form>
    </div>
    
    <div class="center-flex">
        <h1>REGISTRY HISTORY</h1>
    </div>

    <div class="center-flex">
      <table border="1">
        <thead>
          <tr>
              <th class="small-head">ID</th>
              <th class="big-head">NAME</th>
              <th class="big-head">ADDRESS</th>
              <th class="small-head">IMMIGRANT</th>
              <th class="small-head">AGE</th>
          </tr>
        </thead>

      <?php

      $result = mysqli_query($connection, "SELECT * FROM EMPLOYEES");

      while($query_data = mysqli_fetch_row($result)) {
        echo "<tr>";
        echo "<td>",$query_data[0], "</td>",
            "<td>",$query_data[1], "</td>",
            "<td>",$query_data[2], "</td>",
            "<td>",$query_data[3], "</td>",
            "<td>",$query_data[4], "</td>";
        echo "</tr>";
      }
      ?>

      </table>
    </div>

    <?php
      mysqli_free_result($result);
      mysqli_close($connection);
    ?>
  </body>
</html>


<?php

function AddEmployee($connection, $name, $address, $immigrant, $age) {
   $n = mysqli_real_escape_string($connection, $name);
   $a = mysqli_real_escape_string($connection, $address);
   $i = intval($immigrant);
   $ag = intval($age);

   $query = "INSERT INTO EMPLOYEES (NAME, ADDRESS, IMMIGRANT, AGE) VALUES ('$n', '$a', '$i', '$ag');";

   if(!mysqli_query($connection, $query)) echo("<p>Error adding employee data.</p>");
}

function VerifyEmployeesTable($connection, $dbName) {
  if(!TableExists("EMPLOYEES", $connection, $dbName)) {
     $query = "CREATE TABLE EMPLOYEES (
         ID int(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
         NAME VARCHAR(45),
         ADDRESS VARCHAR(90),
         IMMIGRANT BOOLEAN,
         AGE int(3)
       )";

     if(!mysqli_query($connection, $query)) echo("<p>Error creating table.</p>");
  }
}

function TableExists($tableName, $connection, $dbName) {
  $t = mysqli_real_escape_string($connection, $tableName);
  $d = mysqli_real_escape_string($connection, $dbName);

  $checktable = mysqli_query($connection,
      "SELECT TABLE_NAME FROM information_schema.TABLES WHERE TABLE_NAME = '$t' AND TABLE_SCHEMA = '$d'");

  if(mysqli_num_rows($checktable) > 0) return true;

  return false;
}
?>
