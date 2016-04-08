<?php
   include("database.php");
   $p = file_get_contents('php://input');
   $po = json_decode($p);
   if (isset($po->m) == false) {echo "No Mode Set."; return; }

   $playerTable = "";


   if ($po->m==1) {
      $server = strtolower($po->server);

      // must be a ragefire or lockjaw log
      if ($server != "ragefire" && $server != "lockjaw") {
         return false;
      } 


      $playerTable = ($server=="ragefire"?"players":"players_lockjaw");

      file_put_contents("/tmp/wholog-$server", implode("\n", $po->lines)."\n", FILE_APPEND);

      $saveLines = array();
      $nowTime = time(true);

      $playersUpdated = 0;
      $playersInserted = 0;

      syslog(LOG_INFO, sizeof($po->lines));

      for ($i=0;$i<sizeof($po->lines);$i++) {
         $line = $po->lines[$i];
         if (strlen(trim($line))==0) continue;

         syslog(LOG_INFO, $line);

         // play anon or role?
         //syslog(LOG_INFO, json_encode(preg_match("/\[ANONYMOUS\]\s(\w+)\s+(?:<[\w\s]+>)?/", $line)));
         if (preg_match("/\[ANONYMOUS\]\s(\w+)\s+(?:<[\w\s]+>)?/", $line)==1) {
            preg_match("/^\[\w+\s\w+\s\d+\s\d+\d+:\d+:\d+\s\d+\]\s\[ANONYMOUS\]\s(\w+)(?:\s<([\w\s]+)>)?/", $line, $captures);
            syslog(LOG_INFO, "matches:".json_encode($captures));
            $name = mysql_escape_string($captures[1]);
            $guild = mysql_escape_string($captures[2]);
            $database->query($qq = "insert into players values (\"$name\", \"(role/anon)\", \"(role/anon)\", \"$guild\", -1, 10, \"(role/anon)\", $nowTime) ON DUPLICATE KEY update lastSeen=\"$zone\", lastSeenTime=$nowTime");
            syslog(LOG_INFO, "qq:".$qq);

            if (mysql_affected_rows()==2) {
               $playersUpdated++;
            } else {
               $playersInserted++;
            }

            $saveLines[] = $qq;
         } 

         // inf/who  visible
         else {
            preg_match("/\d+\s\d+:\d+:\d+\s\d+\]\s+(?:AFK\s{0,})?\[(\d+)\s([\s\w\(\)]+)\]\s(\w+)\s\(([\s\w]+)\)\s+(?:<([\w\s]+)>\s)?ZONE:\s(\w+)/", $line, $captures);

            // does it container a class alias?
            $level = mysql_escape_string($captures[1]);
            $className = mysql_escape_string($captures[2]);
            
            // sanitize any class aliases
            if (preg_match("/[\d\w\s]+\s\([\d\w\s]+\)/", $className)) {
               $className = preg_match("/\s\([\d\w\s]+\)/", $className)[1];
            }

            $name = mysql_escape_string($captures[3]);
            $race = mysql_escape_string($captures[4]);
            $guild = mysql_escape_string($captures[5]);
            $zone = mysql_escape_string($captures[6]);


            $database->query($qq = "insert into ".$playerTable." VALUES (\"$name\", \"$race\", \"$className\", \"$guild\", $level, 10, \"$zone\", $nowTime) ON DUPLICATE KEY UPDATE level=$level, race=\"$race\", guildName = \"$guild\", lastSeen=\"$zone\", lastSeenTime=$nowTime");
            //syslog(LOG_INFO, "aff:".mysql_affected_rows());
            //syslog(LOG_INFO, "qq:".$qq);

            if (mysql_affected_rows()==2) {
               $playersUpdated++;
            } else {
               $playersInserted++;
            }

            $saveLines[] = $qq;
         }

         //file_put_contents("/tmp/community", implode("\n",$saveLines)."\n", FILE_APPEND);
         //syslog(LOG_INFO, "next");

      }

      echo json_encode(array("updates"=>$playersUpdated, "inserts"=>$playersInserted));
   }


?>