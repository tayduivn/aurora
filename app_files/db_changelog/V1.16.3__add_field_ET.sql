ALTER TABLE `Email Campaign Type Dimension` CHANGE `Email Campaign Type Code` `Email Campaign Type Code` ENUM('New Customer','Delivery Note Undispatched','Invoice Deleted','New Order','AbandonedCart','Delivery Confirmation','GR Reminder','Invite','Invite Mailshot','Invite Full Mailshot','Marketing','Newsletter','OOS Notification','Order Confirmation','Password Reminder','Registration','Registration Approved','Registration Rejected') CHARACTER SET utf8 COLLATE utf8_unicode_ci NULL DEFAULT NULL;