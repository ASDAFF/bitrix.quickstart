CREATE TABLE b_shs_parser_result
(
    ID        int(11)        NOT NULL auto_increment,
    PARSER_ID        int(11)    NULL,
    SETTINGS LONGTEXT NULL,
    START_LAST_TIME DATETIME NULL,
    END_LAST_TIME DATETIME NULL,
    STATUS int(11) NULL,
    PRIMARY KEY (ID)
);
CREATE TABLE b_shs_parser_result_product
(
    ID        int(11)        NOT NULL auto_increment,
    RESULT_ID        int(11)    NULL,
    PRODUCT_ID        int(11) NULL,
    OLD_PRICE        decimal(18,2) NULL,
    NEW_PRICE        decimal(18,2) NULL,
    PROPERTIES LONGTEXT NULL,
    UPDATE_TIME DATETIME NULL,
    PRIMARY KEY (ID)
);