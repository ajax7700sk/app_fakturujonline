ALTER TABLE tax_document CHANGE user_company_id user_company_id INT DEFAULT NULL;
ALTER TABLE tax_document DROP FOREIGN KEY FK_38FD2BC830FCDC3A;
ALTER TABLE `tax_document` ADD CONSTRAINT `FK_38FD2BC830FCDC3A` FOREIGN KEY (`user_company_id`) REFERENCES `user_company`(`id`) ON DELETE SET NULL ON UPDATE RESTRICT;
ALTER TABLE user_company ADD register_info LONGTEXT DEFAULT NULL;



--
--
--
INSERT INTO migration_versions (version)
VALUES ('202307061');