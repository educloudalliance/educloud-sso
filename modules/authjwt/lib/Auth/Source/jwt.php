<?php
require_once('JWT.php');
require_once('extraAttributes.php');

/**
 * Authenticate using Twitter.
 *
 * @author Ville Jyrkkä, Sampo Software Oy
 * @package educloud-sso
 * @version $Id: jwt.php $
 */
class sspmod_authjwt_Auth_Source_jwt extends SimpleSAML_Auth_Source {
	/**
	 * The string used to identify our states.
	 */
	const STAGE_INIT = 'jwt:init';

	/**
	 * The key of the AuthId field in the state.
	 */
	const AUTHID = 'jwt:AuthId';

	private $key;
	private $token;


	/**
	 * Constructor for this authentication source.
	 *
	 * @param array $info  Information about this authentication source.
	 * @param array $config  Configuration.
	 */
	public function __construct($info, $config) {
		assert('is_array($info)');
		assert('is_array($config)');

		/* Call the parent constructor first, as required by the interface. */
		parent::__construct($info, $config);

		$configObject = SimpleSAML_Configuration::loadFromArray($config, 'authsources[' . var_export($this->authId, TRUE) . ']');

		$this->key = $configObject->getString('key');
		$this->token = $configObject->getArray('token');
	}


	/**
	 * Log-in using JWT
	 *
	 * @param array &$state  Information about the current authentication.
	 */
	public function authenticate(&$state) {
		assert('is_array($state)');

		/* We are going to need the authId in order to retrieve this authentication source later. */
		$state[self::AUTHID] = $this->authId;
		
		$stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);
		
		/* REST API call to LMS and JWT token decode goes here */

		$attributes = array();

		// Extra attributes
		$authmethod = 'pedanet';
		$pedanetID = 'vjyrkka'; // This should probably come after user login at Peda.net
		$extraAttributes = getRoleAttributes($authmethod, $pedanetID);
		
		if($extraAttributes != NULL) {
			// Copy paste this to add attributes
			$attributes['educloud.oid'] = array($extraAttributes['educloud.oid']);
			$attributes['educloud.data'] = array($extraAttributes['educloud.data']);
		}

		$state['Attributes'] = $attributes;

		SimpleSAML_Auth_State::loadState($stateID, self::STAGE_INIT);
		SimpleSAML_Auth_Source::completeAuth($state);
	}
	
	/*
	public function finalStep(&$state) {
		$requestToken = $state['authtwitter:authdata:requestToken'];
		$parameters = array();

		if (!isset($_REQUEST['oauth_token'])) {
			throw new SimpleSAML_Error_BadRequest("Missing oauth_token parameter.");
		}
		if ($requestToken->key !== (string)$_REQUEST['oauth_token']) {
			throw new SimpleSAML_Error_BadRequest("Invalid oauth_token parameter.");
		}

		if (!isset($_REQUEST['oauth_verifier'])) {
			throw new SimpleSAML_Error_BadRequest("Missing oauth_verifier parameter.");
		}
		$parameters['oauth_verifier'] = (string)$_REQUEST['oauth_verifier'];
		
		$consumer = new sspmod_oauth_Consumer($this->key, $this->secret);
		
		SimpleSAML_Logger::debug("oauth: Using this request token [" . 
			$requestToken->key . "] with the secret [" . $requestToken->secret . "]");

		// Replace the request token with an access token
		$accessToken = $consumer->getAccessToken('https://api.twitter.com/oauth/access_token', $requestToken, $parameters);
		SimpleSAML_Logger::debug("Got an access token from the OAuth service provider [" . 
			$accessToken->key . "] with the secret [" . $accessToken->secret . "]");
			
		$userdata = $consumer->getUserInfo('https://api.twitter.com/1.1/account/verify_credentials.json', $accessToken);
		
		if (!isset($userdata['id_str']) || !isset($userdata['screen_name'])) {
			throw new SimpleSAML_Error_AuthSource($this->authId, 'Authentication error: id_str and screen_name not set.');
		}

		$attributes = array();
		foreach($userdata AS $key => $value) {
			if (is_string($value))
				$attributes['twitter.' . $key] = array((string)$value);
		}
		
		$attributes['twitter_at_screen_name'] = array('@' . $userdata['screen_name']);
		$attributes['twitter_screen_n_realm'] = array($userdata['screen_name'] . '@twitter.com');
		$attributes['twitter_targetedID'] = array('http://twitter.com!' . $userdata['id_str']);

		// Extra attributes
		$authmethod = 'twitter';
		$twitterID = $attributes['twitter_at_screen_name'][0]; // This has to be attribute that is collected from roleDB users.
		$extraAttributes = getRoleAttributes($authmethod, $twitterID);
		
		if($extraAttributes != NULL) {
			// Copy paste this to add attributes
			$attributes['educloud.oid'] = array($extraAttributes['educloud.oid']);
			$attributes['educloud.data'] = array($extraAttributes['educloud.data']);
		}

		$state['Attributes'] = $attributes;
	}
	*/

}