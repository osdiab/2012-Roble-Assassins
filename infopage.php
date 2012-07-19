<!DOCTYPE html>
<html>
<head>
<title>Roble Assassins HQ</title>
<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link rel="stylesheet" href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
   <script type="text/javascript" src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
   <script type="text/javascript" src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
</head>
<body>
<div data-role="header">
<h1> Roble Assassins HQ </h1>
</div>
<div data-role="content">
<?php
if ($_POST['id']){
  include ('./sqlitedb.php');
  $query = 'select * from user where suid = "'.$_POST['id'].'"';
  $result = $db->query($query);
  $row = $result->fetch();
  if(!$row) { 
?> 
	<p>We do not have that SUID on file. Either you are not actually
	an assassin, or you typed it in wrong. Try again. </p>
	<form method="post" action="infopage.php">
		<label for="id">Please enter your SUID here:</p>
		<input type="text" name="id" />
		<label for="pass">Enter your codename (secretly) here:</p>
		<input type="password" name="pass" />
		<input type="submit" value="Log into HQ!" data-theme="b" />
	</form>
<?php
  }else{ 
	if($_POST['pass'] == $row['pass']){
		if($row['terminated'] == 1){
?>
		<p>You were terminated for being waaay slow. I suggest you disappear. NOW.</p>
<?php
		}else{
		if($row['name']==$row['target']){
?>
			<p><strong>You won! Congratulations, Agent <?php echo $row['name']; ?>, for winning the Roble Assassins game.</strong></p>
<?php
		}else{
			$username = $row['name'];
			if($row['dead'] == 0){
				$target = $row['target'];
				date_default_timezone_set('PST');
	?>
				<p>Welcome, <strong><?php echo $username; ?></strong>. Your current mission - should you choose to accept it - is to take out <strong><?php echo $target; ?></strong> before <strong><?php echo date('M jS, h:i:s A', $row['deadline']) ?></strong>.</p>
				<form method="post" action="report.php">
				  <input type="hidden" name="name" value="<?php echo $username; ?>" />
				  <input type="hidden" name="target" value="<?php echo $target; ?>" />
				  <input type="hidden" name="report" value="<?php echo $row['report']; ?>" />

				<fieldset data-role="controlgroup">
				    <legend>Have an event to report?</legend>
<?php
				if($row['report'] != 1){
?>
			   		 <input type="radio" name="task" id="kill" value="kill" checked="checked" /> 
			   		 <label for="kill">The target has been assassinated, sir. I am at your disposal.</label>
<?php
				}

				// Check to make sure, between the last two people, that you don't both die.
				$query = 'select target, dead from user where name = "'.$target.'"';
				$result = $db->query($query);
				$row2 = $result->fetch();
				if(!($row2['target'] == $username && $row2['dead'] != 0)){
?>
				    <input type="radio" name="task" id="death" value="death" />
				    <label for="death"> I didn't make it. I deeply regret this failure. </label>
<?php
				}
?>
				</fieldset>
				  <input type="submit" value="File Your Report" data-theme="b" />
				</form>
	<?php
			}else{
	?>
				<p>Sir, you have been disavowed by the U.S. government. I suggest you depart immediately.</p>
	<?php
			}
		}
		}
	}else{
?>
	<p>You gave me the wrong code name. Try again, impostor.</p>
	<form method="post" action="infopage.php">
		<label for="id">Please enter your SUID here:</p>
		<input type="text" name="id" />
		<label for="pass">Enter your codename (secretly) here:</p>
		<input type="password" name="pass" />
		<input type="submit" value="Log into HQ!" data-theme="b" />
	</form>
<?php
  	}
  }
  $db=null;
}else{
?>
	<p>You need to log in before we can provide you with classified documents.</p>
	<form method="post" action="infopage.php">
		<label for="id">Please enter your SUID here:</p>
		<input type="text" name="id" />
		<label for="pass">Enter your codename (secretly) here:</p>
		<input type="password" name="pass" />
		<input type="submit" value="Log into HQ!" data-theme="b" />
	</form>
<?php
} 
?>
</div>

<div data-role="footer">
   <center><p> If you have any trouble, please contact me at odiab@stanford.edu.
  Thanks! </p></center>
</div>

</body>
</html>
