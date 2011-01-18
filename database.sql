# --------------------------------------------------------
# Host:                         127.0.0.1
# Server version:               5.5.8
# Server OS:                    Win64
# HeidiSQL version:             6.0.0.3603
# Date/time:                    2011-01-12 20:53:44
# --------------------------------------------------------

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;

# Dumping structure for table facejudgement.comment
CREATE TABLE IF NOT EXISTS `comment` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SessionId` int(10) unsigned NOT NULL COMMENT 'In welcher Session wurde dieser Kommentar verfasst?',
  `PictureId` int(10) unsigned NOT NULL COMMENT 'Auf welches Bild bezieht sich der Kommentar?',
  `DeletionId` int(10) unsigned DEFAULT NULL COMMENT 'Wurde der Kommentar gelöscht?',
  `Time` int(10) unsigned NOT NULL COMMENT 'Wann wurde der Kommentar erstellt?',
  `Text` varchar(255) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `SessionId` (`SessionId`),
  KEY `PictureId` (`PictureId`),
  KEY `DeletionId` (`DeletionId`),
  CONSTRAINT `FK__deletion` FOREIGN KEY (`DeletionId`) REFERENCES `deletion` (`Id`),
  CONSTRAINT `FK__picture` FOREIGN KEY (`PictureId`) REFERENCES `picture` (`Id`),
  CONSTRAINT `FK__session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.comparison_log
CREATE TABLE IF NOT EXISTS `comparison_log` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PictureId1` int(10) unsigned NOT NULL,
  `PictureId2` int(10) unsigned NOT NULL,
  `Time` int(10) unsigned NOT NULL,
  `SessionId` int(10) unsigned NOT NULL,
  `Winner` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `PictureId1` (`PictureId1`),
  KEY `PictureId2` (`PictureId2`),
  KEY `SessionId` (`SessionId`),
  CONSTRAINT `FK_picture` FOREIGN KEY (`PictureId1`) REFERENCES `picture` (`Id`),
  CONSTRAINT `FK_session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`),
  CONSTRAINT `FK_picture_2` FOREIGN KEY (`PictureId2`) REFERENCES `picture` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.deletion
CREATE TABLE IF NOT EXISTS `deletion` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `SessionId` int(10) unsigned NOT NULL COMMENT 'Wer hat gelöscht?',
  `Time` int(10) unsigned NOT NULL COMMENT 'Wann wurde gelöscht?',
  `Reason` text COMMENT 'Warum wurde gelöscht?',
  PRIMARY KEY (`Id`),
  KEY `SessionId` (`SessionId`),
  CONSTRAINT `FK_deletion_session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.favorite_comparison
CREATE TABLE IF NOT EXISTS `favorite_comparison` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PictureId1` int(10) unsigned NOT NULL,
  `PictureId2` int(10) unsigned NOT NULL,
  `SessionId` int(10) unsigned NOT NULL,
  `Time` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `PictureId1` (`PictureId1`),
  KEY `PictureId2` (`PictureId2`),
  KEY `SessionId` (`SessionId`),
  CONSTRAINT `FK_favorite_comparison_picture` FOREIGN KEY (`PictureId1`) REFERENCES `picture` (`Id`),
  CONSTRAINT `FK_favorite_comparison_picture_2` FOREIGN KEY (`PictureId2`) REFERENCES `picture` (`Id`),
  CONSTRAINT `FK_favorite_comparison_session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.picture
CREATE TABLE IF NOT EXISTS `picture` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Md5` binary(16) NOT NULL COMMENT 'MD5 Hash des Bildes. Mit diesem Hash wird überprüft, ob sich das Bild bereits auf der Plattform existiert.',
  `Gender` tinyint(4) unsigned NOT NULL COMMENT 'Geschlecht der auf dem Bild befindlichen Person. 0 = Männlich 1 = Weiblich',
  `SessionId` int(10) unsigned NOT NULL COMMENT 'Wer hat das Bild hochgeladen?',
  `UploadTime` int(10) unsigned NOT NULL COMMENT 'Wann wurde das Bild hochgeladen?',
  `SiteHits` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Wie oft wurde das Bild bereits angezeigt?',
  `PositiveRating` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Wie viele Positive Bewertungen hat das Bild bekommen?',
  `NegativeRating` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Wie viele Negative Bewertungen hat das Bild bekommen?',
  `DeletionId` int(10) unsigned DEFAULT NULL COMMENT 'Das Bild gilt als gelöscht, wenn hier eine Id vermerkt ist.',
  PRIMARY KEY (`Id`),
  KEY `Md5` (`Md5`),
  KEY `SessionId` (`SessionId`),
  KEY `DeletionId` (`DeletionId`),
  CONSTRAINT `FK_pictures_session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`),
  CONSTRAINT `FK_picture_deletion` FOREIGN KEY (`DeletionId`) REFERENCES `deletion` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.rbac_object
CREATE TABLE IF NOT EXISTS `rbac_object` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Default` tinyint(3) unsigned NOT NULL,
  `Name` varchar(32) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Name` (`Name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.rbac_permission
