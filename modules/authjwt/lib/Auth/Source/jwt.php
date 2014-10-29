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

		$stateID = SimpleSAML_Auth_State::saveState($state, self::STAGE_INIT);

		if(isset($_GET["jwt"])) {
			// When LMS redirects back with jwt parameter set, we decode JWT
			$attributes = $this->decodeJWT($_GET["jwt"], $this->key);
			$state['Attributes'] = $attributes;

			SimpleSAML_Auth_State::loadState($stateID, self::STAGE_INIT);
			SimpleSAML_Auth_Source::completeAuth($state);
		} else {
			// First time redirect to login url
			$this->doRedirect($this->url);
		}
	}

	/**
	 * Decode JWT and get role attributes for user ID.
	 *
	 * @param string $url Login url where to redirect
	 */
	public function doRedirect($url) {
		// Get current URL where to return back.
		$returnTo = 'https://'.$_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
		$queryURL = $url.'?return_to='.$returnTo;
		// Redirect to login
		header("Location: ".$queryURL);
		exit();
	}

	/**
	 * Decode JWT and get role attributes for user ID.
	 *
	 * @param string $jwt JWT token
	 * @param string $key JWT key for decode
	 */
	public function decodeJWT($jwt, $key) {
        // Decode JWT
        $decoded = JWT::decode($jwt, $key);
		
		$attributes = array();

		// Extra attributes
		$user = $this->user;
		$userID = $decoded->$user; // Get userID from the decoded JWT
		$extraAttributes = getRoleAttributes($this->authId, $userID);
		
		if($extraAttributes != NULL) {
			// Copy paste this to add attributes
			$attributes['educloud.oid'] = array($extraAttributes['educloud.oid']);
			$attributes['educloud.data'] = array($extraAttributes['educloud.data']);
		}

		return $attributes;
	}
}