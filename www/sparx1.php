<pre>


<?php


   if (isset($_GET["level"])==true) {$level=$_GET["level"];} else {$level=-1;}
   if (isset($_GET["lastSeenWithin"])==true) {$lastSeenWithin=$_GET["lastSeenWithin"];} else {$lastSeenWithin=-1;}


   if ($level==-1) echo "You need to add ?level=N to the url parameters.\n";
   if ($lastSeenWithin==-1) echo "You need to add &lastSeenWithin=5 to the url parameters.\n";

   $level = mysql_escape_string($level);
   $lastSeenWithin = mysql_escape_string($lastSeenWithin);

   if ($level==-1||$lastSeenWithin==-1) die;

   passthru($qq="mysql -H -u root community -e \"select count(*) as 'Count', class, level, '$lastSeenWithin' as 'Last Seen Within' from players where lastSeenTime > (unix_timestamp(now())-(86400*$lastSeenWithin)) and level = $level group by class,level\"");

echo $qq;

?>

</pre>