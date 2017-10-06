SELECT
  f.File,
  c.PSCID,
  s.CandID,
  s.SubprojectID,
  s.Visit_label,
  s.Visit,
  mad.AcquisitionDate AS MRI_Acquisition_Date,
  s.MRIQCStatus  AS MRI_Visit,
  s.MRIQCPending AS MRI_Pending,
  fmc.Comment,

/*QC status*/
  fq.QCStatus    AS File_QC_status,
  f.Caveat       AS File_Caveat,
  fq.Selected    AS File_Selected,

/*HeadCoil*/
  IF (HeadCoil.Value LIKE "%32Ch_Headad%", '32-channel', '12-channel') AS Head_Coil,

/*Intensity*/
  /*drop downs*/
  ParamIntensity.Value AS Intensity,
  /*checkboxes*/
  IF (MissingSlices.PredefinedCommentID               IS NULL, 'No', 'Yes') AS Missing_Slices,
  IF (ReducedDynamicRange.PredefinedCommentID         IS NULL, 'No', 'Yes') AS Reduced_Dynamic_Range_Bright_Pixel,
  IF (SliceIntensityDifference.PredefinedCommentID    IS NULL, 'No', 'Yes') AS Slice_To_Slices_Intensity_Differences,
  IF (NoisyScan.PredefinedCommentID                   IS NULL, 'No', 'Yes') AS Noisy_Scan,
  IF (SusceptibilityEarCanals.PredefinedCommentID     IS NULL, 'No', 'Yes') AS Susceptibility_Artefact_Ear_Canals,
  IF (SusceptibilityDentalWork.PredefinedCommentID    IS NULL, 'No', 'Yes') AS Susceptibility_Artefact_Dental_Work,
  IF (SusceptibilityAnatomy.PredefinedCommentID       IS NULL, 'No', 'Yes') AS Susceptibility_Artefact_Anatomy,
  IF (SagittalGhosts.PredefinedCommentID              IS NULL, 'No', 'Yes') AS Sagittal_Ghosts,
  IF (AxialGhosts.PredefinedCommentID                 IS NULL, 'No', 'Yes') AS Axial_Ghosts,
  IF (WhiteMatterHyperintensities.PredefinedCommentID IS NULL, 'No', 'Yes') AS White_Matter_Hyperintensities,
  IF (Checkerboard.PredefinedCommentID                IS NULL, 'No', 'Yes') AS Checkerboard_Artifact,
  IF (HighIntensity.PredefinedCommentID               IS NULL, 'No', 'Yes') AS High_Intensity_In_Acquisition_Direction_Artifact,
  IF (SignalLoss.PredefinedCommentID                  IS NULL, 'No', 'Yes') AS Signal_Loss_Artifact,
  /*comment*/
  CommentIntensity.Comment AS Intensity_Comment,

/*Movement*/
  /*drop downs*/
  ParamMovement_artifacts_within_scan.Value AS Movement,
  /*checkboxes*/
  IF (SlightRinging.PredefinedCommentID IS NULL,     'No', 'Yes') AS Slight_Ringing_Artifact,
  IF (SevereRinging.PredefinedCommentID IS NULL,     'No', 'Yes') AS Severe_Ringing_Artifact,
  IF (MovementEyes.PredefinedCommentID IS NULL,      'No', 'Yes') AS Movement_Due_To_Eyes,
  IF (MovementCarotid.PredefinedCommentID IS NULL,   'No', 'Yes') AS Movement_Due_To_Carotid_Flow,
  /*comment*/
  CommentMotion.Comment AS Motion_Comment,    

/*Packet Movement*/
  /*drop downs*/
  ParamPacket_movement_artifact.Value AS PacketMovement,
  /*checkboxes*/
  IF (SlightPacket.PredefinedCommentID IS NULL,     'No', 'Yes') AS Slight_Packet,
  IF (LargePacket.PredefinedCommentID IS NULL,      'No', 'Yes') AS Large_Packet,
  /*comment*/
  CommentPacket.Comment AS PacketMovement_Comment,

