ALTER TABLE user_company ADD paypal_email VARCHAR(255) DEFAULT NULL;

--
--
--
INSERT INTO migration_versions (version)
VALUES ('20230812');