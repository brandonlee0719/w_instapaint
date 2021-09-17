<?php

namespace Apps\phpFox_RESTful_API\Service;

class Storage extends \OAuth2\Storage\Pdo {

    private $_user = null;
    public function __construct($config = array())
    {
        $this->config = array_merge(array(
            'client_table' => \Phpfox::getT('oauth_clients'),
            'access_token_table' => \Phpfox::getT('oauth_access_tokens'),
            'refresh_token_table' => \Phpfox::getT('oauth_refresh_tokens'),
            'code_table' => \Phpfox::getT('oauth_authorization_codes'),
            'jwt_table'  => \Phpfox::getT('oauth_jwt'),
            'jti_table'  => \Phpfox::getT('oauth_jti'),
            'scope_table'  => \Phpfox::getT('oauth_scopes'),
            'public_key_table'  => \Phpfox::getT('oauth_public_keys'),
        ), $config);
    }

    /* OAuth2\Storage\ClientCredentialsInterface */
    public function checkClientCredentials($client_id, $client_secret = null)
    {
        $result = db()
            ->select('*')
            ->from($this->config['client_table'])
            ->where(['client_id' => $client_id, 'is_active' => 1])
            ->execute('getSlaveRow');

        // make this extensible
        return $result && $result['client_secret'] == $client_secret;
    }

    public function isPublicClient($client_id)
    {
        $result = db()
            ->select('*')
            ->from($this->config['client_table'])
            ->where(['client_id' => $client_id, 'is_active' => 1])
            ->execute('getSlaveRow');

        if (!$result) {
            return false;
        }

        return empty($result['client_secret']);
    }

    /* OAuth2\Storage\ClientInterface */
    public function getClientDetails($client_id, $checkActive = true)
    {
        $where = compact('client_id');
        if ($checkActive) {
            $where['is_active'] = 1;
        }
        return db()
            ->select('*')
            ->from($this->config['client_table'])
            ->where($where)
            ->execute('getSlaveRow');
    }

    public function getAllClients()
    {
        return db()
            ->select('*')
            ->from($this->config['client_table'])
            ->order('time_stamp DESC')
            ->execute('getSlaveRows');
    }

    public function setClientDetails($client_id, $client_secret = null, $redirect_uri = null, $grant_types = null, $scope = null, $user_id = null)
    {
        $client_name = $client_id;
        if (is_array($client_id)) {
            $client_name = $client_id['name'];
            $client_id = $client_id['id'];
        }
        // if it exists, update it.
        if ($client_id == false) $client_id = 0;
        if ($this->getClientDetails($client_id, false)) {
            return db()->update($this->config['client_table'],
                compact('client_id', 'client_name', 'client_secret', 'redirect_uri', 'grant_types', 'scope', 'user_id'),
                compact('client_id'));
        }
        $time_stamp = PHPFOX_TIME;
        db()->insert($this->config['client_table'],
            compact('client_id', 'client_name', 'client_secret', 'redirect_uri', 'grant_types', 'scope', 'user_id', 'time_stamp'));
        return $client_id;
    }

    public function toggleClient($client_id, $is_active) {
        return db()->update($this->config['client_table'],
            compact('is_active'),
            compact('client_id'));
    }

    public function unsetClient($client_id)
    {
        return db()->delete($this->config['client_table'], compact('client_id'));
    }

    /* OAuth2\Storage\AccessTokenInterface */
    public function getAccessToken($access_token)
    {
        $token = db()
            ->select('*')
            ->from($this->config['access_token_table'])
            ->where(compact('access_token'))
            ->execute('getSlaveRow');

        if ($token) {
            // convert date string back to timestamp
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    public function setAccessToken($access_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAccessToken($access_token)) {
            return db()->update($this->config['access_token_table'],
                compact('client_id', 'user_id', 'expires', 'scope'),
                compact('access_token'));
        }
        return db()->insert($this->config['access_token_table'],
            compact('access_token', 'client_id', 'user_id', 'expires', 'scope'));
    }

    public function unsetAccessToken($access_token)
    {
        return db()->delete($this->config['access_token_table'], compact('access_token'));
    }

    /* OAuth2\Storage\AuthorizationCodeInterface */
    public function getAuthorizationCode($code)
    {
        $code = db()->select('*')
            ->from($this->config['code_table'])
            ->where(['authorization_code' => $code])
            ->execute('getSlaveRow');
        if ($code) {
            // convert date string back to timestamp
            $code['expires'] = strtotime($code['expires']);
        }

        return $code;
    }

    public function setAuthorizationCode($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        if (func_num_args() > 6) {
            // we are calling with an id token
            return call_user_func_array(array($this, 'setAuthorizationCodeWithIdToken'), func_get_args());
        }

        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            return db()->update($this->config['code_table'],
                compact('client_id', 'user_id', 'redirect_uri', 'expires', 'scope'),
                ['authorization_code' => $code]);
        }

        return db()->insert($this->config['code_table'],
            array_merge(['authorization_code' => $code],  compact('client_id', 'user_id', 'redirect_uri', 'expires', 'scope')));
    }

