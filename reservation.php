<?php
	$host="localhost"; //Add your SQL Server host here
	$user="step68"; //SQL Username
	$pass="Nokia33102002."; //SQL Password
	$dbname="guestbook"; //SQL Database Name
	$con=mysqli_connect($host,$user,$pass,$dbname);
	if (mysqli_connect_errno($con))
	{
		echo "<h1>Failed to connect to MySQL: " . mysqli_connect_error() ."</h1>";
	}
	$name=$_POST['name'];

	
	$email=$_POST['email'];
	$tel=$_POST['tel'];
	$adress=$_POST['adress'];
	$date=$_POST['date'];
	$sql="INSERT INTO reservation(name,email,tel,date) VALUES('$name','$email','$tel','$date')";
	if (!mysqli_query($con,$sql))
	{
		die('Error: ' . mysqli_error($con));
	}
	else
		echo "Values Stored in our Database!";
	mysqli_close($con);
?>
