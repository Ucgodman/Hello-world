<?php
require_once("../config.php");
//
$func = filter_var($_GET["do"]);
call_user_func($func);
//
function login()
{
	$username = $_POST["username"];
    $password = md5($_POST["password"]);
    if(empty($username) && empty($password)){
    	redirect('../login.php?error=1');exit();
    }
	$query = query($con, "SELECT adminid,`username`, `password`,`fullname`,`rolecode` FROM `admin` WHERE `username`='$username' and password='$password' and status=1");
	$check = $query->num_rows;
	if($check==1)
	{
		loginlogs($username);
		$row = $query->row;
		$adminid =$row["adminid"];
		$fullname =$row["fullname"];
		$rolecode =$row["rolecode"];
		$image =$row["adminid"].'.jpg';
		//
		session("image",$image);
		session("adminid",$adminid);
		session("rolecode",$rolecode);
		//redirect('../index.php');
	}
	else
	{
		$squery = query($con,"SELECT student_id,`username`, `password`,surname,`othername`,`rolecode` FROM `student` WHERE `username`='$username' and password='$password' and status=1");
		$scheck = $squery->num_rows;
		if($scheck==1)
		{
			loginlogs($username);
			$row = $squery->row;
			$adminid =$row["student_id"];
			$fullname =$row['surname'].' '.$row["othername"];
			$rolecode =$row["rolecode"];
			//
			session("adminid",$adminid);
			session("rolecode",$rolecode);
		}
		else
		{
			// echo 0;
			redirect('../login.php?error=1');
		}
	}
}
function studentlogin()
{
	$admission_no = $_POST["admission_no"];
	$class_master = $_POST["class_master"];
	$term_master = $_POST["term_master"];
	$session_master = $_POST["session_master"];
    if(empty($admission_no)){
    	redirect('../login.php?flag=student&error=1');exit();
    }

	$squery = query($con,"SELECT image,student_id,`admission_no`, `password`,surname,`othername`,`rolecode` FROM `student` WHERE `admission_no`='$admission_no' and `class_master`='$class_master' and `term_master`='$term_master' and `session_master`='$session_master' and status=1");
	$scheck = $squery->num_rows;
	if($scheck==1)
	{
		loginlogs($admission_no);
		$row = $squery->row;
		$adminid =$row["student_id"];
		$image =$row["image"];
		$fullname =$row['surname'].' '.$row["othername"];
		$rolecode ='student';
		session("image",$image);
		session("adminid",$adminid);
		session("rolecode",$rolecode);
	}
	else
	{
		redirect('../login.php?flag=student&error=1');exit();
	}
}
//
function loginlogs($username)
{
	$ip_address = $_SERVER['REMOTE_ADDR'];
	$browser = $_SERVER["HTTP_USER_AGENT"];
	$query = query($con,"INSERT INTO `admin_login`(`userid`, `ip_address`, `browser`, `date_time`) VALUES ('$username','$ip_address','$browser',now())");
}
//
function logout()
{
	$rolecode=$_SESSION['rolecode'];
	session_start();
	session_regenerate_id(true);
	session_unset();
	session_destroy();
	$username = $_COOKIE["username"];
	removeCookies();
	if ($rolecode=='student') {
	redirect("../login.php?flag=student");
	}else{
	redirect("../login.php");
	}
}
//
function forgot()
{
	$username = $_POST["username"];
	$newpassword = $_POST["newpassword"];
	$newpassword2 = $_POST["newpassword2"];
	$csql = query($con, "select username from admin where username='$username'");
	$cnum = $csql->num_rows;
	if($cnum==1)
	{
		if($newpassword==$newpassword2)
	    {
			$newpassword = md5($newpassword);
			$result = query($con,"update admin set password='$newpassword' where username='$username'");
			echo 1;
		}
		else
		{
			echo 'Did not match your password';
		}
	}
	else
	{
		echo 'Invalid username. Please enter valid username';
	}
}
?>