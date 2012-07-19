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
  $time = time();
  echo $time;
  $newdeadline = $time + 36 * 60 * 60;
  include ('./sqlitedb.php');
  $query = 'select name, email, target from user where report = 0 and terminated = 0 and dead = 0 and deadline < '.$time;
  $result = $db->query($query);
  while($row = $result->fetch()){
    $target = $row['target'];
      try{
        $db->beginTransaction();
        $query2 = 'select name, email, report deadline from user where dead = 0 and terminated = 0 and target = "'.$row['name'].'"';
        $result2 = $db->query($query2);
        $row2 = $result2->fetch();
        $killer = $row2['name'];
        $update = $db->prepare ('update user set dead = ( ? ), terminated = 1 where name = ( ? )');
        $update->execute(array($time, $row['name']));
        $update = $db->prepare ('update user set target = ( ? ), deadline = ( ? ), report = ( ? ) where name = ( ? )');
        if($row2['deadline'] >= $time){
                $update->execute(array($target, $newdeadline, 0, $killer));
        }else{
                $update->execute(array($target, $row2['deadline'], $row2['report'], $killer));
        }
        $db->commit();

	if($row2['deadline'] >= $time){
          //Email the killer
          $to = $row2['email'];
          $subject = "You have a new target!";
          $body = "Hello Agent ".$killer.",\n\n Your previous target, ".$row['name'].", was a coward and did not complete his/her duties.  Therefore, he/she has been terminated, and you have been assigned a new target. Come to Assassins HQ to find out who your next target is! \n\n -Assassins HQ";
          if (mail($to, $subject, $body, $headers)) {
            echo("<p>Message sent to your assassin.</p>");
          }else{
            echo("<p>Message delivery failed. Please let your killer know that they can move on.</p>");
};
	}
          //Email the terminated
          $to = $row['email'];
          $subject = "You have been terminated!";
          $body = "Hello Agent ".$row['name'].",\n\n Or should I say... foolish coward! You've been terminated. Goodbye. \n\n -Assassins HQ";
          if (mail($to, $subject, $body, $headers)) {
            echo("<p>Message sent to the terminated one.</p>");
          }else{
            echo("<p>Message delivery failed to the terminated one.</p>");
        };

      }catch(PDOException $e){
        echo "Failed: " . $e->getMessage();
        $db->rollBack();
      }
  }
	
?>
<h1>These users have been terminated:</h1>
<?php
  $query = 'select name from user where terminated = 1 order by dead desc';
  $result = $db->query($query);
  $row = $result->fetch();
?> <ul> <?php
  while($row){
?><li><?php
    echo($row['name']);
    $row = $result->fetch();
?></li><?php
  }
  $db=null;
?></ul>
</div>

<div data-role="footer">
   <center><p> If you have any trouble, please contact me at odiab@stanford.edu.
  Thanks! </p></center>
</div>

</body>
</html>
