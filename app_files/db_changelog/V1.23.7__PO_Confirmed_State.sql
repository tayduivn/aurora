ALTER TABLE `Purchase Order Dimension` CHANGE `Purchase Order State` `Purchase Order State` ENUM('Cancelled','NoReceived','InProcess','Submitted','Confirmed','Inputted','Dispatched','Received','Checked','Placed','Costing','InvoiceChecked') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'InProcess';

