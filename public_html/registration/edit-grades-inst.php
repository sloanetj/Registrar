<!DOCTYPE html>
<html>
  <head>
    <title> Grades </title>
    <link rel="icon" type="image/png" href="images/favicon-32x32.png" sizes="32x32" />
    <link rel="icon" type="image/png" href="images/favicon-16x16.png" sizes="16x16" />
    <link rel = "stylesheet" type="text/css" href="style.css"/>
      <style>
         ul {
        list-style-type: none;
        margin: 0;
        padding: 0;
        overflow: hidden;
        background-color: #333;
        }

        li {
        float: left;
        }

        li a {
        display: block;
        color: white;
        text-align: center;
        padding: 14px 16px;
        text-decoration: none;
        }

        li a:hover:not(.active) {
        background-color: #111;
        }

        .active {
          background-color: #990000;
        }

    </style>
  </head>
  <body>
    <ul>
             <li><a class="active" href="menu.php">Menu</a></li>
             <li style="float:right"><a href="logout.php">Log Out</a></li>
        </ul>
    <h3> Edit Grades </h3>
    <hr>
    <!--
    <form action="edit-grades-admin.php" method="post">
      Select course: <input type="text" name="uid">
      <input type="submit" name="search" value="Search"> <br>
    </form>
  -->
      <?php
        session_start();
        // Send to login page if user is not logged in
        if(!$_SESSION['loggedin']) {
            header("Location: login.php");
            die();
        }

        //send to menu page if they don't have sufficient permissions
        if(!($_SESSION['type']=="inst")) {
          header("Location: menu.php");
          die();
        }

        // Connect to database
        $servername = "localhost";
        $username = "SJL";
        $password = "SJLoss1!";
        $dbname = "SJL";
        $conn = mysqli_connect($servername, $username, $password, $dbname);
        // Check connection
        if(!$conn){
          die("Connection failed: " . mysqli_connect_error());
        }

        $query = "select dept, courseno, crn FROM course where instructor='".$_SESSION['uid']."' AND semester='Spring' AND year=2019;";
        $result = mysqli_query($conn, $query);

        if(mysqli_num_rows($result)>0){
          // Create a drop down menu with list of courses associated with instructor UID
          echo '<form action="students.php" method="post">';
          echo 'Select course: ';
          echo '<select name="crn" style="width: 200px">';
          echo '<option>Select</option>';
          while ($row = mysqli_fetch_assoc($result)) {
            echo '<option value="'.$row['crn'].'">'.$row['dept'].' '.$row['courseno'].'</option>';
          }
          echo '</select>';

          // Send to page listing students associated with selected course
          echo '<input type="submit" name="select" value="Select"></form>';


        }
        else {
          $missErr = "No courses associated with UID: ".$uid."";
          echo $missErr;
        }

      ?>

  </body>
</html>
