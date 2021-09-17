<?php
$installer = new Core\App\Installer();

$installer->onInstall(function() {
    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_clients') ."`(
          client_id             VARCHAR(80)   NOT NULL,
          client_name           VARCHAR(255)  NOT NULL,
          client_secret         VARCHAR(80)   NOT NULL,
          redirect_uri          VARCHAR(2000),
          grant_types           VARCHAR(80),
          scope                 VARCHAR(4000),
          user_id               VARCHAR(80),
          is_active             TINYINT(1)    DEFAULT 1,
          time_stamp            INT(10)       NOT NULL,
          PRIMARY KEY (client_id)
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_access_tokens') ."`(
          access_token         VARCHAR(40)    NOT NULL,
          client_id            VARCHAR(80)    NOT NULL,
          user_id              VARCHAR(80),
          expires              TIMESTAMP      NOT NULL,
          scope                VARCHAR(4000),
          PRIMARY KEY (access_token)
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_authorization_codes') ."`(
          authorization_code  VARCHAR(40)    NOT NULL,
          client_id           VARCHAR(80)    NOT NULL,
          user_id             VARCHAR(80),
          redirect_uri        VARCHAR(2000),
          expires             TIMESTAMP      NOT NULL,
          scope               VARCHAR(4000),
          id_token            VARCHAR(1000),
          PRIMARY KEY (authorization_code)
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_refresh_tokens') ."`(
          refresh_token       VARCHAR(40)    NOT NULL,
          client_id           VARCHAR(80)    NOT NULL,
          user_id             VARCHAR(80),
          expires             TIMESTAMP      NOT NULL,
          scope               VARCHAR(4000),
          PRIMARY KEY (refresh_token)
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_scopes') ."`(
          scope               VARCHAR(80)  NOT NULL,
          is_default          BOOLEAN,
          PRIMARY KEY (scope)
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_jwt') ."`(
          client_id           VARCHAR(80)   NOT NULL,
          subject             VARCHAR(80),
          public_key          VARCHAR(2000) NOT NULL
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_jti') ."`(
          issuer              VARCHAR(80)   NOT NULL,
          subject             VARCHAR(80),
          audiance            VARCHAR(80),
          expires             TIMESTAMP     NOT NULL,
          jti                 VARCHAR(2000) NOT NULL
        );");

    db()->query("CREATE TABLE IF NOT EXISTS `". Phpfox::getT('oauth_public_keys') ."`(
          client_id            VARCHAR(80),
          public_key           VARCHAR(2000),
          private_key          VARCHAR(2000),
          encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
        );");
});