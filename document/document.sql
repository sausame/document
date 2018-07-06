
CREATE TABLE `UserTable` (
 `UserId` int(11) NOT NULL AUTO_INCREMENT,
 `UserName` varchar(50) NOT NULL,
 `Email` varchar(50) NOT NULL,
 `Password` varchar(255) NOT NULL,
 `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`UserId`),
 UNIQUE KEY `username` (`UserName`),
 UNIQUE KEY `email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1

CREATE TABLE `DocumentTable` (
 `DocumentId` int(11) NOT NULL AUTO_INCREMENT,
 `OwnerId` varchar(64) NOT NULL,
 `EditorIds` tinytext NOT NULL,
 `Content` text CHARACTER SET utf8,
 `Trashed` bit(1) DEFAULT NULL,
 `UpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`DocumentId`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=latin1

CREATE TABLE `SegmentTable` (
 `SegmentId` int(11) NOT NULL AUTO_INCREMENT,
 `DocumentId` int(11) NOT NULL,
 `SegmentName` varchar(64) NOT NULL,
 `EditorId` int(11) NOT NULL,
 `Content` text CHARACTER SET utf8,
 `Deleted` bit(1) DEFAULT NULL,
 `UpdateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`SegmentId`),
 KEY `DocumentId` (`DocumentId`),
 KEY `SegmentName` (`SegmentName`),
 KEY `EditorId` (`EditorId`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=latin1

CREATE TABLE `SegmentHistoryTable` (
 `SegmentHistoryId` int(11) NOT NULL AUTO_INCREMENT,
 `SegmentId` int(11) NOT NULL,
 `EditorId` int(11) NOT NULL,
 `OriginalContent` text CHARACTER SET utf8,
 `Content` text CHARACTER SET utf8,
 `CreateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
 PRIMARY KEY (`SegmentHistoryId`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=latin1

