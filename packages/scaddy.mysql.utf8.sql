
CREATE TABLE IF NOT EXISTS `j25_sc_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(32) NULL,
  `caption` varchar(255) NULL,
  `type` varchar(16) NULL,
  `length` varchar(11) NULL,
  `classname` varchar(64)  NULL,
  `required` int(11) NULL,
  `ordering` int(11) NULL,
  `published` int(11) NULL,
  `checkfunction` varchar(64) NULL,
  `fieldcontents` text NULL,
  PRIMARY KEY (`id`)
);
    
CREATE TABLE IF NOT EXISTS `j25_sc_odetails` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `orderid` int(11) NULL,
  `prodcode` varchar(10)  NULL,
  `qty` int(11)  NULL,
  `unitprice` float  NULL,
  `total` float  NULL,
  `shorttext` varchar(255)  NULL,
  `option` varchar(255)  NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `j25_sc_orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255)  NULL,
  `email` varchar(255)  NULL,
  `address` text  NULL,
  `codepostal` varchar(15)  NULL,
  `city` varchar(32)  NULL,
  `telephone` varchar(32)  NULL,
  `ipaddress` varchar(16)  NULL,
  `customfields` text  NULL,
  `orderdt` int(11)  NULL,
  `total` float  NULL,
  `status` varchar(16)  NULL,
  `tax` float  NULL,
  `archive` int(11)  NULL,
  `shipCost` varchar(10)  NULL,
  `shipRegion` varchar(255)  NULL,
  `j_user_id` int(11)  NULL,
  `ordercode` varchar(255)  NULL,
  `orderlink` varchar(255)  NULL,
  `paymentcode` varchar(255)  NULL,
  PRIMARY KEY (`id`)
);
  
CREATE TABLE IF NOT EXISTS `j25_sc_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodcode` varchar(64)  NULL,
  `shorttext` varchar(255)  NULL,
  `av_qty` int(11)  NULL,
  `unitprice` float  NULL,
  `published` int(11)  NULL,
  `optionstitle` varchar(32)  NULL,
  `options` text  NULL,
  `showas` int(11)  NULL,
  `category` varchar(255)  NULL,
  `downloadable` int(11)  NULL,
  `filename` varchar(255)  NULL,
  `shippoints` varchar(10)  NULL,
  `shipwidth` float  NULL,
  `shipheight` float  NULL,
  `shiplength` float  NULL,
  `shipweight` float  NULL,
  `userid` int(11) NULL,
  PRIMARY KEY (`id`)
);
    
CREATE TABLE IF NOT EXISTS `j25_sc_prodoptiongroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `prodcode` varchar(32) NOT NULL,
  `title` varchar(255) NOT NULL,
  `showas` int(11) NOT NULL,
  `disporder` int(11) NOT NULL,
  `productid` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `j25_sc_productoptions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `optgroupid` int(11) NOT NULL,
  `description` varchar(255) NOT NULL,
  `formula` varchar(255) NOT NULL,
  `caption` varchar(255) NOT NULL,
  `defselect` int(11) NOT NULL,
  `disporder` int(11) NOT NULL,
  PRIMARY KEY (`id`)
);

CREATE TABLE IF NOT EXISTS `j25_sc_downloads` (
   `id` int(11) not null auto_increment,
   `filename` varchar(255),
   `paymentkey` varchar(255),
   `datetime` int(11),
   PRIMARY KEY (`id`)
);

