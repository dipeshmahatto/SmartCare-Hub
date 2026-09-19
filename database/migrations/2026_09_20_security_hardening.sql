-- SmartCare Hub Step 8 security migration
-- Run once on an existing database AFTER the appointment lifecycle migration.
-- Existing plaintext passwords remain login-compatible; SmartCare automatically
-- replaces each plaintext value with a password_hash() value after a successful login.

ALTER TABLE `admin` MODIFY `password` VARCHAR(255) NOT NULL;
ALTER TABLE `patient` MODIFY `password` VARCHAR(255) NOT NULL;
ALTER TABLE `doctor` MODIFY `password` VARCHAR(255) NOT NULL;
ALTER TABLE `doctor_approval` MODIFY `password` VARCHAR(255) NOT NULL;
