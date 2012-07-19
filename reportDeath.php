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
<h1>Death Report</h1>
</div>
<div data-role="content">
<?php
if ($_POST['dead']=='dead'){
  $time = time();
  $newdeadline = $time + 36 * 60 * 60;
  include ('./sqlitedb.php');

  $query = 'select name, email, report from user where dead = 0 and name = "'.$_POST['killer'].'"';
  $result = $db->query($query);
  $row = $result->fetch();
  if(!$row){
?>
	<p> Something went terribly wrong! Please go back to <a href="index.html" data-direction="reverse">the entrance.</a> If you keep getting this problem, email me at odiab@stanford.edu</p>
<?php
  }else{
        $update = $db->prepare ('update user set dead = ( ? ) where name = ( ? ) ');
        $update->execute(array($time, htmlspecialchars($_POST['name'])));
	if($row['report'] == 1){
	  //If the other guy reported killing you, move him forward
      	  $update = $db->prepare ('update user set target = ( ? ), deadline = ( ? ), report = 0 where name = ( ? )');
      	  $update->execute(array(htmlspecialchars($_POST['newTarget']), $newdeadline, htmlspecialchars($_POST['killer'])));

          $update = $db->prepare ('update death set time = '.$time.' where victim = ( ? )');
          $update->execute(array(htmlspecialchars($_POST['name'])));

	  //Email the killer	
	  $to = $row['email'];
 	  $subject = "You have a new target!";
	  $body = "Hello Agent ".$row['name'].",\n\n Your previous target, ".$_POST['name'].", has marked that they died. Come to Assassins HQ at http://goo.gl/kWvbz to find out who your next target is! \n\n -Assassins HQ";
	  if (mail($to, $subject, $body, $headers)) {
	    echo("<p>Message sent to your killer.</p>");
	  }else{
	    echo("<p>Message delivery failed. Please let your killer know that they can move on.</p>");
	  }
	}else{
	  //Otherwise, just mark your death.
          //Email the killer
          $to = $row['email'];
          $subject = "Your target has died!";
          $body = "Hello Agent ".$row['name'].",\n\n Your previous target, ".$_POST['name'].", has marked that they died. Come to Assassins HQ at http://goo.gl/kWvbz to claim your kill and find out who your next target is! \n\n -Assassins HQ";
          if (mail($to, $subject, $body, $headers)) {
            echo("<p>Message sent to your killer.</p>");
          }else{
            echo("<p>Message delivery failed. Please let your killer know that they can move on.</p>");
          }
	}

?>
        <p>We are sorry to hear of your passing. The U.S. government is now forced to disavow you. If you wish to survive, I suggest you leave this page now.</p>
<?php
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
