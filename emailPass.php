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
  include ('./sqlitedb.php');	
  $query = 'select suid, name, email, pass from user where dead = 0 and terminated = 0';
  $result = $db->query($query);
  $row = $result->fetch();
  while($row){
          //Email the killer    
          $to = $row['email'];
          $subject = "Your codename, in case you forgot";
          $body = "Hello Agent ".$row['name'].",\n\n You are marked as still being alive.  The game is now back on. Good luck! \n\n -Assassins HQ";
          if (mail($to, $subject, $body, $headers)) {
            echo("<p>Message sent to your assassin.</p>");
          }else{
            echo("<p>Message delivery failed. Please let your killer know that they can move on.</p>");
}
    $row = $result->fetch();
  }
?>
</div>

<div data-role="footer">
   <center><p> If you have any trouble, please contact me at odiab@stanford.edu.
  Thanks! </p></center>
</div>

</body>
</html>