/*Coverage*/
  /*drop downs*/
  ParamCoverage.Value AS Coverage,
  /*checkboxes*/
  IF (LargeAPWrap.PredefinedCommentID IS NULL,     'No', 'Yes') AS Large_AP_Wrap,
  IF (MediumAPWrap.PredefinedCommentID IS NULL,    'No', 'Yes') AS Medium_AP_Wrap,
  IF (SmallAPWrap.PredefinedCommentID IS NULL,     'No', 'Yes') AS Small_AP_Wrap,
  IF (TightLRScalp.PredefinedCommentID IS NULL,    'No', 'Yes') AS Tight_LR_Scalp,
  IF (TightLRBrain.PredefinedCommentID IS NULL,    'No', 'Yes') AS Tight_LR_Brain,
  IF (TopScalpCutoff.PredefinedCommentID IS NULL,  'No', 'Yes') AS Top_Scalp_Cut_Off,
  IF (TopBrainCutoff.PredefinedCommentID IS NULL,  'No', 'Yes') AS Top_Brain_Cut_Off,
  IF (CerebellumCutoff.PredefinedCommentID IS NULL,'No', 'Yes') AS Cerebellum_Cut_Off,
  IF (TemporalCutoff.PredefinedCommentID IS NULL,  'No', 'Yes') AS Temporal_Lobe_Cut_Off,
  IF (OccipitalCutoff.PredefinedCommentID IS NULL, 'No', 'Yes') AS Occipital_Lobe_Cut_Off,
  IF (MissingTopThird.PredefinedCommentID IS NULL, 'No', 'Yes') AS Missing_Top_Third,
  /*comment*/
  CommentCoverage.Comment  AS Coverage_Comment,

/*Overall*/
  /*checkboxes*/
  IF (Duplicate.PredefinedCommentID IS NULL,   'No', 'Yes') AS Duplicate_Series,
  /*comment*/
  CommentOverall.Comment AS Overall_Comment

FROM
  files f

  JOIN
    session s
    ON (s.ID=f.SessionID)
  JOIN 
    candidate c
    ON (c.CandID=s.CandID)
  LEFT JOIN
    feedback_mri_comments fmc
    ON fmc.SessionID=s.ID


/*HeadCoil*/
  LEFT JOIN parameter_file AS HeadCoil
    ON (HeadCoil.FileID=f.FileID
      AND HeadCoil.ParameterTypeID=59306)

/*Intensity*/
  /*drop downs*/
  LEFT JOIN parameter_file AS ParamIntensity
    ON (ParamIntensity.FileID=f.FileID
      AND ParamIntensity.ParameterTypeID=70762)
  /*checkboxes*/
  LEFT JOIN feedback_mri_comments AS MissingSlices
  	ON (MissingSlices.FileID=f.FileID 
      AND MissingSlices.PredefinedCommentID=1) 
  LEFT JOIN feedback_mri_comments AS ReducedDynamicRange
   	ON (ReducedDynamicRange.FileID=f.FileID
      AND ReducedDynamicRange.PredefinedCommentID=2) 
  LEFT JOIN feedback_mri_comments AS SliceIntensityDifference
   	ON (SliceIntensityDifference.FileID=f.FileID
      AND SliceIntensityDifference.PredefinedCommentID=3) 
  LEFT JOIN feedback_mri_comments AS NoisyScan
   	ON (NoisyScan.FileID=f.FileID
      AND NoisyScan.PredefinedCommentID=4) 
  LEFT JOIN feedback_mri_comments AS SusceptibilityEarCanals
   	ON (SusceptibilityEarCanals.FileID=f.FileID
      AND SusceptibilityEarCanals.PredefinedCommentID=5) 
  LEFT JOIN feedback_mri_comments AS SusceptibilityDentalWork
   	ON (SusceptibilityDentalWork.FileID=f.FileID
      AND SusceptibilityDentalWork.PredefinedCommentID=6) 
  LEFT JOIN feedback_mri_comments AS SusceptibilityAnatomy
   	ON (SusceptibilityAnatomy.FileID=f.FileID
      AND SusceptibilityAnatomy.PredefinedCommentID=55) 
  LEFT JOIN feedback_mri_comments AS SagittalGhosts
   	ON (SagittalGhosts.FileID=f.FileID
      AND SagittalGhosts.PredefinedCommentID=7) 
  LEFT JOIN feedback_mri_comments AS AxialGhosts
   	ON (AxialGhosts.FileID=f.FileID
      AND AxialGhosts.PredefinedCommentID=27) 
  LEFT JOIN feedback_mri_comments AS WhiteMatterHyperintensities
   	ON (WhiteMatterHyperintensities.FileID=f.FileID
      AND WhiteMatterHyperintensities.PredefinedCommentID=24) 
  LEFT JOIN feedback_mri_comments AS Checkerboard
   	ON (Checkerboard.FileID=f.FileID
      AND Checkerboard.PredefinedCommentID=40) 
  LEFT JOIN feedback_mri_comments AS HighIntensity
   	ON (HighIntensity.FileID=f.FileID
      AND HighIntensity.PredefinedCommentID=43) 
  LEFT JOIN feedback_mri_comments AS SignalLoss
   	ON (SignalLoss.FileID=f.FileID
      AND SignalLoss.PredefinedCommentID=44) 
  /*comment*/
  LEFT JOIN feedback_mri_comments AS CommentIntensity
  	ON (CommentIntensity.FileID=f.FileID
      AND CommentIntensity.CommentTypeID=2
      AND CommentIntensity.PredefinedCommentID IS NULL)