    private function setAuthorizationCodeWithIdToken($code, $client_id, $user_id, $redirect_uri, $expires, $scope = null, $id_token = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        // if it exists, update it.
        if ($this->getAuthorizationCode($code)) {
            return db()->update($this->config['code_table'],
                compact('code', 'client_id', 'user_id', 'redirect_uri', 'expires', 'scope', 'id_token'),
                ['authorization_code' => $code]);
        }

        return db()->insert($this->config['code_table'],
            array_merge(['authorization_code' => $code],  compact('client_id', 'user_id', 'redirect_uri', 'expires', 'scope')));
    }

    public function expireAuthorizationCode($code)
    {
        return db()->delete($this->config['code_table'], ['authorization_code' => $code]);
    }

    /* OAuth2\Storage\RefreshTokenInterface */
    public function getRefreshToken($refresh_token)
    {
        $token = db()->select('*')
            ->from($this->config['refresh_token_table'])
            ->where(compact('refresh_token'))
            ->execute('getSlaveRow');

        if ($token) {
            // convert expires to epoch time
            $token['expires'] = strtotime($token['expires']);
        }

        return $token;
    }

    public function setRefreshToken($refresh_token, $client_id, $user_id, $expires, $scope = null)
    {
        // convert expires to datestring
        $expires = date('Y-m-d H:i:s', $expires);

        return db()->insert($this->config['refresh_token_table'], compact('refresh_token', 'client_id', 'user_id', 'expires', 'scope'));
    }

    public function unsetRefreshToken($refresh_token)
    {
        return db()->delete($this->config['refresh_token_table'], compact('refresh_token'));
    }

    protected function checkPassword($user, $password)
    {
        list($result, $user) = \User_Service_Auth::instance()->login($user['email'], $password);
        $this->_user = $user;
        return $result;
    }

    public function getUserDetails($username)
    {
        if ($this->_user) return $this->_user;
        return $this->getUser($username);
    }

    public function getUser($user_email)
    {
        if ($this->_user) return $this->_user;
        return ['email' => $user_email];
    }

    /**
     * @param      $username
     * @param      $password
     * @param null $firstName
     * @param null $lastName
     * @deprecated
     * @return bool|void
     */
    public function setUser($username, $password, $firstName = null, $lastName = null)
    {
       //TODO
    }

    /* ScopeInterface */
    public function scopeExists($scope)
    {
        $scope = explode(' ', $scope);
        $whereIn = implode(',', array_fill(0, count($scope), '?'));
        $result = db()->select('count(scope) as count')
            ->from($this->config['scope_table'])
            ->where(sprintf('scope IN (%s)', $whereIn))
            ->execute('getSlaveRow');

        if ($result) {
            return $result['count'] == count($scope);
        }

        return false;
    }

    public function getDefaultScope($client_id = null)
    {
        $result = db()->select('scope')
            ->from($this->config['scope_table'])
            ->where(['is_default' => true])
            ->execute('getSlaveRows');


        if ($result) {
            $defaultScope = array_map(function ($row) {
                return $row['scope'];
            }, $result);

            return implode(' ', $defaultScope);
        }

        return null;
    }

    /* JWTBearerInterface */
    public function getClientKey($client_id, $subject)
    {
        return db()->select('public_key')
            ->from($this->config['jwt_table'])
            ->where(compact('client_id', 'subject'))
            ->execute('getSlaveField');
    }

