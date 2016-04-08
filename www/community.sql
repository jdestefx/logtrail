drop table if exists players;
 CREATE TABLE `players` (
  `name` char(128) DEFAULT NULL,
  `class` char(128) DEFAULT NULL,
  `guildName` char(128) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `reputation` char(128) DEFAULT NULL,
  `lastSeen` char(128) DEFAULT NULL,
  PRIMARY KEY (name)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