/*Movement*/
  /*drop downs*/
  LEFT JOIN parameter_file AS ParamMovement_artifacts_within_scan 
  	ON (ParamMovement_artifacts_within_scan.FileID=f.FileID
      AND ParamMovement_artifacts_within_scan.ParameterTypeID=70763)
  /*checkboxes*/
  LEFT JOIN feedback_mri_comments AS SlightRinging
  	ON (SlightRinging.FileID=f.FileID
      AND SlightRinging.PredefinedCommentID=8) 
  LEFT JOIN feedback_mri_comments AS SevereRinging
  	ON (SevereRinging.FileID=f.FileID
      AND SevereRinging.PredefinedCommentID=9) 
  LEFT JOIN feedback_mri_comments AS MovementEyes
  	ON (MovementEyes.FileID=f.FileID
      AND MovementEyes.PredefinedCommentID=10) 
  LEFT JOIN feedback_mri_comments AS MovementCarotid
  	ON (MovementCarotid.FileID=f.FileID
      AND MovementCarotid.PredefinedCommentID=11) 
  /*comment*/
  LEFT JOIN feedback_mri_comments AS CommentMotion
  	ON (CommentMotion.FileID=f.FileID
      AND CommentMotion.CommentTypeID=3 
      AND CommentMotion.PredefinedCommentID IS NULL)

/*Packet Movement*/
  /*drop downs*/
  LEFT JOIN parameter_file AS ParamPacket_movement_artifact
  	ON (ParamPacket_movement_artifact.FileID=f.FileID
      AND ParamPacket_movement_artifact.ParameterTypeID=70764)
  /*checkboxes*/
  LEFT JOIN feedback_mri_comments AS SlightPacket
  	ON (SlightPacket.FileID=f.FileID
      AND SlightPacket.PredefinedCommentID=12) 
  LEFT JOIN feedback_mri_comments AS LargePacket
  	ON (LargePacket.FileID=f.FileID
      AND LargePacket.PredefinedCommentID=13) 
  /*comment*/
  LEFT JOIN feedback_mri_comments AS CommentPacket
	ON (CommentPacket.FileID=f.FileID
      AND CommentPacket.CommentTypeID=4
      AND CommentPacket.PredefinedCommentID IS NULL)

/*Coverage*/
  /*drop downs*/
  LEFT JOIN parameter_file AS ParamCoverage 
  	ON (ParamCoverage.FileID=f.FileID
      AND ParamCoverage.ParameterTypeID=70765)
  /*checkboxes*/
  LEFT JOIN feedback_mri_comments AS LargeAPWrap
  	ON (LargeAPWrap.FileID=f.FileID
      AND LargeAPWrap.PredefinedCommentID=14) 
  LEFT JOIN feedback_mri_comments AS MediumAPWrap
  	ON (MediumAPWrap.FileID=f.FileID
      AND MediumAPWrap.PredefinedCommentID=15) 
  LEFT JOIN feedback_mri_comments AS SmallAPWrap
  	ON (SmallAPWrap.FileID=f.FileID
      AND SmallAPWrap.PredefinedCommentID=16) 
  LEFT JOIN feedback_mri_comments AS TightLRBrain
  	ON (TightLRBrain.FileID=f.FileID
      AND TightLRBrain.PredefinedCommentID=18) 
  LEFT JOIN feedback_mri_comments AS TightLRScalp
  	ON (TightLRScalp.FileID=f.FileID
     AND TightLRScalp.PredefinedCommentID=17) 
  LEFT JOIN feedback_mri_comments AS TopScalpCutoff
  	ON (TopScalpCutoff.FileID=f.FileID
      AND TopScalpCutoff.PredefinedCommentID=19) 
  LEFT JOIN feedback_mri_comments AS TopBrainCutoff
  	ON (TopBrainCutoff.FileID=f.FileID
      AND TopBrainCutoff.PredefinedCommentID=20) 
  LEFT JOIN feedback_mri_comments AS CerebellumCutoff
  	ON (CerebellumCutoff.FileID=f.FileID
      AND CerebellumCutoff.PredefinedCommentID=21) 
  LEFT JOIN feedback_mri_comments AS TemporalCutoff
  	ON (TemporalCutoff.FileID=f.FileID
      AND TemporalCutoff.PredefinedCommentID=25)
  LEFT JOIN feedback_mri_comments AS OccipitalCutoff
  	ON (TemporalCutoff.FileID=f.FileID
      AND OccipitalCutoff.PredefinedCommentID=56)
  LEFT JOIN feedback_mri_comments AS MissingTopThird
  	ON (MissingTopThird.FileID=f.FileID
      AND MissingTopThird.PredefinedCommentID=22) 
  /*comment*/
  LEFT JOIN feedback_mri_comments AS CommentCoverage
  	ON (CommentCoverage.FileID=f.FileID
      AND CommentCoverage.CommentTypeID=5
      AND CommentCoverage.PredefinedCommentID IS NULL)

