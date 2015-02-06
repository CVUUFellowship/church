-- MySQL dump 10.13  Distrib 5.5.20, for Linux (x86_64)
--
-- Host: localhost    Database: cvuuf_cvuufinfo
-- ------------------------------------------------------
-- Server version	5.5.20-55

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `announcements`
--

DROP TABLE IF EXISTS `announcements`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `announcements` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` varchar(25) COLLATE utf8_bin NOT NULL,
  `Xdate` varchar(25) COLLATE utf8_bin NOT NULL,
  `Time` varchar(15) COLLATE utf8_bin NOT NULL,
  `Place` varchar(50) COLLATE utf8_bin NOT NULL,
  `Contact` varchar(100) COLLATE utf8_bin NOT NULL,
  `Title` varchar(255) COLLATE utf8_bin NOT NULL,
  `Description` text COLLATE utf8_bin NOT NULL,
  `Link` varchar(255) COLLATE utf8_bin NOT NULL,
  `LinkText` varchar(100) COLLATE utf8_bin NOT NULL,
  `Owner` int(11) NOT NULL,
  `Type` enum('CVUUF','RE','UUA','PSWD','Outside') COLLATE utf8_bin NOT NULL DEFAULT 'CVUUF',
  `Status` enum('entered','approved','rejected','deleted') COLLATE utf8_bin NOT NULL DEFAULT 'entered',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `Date` (`Date`),
  KEY `Time` (`Time`),
  KEY `Owner` (`Owner`)
) ENGINE=MyISAM AUTO_INCREMENT=767 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `authuser`
--

DROP TABLE IF EXISTS `authuser`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `authuser` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `memberid` int(4) NOT NULL DEFAULT '0',
  `uname` varchar(25) NOT NULL DEFAULT '',
  `passwd` varchar(32) NOT NULL DEFAULT '',
  `team` varchar(25) NOT NULL DEFAULT '',
  `level` int(4) NOT NULL DEFAULT '0',
  `status` varchar(10) NOT NULL DEFAULT '',
  `lastlogin` datetime DEFAULT NULL,
  `logincount` int(11) DEFAULT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `uname` (`uname`),
  KEY `level` (`level`),
  KEY `status` (`status`)
) ENGINE=MyISAM AUTO_INCREMENT=463 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_people`
--

DROP TABLE IF EXISTS `av_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_people` (
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PeopleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `av_status`
--

DROP TABLE IF EXISTS `av_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `av_status` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `DateID` int(11) NOT NULL DEFAULT '0',
  `Early` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Late` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `PeopleID` (`PeopleID`),
  KEY `DateID` (`DateID`)
) ENGINE=MyISAM AUTO_INCREMENT=623 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_approvers`
--

DROP TABLE IF EXISTS `cal_approvers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_approvers` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Title` varchar(100) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`RecordID`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_comments`
--

DROP TABLE IF EXISTS `cal_comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_comments` (
  `EventID` int(11) NOT NULL AUTO_INCREMENT,
  `Comments` text NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`EventID`)
) ENGINE=MyISAM AUTO_INCREMENT=1596 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_events`
--

DROP TABLE IF EXISTS `cal_events`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_events` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `EventID` int(11) NOT NULL,
  `RequestTime` datetime NOT NULL,
  `RequesterID` int(11) NOT NULL,
  `resultEmail` datetime NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `CalendarID` (`EventID`),
  KEY `PeopleID` (`RequesterID`)
) ENGINE=MyISAM AUTO_INCREMENT=1200 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_groups`
--

DROP TABLE IF EXISTS `cal_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_groups` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupID` int(11) NOT NULL,
  `GroupName` varchar(100) COLLATE utf8_bin NOT NULL,
  `GroupCode` varchar(100) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `GroupName` (`GroupID`)
) ENGINE=MyISAM AUTO_INCREMENT=82 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cal_rooms`
--

