-- alter mri_upload table add column isAnimal, need to edit loris mri files to not process if animal is yes
ALTER TABLE mri_upload ADD COLUMN IsAnimal enum('Y', 'N') NOT NULL DEFAULT 'N';
