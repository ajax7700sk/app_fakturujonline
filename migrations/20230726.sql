ALTER TABLE address CHANGE business_id business_id VARCHAR (255) DEFAULT NULL;
ALTER TABLE payment_data CHANGE type type VARCHAR (255) DEFAULT NULL;
ALTER TABLE tax_document
    ADD publish_state VARCHAR(255) NOT NULL, CHANGE number number VARCHAR(255) DEFAULT NULL,
    CHANGE currency_code currency_code VARCHAR(255) DEFAULT NULL, CHANGE sent_at sent_at DATETIME DEFAULT NULL,
    CHANGE paid_at paid_at DATETIME DEFAULT NULL, CHANGE issued_at issued_at DATETIME DEFAULT NULL,
    CHANGE delivery_date_at delivery_date_at DATETIME DEFAULT NULL, CHANGE due_date_at due_date_at DATETIME DEFAULT NULL;

UPDATE tax_document SET publish_state = 'publish';

--
--
--
INSERT INTO migration_versions (version)
VALUES ('20230726');