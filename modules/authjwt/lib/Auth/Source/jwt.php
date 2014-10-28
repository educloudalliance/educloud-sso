<?php
require_once('JWT.php');
require_once('extraAttributes.php');

/**
 * Authenticate using JWT.
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

	private $user;
	private $key;
	private $url;


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

		$this->user = $configObject->getString('user');
		$this->key = $configObject->getString('key');
		$this->url = $configObject->getString('url');
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
		
		$returnTo = 'ireallydontknow';
		// Get userdata
		$this->getUserInfo($this->url, $this->key, $returnTo, $state); //TODO: how to get simplesaml return link
	}

	/**
	 * Get user info
	 * 1. Redirect to $url with $returnTo parameter. 
	 * 2. Decode JWT Token with $key, parse pedanet.user value
	 *
	 * @param string $url JWT auth url
	 * @param string $key JWT key for decode
	 * @param string $returnTo Redirect URL
	 * @param array  &$state Authentication state info
	 */
	public function getUserInfo($url, $key, $returnTo, &$state) {
		
		$stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);

		/*
		* 1. Redirect to $url with $returnTo parameter. 
	 	* 2. Decode JWT Token with $key, parse pedanet.user value
		*/

		// TODO: create add extraAttributes function
		$attributes = array();

		// Extra attributes
		$pedanetID = 'vjyrkka'; // This should probably come after user login at Peda.net
		$extraAttributes = getRoleAttributes($this->authId, $pedanetID);
		
		if($extraAttributes != NULL) {
			// Copy paste this to add attributes
			$attributes['educloud.oid'] = array($extraAttributes['educloud.oid']);
			$attributes['educloud.data'] = array($extraAttributes['educloud.data']);
		}

		$state['Attributes'] = $attributes;

		SimpleSAML_Auth_State::loadState($stateID, self::STAGE_INIT);
		SimpleSAML_Auth_Source::completeAuth($state);	
	}
}