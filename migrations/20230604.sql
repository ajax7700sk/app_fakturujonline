CREATE TABLE `migration_versions`
(
    `version`     VARCHAR(255) NOT NULL,
    `executed_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`version`)
) ENGINE = InnoDB;

CREATE TABLE address
(
    id           INT AUTO_INCREMENT NOT NULL,
    name         VARCHAR(255) NOT NULL,
    phone        VARCHAR(255) DEFAULT NULL,
    email        VARCHAR(255) DEFAULT NULL,
    business_id  INT          DEFAULT NULL,
    tax_id       VARCHAR(255) DEFAULT NULL,
    vat_number   VARCHAR(255) DEFAULT NULL,
    city         VARCHAR(255) NOT NULL,
    street       VARCHAR(255) NOT NULL,
    zip_code     VARCHAR(255) NOT NULL,
    country_code VARCHAR(255) NOT NULL,
    created_at   DATETIME     NOT NULL,
    updated_at   DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE bank_account
(
    id             INT AUTO_INCREMENT NOT NULL,
    account_number VARCHAR(255) DEFAULT NULL,
    iban           VARCHAR(255) DEFAULT NULL,
    swift          VARCHAR(255) DEFAULT NULL,
    created_at     DATETIME NOT NULL,
    updated_at     DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE contact
(
    id                       INT AUTO_INCREMENT NOT NULL,
    user_id                  INT      NOT NULL,
    billing_address_id       INT      NOT NULL,
    shipping_address_id      INT      DEFAULT NULL,
    bank_account_id          INT      DEFAULT NULL,
    name VARCHAR(255) DEFAULT NULL,
    billing_same_as_shipping TINYINT(1) NOT NULL,
    created_at               DATETIME NOT NULL,
    updated_at               DATETIME DEFAULT NULL,
    INDEX                    IDX_4C62E638A76ED395 (user_id),
    INDEX                    IDX_4C62E63879D0C0E4 (billing_address_id),
    INDEX                    IDX_4C62E6384D4CFF2B (shipping_address_id),
    INDEX                    IDX_4C62E63812CB990C (bank_account_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE `order`
(
    id                   INT AUTO_INCREMENT NOT NULL,
    user_id              INT            NOT NULL,
    billing_address_id   INT            NOT NULL,
    number               VARCHAR(255)   NOT NULL,
    state                VARCHAR(255)   NOT NULL,
    currency_code        VARCHAR(255) NOT NULL,
    locale_code          VARCHAR(255)   NOT NULL,
    total_price_tax_excl NUMERIC(20, 2) NOT NULL,
    total_price_tax_incl NUMERIC(20, 2) NOT NULL,
    total_tax            NUMERIC(20, 2) NOT NULL,
    created_at DATETIME  NOT NULL,
    updated_at           DATETIME DEFAULT NULL,
    INDEX                IDX_F5299398A76ED395 (user_id),
    INDEX                IDX_F529939879D0C0E4 (billing_address_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE order_item
(
    id                   INT AUTO_INCREMENT NOT NULL,
    _order_id            INT            NOT NULL,
    name                 VARCHAR(255)   NOT NULL,
    type                 VARCHAR(255)   NOT NULL,
    quantity             INT            NOT NULL,
    unit_price_tax_excl  NUMERIC(20, 2) NOT NULL,
    unit_tax_total       NUMERIC(20, 2) NOT NULL,
    total_price_tax_excl NUMERIC(20, 2) NOT NULL,
    total_tax            NUMERIC(20, 2) NOT NULL,
    tax_rate             NUMERIC(20, 2) NOT NULL,
    created_at           DATETIME       NOT NULL,
    updated_at           DATETIME DEFAULT NULL,
    INDEX                IDX_52EA1F09A35F2858 (_order_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE payment
(
    id                    INT AUTO_INCREMENT NOT NULL,
    _order_id             INT          NOT NULL,
    currency_code         VARCHAR(255) NOT NULL,
    state                 VARCHAR(255) NOT NULL,
    payment_method        VARCHAR(255) NOT NULL,
    amount NUMERIC(20, 2) NOT NULL,
    stripe_payment_intent VARCHAR(255) DEFAULT NULL,
    created_at            DATETIME     NOT NULL,
    updated_at            DATETIME     DEFAULT NULL,
    INDEX                 IDX_6D28840DA35F2858 (_order_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE subscription
(
    id         INT AUTO_INCREMENT NOT NULL,
    user_id    INT          NOT NULL,
    _order_id  INT      DEFAULT NULL,
    type       VARCHAR(255) NOT NULL,
    start_at   DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    end_at     DATETIME     NOT NULL COMMENT '(DC2Type:datetime_immutable)',
    created_at DATETIME     NOT NULL,
    updated_at DATETIME DEFAULT NULL,
    INDEX      IDX_A3C664D3A76ED395 (user_id),
    INDEX      IDX_A3C664D3A35F2858 (_order_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE line_item
(
    id                   INT AUTO_INCREMENT NOT NULL,
    tax_document_id      INT            NOT NULL,
    name                 VARCHAR(255)   NOT NULL,
    type                 VARCHAR(255)   NOT NULL,
    unit                 VARCHAR(255)   NOT NULL,
    quantity             INT NOT NULL,
    unit_price_tax_excl  NUMERIC(20, 2) NOT NULL,
    unit_tax_total       NUMERIC(20, 2) NOT NULL,
    total_price_tax_excl NUMERIC(20, 2) NOT NULL,
    total_tax            NUMERIC(20, 2) NOT NULL,
    tax_rate             NUMERIC(20, 2) NOT NULL,
    created_at           DATETIME       NOT NULL,
    updated_at           DATETIME DEFAULT NULL,
    INDEX                IDX_9456D6C74C817138 (tax_document_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE payment_data
(
    id                  INT AUTO_INCREMENT NOT NULL,
    type                VARCHAR(255) NOT NULL,
    paypal_mail         VARCHAR(255) DEFAULT NULL,
    bank_account_number VARCHAR(255) DEFAULT NULL,
    bank_account_iban   VARCHAR(255) DEFAULT NULL,
    bank_account_swift  VARCHAR(255) DEFAULT NULL,
    created_at          DATETIME     NOT NULL,
    updated_at          DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE tax_document
(
    id                            INT AUTO_INCREMENT NOT NULL,
    user_company_id               INT            NOT NULL,
    contact_id                    INT          DEFAULT NULL,
    bank_account_id               INT          DEFAULT NULL,
    supplier_billing_address_id   INT
                                                 NOT NULL,
    subscriber_billing_address_id INT            NOT NULL,
    payment_data_id               INT            NOT NULL,
    type                          VARCHAR(255)   NOT NULL,
    transfered_tax_liability      TINYINT(1) NOT NULL,
    vat_payer                     TINYINT(1) NOT NULL,
    number                        VARCHAR(255)   NOT NULL,
    constant_symbol               VARCHAR(255) DEFAULT NULL,
    specific_symbol               VARCHAR(255) DEFAULT NULL,
    currency_code                 VARCHAR(255)   NOT NULL,
    locale_code                   VARCHAR(255) NOT NULL,
    total_price_tax_excl          NUMERIC(20, 2) NOT NULL,
    total_price_tax_incl          NUMERIC(20, 2) NOT NULL,
    note_above_items              LONGTEXT     DEFAULT NULL,
    note                          LONGTEXT     DEFAULT NULL,
    sent_at                       DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    paid_at                       DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    issued_by                     VARCHAR(255) DEFAULT NULL,
    issued_at                     DATETIME DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    delivery_date_at              DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    due_date_at                   DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    created_at                    DATETIME       NOT NULL,
    updated_at                    DATETIME     DEFAULT NULL,
    INDEX                         IDX_38FD2BC830FCDC3A (user_company_id),
    INDEX                         IDX_38FD2BC8E7A1254A (contact_id),
    INDEX                         IDX_38FD2BC812CB990C (bank_account_id),
    INDEX                         IDX_38FD2BC8321994A4 (supplier_billing_address_id),
    INDEX                         IDX_38FD2BC847A80B0D (subscriber_billing_address_id),
    INDEX                         IDX_38FD2BC82EBCAFD6 (payment_data_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE user
(
    id                   INT AUTO_INCREMENT NOT NULL,
    first_name           VARCHAR(255) NOT NULL,
    last_name            VARCHAR(255) NOT NULL,
    email                VARCHAR(255) NOT NULL,
    password             VARCHAR(255) NOT NULL,
    phone_number VARCHAR(255) NOT NULL,
    reset_token          VARCHAR(255) DEFAULT NULL,
    reset_token_valid_at DATETIME     DEFAULT NULL COMMENT '(DC2Type:datetime_immutable)',
    created_at           DATETIME     NOT NULL,
    updated_at           DATETIME     DEFAULT NULL,
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
CREATE TABLE user_company
(
    id                       INT AUTO_INCREMENT NOT NULL,
    user_id                  INT          NOT NULL,
    billing_address_id       INT          NOT NULL,
    shipping_address_id      INT      DEFAULT NULL,
    bank_account_id          INT      DEFAULT NULL,
    vat_payer                TINYINT(1) NOT NULL,
    name                     VARCHAR(255) NOT NULL,
    billing_same_as_shipping TINYINT(1) NOT NULL,
    created_at               DATETIME     NOT NULL,
    updated_at               DATETIME DEFAULT NULL,
    INDEX                    IDX_17B21745A76ED395 (user_id),
    INDEX                    IDX_17B2174579D0C0E4 (billing_address_id),
    INDEX                    IDX_17B217454D4CFF2B (shipping_address_id),
    INDEX                    IDX_17B2174512CB990C (bank_account_id),
    PRIMARY KEY (id)
) DEFAULT CHARACTER SET utf8 COLLATE `utf8_unicode_ci` ENGINE = InnoDB;
ALTER TABLE contact
    ADD CONSTRAINT FK_4C62E638A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE contact
    ADD CONSTRAINT FK_4C62E63879D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES address (id);
ALTER TABLE contact
    ADD CONSTRAINT FK_4C62E6384D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES address (id);
ALTER TABLE contact
    ADD CONSTRAINT FK_4C62E63812CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id);
ALTER TABLE `order`
    ADD CONSTRAINT FK_F5299398A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE `order`
    ADD CONSTRAINT FK_F529939879D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES address (id);
ALTER TABLE order_item
    ADD CONSTRAINT FK_52EA1F09A35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id);
ALTER TABLE payment
    ADD CONSTRAINT FK_6D28840DA35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id);
ALTER TABLE subscription
    ADD CONSTRAINT FK_A3C664D3A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE subscription
    ADD CONSTRAINT FK_A3C664D3A35F2858 FOREIGN KEY (_order_id) REFERENCES `order` (id);
ALTER TABLE line_item
    ADD CONSTRAINT FK_9456D6C74C817138 FOREIGN KEY (tax_document_id) REFERENCES tax_document (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC830FCDC3A FOREIGN KEY (user_company_id) REFERENCES user_company (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC8E7A1254A FOREIGN KEY (contact_id) REFERENCES contact (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC812CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC8321994A4 FOREIGN KEY (supplier_billing_address_id) REFERENCES address (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC847A80B0D FOREIGN KEY (subscriber_billing_address_id) REFERENCES address (id);
ALTER TABLE tax_document
    ADD CONSTRAINT FK_38FD2BC82EBCAFD6 FOREIGN KEY (payment_data_id) REFERENCES payment_data (id);
ALTER TABLE user_company
    ADD CONSTRAINT FK_17B21745A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);
ALTER TABLE user_company
    ADD CONSTRAINT FK_17B2174579D0C0E4 FOREIGN KEY (billing_address_id) REFERENCES address (id);
ALTER TABLE user_company
    ADD CONSTRAINT FK_17B217454D4CFF2B FOREIGN KEY (shipping_address_id) REFERENCES address (id);
ALTER TABLE user_company
    ADD CONSTRAINT FK_17B2174512CB990C FOREIGN KEY (bank_account_id) REFERENCES bank_account (id);

--
--
--
INSERT INTO migration_versions (version)
VALUES ('20230604');