<?php

/**
 * Handle linkback() response from JWT IDP.
 */

if (!array_key_exists('AuthState', $_REQUEST) || empty($_REQUEST['AuthState'])) {
	throw new SimpleSAML_Error_BadRequest('Missing state parameter on JWT linkback endpoint.');
}
$stateID = $_REQUEST['AuthState'];

$state = SimpleSAML_Auth_State::loadState($stateID, sspmod_authjwt_Auth_Source_jwt::STAGE_INIT);

/* Find authentication source. */
if (!array_key_exists(sspmod_authjwt_Auth_Source_jwt::AUTHID, $state)) {
	throw new SimpleSAML_Error_BadRequest('No data in state for ' . sspmod_authjwt_Auth_Source_jwt::AUTHID);
}
$sourceId = $state[sspmod_authjwt_Auth_Source_jwt::AUTHID];

$source = SimpleSAML_Auth_Source::getById($sourceId);
if ($source === NULL) {
	throw new SimpleSAML_Error_BadRequest('Could not find authentication source with id ' . var_export($sourceId, TRUE));
}

try {
	if(!isset($_GET["jwt"])) {
		throw new SimpleSAML_Error_Exception('No JWT payload in request.');
	}
	$jwt = $_GET["jwt"];
	$source->decodeJWT($jwt, $state);
} catch (SimpleSAML_Error_Exception $e) {
	SimpleSAML_Auth_State::throwException($state, $e);
} catch (Exception $e) {
	SimpleSAML_Auth_State::throwException($state, new SimpleSAML_Error_AuthSource($sourceId, 'Error on authjwt linkback endpoint.', $e));
}

SimpleSAML_Auth_Source::completeAuth($state);
