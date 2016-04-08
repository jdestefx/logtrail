

<html>
   <head>
      <link type="text/css" href="yui-3-3-0-reset-grids-fonts.css" rel="stylesheet" />
      <link type="text/css" href="logtrail.css" rel="stylesheet" />

      <script type="text/javascript" src="jquery-2.0.3.min.js"></script>
      <script type="text/javascript" src="logtrail.js"></script>
   </head>

   <body>
      <div class="yui3-u-1 page-title">:TLP Community Player Database:<br>by lockjaw.xanadas<br>updated 1/30/2015</div>
      <div class="yui3-u-1 links"><a href="showplayers.php?server=ragefire">view ragefire players</a>|<a href="showplayers.php?server=lockjaw">view lockjaw players</a></div>
      <div class="yui3-u-1 page-about">
         What is this page?  This is a small webapp that monitors your eqlog file for /who activity and transmits that information to this web server. The character data captured by /who is stored in a database and can be viewed with the link above. The goal is to create a massive database of character information for analysis. 
         <br><br>Your browser must support HTML5's FILE API. This application has ONLY been tested and verified in CHROME and IE11. FireFox does NOT work.
         <br><br>This mechanism does *not* transmit your group/say/tell chat lines to the server. It's ONLY transmits /who related data. Please view the javascript <a href="logtrail.js">source</a> line 49-67 if you have concerns.<br>
      </div>

      <div class="yui3-u-1 steps">
         <div class="yui3-u-1 step 1">1. Use the file selection control below to find your <b>logs\eqlog_Soandso_ragefire.txt</b> log file. (or eqlog_Soandso_lockjaw.txt for Lockjaw of course)</div>
         <div class="yui3-u-1 step 2">2. In EverQuest, make sure logging is turned on by typing "/log".</div>
         <div class="yui3-u-1 step 3">3. In EverQuest, type "/who". This will log an index of players in your current zone to your eqlog. This web application will be constantly monitoring your eqlog for this data.</div>
         <div class="yui3-u-1 step 4">4. After typing "/who", you should see the captured data displayed below.</div>
         <div class="yui3-u-1 step 5">- If there are more than 100 players in the zone, you'll receive the message about the list being cut short.  You can get around this by adding parameters to the /who command like this: /who 1 20,  then doing another /who 21 50. You don't *have* to do this, I would be perfectly happy with just a normal /who report, regardless of whether or not it gets cut off.</div>

      </div>
   </body>

</html>


<br>

<br>

<input type="file"></input>
   
<br><br>
Status:&nbsp;<span id="status">-NO LOG FILE SELECTED-</span>

<br><br>

<pre id="transmit"></pre>

<pre id="response"></pre>


<script type="text/javascript">
   var lt = new logTrail();
</script>