
-- 
-- Table structure for table `history`
-- 

CREATE TABLE `history` (
  `title` text NOT NULL,
  `model` int(10) unsigned NOT NULL,
  `date_created` datetime default NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `history`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `last_submit`
-- 

CREATE TABLE `last_submit` (
  `ip` varchar(20) NOT NULL,
  `date_submit` datetime NOT NULL,
  PRIMARY KEY  (`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `last_submit`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ratings`
-- 

CREATE TABLE `ratings` (
  `id` varchar(50) NOT NULL,
  `total_votes` int(11) NOT NULL default '0',
  `total_value` int(11) NOT NULL default '0',
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `ratings`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `ratings_ip`
-- 

CREATE TABLE `ratings_ip` (
  `id` varchar(50) NOT NULL,
  `ip` varchar(20) NOT NULL,
  PRIMARY KEY  (`id`,`ip`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `ratings_ip`
-- 


-- --------------------------------------------------------

-- 
-- Table structure for table `visuel`
-- 

CREATE TABLE `visuel` (
  `md5` varchar(50) NOT NULL,
  `model` int(10) unsigned NOT NULL,
  `title` text NOT NULL,
  `date` datetime NOT NULL,
  `ip` varchar(20) NOT NULL,
  `warnings` int(10) unsigned NOT NULL default '0',
  `candidate` tinyint(3) unsigned NOT NULL default '1',
  `accepted` tinyint(3) unsigned NOT NULL default '1',
  `date_accepted` datetime default NULL,
  PRIMARY KEY  (`md5`),
  FULLTEXT KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- 
-- Dumping data for table `visuel`
-- 

