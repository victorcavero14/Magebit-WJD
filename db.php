<!DOCTYPE html>
<html lang="EN">
  <head> 
    <meta http-equiv='Content-type' content='text/html; charset=utf-8' />
    <title> My database </title> 
    <script> 

      var sort = "date";
      var last_q = "";
      var selectedEmails = [];

      function changeSortMethod(str){
        sort = str;
        if(last_q == "") return;
        else {
          showEmails(last_q); // Refresh the data with the new sorting method
        }
      }

      function selectEmail(str)
      {
        if(str == "") return;
        else {
          selectedEmails.push(str);
        }
      }

      function removeSelectedEmails(){
        var aux = "";
        for (x in selectedEmails) {
          if(x== 0) {aux += "?emailD[]=" + selectedEmails[x];}
          else {aux += "emailD[]=" + selectedEmails[x];}
          if(selectedEmails[x] != selectedEmails.slice(-1)[0]) {
            aux += "&";
          }
        }
        var xmlhttp2 = new XMLHttpRequest();
        xmlhttp2.onreadystatechange = function() {
          if (this.readyState == 4 && this.status == 200) {
            showEmails(last_q);
          }
        };
        xmlhttp2.open("DELETE","db.php" + aux,true);
        xmlhttp2.send();
      }

      function showEmails(str){
        if (str == "") { 
          document.getElementById("txtHint").innerHTML = "";
          return; 
        } 
        else {
          var xmlhttp = new XMLHttpRequest();
          xmlhttp.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
              document.getElementById("txtHint").innerHTML = this.responseText;
            }
          };
          last_q = str;
          xmlhttp.open("GET","db.php?q="+str+"&s="+sort,true);
          xmlhttp.send();
        }
      }
    </script> 
  </head>

  <body> 

    <?php
      function executeQuery($conn, $q){
        if (!($stmt = $conn->prepare($q))) {
          echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
      }

      function insertEmailDB($conn,$email){
        $nowFormat = date('Y-m-d H:i:s');
        /* Prepared statement, stage 1: prepare */
        if (!($stmt = $conn->prepare("INSERT INTO emails(email,dTime)  VALUES (?,?)"))) {
          echo "Prepare failed: (" . $conn->errno . ") " . $conn->error;
        }
        if (!$stmt->bind_param('ss', $email, $nowFormat)) {
            echo "Binding parameters failed: (" . $stmt->errno . ") " . $stmt->error;
        }
        if (!$stmt->execute()) {
            echo "Execute failed: (" . $stmt->errno . ") " . $stmt->error;
        }
      }
      
      $servername = "localhost:3306"; /* SQL port used by default */
      $username = "root";
      $password = "";

      // Create connection
      $conn = new mysqli($servername, $username, $password);

      // Check connection
      if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
      }

      if($_SERVER['REQUEST_METHOD'] === 'GET') {
        mysqli_select_db($conn, 'myDB');
        $q = mysqli_real_escape_string($conn, $_GET['q']);

        $query = "SELECT * FROM emails WHERE email LIKE '%$q%' ORDER BY dTime DESC";
        if($_GET['s'] == 'name') { $query = "SELECT * FROM emails WHERE email LIKE '%$q%'"; }
        $result = $conn->query($query);

        echo "</p><p> <table style='width:100%; border: 1px solid black;'>";

        while ($row = mysqli_fetch_assoc($result)) { 
          echo "<tr>";
          echo "<td> <input type='checkbox' id='" . $row['email'] . "'  onClick='selectEmail(this.id)'/> </td>";
          foreach ($row as $value) {
            echo "<td>" . $value . "</td>";
          }
          echo "</tr>";
        }

        echo "</table></p>";
      }
      else if ($_SERVER['REQUEST_METHOD'] === 'POST'){
        $email_input = mysqli_real_escape_string($conn, $_POST['email']);

        if(filter_var($email_input, FILTER_VALIDATE_EMAIL)) 
        {
          executeQuery($conn, 'CREATE DATABASE IF NOT EXISTS myDB');
          mysqli_select_db($conn, 'myDB');
          executeQuery($conn, "CREATE TABLE IF NOT EXISTS emails (email VARCHAR(100) PRIMARY KEY, dTime DATETIME NOT NULL)");
  
          insertEmailDB($conn, $email_input);
  
          $result = $conn->query("SELECT email FROM emails");
          $arr = array([]);
  
          echo "<p>";

          echo "<input type='button' value='Delete Selected' onClick='removeSelectedEmails()'></input>";
          echo "<input type='button' value='All' id='%' onClick='showEmails(this.id)'></input>";
          echo "<input type='button' value='Sort by name' id='name' onClick='changeSortMethod(this.id)'></input>";
          echo "<input type='button' value='Sort by date' id='date' onClick='changeSortMethod(this.id)'></input>";
  
          while ($row = mysqli_fetch_assoc($result)) { 
            foreach ($row as $value) {
              $aux = explode('@', $value);
              $domain_region = array_pop($aux);
              $domain = explode('.', $domain_region)[0];
              if (array_search($domain, $arr) === FALSE) {
                if($value != '') {
                  echo "<input type='button' value='" . $domain . "' onClick='showEmails(this.value)'></input>";
                }
                array_push($arr, $domain);
              }
            }
          }

          echo "<input type='text' placeholder='Type your email to search here...' style='width:100%;' onkeyup='showEmails(this.value)'></input>";

        }
        else {
          echo "Not a valid EMAIL, please remake the POST request";        
        }
      }
      else if ($_SERVER['REQUEST_METHOD'] === 'DELETE'){
        if(array_key_exists('emailD', $_POST)){
          foreach ($_DELETE['emailD'] as $key => $value) { 
            $query = "DELETE FROM emails WHERE email=" . $value;
  
            if ($conn->query($query)) {
              echo "Record deleted successfully";
            } else {
              echo "Error deleting record: " . $conn->error;
            }
          }
        }
      }
    ?>
    <div id="txtHint"></div>

  </body>
</html>
