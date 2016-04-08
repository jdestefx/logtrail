<html>
   <style type="text/css">

   </style>

   <head>

   </head>

   <body>


      <pre>
         Contribute here:  <a href="http://destef.dyndns.org/logtrail/index.php">[help contribute]</a>


         <div style="max-height:300px; overflow-y:scroll">
         <?php

            if (isset($_GET["activeWithinDays"])==true) {$activeWithinDays=$_GET["activeWithinDays"];} else {$activeWithinDays=-1;}
            if (isset($_GET["minLevel"])==true) {$minLevel=$_GET["minLevel"];} else {$minLevel=1;}

            if ($activeWithinDays==-1) $seenEpoch = 100000000;
            if ($activeWithinDays!=-1) $seenEpoch = time(true)-(86400*$activeWithinDays);

            include("database.php");

            // must have a server
            if (isset($_GET["server"])==false) return false;

            // was a letter passed?
            if (isset($_GET["letter"])==false) {
               $letter = "A";
            } else {
               $letter = strtoupper($_GET["letter"])[0];
            }

            // determine server
            $server = strtolower($_GET["server"]);
            $playerTable = ($server=="ragefire"?"players":"players_lockjaw");

            
            for ($i=65;$i<=90;$i++) {
               echo "<a href=\"showplayers.php?server=$server&letter=".chr($i)."\">".chr($i)."</a>|";
            }
            echo "\n\n";


            $ttlres = $database->queryToObject("select count(*) as cnt from $playerTable where lastSeenTime>=$seenEpoch and level >= $minLevel");
            $res = $database->queryToObject("select * from $playerTable where name like \"$letter%\" and lastSeenTime>=$seenEpoch and level >= $minLevel order by name");

            echo "Total players indexed: <b>".$ttlres[0]["cnt"]."</b>  (names censored to protect the innocent :))\n";

            echo str_pad("name",20);
            echo str_pad("race",20);
            echo str_pad("class", 20);
            echo str_pad("guildName", 40);
            echo str_pad("level", 15);
            echo str_pad("reputation", 15);
            echo str_pad("lastSeen", 30);
            echo str_pad("lastSeenDate", 20);
            echo "\n\n";

            for ($i=0;$i<sizeof($res);$i++) {
               $newname = $res[$i]["name"];
               if (strlen($newname)<=5) {
                  $newname[4] = "*";
               } else {
                  $newname[strlen($newname)-2]= "*";
                  $newname[strlen($newname)-1]= "*";
               }

               echo str_pad($newname, 20);
               echo str_pad($res[$i]["race"], 20);
               echo str_pad($res[$i]["class"], 20);
               echo str_pad($res[$i]["guildName"], 40);
               echo str_pad($res[$i]["level"], 15);
               echo str_pad($res[$i]["reputation"], 15);
               echo str_pad($res[$i]["lastSeen"], 30);

               if ($res[$i]["lastSeenTime"]==0) {
                  echo str_pad("--no timestamp collected--",20);
               } else {
                  echo str_pad(date("Y-m-d H:m:s",$res[$i]["lastSeenTime"])." GMT", 20);
               }

               echo "\n";
            }

            //echo json_encode($res, JSON_PRETTY_PRINT);

         ?>

      </pre>
      </div>
      <?
         passthru("mysql -H -u root community -e \"select race, count(*) as count from $playerTable where race is not NULL and lastSeenTime>=$seenEpoch and level >= $minLevel group by race order by count desc\"");
         passthru("mysql -H -u root community -e \"select class, count(*) as count from $playerTable where race is not NULL and lastSeenTime>=$seenEpoch and level >= $minLevel group by class order by count desc\"");
         passthru("mysql -H -u root community -e \"select class, race, count(*) as count from $playerTable where race is not NULL and lastSeenTime>=$seenEpoch and level >= $minLevel group by class, race order by count desc\"");
         passthru("mysql -H -u root community -e \"select level, count(*) as numberOfPlayersAtThisLevel from $playerTable where lastSeenTime>=$seenEpoch group by level;\"");
         passthru("mysql -H -u root community -e \"select guildName , count(*) as cnt, (select sum(level) from $playerTable where guildName = a.guildName) / (select count(*) from $playerTable where guildName = a.guildName and level != -1) as averageLevel from $playerTable a where lastSeenTime>=$seenEpoch and level >= $minLevel group by guildName order by guildName asc;\"");
      ?>

   </body>

</html>