-- SmartCare Hub Step 7: appointment lifecycle migration
-- Safe for the original schema and safe to re-run after a successful migration.
-- Original numeric meanings are preserved as closely as possible:
--   0 (Active)    -> confirmed
--   1 (Completed) -> completed

START TRANSACTION;

ALTER TABLE `appointment`
  MODIFY `status` VARCHAR(20) NOT NULL DEFAULT 'pending';

UPDATE `appointment` SET `status` = 'confirmed' WHERE `status` = '0';
UPDATE `appointment` SET `status` = 'completed' WHERE `status` = '1';

ALTER TABLE `appointment`
  MODIFY `status` ENUM('pending','confirmed','completed','cancelled') NOT NULL DEFAULT 'pending';

COMMIT;
