ALTER TABLE tax_document ADD evidence_number VARCHAR(255) DEFAULT NULL;

--
--
--
INSERT INTO migration_versions (version)
VALUES ('20231206');