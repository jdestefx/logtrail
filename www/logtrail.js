var logTrail = function(opts) {

   $.extend(this, {
      inputElement: $("input"),
   }, opts)

   var lt = this;

   lt.selectedFile = undefined;
   lt.fileReader = new FileReader;
   lt.lastKnownFileSize = undefined;
   lt.slice = undefined;
   lt.checkTimerID = undefined;
   lt.input = $("input");

   lt.construct = function() {
      var checkTimerID = window.setInterval(lt.onCheckInterval, 500);
      lt.spanStatus = $("span#status");
      lt.transmitInfo = $("pre#transmit");
      lt.responseInfo = $("pre#response");
      lt.inputElement.on({change: lt.onInputFileChanged});
   }

   // events
   lt.onInputFileChanged = function(event) {
      var file = lt.inputElement[0].files[0];

      lt.setFile(file);
      lt.spanStatus.text("Monitoring: "+lt.selectedFile.name).css({color:"green"});
      lt.inputElement.hide();
   }
   lt.fileReader.onloadend = function(event) {
      lt.transmit(event.target.result);
   }
   lt.onCheckInterval = function() {
      if (typeof lt.selectedFile == "undefined") return false;
      if (lt.selectedFile.size <= lt.lastKnownFileSize) return false;
      //console.log(lt.lastKnownFileSize, lt.selectedFile.size);
      lt.slice = lt.selectedFile.slice(lt.lastKnownFileSize, lt.selectedFile.size);
      lt.lastKnownFileSize = lt.selectedFile.size;
      lt.fileReader.readAsText(lt.slice);

   }
   lt.onGoClicked = function() {

   }


   // methods
   lt.transmit = function(newData) {
      var lines = newData.split("\n");
      var newlines = [];

      // filter and keep ONLY log lines dealing with /who-type data
      for (var i=lines.length-1;i>=0;i--) {

         // not anon or role?
         if (lines[i].match(/^\[\w+\s\w+\s\d+\s\d+\d+:\d+:\d+\s\d+\]\s+(?:AFK\s{0,})?\[[\d\s\w\(\)]+\]/)!=null) {
            newlines.push(lines[i]);
            continue;
         }

         // anon or role?
         if (lines[i].match(/^\[\w+\s\w+\s\d+\s\d+\d+:\d+:\d+\s\d+\]\s\[ANONYMOUS\]/)!=null) {
            newlines.push(lines[i]);
            continue;
         }

      }

      if (newlines.length==0) return false;

      newlines.reverse();

      // figure out the server
      var server =  lt.selectedFile.name.match(/\w+_\w+_(\w+)/)[1];
      if (typeof server == "undefined") return false;


      lt.transmitInfo.text("Transmitted to the server:\n\n"+ newlines.join("\n"));

      $.ajax({url: "logtrail-actions.php", type: "POST", dataType: "json", data: JSON.stringify({m:1, server: server, lines: newlines}),
         success: function(data) {
            lt.responseInfo.text("Players Updated: "+data.updates+ ", New Players Added: "+data.inserts);
         }
      });


   }
   lt.setFile = function(newFile) {
      lt.selectedFile = newFile;
      lt.lastKnownFileSize = lt.selectedFile.size;
   }

   lt.construct()
   return this;
}