DROP TABLE IF EXISTS `cal_rooms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cal_rooms` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `RoomName` varchar(50) COLLATE utf8_bin NOT NULL,
  `RoomCode` varchar(2) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `RoomName` (`RoomName`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charters_groups`
--

DROP TABLE IF EXISTS `charters_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charters_groups` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `GroupName` varchar(255) COLLATE utf8_bin NOT NULL,
  `GroupType` set('Activity Group','Service Group','Support Group','Social Group','Activity','Service','Support','Social') COLLATE utf8_bin NOT NULL,
  `Purpose` text COLLATE utf8_bin NOT NULL,
  `InclusivePolicy` enum('Open and publicized','Closed with publicized openings for new members','Closed with seeding of new groups when necessary','Closed with publicized openings and seeding') COLLATE utf8_bin NOT NULL,
  `ConfidentialityPolicy` varchar(100) COLLATE utf8_bin NOT NULL,
  `NumberMembers` varchar(100) COLLATE utf8_bin NOT NULL,
  `MeetingLocation` text COLLATE utf8_bin NOT NULL,
  `Meetings` varchar(100) COLLATE utf8_bin NOT NULL,
  `NonCVUUFPolicy` text COLLATE utf8_bin NOT NULL,
  `ApprovalDate` varchar(25) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `charters_org`
--

DROP TABLE IF EXISTS `charters_org`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `charters_org` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Type` enum('Committee','Group','Officer') COLLATE utf8_bin NOT NULL,
  `Name` varchar(255) COLLATE utf8_bin NOT NULL,
  `Purpose` text COLLATE utf8_bin NOT NULL,
  `Organization` text COLLATE utf8_bin NOT NULL,
  `LeaderSelection` text COLLATE utf8_bin NOT NULL,
  `LeaderTerm` text COLLATE utf8_bin NOT NULL,
  `MemberTerm` text COLLATE utf8_bin NOT NULL,
  `NumberMembers` varchar(100) COLLATE utf8_bin NOT NULL,
  `ReportTo` text COLLATE utf8_bin NOT NULL,
  `Meetings` text COLLATE utf8_bin NOT NULL,
  `Duties` text COLLATE utf8_bin NOT NULL,
  `ApprovalDate` varchar(25) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `connections`
--

DROP TABLE IF EXISTS `connections`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `connections` (
  `ConnectID` int(11) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `Comments` varchar(50) NOT NULL DEFAULT '',
  `Angel` varchar(50) NOT NULL DEFAULT '',
  `AngelID` int(11) NOT NULL DEFAULT '0',
  `AskToSign` enum('X','','Yes','No') NOT NULL DEFAULT 'No',
  `Inducted` enum('Yes','No') NOT NULL DEFAULT 'No',
  `InductionDontAsk` set('Yes','No') NOT NULL DEFAULT 'No',
  `OrientationLunch` date NOT NULL DEFAULT '0000-00-00',
  `OrientationComments` varchar(255) NOT NULL DEFAULT '',
  `OrientationDontAsk` enum('','X') NOT NULL DEFAULT '',
  `OrientationInvitedDate` date NOT NULL DEFAULT '0000-00-00',
  `OrientationPriorDate` date NOT NULL DEFAULT '0000-00-00',
  `OrientationInvitedCount` int(11) NOT NULL DEFAULT '0',
  `NewUUDontAsk` enum('X','','Yes','No') NOT NULL DEFAULT 'No',
  `NewUUCommunity` date NOT NULL DEFAULT '0000-00-00',
  `NewUUHeritage` date NOT NULL DEFAULT '0000-00-00',
  `NewUUValues` date NOT NULL DEFAULT '0000-00-00',
  `UU_date` date NOT NULL DEFAULT '0000-00-00',
  `US_date` date NOT NULL DEFAULT '0000-00-00',
  `FriendDate` date NOT NULL DEFAULT '0000-00-00',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`ConnectID`),
  KEY `People` (`PeopleID`)
) ENGINE=MyISAM AUTO_INCREMENT=1090 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `cookies`
--

