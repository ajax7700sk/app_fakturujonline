ALTER TABLE `order` ADD subscription_type VARCHAR(255) NOT NULL;

--
--
--
INSERT INTO migration_versions (version)
VALUES ('202307010');