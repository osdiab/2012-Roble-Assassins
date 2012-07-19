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
  $time = time();
  $newdeadline = $time + 36 * 60 * 60;
  include ('./sqlitedb.php');
  if($_POST['task']=='kill'){
        $query = 'select target, email, dead from user where name = "'.$_POST['target'].'"';
        $result = $db->query($query);
        $row = $result->fetch();
	if(!$row){
?>
		<p> Something went terribly wrong! Please go back to <a href="index.html" data-direction="reverse">the entrance of HQ.</a></p>
<?php
	}else{
	  $newTarget = $row['target'];
	  $oldTargetDead = $row['dead'];

	  if($oldTargetDead == 0){
		//Don't set the new target unless the old one is dead.
		//Since he/she isn't, just set the report variable for that user and save the story.
		$update = $db->prepare ('update user set report = 1 where name = ( ? )');
		$update->execute(array(htmlspecialchars($_POST['name'])));

		$update = $db->prepare ('insert into death values (( ? ), ( ? ), ( ? ), ( ? ))');
		$update->execute(array(htmlspecialchars($_POST['name']), htmlspecialchars($_POST['target']), htmlspecialchars($_POST['story']), 0));
		//Email the victim
		$to = $row['email'];
		$subject = "Your assassin has sent a report for your death!";
		$body = "Hello Agent ".$_POST['target'].",\n\n Your assassin reports that they have killed you. Come to Assassins HQ at http://goo.gl/kWvbz and confirm your death. If you disagree with this report, email odiab@stanford.edu or come to A317 with your assassin, and make your claim. \n\n -Assassins HQ";
		if (mail($to, $subject, $body, $headers)) {
		  echo("<p>Message sent to your victim.</p>");
		 }else{
		  echo("<p>Message delivery failed. Please let your victim know that they need to report their death.</p>");
		}
?>
		<p>Congratulations on your kill. We shall wait for confirmation from your victim; check back later for your new target.</p>
<?php
	  }else{
		//Old target is definitively dead, so move the target foward.
 	     	$update = $db->prepare ('update user set target = ( ? ), deadline = ( ? ) where name = ( ? )');
      		$update->execute(array(htmlspecialchars($newTarget), $newdeadline, htmlspecialchars($_POST['name'])));

        	$update = $db->prepare ('insert into death values (( ? ), ( ? ), ( ? ), ( ? ))');
         	$update->execute(array(htmlspecialchars($_POST['name']), htmlspecialchars($_POST['target']), htmlspecialchars($_POST['story']), $time));

		if($_POST['name'] != $newTarget){
?>
			<p>Congratulations on your kill, <?php echo $_POST['name']; ?>. We have received confirmation from our intelligence that your target has indeed been neutralized. Therefore, your new target is <strong><?php echo $newTarget ?></strong>. Good luck.</p>
<?php 
		}else{
?>
			<p><strong>You won! Congratulations, Agent <?php echo $_POST['name']; ?>, for winning the Roble Assassins game.</strong></p>
<?php
		}
	  }
	}
  }else{
        $query = 'select name from user where target = "'.$_POST['name'].'"';
        $result = $db->query($query);
        $row = $result->fetch();
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
    	}
  }
  $db=null;
}else{
?>
	<p> You don't belong here. Go back to <a href="index.html">the Assassins HQ entrance.</a></p>
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