DROP TABLE IF EXISTS `cookies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `cookies` (
  `SecurityID` int(11) NOT NULL DEFAULT '0',
  `Value` varchar(38) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `SecurityID` (`SecurityID`),
  KEY `Value` (`Value`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `em_people`
--

DROP TABLE IF EXISTS `em_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `em_people` (
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PeopleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `em_status`
--

DROP TABLE IF EXISTS `em_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `em_status` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `DateID` int(11) NOT NULL DEFAULT '0',
  `Early` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Late` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `PeopleID` (`PeopleID`),
  KEY `DateID` (`DateID`)
) ENGINE=MyISAM AUTO_INCREMENT=606 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_forward`
--

DROP TABLE IF EXISTS `email_forward`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_forward` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Forwarder` char(50) NOT NULL,
  `ForwardTo` char(100) NOT NULL,
  PRIMARY KEY (`RecordID`),
  KEY `To` (`ForwardTo`),
  KEY `Forwarder` (`Forwarder`)
) ENGINE=InnoDB AUTO_INCREMENT=4941 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `email_lists`
--

DROP TABLE IF EXISTS `email_lists`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `email_lists` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Listname` varchar(20) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Email` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `groups`
--

DROP TABLE IF EXISTS `groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `groups` (
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `GroupID` int(11) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `GroupID` (`GroupID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hood_from_zip`
--

DROP TABLE IF EXISTS `hood_from_zip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hood_from_zip` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `HoodID` int(11) NOT NULL,
  `Low` varchar(10) NOT NULL DEFAULT '',
  `High` varchar(10) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `hoods`
--

DROP TABLE IF EXISTS `hoods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `hoods` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `HoodName` varchar(50) COLLATE utf8_bin NOT NULL,
  `Dot` varchar(50) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `HoodName` (`HoodName`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `households`
--

DROP TABLE IF EXISTS `households`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `households` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Inactive` set('yes','no') NOT NULL DEFAULT 'no',
  `HouseholdName` varchar(50) DEFAULT NULL,
  `CreationDate` date DEFAULT NULL,
  `ModificationDate` date DEFAULT NULL,
  `Street` varchar(50) DEFAULT NULL,
  `City` varchar(50) DEFAULT NULL,
  `State` char(2) DEFAULT NULL,
  `Zip` varchar(10) DEFAULT NULL,
  `AreaCode` char(3) DEFAULT NULL,
  `Phone` varchar(20) DEFAULT NULL,
  `DirectoryChange` set('X','') NOT NULL DEFAULT '',
  `IsOrganization` set('yes','') DEFAULT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `CreationDate` (`CreationDate`),
  KEY `ModificationDate` (`ModificationDate`),
  KEY `Inactive` (`Inactive`)
) ENGINE=MyISAM AUTO_INCREMENT=3631 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `libraryaudio`
--

DROP TABLE IF EXISTS `libraryaudio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `libraryaudio` (
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL DEFAULT '0000-00-00',
  `Presenter` varchar(100) NOT NULL DEFAULT '',
  `Title` varchar(255) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=2014 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `librarycatalog`
--

DROP TABLE IF EXISTS `librarycatalog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `librarycatalog` (
  `Title` varchar(255) DEFAULT NULL,
  `Author` varchar(60) DEFAULT NULL,
  `CallNumber` varchar(15) DEFAULT NULL,
  `Subject1` varchar(60) DEFAULT NULL,
  `Subject2` varchar(60) DEFAULT NULL,
  `Subject3` varchar(60) DEFAULT NULL,
  `Subject4` varchar(60) DEFAULT NULL,
  `Publisher` varchar(60) DEFAULT NULL,
  `Date` varchar(15) DEFAULT NULL,
  `Price` varchar(15) DEFAULT NULL,
  `Number` int(11) NOT NULL AUTO_INCREMENT,
  `CreateDate` date NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Number`),
  KEY `Title` (`Title`,`Author`),
  KEY `CallNumber` (`CallNumber`),
  KEY `Subject1` (`Subject1`),
  KEY `Subject2` (`Subject2`),
  KEY `Subject3` (`Subject3`),
  KEY `Subject4` (`Subject4`),
  KEY `Publisher` (`Publisher`),
  KEY `Date` (`Date`),
  KEY `Price` (`Price`)
) ENGINE=MyISAM AUTO_INCREMENT=2127 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_contacts`
--

DROP TABLE IF EXISTS `list_contacts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_contacts` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL DEFAULT '0',
  `Title` varchar(240) NOT NULL DEFAULT '',
  `Contact1` int(4) NOT NULL DEFAULT '0',
  `Contact2` int(4) NOT NULL DEFAULT '0',
  `Contact3` int(4) NOT NULL DEFAULT '0',
  `Contact4` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Type`,`RecordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_groups`
--

DROP TABLE IF EXISTS `list_groups`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_groups` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Title` varchar(240) NOT NULL DEFAULT '',
  `Sequence` int(11) NOT NULL,
  `HeadingID` int(11) NOT NULL DEFAULT '0',
  `Description` text NOT NULL,
  `Contact1` int(4) NOT NULL DEFAULT '0',
  `Contact2` int(4) NOT NULL DEFAULT '0',
  `Contact3` int(4) NOT NULL DEFAULT '0',
  `Contact4` int(4) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `PublicPage` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=925 DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_headings`
--

DROP TABLE IF EXISTS `list_headings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_headings` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `HeadingName` varchar(50) NOT NULL DEFAULT '0',
  `Sequence` int(11) NOT NULL DEFAULT '0',
  `PrintSequence` int(2) NOT NULL DEFAULT '0',
  `WebSequence` int(2) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Type`,`RecordID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_index`
--

DROP TABLE IF EXISTS `list_index`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_index` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `HeadingID` int(4) NOT NULL DEFAULT '0',
  `TitleID` int(4) NOT NULL DEFAULT '0',
  `Sequence` int(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Type`,`RecordID`),
  KEY `HeadingName` (`HeadingID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_positions`
--

DROP TABLE IF EXISTS `list_positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_positions` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Title` varchar(240) NOT NULL DEFAULT '',
  `Sequence` int(11) NOT NULL DEFAULT '0',
  `HeadingID` int(11) NOT NULL DEFAULT '0',
  `Description` text NOT NULL,
  `Contact1` int(4) NOT NULL DEFAULT '0',
  `Contact2` int(4) NOT NULL DEFAULT '0',
  `Contact3` int(4) NOT NULL DEFAULT '0',
  `Contact4` int(4) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `PublicPage` enum('yes','no') NOT NULL DEFAULT 'no',
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=364 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `list_sequence`
--

DROP TABLE IF EXISTS `list_sequence`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `list_sequence` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `HeadingID` int(4) NOT NULL DEFAULT '0',
  `TitleID` int(4) NOT NULL DEFAULT '0',
  `NextGroup` int(4) NOT NULL DEFAULT '0',
  `Sequence` int(11) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Type`,`RecordID`),
  KEY `HeadingName` (`HeadingID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1 PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_email`
--

DROP TABLE IF EXISTS `log_email`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_email` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `controller` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `action` varchar(50) COLLATE utf8_bin NOT NULL DEFAULT '',
  `listcount` int(11) NOT NULL DEFAULT '0',
  `sentcount` int(11) NOT NULL DEFAULT '0',
  `invalid` int(11) NOT NULL DEFAULT '0',
  `unsub` int(11) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4778 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `log_login`
--

DROP TABLE IF EXISTS `log_login`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `log_login` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ip` varchar(20) COLLATE utf8_bin NOT NULL,
  `memberid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3376 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `menus`
--

DROP TABLE IF EXISTS `menus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `menus` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Page` varchar(10) NOT NULL DEFAULT '',
  `Name` varchar(20) NOT NULL DEFAULT '',
  `Position` int(4) NOT NULL DEFAULT '0',
  `Level` int(11) NOT NULL DEFAULT '0',
  `Text` varchar(50) NOT NULL DEFAULT '',
  `Type` set('url','menu') NOT NULL DEFAULT 'url',
  `Item` varchar(60) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `Name` (`Name`),
  KEY `Position` (`Position`)
) ENGINE=MyISAM AUTO_INCREMENT=992 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `neighborhoods`
--

DROP TABLE IF EXISTS `neighborhoods`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `neighborhoods` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Active` enum('yes','no') NOT NULL DEFAULT 'yes',
  `NoCall` enum('yes','no') NOT NULL DEFAULT 'no',
  `HouseholdID` int(11) DEFAULT NULL,
  `Neighborhood` enum('10 Degrees Cooler','Newbury Neighbors','The Belly Button','Simi','TOWN','The Redwoods','Northwest Territory','Old Meadows','Westlake','Southern Comfort','','Wildwood') NOT NULL DEFAULT '',
  `HoodID` int(11) NOT NULL,
  `Comments` varchar(50) NOT NULL DEFAULT '',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `Neighborhood` (`Neighborhood`),
  KEY `HouseholdID` (`HouseholdID`),
  KEY `HouseholdID_2` (`HouseholdID`),
  KEY `HoodID` (`HoodID`)
) ENGINE=MyISAM AUTO_INCREMENT=622 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletteremails`
--

DROP TABLE IF EXISTS `newsletteremails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletteremails` (
  `PersonID` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletterlog`
--

DROP TABLE IF EXISTS `newsletterlog`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletterlog` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `IP` varchar(20) NOT NULL DEFAULT '0',
  `Edition` varchar(20) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  KEY `PeopleID` (`IP`),
  KEY `Edition` (`Edition`),
  KEY `RecordID` (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=13527 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `newsletters`
--

DROP TABLE IF EXISTS `newsletters`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `newsletters` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Inactive` enum('yes','no') NOT NULL DEFAULT 'no',
  `PersonID1` int(11) NOT NULL DEFAULT '0',
  `PersonID2` int(11) NOT NULL DEFAULT '0',
  `MostRecent` date NOT NULL DEFAULT '0000-00-00',
  `OptOut` enum('yes','no') NOT NULL DEFAULT 'no',
  `Email1` enum('yes','no') NOT NULL DEFAULT 'no',
  `Email2` enum('yes','no') NOT NULL DEFAULT 'no',
  `LastTime` enum('yes','no','sent') NOT NULL DEFAULT 'no',
  `FirstMonth` date NOT NULL DEFAULT '0000-00-00',
  `ReStarted` enum('yes','no') NOT NULL DEFAULT 'no',
  `CountSent` int(11) NOT NULL DEFAULT '0',
  `Subscription` int(11) NOT NULL DEFAULT '3',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=982 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `nodes`
--

DROP TABLE IF EXISTS `nodes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `nodes` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Nid` int(11) NOT NULL,
  `Title` varchar(255) NOT NULL,
  `Content` enum('Minutes','Agenda','Text') NOT NULL DEFAULT 'Minutes',
  `Org` enum('Board','Council','Fellowship','Joint') NOT NULL,
  `Special` enum('Regular','Special') NOT NULL,
  `Date` varchar(10) NOT NULL,
  `Body` mediumtext NOT NULL,
  `Preliminary` enum('yes','no','') NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=213 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `oos_services`
--

DROP TABLE IF EXISTS `oos_services`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `oos_services` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Sunday` date NOT NULL DEFAULT '0000-00-00',
  `SermonTitle` varchar(255) NOT NULL DEFAULT '+',
  `Presenter` varchar(255) NOT NULL DEFAULT '+',
  `Summary` text NOT NULL,
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `Sunday` (`Sunday`)
) ENGINE=MyISAM AUTO_INCREMENT=1139 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `people`
--

DROP TABLE IF EXISTS `people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `people` (
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Inactive` set('yes','no') CHARACTER SET latin1 NOT NULL DEFAULT 'no',
  `FirstName` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `LastName` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `CreationDate` date NOT NULL DEFAULT '0000-00-00',
  `ResignDate` date NOT NULL DEFAULT '0000-00-00',
  `MembershipDate` date NOT NULL DEFAULT '0000-00-00',
  `BirthDate` date NOT NULL DEFAULT '0000-00-00',
  `HouseholdID` int(11) NOT NULL DEFAULT '0',
  `Gender` set('Male','Female') CHARACTER SET latin1 NOT NULL DEFAULT '',
  `Status` set('Member','Friend','Child','Visitor','Spouse','Special','Deceased','Resigned','Guardian','Affiliate','NewFriend','Guest','Staff') CHARACTER SET latin1 NOT NULL DEFAULT '',
  `Photolink` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `PPhone` varchar(25) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `Email` varchar(60) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `FirstName` (`FirstName`),
  KEY `LastName` (`LastName`),
  KEY `CreationDate` (`CreationDate`),
  KEY `HouseholdID` (`HouseholdID`),
  KEY `Status` (`Status`),
  KEY `Inactive` (`Inactive`)
) ENGINE=MyISAM AUTO_INCREMENT=5489 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `policies`
--

DROP TABLE IF EXISTS `policies`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `policies` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Number` int(4) NOT NULL DEFAULT '0',
  `Status` enum('Current','Old','Proposed','Deleted','Updating') COLLATE utf8_bin NOT NULL,
  `BelowPolicy` int(11) NOT NULL,
  `PolicyType` enum('Council Limitations','Board-Council Linkage','Organizational Documents','Ends Policies','Governance Process','Other') COLLATE utf8_bin NOT NULL,
  `Level` int(11) NOT NULL,
  `Name` varchar(100) COLLATE utf8_bin NOT NULL,
  `Description` varchar(255) COLLATE utf8_bin NOT NULL,
  `Revision` int(11) NOT NULL,
  `SubmitDate` datetime NOT NULL,
  `ApprovalDate` datetime NOT NULL,
  `PDFFile` varchar(50) COLLATE utf8_bin NOT NULL,
  `RTFFile` varchar(50) COLLATE utf8_bin NOT NULL,
  `Times` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=83 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `positions`
--

DROP TABLE IF EXISTS `positions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `positions` (
  `Type` enum('group','position','other') NOT NULL DEFAULT 'other',
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Title` varchar(240) NOT NULL DEFAULT '',
  `Description` text NOT NULL,
  `Contact1` int(4) NOT NULL DEFAULT '0',
  `Contact2` int(4) NOT NULL DEFAULT '0',
  `Contact3` int(4) NOT NULL DEFAULT '0',
  `Contact4` int(4) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=335 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `progtable`
--

DROP TABLE IF EXISTS `progtable`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `progtable` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `ReserveDate` int(11) NOT NULL DEFAULT '0',
  `Person` varchar(50) COLLATE utf8_bin NOT NULL,
  `Program` varchar(50) COLLATE utf8_bin NOT NULL,
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `re`
--

DROP TABLE IF EXISTS `re`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `re` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Inactive` enum('yes','no') NOT NULL DEFAULT 'no',
  `Registered` enum('yes','no','track','guest') NOT NULL DEFAULT 'no',
  `Status` enum('Over 18','YRUU','') NOT NULL,
  `Class` enum('None','Child Space','Pre-School','Lower Elementary','Upper Elementary','Middle School','YRUU') NOT NULL,
  `ChildID` int(11) NOT NULL DEFAULT '0',
  `Birth` varchar(50) NOT NULL DEFAULT '',
  `Grade` varchar(10) NOT NULL DEFAULT '',
  `Gender` enum('M','F','') NOT NULL DEFAULT 'M',
  `PPersonID` int(11) NOT NULL DEFAULT '0',
  `APersonID` int(11) NOT NULL DEFAULT '0',
  `Allergies` set('Weeds/Grasses/Pollens','Bee Stings','Child Carries Inhaler','Child carries Epinephrine Pen','Food Allergies','Allergy Medications') NOT NULL DEFAULT '',
  `FoodAllergies` text NOT NULL,
  `AllergyMeds` text NOT NULL,
  `Health` set('Behavioral box','Developmental box','Language box','Other box','Medications box') NOT NULL DEFAULT '',
  `BehavIssues` text NOT NULL,
  `DevelIssues` text NOT NULL,
  `LangIssues` text NOT NULL,
  `OtherIssues` text NOT NULL,
  `Medications` text NOT NULL,
  `Characteristics` set('Artistic','Athletic','Cerebral','Creative','Agitated','Embarrassed','Follower','Helpful','Idealistic','Imaginative','Leader','Musical','Quiet','Resourceful','Temper','Shy','Spirited','Solitary','Talkative','Other') NOT NULL DEFAULT '',
  `OtherText` text NOT NULL,
  `Receive` text NOT NULL,
  `Discuss` enum('Yes','No','') NOT NULL DEFAULT 'Yes',
  `Insurance` text NOT NULL,
  `InsNumber` varchar(40) NOT NULL DEFAULT '',
  `SigName` varchar(100) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `Registered` (`Registered`,`Status`,`Class`)
) ENGINE=MyISAM AUTO_INCREMENT=334 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `re_attendance`
--

DROP TABLE IF EXISTS `re_attendance`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `re_attendance` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `ChildID` int(11) NOT NULL,
  `Date` char(10) COLLATE utf8_bin NOT NULL,
  PRIMARY KEY (`RecordID`),
  KEY `ChildID` (`ChildID`),
  KEY `Date` (`Date`)
) ENGINE=MyISAM AUTO_INCREMENT=3515 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `repeople`
--

DROP TABLE IF EXISTS `repeople`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `repeople` (
  `PeopleID` int(4) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sexton_people`
--

DROP TABLE IF EXISTS `sexton_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sexton_people` (
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PeopleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `sexton_status`
--

DROP TABLE IF EXISTS `sexton_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `sexton_status` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `DateID` int(11) NOT NULL DEFAULT '0',
  `Status` enum('available','away','clear','early','late','both') NOT NULL DEFAULT 'available',
  `Early` enum('available','away','scheduled','clear') DEFAULT 'available',
  `Late` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `PeopleID` (`PeopleID`),
  KEY `DateID` (`DateID`)
) ENGINE=MyISAM AUTO_INCREMENT=638 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unsub`
--

DROP TABLE IF EXISTS `unsub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unsub` (
  `PersonID` int(11) NOT NULL,
  `all` tinyint(1) NOT NULL DEFAULT '0',
  `weekly` tinyint(1) NOT NULL DEFAULT '0',
  `newsletter` tinyint(1) NOT NULL DEFAULT '0',
  `neighborhood` tinyint(1) NOT NULL DEFAULT '0',
  `individual` tinyint(1) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PersonID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `unsub_log`
--

DROP TABLE IF EXISTS `unsub_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `unsub_log` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `Email` varchar(255) NOT NULL DEFAULT '',
  `PersonID` int(11) NOT NULL DEFAULT '0',
  `UnsubType` varchar(25) NOT NULL DEFAULT '',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `PersonID` (`PersonID`)
) ENGINE=InnoDB AUTO_INCREMENT=147 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visitors`
--

DROP TABLE IF EXISTS `visitors`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visitors` (
  `PersonID` int(4) NOT NULL DEFAULT '0',
  `Inactive` enum('','yes','no') NOT NULL DEFAULT '',
  `SignedDate` date DEFAULT NULL,
  `ResignedDate` date DEFAULT NULL,
  `Reference` set('None','Friend','Other','Paper','Phone','Web','Radio','TV') DEFAULT NULL,
  `Comment` char(50) DEFAULT NULL,
  `PriorUU` enum('','yes','no') NOT NULL DEFAULT '',
  `KeepActive` enum('','yes','no') NOT NULL DEFAULT '',
  `NewslettersSent` tinyint(4) NOT NULL DEFAULT '0',
  `TimeStamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`PersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `visits`
--

DROP TABLE IF EXISTS `visits`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `visits` (
  `VisitID` int(5) NOT NULL AUTO_INCREMENT,
  `PersonID` int(11) NOT NULL DEFAULT '0',
  `VisitDate` date NOT NULL DEFAULT '0000-00-00',
  `Service` enum('1','2','0') NOT NULL DEFAULT '0',
  PRIMARY KEY (`VisitID`),
  KEY `VisitDateTime` (`Service`,`VisitDate`)
) ENGINE=MyISAM AUTO_INCREMENT=4702 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_access_function`
--

DROP TABLE IF EXISTS `webcal_access_function`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_access_function` (
  `cal_login` varchar(25) NOT NULL,
  `cal_permissions` varchar(64) NOT NULL,
  PRIMARY KEY (`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_access_user`
--

DROP TABLE IF EXISTS `webcal_access_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_access_user` (
  `cal_login` varchar(25) NOT NULL,
  `cal_other_user` varchar(25) NOT NULL,
  `cal_can_view` int(11) NOT NULL DEFAULT '0',
  `cal_can_edit` int(11) NOT NULL DEFAULT '0',
  `cal_can_approve` int(11) NOT NULL DEFAULT '0',
  `cal_can_invite` char(1) DEFAULT 'Y',
  `cal_can_email` char(1) DEFAULT 'Y',
  `cal_see_time_only` char(1) DEFAULT 'N',
  PRIMARY KEY (`cal_login`,`cal_other_user`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_asst`
--

DROP TABLE IF EXISTS `webcal_asst`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_asst` (
  `cal_boss` varchar(25) NOT NULL,
  `cal_assistant` varchar(25) NOT NULL,
  PRIMARY KEY (`cal_boss`,`cal_assistant`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_blob`
--

DROP TABLE IF EXISTS `webcal_blob`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_blob` (
  `cal_blob_id` int(11) NOT NULL,
  `cal_id` int(11) DEFAULT NULL,
  `cal_login` varchar(25) DEFAULT NULL,
  `cal_name` varchar(30) DEFAULT NULL,
  `cal_description` varchar(128) DEFAULT NULL,
  `cal_size` int(11) DEFAULT NULL,
  `cal_mime_type` varchar(50) DEFAULT NULL,
  `cal_type` char(1) NOT NULL,
  `cal_mod_date` int(11) NOT NULL,
  `cal_mod_time` int(11) NOT NULL,
  `cal_blob` longblob,
  PRIMARY KEY (`cal_blob_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_categories`
--

DROP TABLE IF EXISTS `webcal_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_owner` varchar(25) DEFAULT NULL,
  `cat_name` varchar(80) NOT NULL,
  `cat_color` varchar(8) DEFAULT NULL,
  PRIMARY KEY (`cat_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_config`
--

DROP TABLE IF EXISTS `webcal_config`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_config` (
  `cal_setting` varchar(50) NOT NULL,
  `cal_value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cal_setting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_cvuuf_for`
--

DROP TABLE IF EXISTS `webcal_cvuuf_for`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_cvuuf_for` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry`
--

DROP TABLE IF EXISTS `webcal_entry`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry` (
  `cal_id` int(11) NOT NULL,
  `cal_group_id` int(11) DEFAULT NULL,
  `cal_ext_for_id` int(11) DEFAULT NULL,
  `cal_create_by` varchar(25) NOT NULL,
  `cal_date` int(11) NOT NULL,
  `cal_time` int(11) DEFAULT NULL,
  `cal_mod_date` int(11) DEFAULT NULL,
  `cal_mod_time` int(11) DEFAULT NULL,
  `cal_duration` int(11) NOT NULL,
  `cal_due_date` int(11) DEFAULT NULL,
  `cal_due_time` int(11) DEFAULT NULL,
  `cal_priority` int(11) DEFAULT '5',
  `cal_type` char(1) DEFAULT 'E',
  `cal_access` char(1) DEFAULT 'P',
  `cal_name` varchar(80) NOT NULL,
  `cal_location` varchar(100) DEFAULT NULL,
  `cal_url` varchar(100) DEFAULT NULL,
  `cal_completed` int(11) DEFAULT NULL,
  `cal_description` text,
  PRIMARY KEY (`cal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_categories`
--

DROP TABLE IF EXISTS `webcal_entry_categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_categories` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cat_id` int(11) NOT NULL DEFAULT '0',
  `cat_order` int(11) NOT NULL DEFAULT '0',
  `cat_owner` varchar(25) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_ext_user`
--

DROP TABLE IF EXISTS `webcal_entry_ext_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_ext_user` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_fullname` varchar(50) NOT NULL,
  `cal_email` varchar(75) DEFAULT NULL,
  PRIMARY KEY (`cal_id`,`cal_fullname`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_log`
--

DROP TABLE IF EXISTS `webcal_entry_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_log` (
  `cal_log_id` int(11) NOT NULL,
  `cal_entry_id` int(11) NOT NULL,
  `cal_login` varchar(25) NOT NULL,
  `cal_user_cal` varchar(25) DEFAULT NULL,
  `cal_type` char(1) NOT NULL,
  `cal_date` int(11) NOT NULL,
  `cal_time` int(11) DEFAULT NULL,
  `cal_text` text,
  PRIMARY KEY (`cal_log_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_repeats`
--

DROP TABLE IF EXISTS `webcal_entry_repeats`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_repeats` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_type` varchar(20) DEFAULT NULL,
  `cal_end` int(11) DEFAULT NULL,
  `cal_endtime` int(11) DEFAULT NULL,
  `cal_frequency` int(11) DEFAULT '1',
  `cal_days` char(7) DEFAULT NULL,
  `cal_bymonth` varchar(50) DEFAULT NULL,
  `cal_bymonthday` varchar(100) DEFAULT NULL,
  `cal_byday` varchar(100) DEFAULT NULL,
  `cal_bysetpos` varchar(50) DEFAULT NULL,
  `cal_byweekno` varchar(50) DEFAULT NULL,
  `cal_byyearday` varchar(50) DEFAULT NULL,
  `cal_wkst` char(2) DEFAULT 'MO',
  `cal_count` int(11) DEFAULT NULL,
  PRIMARY KEY (`cal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_repeats_not`
--

DROP TABLE IF EXISTS `webcal_entry_repeats_not`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_repeats_not` (
  `cal_id` int(11) NOT NULL,
  `cal_date` int(11) NOT NULL,
  `cal_exdate` int(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`cal_id`,`cal_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_entry_user`
--

DROP TABLE IF EXISTS `webcal_entry_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_entry_user` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_login` varchar(25) NOT NULL,
  `cal_status` char(1) DEFAULT 'A',
  `cal_category` int(11) DEFAULT NULL,
  `cal_percent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cal_id`,`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_group`
--

DROP TABLE IF EXISTS `webcal_group`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_group` (
  `cal_group_id` int(11) NOT NULL,
  `cal_owner` varchar(25) DEFAULT NULL,
  `cal_name` varchar(50) NOT NULL,
  `cal_last_update` int(11) NOT NULL,
  PRIMARY KEY (`cal_group_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_group_user`
--

DROP TABLE IF EXISTS `webcal_group_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_group_user` (
  `cal_group_id` int(11) NOT NULL,
  `cal_login` varchar(25) NOT NULL,
  PRIMARY KEY (`cal_group_id`,`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_import`
--

DROP TABLE IF EXISTS `webcal_import`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_import` (
  `cal_import_id` int(11) NOT NULL,
  `cal_name` varchar(50) DEFAULT NULL,
  `cal_date` int(11) NOT NULL,
  `cal_type` varchar(10) NOT NULL,
  `cal_login` varchar(25) DEFAULT NULL,
  PRIMARY KEY (`cal_import_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_import_data`
--

DROP TABLE IF EXISTS `webcal_import_data`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_import_data` (
  `cal_import_id` int(11) NOT NULL,
  `cal_id` int(11) NOT NULL,
  `cal_login` varchar(25) NOT NULL,
  `cal_import_type` varchar(15) NOT NULL,
  `cal_external_id` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`cal_id`,`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_nonuser_cals`
--

DROP TABLE IF EXISTS `webcal_nonuser_cals`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_nonuser_cals` (
  `cal_login` varchar(25) NOT NULL,
  `cal_lastname` varchar(25) DEFAULT NULL,
  `cal_firstname` varchar(25) DEFAULT NULL,
  `cal_admin` varchar(25) NOT NULL,
  `cal_is_public` char(1) NOT NULL DEFAULT 'N',
  `cal_url` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_reminder_log`
--

DROP TABLE IF EXISTS `webcal_reminder_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_reminder_log` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_name` varchar(25) NOT NULL,
  `cal_event_date` int(11) NOT NULL DEFAULT '0',
  `cal_last_sent` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`cal_id`,`cal_name`,`cal_event_date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_reminders`
--

DROP TABLE IF EXISTS `webcal_reminders`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_reminders` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_date` int(11) NOT NULL DEFAULT '0',
  `cal_offset` int(11) NOT NULL DEFAULT '0',
  `cal_related` char(1) NOT NULL DEFAULT 'S',
  `cal_before` char(1) NOT NULL DEFAULT 'Y',
  `cal_last_sent` int(11) NOT NULL DEFAULT '0',
  `cal_repeats` int(11) NOT NULL DEFAULT '0',
  `cal_duration` int(11) NOT NULL DEFAULT '0',
  `cal_times_sent` int(11) NOT NULL DEFAULT '0',
  `cal_action` varchar(12) NOT NULL DEFAULT 'EMAIL',
  PRIMARY KEY (`cal_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_report`
--

DROP TABLE IF EXISTS `webcal_report`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_report` (
  `cal_login` varchar(25) NOT NULL,
  `cal_report_id` int(11) NOT NULL,
  `cal_is_global` char(1) NOT NULL DEFAULT 'N',
  `cal_report_type` varchar(20) NOT NULL,
  `cal_include_header` char(1) NOT NULL DEFAULT 'Y',
  `cal_report_name` varchar(50) NOT NULL,
  `cal_time_range` int(11) NOT NULL,
  `cal_user` varchar(25) DEFAULT NULL,
  `cal_allow_nav` char(1) DEFAULT 'Y',
  `cal_cat_id` int(11) DEFAULT NULL,
  `cal_include_empty` char(1) DEFAULT 'N',
  `cal_show_in_trailer` char(1) DEFAULT 'N',
  `cal_update_date` int(11) NOT NULL,
  PRIMARY KEY (`cal_report_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_report_template`
--

DROP TABLE IF EXISTS `webcal_report_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_report_template` (
  `cal_report_id` int(11) NOT NULL,
  `cal_template_type` char(1) NOT NULL,
  `cal_template_text` text,
  PRIMARY KEY (`cal_report_id`,`cal_template_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_site_extras`
--

DROP TABLE IF EXISTS `webcal_site_extras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_site_extras` (
  `cal_id` int(11) NOT NULL DEFAULT '0',
  `cal_name` varchar(25) NOT NULL,
  `cal_type` int(11) NOT NULL,
  `cal_date` int(11) DEFAULT '0',
  `cal_remind` int(11) DEFAULT '0',
  `cal_data` text
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_timezones`
--

DROP TABLE IF EXISTS `webcal_timezones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_timezones` (
  `tzid` varchar(100) NOT NULL DEFAULT '',
  `dtstart` varchar(25) DEFAULT NULL,
  `dtend` varchar(25) DEFAULT NULL,
  `vtimezone` text,
  PRIMARY KEY (`tzid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_user`
--

DROP TABLE IF EXISTS `webcal_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_user` (
  `cal_login` varchar(25) NOT NULL,
  `cal_passwd` varchar(32) DEFAULT NULL,
  `cal_lastname` varchar(25) DEFAULT NULL,
  `cal_firstname` varchar(25) DEFAULT NULL,
  `cal_is_admin` char(1) DEFAULT 'N',
  `cal_email` varchar(75) DEFAULT NULL,
  `cal_enabled` char(1) DEFAULT 'Y',
  `cal_telephone` varchar(50) DEFAULT NULL,
  `cal_address` varchar(75) DEFAULT NULL,
  `cal_title` varchar(75) DEFAULT NULL,
  `cal_birthday` int(11) DEFAULT NULL,
  `cal_last_login` int(11) DEFAULT NULL,
  PRIMARY KEY (`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_user_layers`
--

DROP TABLE IF EXISTS `webcal_user_layers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_user_layers` (
  `cal_layerid` int(11) NOT NULL DEFAULT '0',
  `cal_login` varchar(25) NOT NULL,
  `cal_layeruser` varchar(25) NOT NULL,
  `cal_color` varchar(25) DEFAULT NULL,
  `cal_dups` char(1) DEFAULT 'N',
  PRIMARY KEY (`cal_login`,`cal_layeruser`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_user_pref`
--

DROP TABLE IF EXISTS `webcal_user_pref`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_user_pref` (
  `cal_login` varchar(25) NOT NULL,
  `cal_setting` varchar(25) NOT NULL,
  `cal_value` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`cal_login`,`cal_setting`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_user_template`
--

DROP TABLE IF EXISTS `webcal_user_template`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_user_template` (
  `cal_login` varchar(25) NOT NULL,
  `cal_type` char(1) NOT NULL,
  `cal_template_text` text,
  PRIMARY KEY (`cal_login`,`cal_type`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_view`
--

DROP TABLE IF EXISTS `webcal_view`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_view` (
  `cal_view_id` int(11) NOT NULL,
  `cal_owner` varchar(25) NOT NULL,
  `cal_name` varchar(50) NOT NULL,
  `cal_view_type` char(1) DEFAULT NULL,
  `cal_is_global` char(1) NOT NULL DEFAULT 'N',
  PRIMARY KEY (`cal_view_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `webcal_view_user`
--

DROP TABLE IF EXISTS `webcal_view_user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `webcal_view_user` (
  `cal_view_id` int(11) NOT NULL,
  `cal_login` varchar(25) NOT NULL,
  PRIMARY KEY (`cal_view_id`,`cal_login`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `welcoming_people`
--

DROP TABLE IF EXISTS `welcoming_people`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `welcoming_people` (
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`PeopleID`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `welcoming_status`
--

DROP TABLE IF EXISTS `welcoming_status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `welcoming_status` (
  `RecordID` int(11) NOT NULL AUTO_INCREMENT,
  `PeopleID` int(11) NOT NULL DEFAULT '0',
  `DateID` int(11) NOT NULL DEFAULT '0',
  `Early` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Late` enum('available','away','scheduled','clear') NOT NULL DEFAULT 'available',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`),
  KEY `PeopleID` (`PeopleID`),
  KEY `DateID` (`DateID`)
) ENGINE=MyISAM AUTO_INCREMENT=526 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `worshipgrid`
--

DROP TABLE IF EXISTS `worshipgrid`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `worshipgrid` (
  `RecordID` int(4) NOT NULL AUTO_INCREMENT,
  `Sunday` enum('yes','no') NOT NULL DEFAULT 'yes',
  `ServiceDate` date NOT NULL DEFAULT '0000-00-00',
  `ServiceTime` varchar(10) NOT NULL DEFAULT '',
  `Presenter` varchar(60) NOT NULL DEFAULT '',
  `Topic` varchar(60) NOT NULL DEFAULT '',
  `Music` varchar(60) NOT NULL DEFAULT '',
  `SpecialMusic` varchar(60) NOT NULL DEFAULT '',
  `Hymns` varchar(255) NOT NULL DEFAULT '',
  `Early` varchar(5) NOT NULL DEFAULT '',
  `Late` varchar(5) NOT NULL DEFAULT '',
  `Organizer` varchar(5) NOT NULL DEFAULT '',
  `WorshipAssoc` varchar(60) NOT NULL DEFAULT '',
  `OtherInfo` varchar(250) NOT NULL DEFAULT '',
  `AttEarly` int(3) NOT NULL DEFAULT '0',
  `AttLate` int(3) NOT NULL DEFAULT '0',
  `Timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`RecordID`)
) ENGINE=MyISAM AUTO_INCREMENT=1986 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2015-02-06  1:19:08
