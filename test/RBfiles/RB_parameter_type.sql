TRUNCATE TABLE `parameter_type`;
LOCK TABLES `parameter_type` WRITE;
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (2,'Geometric_distortion','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (3,'Intensity_artifact','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (4,'Movement_artifacts_within_scan','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (5,'Movement_artifacts_between_packets','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (6,'Coverage','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (7,'md5hash','varchar(255)','md5hash magically created by NeuroDB::File',NULL,NULL,'parameter_file.Value','parameter_file',NULL,'quat_table_1',1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (8,'Color_Artifact','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (9,'Entropy','text',NULL,NULL,NULL,NULL,'parameter_file',NULL,NULL,0,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (10,'candidate_label','text','Identifier_of_candidate',NULL,NULL,'PSCID','candidate',NULL,NULL,1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (11,'Visit_label','varchar(255)','Visit_label',NULL,NULL,'visit_label','session',NULL,NULL,1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (12,'candidate_dob','date','Candidate_Dob',NULL,NULL,'DoB','candidate',NULL,NULL,1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (13,'aosi_Date_taken','date','Date of Administration',NULL,NULL,'Date_taken','aosi',NULL,'quat_table_1',1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (14,'aosi_Candidate_Age','varchar(255)','Candidate Age (Months)',NULL,NULL,'Candidate_Age','aosi',NULL,'quat_table_1',1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (15,'aosi_Window_Difference','varchar(255)','Window Difference (+/- Days)',NULL,NULL,'Window_Difference','aosi',NULL,'quat_table_1',1,0);
INSERT INTO `parameter_type` (`ParameterTypeID`, `Name`, `Type`, `Description`, `RangeMin`, `RangeMax`, `SourceField`, `SourceFrom`, `SourceCondition`, `CurrentGUITable`, `Queryable`, `IsFile`) VALUES (16,'aosi_Examiner','varchar(255)','Examiner',NULL,NULL,'Examiner','aosi',NULL,'quat_table_1',1,0);
UNLOCK TABLES;
