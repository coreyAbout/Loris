TRUNCATE TABLE `instrument_subtests`;
LOCK TABLES `instrument_subtests` WRITE;
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (1,'medical_history','medical_history_page1','Page 1',1);
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (2,'medical_history','medical_history_page2','Page 2',2);
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (3,'medical_history','medical_history_page3','Page 3',3);
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (7,'aosi','aosi_page1','Item Adminstration',1);
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (8,'aosi','aosi_page2','General Observations',2);
INSERT INTO `instrument_subtests` (`ID`, `Test_name`, `Subtest_name`, `Description`, `Order_number`) VALUES (9,'mri_parameter_form','mri_parameter_form_page1','Page 1',1);
UNLOCK TABLES;
