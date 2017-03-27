CREATE TABLE IF NOT EXISTS `exp_freeform_next_forms` (
  `id`             INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`         INT(11)      NOT NULL  DEFAULT '1',
  `name`           VARCHAR(255) NOT NULL,
  `handle`         VARCHAR(255) NOT NULL,
  `spamBlockCount` INT(10)                DEFAULT '0',
  `description`    TEXT,
  `layoutJson`     TEXT         NOT NULL,
  `returnUrl`      VARCHAR(255)           DEFAULT NULL,
  `defaultStatus`  INT(10)      NOT NULL,
  `dateCreated`    DATETIME               DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`    DATETIME               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_forms_handle_unq_idx` (`handle`)
)
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;


CREATE TABLE IF NOT EXISTS `exp_freeform_next_fields` (
  `id`             INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`         INT(11)             NOT NULL  DEFAULT '1',
  `notificationId` INT(11)                       DEFAULT NULL,
  `assetSourceId`  INT(11)                       DEFAULT NULL,
  `type`           VARCHAR(40)                   DEFAULT NULL,
  `handle`         VARCHAR(255)        NOT NULL,
  `label`          VARCHAR(255)        NOT NULL,
  `required`       TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `value`          VARCHAR(255)                  DEFAULT NULL,
  `placeholder`    VARCHAR(255)                  DEFAULT NULL,
  `instructions`   TEXT,
  `values`         TEXT,
  `options`        TEXT,
  `checked`        TINYINT(1)                    DEFAULT NULL,
  `rows`           INT(10)                       DEFAULT NULL,
  `fileKinds`      TEXT,
  `maxFileSizeKB`  INT(10)                       DEFAULT NULL,
  `dateCreated`    DATETIME                      DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`    DATETIME                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_fields_handle_unq_idx` (`handle`),
  KEY `exp_freeform_next_fields_notificationId_fk` (`notificationId`),
  KEY `exp_freeform_next_fields_assetSourceId_fk` (`assetSourceId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;


CREATE TABLE IF NOT EXISTS `exp_freeform_next_integrations` (
  `id`          INT(11)                      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)                      NOT NULL  DEFAULT '1',
  `name`        VARCHAR(255)                 NOT NULL,
  `handle`      VARCHAR(255)                 NOT NULL,
  `type`        ENUM ('mailing_list', 'crm') NOT NULL,
  `class`       VARCHAR(255)                 NOT NULL,
  `accessToken` VARCHAR(255)                           DEFAULT NULL,
  `settings`    TEXT,
  `forceUpdate` TINYINT(1) UNSIGNED          NOT NULL  DEFAULT '0',
  `lastUpdate`  DATETIME                               DEFAULT NULL,
  `dateCreated` DATETIME                               DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated` DATETIME                               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_class_handle_unq_idx` (`class`, `handle`),
  UNIQUE KEY `exp_freeform_next_handle_unq_idx` (`handle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_crm_fields` (
  `id`            INT(11)                                        NOT NULL AUTO_INCREMENT,
  `siteId`        INT(11)                                        NOT NULL DEFAULT '1',
  `integrationId` INT(11)                                        NOT NULL,
  `handle`        VARCHAR(255)                                   NOT NULL,
  `label`         VARCHAR(255)                                   NOT NULL,
  `type`          ENUM ('string', 'numeric', 'boolean', 'array') NOT NULL DEFAULT 'string',
  `required`      INT(1) UNSIGNED                                NOT NULL DEFAULT '0',
  `dateCreated`   DATETIME                                                DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`   DATETIME                                                DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_crm_fields_integrationId_handle_unq_idx` (`integrationId`, `handle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = utf8
  COLLATE = utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_mailing_list_fields` (
  `id`            INT(11)         NOT NULL  AUTO_INCREMENT,
  `siteId`        INT(11)         NOT NULL  DEFAULT '1',
  `mailingListId` INT(11)         NOT NULL,
  `handle`        VARCHAR(255)    NOT NULL,
  `label`         VARCHAR(255)    NOT NULL,
  `type`          VARCHAR(40)     NOT NULL  DEFAULT 'string',
  `required`      INT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `dateCreated`   DATETIME                  DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`   DATETIME                  DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_mailingListId_handle_unq_idx` (`mailingListId`, `handle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_mailing_lists` (
  `id`            INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`        INT(11)      NOT NULL  DEFAULT '1',
  `integrationId` INT(11)      NOT NULL,
  `resourceId`    VARCHAR(255) NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `memberCount`   INT(11)                DEFAULT NULL,
  `dateCreated`   DATETIME               DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`   DATETIME               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_integrationId_resourceId_unq_idx` (`integrationId`, `resourceId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_notifications` (
  `id`                 INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`             INT(11)             NOT NULL  DEFAULT '1',
  `name`               VARCHAR(255)                  DEFAULT NULL,
  `handle`             VARCHAR(255)        NOT NULL,
  `description`        TEXT,
  `fromName`           VARCHAR(255)                  DEFAULT NULL,
  `fromEmail`          VARCHAR(255)                  DEFAULT NULL,
  `replyToEmail`       VARCHAR(255)                  DEFAULT NULL,
  `includeAttachments` TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `subject`            VARCHAR(255)                  DEFAULT NULL,
  `bodyHtml`           TEXT,
  `bodyText`           TEXT,
  `sortOrder`          INT(10)                       DEFAULT NULL,
  `dateCreated`        DATETIME                      DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated`        DATETIME                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_notifications_handle_unq_idx` (`handle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_statuses` (
  `id`          INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)             NOT NULL  DEFAULT '1',
  `name`        VARCHAR(255)        NOT NULL,
  `handle`      VARCHAR(255)        NOT NULL,
  `color`       VARCHAR(30)         NOT NULL  DEFAULT 'grey',
  `isDefault`   TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `sortOrder`   INT(10)                       DEFAULT NULL,
  `dateCreated` DATETIME                      DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated` DATETIME                      DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `exp_freeform_next_statuses_name_unq_idx` (`name`),
  UNIQUE KEY `exp_freeform_next_statuses_handle_unq_idx` (`handle`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_submissions` (
  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)      NOT NULL  DEFAULT '1',
  `statusId`    INT(11)                DEFAULT NULL,
  `formId`      INT(11)      NOT NULL,
  `title`       VARCHAR(255) NULL      DEFAULT NULL,
  `dateCreated` DATETIME               DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated` DATETIME               DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `exp_freeform_next_submissions_statusId_fk` (`statusId`),
  KEY `exp_freeform_next_submissions_formId_fk` (`formId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_unfinalized_files` (
  `id`          INT(11) NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11) NOT NULL  DEFAULT '1',
  `assetId`     INT(11) NOT NULL,
  `dateCreated` DATETIME          DEFAULT CURRENT_TIMESTAMP,
  `dateUpdated` DATETIME          DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `exp_freeform_next_unfinalized_files_assetId_fk` (`assetId`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_settings` (
  `id`                         INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`                     INT(11)             NOT NULL  DEFAULT '1',
  `spamProtectionEnabled`      TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `showTutorial`               TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `fieldDisplayOrder`          VARCHAR(30)         NULL      DEFAULT NULL,
  `formattingTemplatePath`     VARCHAR(255)        NULL      DEFAULT NULL,
  `notificationTemplatePath`   VARCHAR(255)        NULL      DEFAULT NULL,
  `notificationCreationMethod` VARCHAR(30)         NULL      DEFAULT NULL,
  PRIMARY KEY (`id`)
)
  ENGINE = InnoDB
  DEFAULT CHARSET = `utf8`
  COLLATE = `utf8_unicode_ci`;

