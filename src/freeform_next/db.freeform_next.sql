CREATE TABLE IF NOT EXISTS `exp_freeform_next_forms` (
  `id`             INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`         INT(11)      NOT NULL  DEFAULT '1',
  `name`           VARCHAR(150) NOT NULL,
  `handle`         VARCHAR(150) NOT NULL,
  `spamBlockCount` INT(10)                DEFAULT '0',
  `description`    TEXT,
  `layoutJson`     TEXT         NOT NULL,
  `returnUrl`      VARCHAR(255)           DEFAULT NULL,
  `legacyId`       INT(11)                DEFAULT NULL,
  `defaultStatus`  INT(10)      NOT NULL,
  `dateCreated`    DATETIME               DEFAULT NULL,
  `dateUpdated`    DATETIME               DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_forms_handle` (`handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `exp_freeform_next_fields` (
  `id`                   INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`               INT(11)             NOT NULL  DEFAULT '1',
  `notificationId`       INT(11)                       DEFAULT NULL,
  `assetSourceId`        INT(11)                       DEFAULT NULL,
  `type`                 VARCHAR(40)                   DEFAULT NULL,
  `handle`               VARCHAR(150)        NOT NULL,
  `label`                VARCHAR(150)        NOT NULL,
  `required`             TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `value`                VARCHAR(255)                  DEFAULT NULL,
  `placeholder`          VARCHAR(255)                  DEFAULT NULL,
  `instructions`         TEXT,
  `values`               TEXT,
  `options`              TEXT,
  `checked`              TINYINT(1)                    DEFAULT NULL,
  `rows`                 INT(10)                       DEFAULT NULL,
  `fileKinds`            TEXT,
  `maxFileSizeKB`        INT(10)                       DEFAULT NULL,
  `additionalProperties` TEXT                          DEFAULT NULL,
  `dateCreated`          DATETIME                      DEFAULT NULL,
  `dateUpdated`          DATETIME                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_fields_handle` (`handle`),
  KEY `ffn_fields_notificationId_fk` (`notificationId`),
  KEY `ffn_fields_assetSourceId_fk` (`assetSourceId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;


CREATE TABLE IF NOT EXISTS `exp_freeform_next_integrations` (
  `id`          INT(11)                      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)                      NOT NULL  DEFAULT '1',
  `name`        VARCHAR(150)                 NOT NULL,
  `handle`      VARCHAR(150)                 NOT NULL,
  `type`        ENUM ('mailing_list', 'crm') NOT NULL,
  `class`       VARCHAR(150)                 NOT NULL,
  `accessToken` VARCHAR(255)                           DEFAULT NULL,
  `settings`    TEXT,
  `forceUpdate` TINYINT(1) UNSIGNED          NOT NULL  DEFAULT '0',
  `lastUpdate`  DATETIME                               DEFAULT NULL,
  `dateCreated` DATETIME                               DEFAULT NULL,
  `dateUpdated` DATETIME                               DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_class_handle` (`class`, `handle`),
  UNIQUE KEY `ffn_handle` (`handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_crm_fields` (
  `id`            INT(11)                                        NOT NULL AUTO_INCREMENT,
  `siteId`        INT(11)                                        NOT NULL DEFAULT '1',
  `integrationId` INT(11)                                        NOT NULL,
  `handle`        VARCHAR(150)                                   NOT NULL,
  `label`         VARCHAR(150)                                   NOT NULL,
  `type`          ENUM ('string', 'numeric', 'boolean', 'array') NOT NULL DEFAULT 'string',
  `required`      INT(1) UNSIGNED                                NOT NULL DEFAULT '0',
  `dateCreated`   DATETIME                                                DEFAULT NULL,
  `dateUpdated`   DATETIME                                                DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_crm_fields_iId_handle` (`integrationId`, `handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_mailing_list_fields` (
  `id`            INT(11)         NOT NULL  AUTO_INCREMENT,
  `siteId`        INT(11)         NOT NULL  DEFAULT '1',
  `mailingListId` INT(11)         NOT NULL,
  `handle`        VARCHAR(150)    NOT NULL,
  `label`         VARCHAR(150)    NOT NULL,
  `type`          VARCHAR(40)     NOT NULL  DEFAULT 'string',
  `required`      INT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `dateCreated`   DATETIME                  DEFAULT NULL,
  `dateUpdated`   DATETIME                  DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_mailingListId_handle` (`mailingListId`, `handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_mailing_lists` (
  `id`            INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`        INT(11)      NOT NULL  DEFAULT '1',
  `integrationId` INT(11)      NOT NULL,
  `resourceId`    VARCHAR(150) NOT NULL,
  `name`          VARCHAR(255) NOT NULL,
  `memberCount`   INT(11)                DEFAULT NULL,
  `dateCreated`   DATETIME               DEFAULT NULL,
  `dateUpdated`   DATETIME               DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_integrationId_resourceId` (`integrationId`, `resourceId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_notifications` (
  `id`                 INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`             INT(11)             NOT NULL  DEFAULT '1',
  `name`               VARCHAR(150)                  DEFAULT NULL,
  `handle`             VARCHAR(150)        NOT NULL,
  `description`        TEXT,
  `fromName`           VARCHAR(255)                  DEFAULT NULL,
  `fromEmail`          VARCHAR(255)                  DEFAULT NULL,
  `replyToEmail`       VARCHAR(255)                  DEFAULT NULL,
  `includeAttachments` TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `subject`            VARCHAR(255)                  DEFAULT NULL,
  `bodyHtml`           TEXT,
  `bodyText`           TEXT,
  `sortOrder`          INT(10)                       DEFAULT NULL,
  `legacyId`           INT(11)                       DEFAULT NULL,
  `dateCreated`        DATETIME                      DEFAULT NULL,
  `dateUpdated`        DATETIME                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_notifications_handle` (`handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_statuses` (
  `id`          INT(11)             NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)             NOT NULL  DEFAULT '1',
  `name`        VARCHAR(150)        NOT NULL,
  `handle`      VARCHAR(150)        NOT NULL,
  `color`       VARCHAR(30)         NOT NULL  DEFAULT 'grey',
  `isDefault`   TINYINT(1) UNSIGNED NOT NULL  DEFAULT '0',
  `sortOrder`   INT(10)                       DEFAULT NULL,
  `dateCreated` DATETIME                      DEFAULT NULL,
  `dateUpdated` DATETIME                      DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ffn_statuses_name` (`name`),
  UNIQUE KEY `ffn_statuses_handle` (`handle`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_submissions` (
  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)      NOT NULL  DEFAULT '1',
  `statusId`    INT(11)                DEFAULT NULL,
  `formId`      INT(11)      NOT NULL,
  `title`       VARCHAR(255) NULL      DEFAULT NULL,
  `dateCreated` DATETIME               DEFAULT NULL,
  `dateUpdated` DATETIME               DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ffn_submissions_statusId_fk` (`statusId`),
  KEY `ffn_submissions_formId_fk` (`formId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_export_profiles` (
  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)      NOT NULL  DEFAULT '1',
  `name`        VARCHAR(255) NULL,
  `formId`      INT(11) NOT NULL,
  `limit`       INT(11) NULL,
  `dateRange`   VARCHAR(255) NULL,
  `fields`      TEXT NULL,
  `filters`     TEXT NULL,
  `statuses`    TEXT NULL,
  `dateCreated` DATETIME DEFAULT NULL,
  `dateUpdated` DATETIME DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ffn_export_formId_fk` (`formId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_export_settings` (
  `id`          INT(11)      NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11)      NOT NULL  DEFAULT '1',
  `userId`      INT(11)      NOT NULL,
  `settings`    TEXT         NOT NULL,
  PRIMARY KEY (`id`),
  KEY `ffn_export_userId_fk` (`userId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_unfinalized_files` (
  `id`          INT(11) NOT NULL  AUTO_INCREMENT,
  `siteId`      INT(11) NOT NULL  DEFAULT '1',
  `assetId`     INT(11) NOT NULL,
  `dateCreated` DATETIME          DEFAULT NULL,
  `dateUpdated` DATETIME          DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `ffn_unfinalized_files_assetId_fk` (`assetId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_settings` (
  `id`                          INT(11)                NOT NULL  AUTO_INCREMENT,
  `siteId`                      INT(11)                NOT NULL  DEFAULT '1',
  `spamProtectionEnabled`       TINYINT(1) UNSIGNED    NOT NULL  DEFAULT '0',
  `spamBlockLikeSuccessfulPost` TINYINT(1) UNSIGNED    NOT NULL  DEFAULT '0',
  `showTutorial`                TINYINT(1) UNSIGNED    NOT NULL  DEFAULT '0',
  `fieldDisplayOrder`           VARCHAR(30)            NULL      DEFAULT NULL,
  `formattingTemplatePath`      VARCHAR(255)           NULL      DEFAULT NULL,
  `notificationTemplatePath`    VARCHAR(255)           NULL      DEFAULT NULL,
  `notificationCreationMethod`  VARCHAR(30)            NULL      DEFAULT NULL,
  `license`                     VARCHAR(100)           NULL      DEFAULT NULL,
  `sessionStorage`              ENUM ('session', 'db') NULL      DEFAULT 'session',
  `defaultTemplates`            TINYINT(1)                       DEFAULT 1,
  `removeNewlines`              TINYINT(1)                       DEFAULT 0,
  `formSubmitDisable`           TINYINT(1)                       DEFAULT 1,
  PRIMARY KEY (`id`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_submission_preferences`
(
  `id`       INT  NOT NULL AUTO_INCREMENT,
  `siteId`   INT  NOT NULL,
  `memberId` INT  NOT NULL,
  `formId`   INT  NOT NULL,
  `settings` TEXT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `site_member_form` (`siteId`, `memberId`, `formId`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;

CREATE TABLE IF NOT EXISTS `exp_freeform_next_session_data`
(
  `sessionId`   VARCHAR(150) NOT NULL,
  `key`         VARCHAR(150) NOT NULL,
  `data`        TEXT         NULL,
  `dateCreated` DATETIME     NOT NULL,
  PRIMARY KEY (`sessionId`, `key`)
) CHARACTER SET utf8 COLLATE utf8_general_ci;