    public function getJti($client_id, $subject, $audience, $expires, $jti)
    {
        $result = db()->select('*')
            ->from($this->config['jti_table'])
            ->where(array_merge(['issuer' => $client_id], compact('subject', 'audience', 'expires', 'jti')))
            ->execute('getSlaveRow');

        if ($result) {
            return array(
                'issuer' => $result['issuer'],
                'subject' => $result['subject'],
                'audience' => $result['audience'],
                'expires' => $result['expires'],
                'jti' => $result['jti'],
            );
        }

        return null;
    }

    public function setJti($client_id, $subject, $audience, $expires, $jti)
    {
        return db()->insert($this->config['jti_table'], array_merge(['issuer' => $client_id], compact('subject', 'audience', 'expires', 'jti')));
    }

    /* PublicKeyInterface */
    public function getPublicKey($client_id = null)
    {
        $result = db()->select('public_key')
            ->from($this->config['public_key_table'])
            ->where('client_id = ' . $client_id . ' OR client_id IS NULL')
            ->order('client_id IS NOT NULL DESC')
            ->execute('getSlaveRow');
        if ($result) {
            return $result['public_key'];
        }

        return false;
    }

    public function getPrivateKey($client_id = null)
    {
        $result = db()->select('private_key')
            ->from($this->config['public_key_table'])
            ->where('client_id = ' . $client_id . ' OR client_id IS NULL')
            ->order('client_id IS NOT NULL DESC')
            ->execute('getSlaveRow');
        if ($result) {
            return $result['private_key'];
        }

        return false;
    }

    public function getEncryptionAlgorithm($client_id = null)
    {
        $result = db()->select('encryption_algorithm')
            ->from($this->config['public_key_table'])
            ->where('client_id = ' . $client_id . ' OR client_id IS NULL')
            ->order('client_id IS NOT NULL DESC')
            ->execute('getSlaveRow');

        if ($result) {
            return $result['encryption_algorithm'];
        }

        return 'RS256';
    }

    /**
     * DDL to create OAuth2 database and tables for PDO storage
     *
     * @see https://github.com/dsquier/oauth2-server-php-mysql
     * @param string $dbName
     * @return string
     */
    public function getBuildSql($dbName = 'oauth2_server_php')
    {
        $sql = "
        CREATE TABLE IF NOT EXISTS {$this->config['client_table']} (
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
        );

        CREATE TABLE IF NOT EXISTS {$this->config['access_token_table']} (
          access_token         VARCHAR(40)    NOT NULL,
          client_id            VARCHAR(80)    NOT NULL,
          user_id              VARCHAR(80),
          expires              TIMESTAMP      NOT NULL,
          scope                VARCHAR(4000),
          PRIMARY KEY (access_token)
        );

        CREATE TABLE IF NOT EXISTS {$this->config['code_table']} (
          authorization_code  VARCHAR(40)    NOT NULL,
          client_id           VARCHAR(80)    NOT NULL,
          user_id             VARCHAR(80),
          redirect_uri        VARCHAR(2000),
          expires             TIMESTAMP      NOT NULL,
          scope               VARCHAR(4000),
          id_token            VARCHAR(1000),
          PRIMARY KEY (authorization_code)
        );

        CREATE TABLE IF NOT EXISTS {$this->config['refresh_token_table']} (
          refresh_token       VARCHAR(40)    NOT NULL,
          client_id           VARCHAR(80)    NOT NULL,
          user_id             VARCHAR(80),
          expires             TIMESTAMP      NOT NULL,
          scope               VARCHAR(4000),
          PRIMARY KEY (refresh_token)
        );

        CREATE TABLE IF NOT EXISTS {$this->config['scope_table']} (
          scope               VARCHAR(80)  NOT NULL,
          is_default          BOOLEAN,
          PRIMARY KEY (scope)
        );

        CREATE TABLE IF NOT EXISTS {$this->config['jwt_table']} (
          client_id           VARCHAR(80)   NOT NULL,
          subject             VARCHAR(80),
          public_key          VARCHAR(2000) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS {$this->config['jti_table']} (
          issuer              VARCHAR(80)   NOT NULL,
          subject             VARCHAR(80),
          audiance            VARCHAR(80),
          expires             TIMESTAMP     NOT NULL,
          jti                 VARCHAR(2000) NOT NULL
        );

        CREATE TABLE IF NOT EXISTS {$this->config['public_key_table']} (
          client_id            VARCHAR(80),
          public_key           VARCHAR(2000),
          private_key          VARCHAR(2000),
          encryption_algorithm VARCHAR(100) DEFAULT 'RS256'
        ) 
";

        return $sql;
    }
}