/*NIAK quarantine*/
  /*drop down*/
  LEFT JOIN parameter_file AS ParamNIAKquarantine
    ON (ParamNIAKquarantine.FileID=f.FileID
      AND ParamNIAKquarantine.ParameterTypeID=78252)
  /*comment*/
  LEFT JOIN feedback_mri_comments AS NIAKquarantineComment
    ON (NIAKquarantineComment.FileID=f.FileID
      AND NIAKquarantineComment.CommentTypeID=8
      AND NIAKquarantineComment.PredefinedCommentID IS NULL)

/*NIAK T1 vs stereotaxic registration*/
  /*drop down*/
  LEFT JOIN parameter_file AS ParamNIAKT1vsSTX
    ON (ParamNIAKT1vsSTX.FileID=f.FileID
      AND ParamNIAKT1vsSTX.ParameterTypeID=78253)
  /*comment*/
  LEFT JOIN feedback_mri_comments AS NIAKT1vsSTXComment
    ON (NIAKT1vsSTXComment.FileID=f.FileID
      AND NIAKT1vsSTXComment.CommentTypeID=9
      AND NIAKT1vsSTXComment.PredefinedCommentID IS NULL)

/*NIAK BOLD vs T1 registration*/
  /*drop down*/
  LEFT JOIN parameter_file AS ParamNIAKBOLDvsT1
    ON (ParamNIAKBOLDvsT1.FileID=f.FileID
      AND ParamNIAKBOLDvsT1.ParameterTypeID=78254)
  /*comment*/
  LEFT JOIN feedback_mri_comments AS NIAKBOLDvsT1Comment
    ON (NIAKBOLDvsT1Comment.FileID=f.FileID
      AND NIAKBOLDvsT1Comment.CommentTypeID=10
      AND NIAKBOLDvsT1Comment.PredefinedCommentID IS NULL)

/*NIAK fMRI scrubbing*/
  /*checkbox*/
  LEFT JOIN feedback_mri_comments AS NIAKlessThan70vol
    ON (NIAKlessThan70vol.FileID=f.FileID
      AND NIAKlessThan70vol.PredefinedCommentID=36)
  /*comment*/
  LEFT JOIN feedback_mri_comments AS NIAKscrubbingComment
    ON (NIAKscrubbingComment.FileID=f.FileID
      AND NIAKscrubbingComment.CommentTypeID=11
      AND NIAKscrubbingComment.PredefinedCommentID IS NULL)

/*Overall*/
  /*checkboxes*/
  LEFT JOIN feedback_mri_comments AS Duplicate
  	ON (Duplicate.FileID=f.FileID
      AND Duplicate.PredefinedCommentID=52) 
  /*comment*/
  LEFT JOIN feedback_mri_comments AS CommentOverall 
  	ON (CommentOverall.FileID=f.FileID
      AND CommentOverall.CommentTypeID=6
      AND CommentOverall.PredefinedCommentID IS NULL)

/*QC Status*/
  JOIN files_qcstatus fq ON (fq.FileID = f.FileID) 

/*mri_acquisition_date*/
  JOIN mri_acquisition_dates mad ON (mad.SessionID=f.SessionID)

