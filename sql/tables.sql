-- 
-- Table structure for table `attribute`
-- 

CREATE TABLE `attribute` (
  `id` bigint(20) NOT NULL auto_increment,
  `objectid` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `value` text,
  PRIMARY KEY  (`id`),
  KEY `objectid` (`objectid`,`name`),
  KEY `name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Objektattribute' AUTO_INCREMENT=88 ;

-- 
-- Table structure for table `object`
-- 

CREATE TABLE `object` (
  `id` bigint(20) NOT NULL auto_increment,
  `__createdon` datetime default NULL,
  `__createdby` datetime default NULL,
  `__changedon` datetime default NULL,
  `__changedby` datetime default NULL,
  `type` varchar(100) default NULL,
  PRIMARY KEY  (`id`),
  KEY `type` (`type`),
  KEY `__changedby` (`__changedby`),
  KEY `__changedon` (`__changedon`),
  KEY `__createdby` (`__createdby`),
  KEY `__createdon` (`__createdon`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Allgemeine Objekte' AUTO_INCREMENT=11 ;

-- 
-- Table structure for table `relation`
-- 

CREATE TABLE `relation` (
  `object1` bigint(20) NOT NULL,
  `object2` bigint(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  PRIMARY KEY  (`object1`,`object2`),
  KEY `object2` (`object2`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Objektrelationen';

-- 
-- Constraints for table `attribute`
-- 
ALTER TABLE `attribute`
  ADD CONSTRAINT `attribute_ibfk_1` FOREIGN KEY (`objectid`) REFERENCES `object` (`id`) ON DELETE CASCADE;

-- 
-- Constraints for table `relation`
-- 
ALTER TABLE `relation`
  ADD CONSTRAINT `relation_ibfk_1` FOREIGN KEY (`object1`) REFERENCES `object` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `relation_ibfk_2` FOREIGN KEY (`object2`) REFERENCES `object` (`id`) ON DELETE CASCADE;