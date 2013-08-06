CREATE TABLE `JUDGED_SUBMISSIONS_COPY` 
	( `JUDGED_ID` int(11) NOT NULL auto_increment, `TEAM_ID` int(11) NOT NULL default '0', 
	`PROBLEM_ID` int(11) NOT NULL default '0', `TS` int(11) NOT NULL default '0', 
	`ATTEMPT` int(11) NOT NULL default '0', `SOURCE_FILE` char(255) NOT NULL default '', 
	`RESPONSE_ID` int(11) NOT NULL default '0', `VIEWED` int(11) NOT NULL default '0', 
	`JUDGED` int(10) NOT NULL default '0', PRIMARY KEY (`JUDGED_ID`) ) TYPE=MyISAM AUTO_INCREMENT=18 ;


CREATE TABLE `AUTO_RESPONSES_COPY` (
  `JUDGED_ID` int(11) NOT NULL default '0',
  `IN_FILE` varchar(255) NOT NULL default '',
  `AUTO_RESPONSE` int(10) NOT NULL default '0',
  `ERROR_NO` int(10) default '0'
) TYPE=MyISAM;

