<?php
   include("constants.php");

   class MySQLDB {
      var $connection;
      
      var $syslogOutput;

      function MySQLDB(){
         $this->syslogOutput = false;
         $this->connection = mysql_connect(DB_SERVER, DB_USER, DB_PASS) or die(mysql_error());
         mysql_select_db(DB_NAME, $this->connection) or die(mysql_error());
      }
      
      function getNewID() {
         return intval(microtime(true) * 1000);
      }

      function queryToObject($query) {
         $res = $this->query($query);

         $rows = array();
         while($r = mysql_fetch_assoc($res)) {
            $rows[] = $r;
         }

         return $rows;
      }

      function json_encode_results($res) {
         $rows = array();
         while($r = mysql_fetch_assoc($res)) {
            $rows[] = $r;
         }
         return json_encode($rows);
      }
      
      function array_encode_results($res) {
         $rows = array();
         while($r = mysql_fetch_assoc($res)) {
            $rows[] = $r;
         }
         return $rows;
       }


      function array_encode_query($q) {
         $res = $this->query($q);
         return $this->array_encode_results($res);
      }


      function query($query){
         $query = str_replace(array("\r\n", "\n", "\r"), " ", $query);
         if ($this->syslogOutput ==true) syslog(LOG_INFO, "ui_sql_dbg: ".$query);
         return mysql_query($query, $this->connection);
      }

   };

$database = new MySQLDB;

?>
