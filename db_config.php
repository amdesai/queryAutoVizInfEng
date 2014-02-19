<?php
	ob_start();
	$DBLink = "localhost";
	$DBName = "ospi_final";
	$DBUser = "root";
	$DBPswd = "roar77";
	$col = "";
	function execute($sql)//,$DBLink,$DBName,$DBUser,$DBPswd)
	{	
		global $DBLink,$DBName,$DBUser,$DBPswd;
		$con = mysqli_connect($DBLink,$DBUser,$DBPswd,$DBName);
//		mysql_select_db(, $con);
		$result = mysqli_query($con,$sql);
		if (!$result) {
			return ('Invalid query: ' . mysql_error());
		}
		$res = array();
		$x = 0;
		while($row = mysqli_fetch_array($result))
		{
			$res[$x] = $row; 
			$x++;
		}
		
		return $res;
		mysqli_close($con);
	}
	function executeTest($sql,$DBLink,$DBName,$DBUser,$DBPswd)
	{
		//global $DBLink,$DBName,$DBUser,$DBPswd;
		$con = mysqli_connect($DBLink,$DBUser,$DBPswd,$DBName);
//		mysql_select_db($DBName, $con);
		$result = mysqli_query($con,$sql);
		if (!$result) {
			return ('Invalid query: ' . mysql_error());
		}
		else if(mysqli_num_fields($result)>1)
		{
			return "You have selected more than 1 columns";
		}
		else
		{
			return "1";
		}
	}
	
	function getColumns($sql,$DBLink,$DBName,$DBUser,$DBPswd)
	{
		//global $DBLink,$DBName,$DBUser,$DBPswd;
		$con = mysql_connect($DBLink,$DBUser,$DBPswd);
		mysql_select_db($DBName, $con);
		$result = mysql_query($sql);
		$i = 0;
		$meta = array();
		while ($i < mysql_num_fields($result))
		{
			$meta[$i] = mysql_fetch_field($result, $i);
			$i = $i + 1;
		} 
		return $meta;
	}
	
	
	function update($sql)//,$DBLink,$DBName,$DBUser,$DBPswd)
	{
		global $DBLink,$DBName,$DBUser,$DBPswd;
		$con = mysqli_connect($DBLink,$DBUser,$DBPswd,$DBName);
//		mysql_select_db($DBName, $con);
		$result = mysqli_query($con,$sql);
		return mysqli_affected_rows($con);
		mysqli_close($con);
	}
	
	function is_int_val($data) {
	if (is_string($data) === true && is_numeric($data) === true) {
		return true;
	}
	else
	{
		return false;
	}
}
?>