CREATE TABLE IF NOT EXISTS `rbac_permission` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RoleId` int(10) unsigned NOT NULL,
  `ObjectId` int(10) unsigned NOT NULL,
  `State` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `RoleId` (`RoleId`),
  KEY `ObjectId` (`ObjectId`),
  CONSTRAINT `FK_rbac_permission2role_rbac_role` FOREIGN KEY (`RoleId`) REFERENCES `rbac_role` (`Id`),
  CONSTRAINT `FK_rbac_permission_rbac_object` FOREIGN KEY (`ObjectId`) REFERENCES `rbac_object` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.rbac_role
CREATE TABLE IF NOT EXISTS `rbac_role` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Name` varchar(32) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.rbac_user2role
CREATE TABLE IF NOT EXISTS `rbac_user2role` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `RoleId` int(10) unsigned NOT NULL,
  `UserId` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `RoleId` (`RoleId`),
  KEY `UserId` (`UserId`),
  CONSTRAINT `FK_rbac_role` FOREIGN KEY (`RoleId`) REFERENCES `rbac_role` (`Id`),
  CONSTRAINT `FK_rbac_user2role_user` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.ruleviolation
CREATE TABLE IF NOT EXISTS `ruleviolation` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `PictureId` int(10) unsigned NOT NULL COMMENT 'Um welches Bild handelt es sich?',
  `SessionId` int(10) unsigned NOT NULL COMMENT 'Wer hat den Regelverstoß gemeldet?',
  `Time` int(10) unsigned NOT NULL COMMENT 'Wann wurde der Regelverstoß gemeldet?',
  `Handled` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT 'Wurde der Regelverstoß bereits bearbeitet?',
  `Reason` text NOT NULL COMMENT 'Gegen was verstößt dieses Bild?',
  PRIMARY KEY (`Id`),
  KEY `PictureId` (`PictureId`),
  KEY `SessionId` (`SessionId`),
  CONSTRAINT `FK_ruleviolation_picture` FOREIGN KEY (`PictureId`) REFERENCES `picture` (`Id`),
  CONSTRAINT `FK_ruleviolation_session` FOREIGN KEY (`SessionId`) REFERENCES `session` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.session
CREATE TABLE IF NOT EXISTS `session` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `UserId` int(10) unsigned DEFAULT NULL COMMENT 'Optionales Feld',
  `Ip` int(10) unsigned NOT NULL COMMENT 'IPv6 ?!',
  `StartTime` int(10) unsigned NOT NULL,
  `LastRequest` int(10) unsigned NOT NULL,
  `Hits` int(10) unsigned NOT NULL COMMENT 'Anzahl der Requests aus dieser einen Session',
  `UserAgent` varchar(256) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `UserId` (`UserId`),
  CONSTRAINT `FK_session_user` FOREIGN KEY (`UserId`) REFERENCES `user` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.


# Dumping structure for table facejudgement.user
CREATE TABLE IF NOT EXISTS `user` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CreationTime` int(10) unsigned NOT NULL,
  `CreationIp` int(10) unsigned NOT NULL,
  `DeletionId` int(10) unsigned DEFAULT NULL COMMENT 'Der Benutzer gilt als gelöscht, wenn hier eine Id vermerkt ist.',
  `Username` varchar(32) NOT NULL,
  `Passhash` binary(16) NOT NULL COMMENT 'MD5-Hash in der Binärdarstellung.',
  `Email` varchar(128) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Email` (`Email`),
  KEY `Username` (`Username`),
  KEY `DeletionId` (`DeletionId`),
  CONSTRAINT `FK_user_deletion` FOREIGN KEY (`DeletionId`) REFERENCES `deletion` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

# Data exporting was unselected.
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
