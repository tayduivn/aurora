ALTER TABLE `Staff Dimension` CHANGE `Staff Attendance Status` `Staff Attendance Status` ENUM('Work','Home','Outside','Off','Break','Finish') CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'Off';
