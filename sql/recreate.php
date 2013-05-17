<?php	$recreate = "
--
-- Table structure for table `pile`
--

CREATE TABLE IF NOT EXISTS `pile` (
  `pile_id` int(11) NOT NULL auto_increment,
  `user_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `note` varchar(1000) NOT NULL,
  PRIMARY KEY  (`pile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `pile`
--


-- --------------------------------------------------------

--
-- Table structure for table `site`
--

CREATE TABLE IF NOT EXISTS `site` (
  `site_id` int(11) NOT NULL auto_increment,
  `uri` varchar(200) NOT NULL,
  `viewable` tinyint(1) NOT NULL,
  PRIMARY KEY  (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 AUTO_INCREMENT=1 ;

--
-- Dumping data for table `site`
--


-- --------------------------------------------------------

--
-- Table structure for table `privatetag`
--

CREATE TABLE IF NOT EXISTS `privatetag` (
  `pile_id` int(11) NOT NULL,
  `tag` varchar(20) NOT NULL,
  PRIMARY KEY  (`pile_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `privatetag`
--


-- --------------------------------------------------------

--
-- Table structure for table `publictag`
--

CREATE TABLE IF NOT EXISTS `publictag` (
  `site_id` int(11) NOT NULL,
  `tag` varchar(20) NOT NULL,
  PRIMARY KEY  (`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `publictag`
--


-- --------------------------------------------------------

--
-- Table structure for table `review`
--

CREATE TABLE IF NOT EXISTS `review` (
  `user_id` int(11) NOT NULL,
  `site_id` int(11) NOT NULL,
  `vote` tinyint(1) NOT NULL default '0',
  `review` varchar(500) NOT NULL,
  PRIMARY KEY  (`user_id`,`site_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `review`
--


-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE IF NOT EXISTS `user` (
  `user_id` int(5) NOT NULL auto_increment,
  `user_type` tinyint(1) NOT NULL,
  `email` varchar(20) NOT NULL,
  `password` char(64) NOT NULL,
  `firstname` varchar(20) NOT NULL,
  `lastname` varchar(20) NOT NULL,
  PRIMARY KEY  (`user_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=30 ;

--
-- Dumping data for table `user`
--

INSERT INTO `user` VALUES(20, 1, 'admin', '6b86b273ff34fce19d6b804eff5a3f5747ada4eaa22f1d49c01e52ddb7875b4b', 'Default', 'Administrator');
INSERT INTO `user` VALUES(27, 1, 'terrence', '3d5d5a79ed0557cb30b5af692127adcb126d3f0a3bac86385eb9805cb9822807', 'Terrence', 'Chiu');
INSERT INTO `user` VALUES(28, 1, 'horace', '4d63f2f79178d0ad209717e3befbdd4668551f427e7137ac419f61e54307489d', 'Horace', 'Li');
INSERT INTO `user` VALUES(29, 1, 'jerrine', 'db71d1bfb03f60328c0665a40a47bff96b025e7b9263ca63a538cbab08e55e4d', 'Jerrine', 'Soh');

"	?>