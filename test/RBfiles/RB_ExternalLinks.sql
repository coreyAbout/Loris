TRUNCATE TABLE `ExternalLinks`;
LOCK TABLES `ExternalLinks` WRITE;
INSERT INTO `ExternalLinks` (`LinkID`, `LinkTypeID`, `LinkText`, `LinkURL`) VALUES (6,4,'Loris Website','http://www.loris.ca');
INSERT INTO `ExternalLinks` (`LinkID`, `LinkTypeID`, `LinkText`, `LinkURL`) VALUES (7,4,'GitHub','https://github.com/aces/Loris');
INSERT INTO `ExternalLinks` (`LinkID`, `LinkTypeID`, `LinkText`, `LinkURL`) VALUES (8,5,'Loris Website','http://www.loris.ca');
INSERT INTO `ExternalLinks` (`LinkID`, `LinkTypeID`, `LinkText`, `LinkURL`) VALUES (9,5,'GitHub','https://github.com/aces/Loris');
INSERT INTO `ExternalLinks` (`LinkID`, `LinkTypeID`, `LinkText`, `LinkURL`) VALUES (10,6,'Loris Website','http://www.loris.ca');
UNLOCK TABLES;
