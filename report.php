<!DOCTYPE html>
<html>
<head>
<title>Roble Assassins HQ</title>

<meta name="viewport" content="width=device-width, initial-scale=1"> 
<link rel="stylesheet"
href="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.css" />
   <script type="text/javascript"
   src="http://code.jquery.com/jquery-1.6.4.min.js"></script>
   <script type="text/javascript"
   src="http://code.jquery.com/mobile/1.0/jquery.mobile-1.0.min.js"></script>
</head>
<body>
<div data-role="header">
<h1>Event Report</h1>
</div>
<div data-role="content">
<?php
if ($_POST['name']){
  include ('./sqlitedb.php');
  if($_POST['task']=='kill'){
        $query = 'select target, report from user where name = "'.$_POST['target'].'"';
        $result = $db->query($query);
        $row = $result->fetch();
	if(!$row){
?>
		<p>Well, this is awkward... It seems that you don't have a target.</p>
<?php
		//Email admin
		$to = "odiab@stanford.edu";
		$subject = "Oh poop.";
		$body = "Name: ".$_POST['name'].". Target: ".$_POST['target'];
		if (mail($to, $subject, $body, $headers)) {
		  echo("<p>Message sent to Omar to inform him of this weirdness.</p>");
		}else{
		  echo("<p>Message delivery failed. Please email Omar at odiab@stanford.edu to let him know that something is wrong.</p>");
		}
	}else{
		if($row['target'] == $_POST['name'] && $row['report'] == 1){
?>
			<p>Silly goose, it's you versus your opponent now... and you can't have both killed each other. Suck it up and take your defeat! <a href="index">Back to the login page</a></p>

<?php
		}else{

?>
			<form action="completeReport.php" method="post">
			  <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>" />
			  <input type="hidden" name="target" value="<?php echo $_POST['target']; ?>" />
			  <input type="hidden" name="task" value="kill" />

			  <div data-role="fieldcontain">
			  <label for="story">Tell us how the kill went down:</label>
				<textarea name="story" id="story"></textarea>
			  </div>
			  <input type="submit" value = "File your report" data-theme="b" />
			</form>
<?php
		}
	}
  }else{
	if($_POST['report'] != 0){
?>
		<p>Before you mark yourself as dead, you need to make sure your victim has already confirmed
		his/her death first. Tell that person to mark themselves as dead first, then come back and
		mark your death.</p>
<?php
	}else{
		$query = 'select name from user where dead = 0 and target = "'.$_POST['name'].'"';
		$result = $db->query($query);
		$row = $result->fetch();

		$query = 'select dead from user where name = "'.$_POST['target'].'"';
		$result = $db->query($query);
		$row2 = $result->fetch();
		if($row2['dead'] != 0){
	?>
		<p>Your target claims that they have died. Before you label yourself as dead, at least claim that victory! <a href="index.html" data-direction="reverse">Back to the login page</a></p>
	<?php
		}else{
			if($row){
				$killer = $row['name'];
	?>
				<p>You sure you have failed the mission? To confirm your death, hit the button below.</p>
				<form action="reportDeath.php" method="post">
				  <input type="hidden" name="name" value="<?php echo $_POST['name']; ?>" />
				  <input type="hidden" name="killer" value="<?php echo $killer; ?>" />
				  <input type="hidden" name="newTarget" value="<?php echo $_POST['target'] ; ?>" />
				  <input type="hidden" name="dead" value="dead" />
				  <input type="submit" value="Yes, I failed." />
				</form>
	<?php
			}else{
	?>
				<p>Something strange is happening with the Assassins chain...</p>
	<?php
				//Email admin
				$to = "odiab@stanford.edu";
				$subject = "Oh poop.";
				$body = "Name: ".$_POST['name'].". Target: ".$_POST['target'];
				if (mail($to, $subject, $body, $headers)) {
				  echo("<p>Message sent to Omar. He will work this out.</p>");
				}else{
				  echo("<p>Message delivery failed. Please email Omar at odiab@stanford.edu to let him know that something is wrong.</p>");
				}
			}
		}
	}
  }
  $db=null;
}else{
?>
	<p> You don't belong here. Go back to <a href="index.html" data-direction="reverse">the Assassins HQ entrance.</a></p>
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
