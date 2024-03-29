
<html>
<head>
	<title>Log In to APPS</title>
	<!--<link rel="stylesheet" type="text/css" href="style.css">

 	CSS Styling -->	
	<style>
		/* 2 columns for logging in/signing up */
		/*.row {
			text-align: center;
			display: flex;
		}
*/
		/*.column {
			text-align: center;
			flex: 50%;
		}
*/
		form label {  
			display: inline-block;  
			width: 150px;
			font-weight: bold;
		}
		
		.error {
			font-weight: bold;
			color: red;
		}
		.center {
			text-align: center;
		}


		/*.box {
			background: rgb(255,255,255); 
			background: -moz-linear-gradient(left, rgba(255,255,255,1) 10%, rgba(165,165,165,1) 11%, rgba(165,165,165,1) 38%, rgba(165,165,165,1) 38%, rgba(165,165,165,1) 55%, rgba(165,165,165,1) 89%, rgba(255,255,255,1) 90%); 
			background: -webkit-linear-gradient(left, rgba(255,255,255,1) 10%,rgba(165,165,165,1) 11%,rgba(165,165,165,1) 38%,rgba(165,165,165,1) 38%,rgba(165,165,165,1) 55%,rgba(165,165,165,1) 89%,rgba(255,255,255,1) 90%); 
			background: linear-gradient(to right, rgba(255,255,255,1) 10%,rgba(165,165,165,1) 11%,rgba(165,165,165,1) 38%,rgba(165,165,165,1) 38%,rgba(165,165,165,1) 55%,rgba(165,165,165,1) 89%,rgba(255,255,255,1) 90%); 
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#ffffff', endColorstr='#ffffff',GradientType=1 ); 
		} */


	</style>
	<link rel="stylesheet" href="style.css">
</head>

<body>

	<?php 
		session_start();  

		// connect to the database
		$conn = mysqli_connect("localhost", "SJL", "SJLoss1!", "SJL");
		if (!$conn) die("Connection failed: ".mysqli_connect_error());

		// if they tried to log in, verify their information
		if (isset($_POST['login'])) {
			$_SESSION['id'] = $_POST['uid'];
			verify_user($conn);
		}

		// if they tried to sign up, validate data and add to database
		if (isset($_POST['signup'])) {
			$_SESSION['role'] = 'A';
			sign_up($conn);
		}
	?>

	<h2 style="text-align: center;">Graduate Application System</h2>
	<div class="row">
		<!-- Log in -->
		
			<div class="crimson-bg box">
			<h3>Log In</h3>
			<p>Log in to complete your application, view its status, or see the final decision</p>
			<?php echo $_SESSION['errL']; ?><br>
			<form method="POST" action="login.php">
				<input type="text" name="uid" placeholder="UID" required pattern="[0-9]*"><br/><br/>
				<input type="password" name="password_login" placeholder="Password" required><br/><br/>
				<input type="submit" name="login" value="Log In">
			</form>
			</div>
		


		<!-- Sign up -->
			<div class="crimson-bg box">
			<h3>Sign Up</h3>
			<p>Sign up here if you don't already have an account to begin your application</p>
			<?php echo $_SESSION['errS']; ?><br>
			<form method="POST" action="login.php">
				<label for="fname">First name:</label>
				<input type="text" name="fname" required><br/><br/>

				<label for="lname">Last name:</label>
				<input type="text" name="lname" required><br/><br/>

				<label for="email">Email:</label>
				<input style="font-size:14pt;" type="email" name="email" pattern="[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,4}$"><br/><br/>

				<label for="password">Password:</label>
				<input type="password" name="password" required><br/>
				
				<label for="password2">Confirm Password:</label>
				<input type="password" name="password2" required><br/><br/>

			    <input type="submit" name="signup" value="Create Account"><br/>
			</form>
			<br/>
			</div>
	</div>

	<!-- RESET button -->
	<br/>
	<div align='center';>
		<form action="reset.php" method="POST">
			<input type="submit" name="RESET" value="RESET">
		</form>
	</div>

	<?php
		function verify_user ($conn)
		{
			// query the database for entered user ID
			$query = 'SELECT role, password FROM users WHERE userID='.$_SESSION['id'];
			$result = mysqli_query($conn, $query);
			
			// validate the password
			if (mysqli_num_rows($result)<=0) {
				$_SESSION['errL'] = "<p class='error'>No users with that ID. Try again:</p>";
			}
			else {
				$row = $result->fetch_assoc();
				if ($_POST['password_login'] != $row['password']) {
					$_SESSION['errL'] = "<p class='error'>Incorrect password, try again:</p>";
				}
				else {
					$_SESSION['role'] = $row['role'];
					$_SESSION['errL'] = "";

					// set up session variables for main site
					$_SESSION['loggedin'] = true;
					$q = "SELECT fname, type, isAdvisor, isReviewer FROM user WHERE uid = ".$_SESSION['id'];
					$r = mysqli_query($conn, $q) or die("user session variables failed");
					$value = mysqli_fetch_object($r);
					$_SESSION['fname'] = $value->fname;
					$_SESSION['type'] = $value->type;
					$_SESSION['isAdvisor'] = $value->isAdvisor;
					$_SESSION['isReviewer'] = $value->isReviewer;



					// direct to application page
					header("Location: home.php");
					exit();
				}
			}
		}

		function sign_up ($conn)
		{
            // make sure they don't already have an account
            if(mysqli_num_rows(mysqli_query($conn, "SELECT * FROM users WHERE email='".$_POST['email']."'")) > 0)
		    $_SESSION['errS'] = "<p class='error'>There is already an account with that email address, try logging in:</p>";

            // make sure their passwords matched
			else if ($_POST['password'] == $_POST['password2']) {

	            // create a user id for the new account by doing max+1
	            $query = "SELECT MAX(userID) AS max FROM users";
	            $row = mysqli_query($conn, $query)->fetch_assoc();
	            $_SESSION['id'] = $row['max'] + 1;

	            // add info to the database
	            $query = "INSERT INTO users VALUES ('A', '".$_POST['fname']."', '".$_POST['lname']."', '".$_POST['password']."', '".$_POST['email']."', ".$_SESSION['id'].")";
	            //JACK: I added these additional queries when creating a user to make the app forms work properly
	            //$query2 = "INSERT INTO app_review (uid, reviewerRole) VALUES (" .$_SESSION['id']. ", 'FR')";
	            $query2 = "INSERT INTO app_review (uid, reviewerRole, reviewerID) VALUES (" .$_SESSION['id']. ", 'CAC', 42142172)";	
	            if (mysqli_query($conn, $query) && mysqli_query($conn, $query2)) {
					$_SESSION['role'] = 'A';
					$_SESSION['errS'] = "";
					echo "redirect";
                    header("Location: home.php");
                    exit();
            	}
                else
                    $_SESSION['errS'] = "<p class='error'>Failure creating account: ".mysqli_error()."</p>";
			}

			else 
				$_SESSION['errS'] = "<p class='error'>Passwords must match.</p>";
		}
	?>

</body>
</html>
