<?php

	// Define function to handle basic user input
	function parse_input($data) 
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}

	// Define function to check that inputted expense number has a maximum of 2 decimal places
	function validateTwoDecimals($number)
	{
	   return (preg_match('/^[0-9]+(\.[0-9]{1,2})?$/', $number));
	}
 
	// PHP script used to connect to backend Azure SQL database
	require 'ConnectToDatabase.php';

	// Start session for this particular PHP script execution.
	session_start();

	// Define ariables and set to empty values
	$make = $model = $startdate = $enddate = $employee = NULL;

	// Get input variables
	$make= (string) parse_input($_POST['make']);
	$model= (string) parse_input($_POST['model']);
	$startdate= (string) parse_input($_POST['startdate']);
	$enddate= (string) parse_input($_POST['enddate']);
	$employee= (string) parse_input($_POST['employee']);

	// Get the authentication claims stored in the Token Store after user logins using Azure Active Directory
	$claims= json_decode($_SERVER['MS_CLIENT_PRINCIPAL'])->claims;
	foreach($claims as $claim)
	{		
		if ( $claim->typ == "http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress" )
		{
			$userEmail= $claim->val;
			break;
		}
	}

	///////////////////////////////////////////////////////
	//////////////////// INPUT VALIDATION /////////////////
	///////////////////////////////////////////////////////
	

	///////////////////////////////////////////////////////
	////////// INPUT PARSING AND WRITE TO SQL DB //////////
	///////////////////////////////////////////////////////


		// Connect to Azure SQL Database
		$conn = ConnectToDabase();

		// Build SQL query to insert new expense data into SQL database
		$tsql=
		"INSERT INTO Vehicles (	
				make,
				model,
				startdate,
				enddate,
				employee)
		VALUES ('" . $make . "',
				'" . $model . "', 
				'" . $startdate . "', 
				'" . $enddate . "', 
				'" . $employee . "')";

		// Run query
		$sqlQueryStatus= sqlsrv_query($conn, $tsql);

		// Close SQL database connection
		sqlsrv_close ($conn);
	


	// Store previously-selected data as part of info to carry over after URL redirection
	$_SESSION['prevSelections'] = $prevSelections;

	/* Redirect browser to home page */
	header("Location: /"); 
?>