ALTER TABLE user DROP created_at, DROP updated_at;
CREATE UNIQUE INDEX UNIQ_8D93D649E7927C74 ON user (email);

--
--
--
INSERT INTO migration_versions (version)
VALUES ('20230618');