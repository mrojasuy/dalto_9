<?php
require_api( 'authentication_api.php' );
require_api( 'form_api.php' );

auth_ensure_user_authenticated();

header( 'Content-Type: application/json; charset=utf-8' );

$t_response = array( 'ok' => false );

try {
	form_security_validate( 'plugin_ModoDaltonico_toggle' );

	$f_active = gpc_get_string( 'active', '' );

	if( $f_active !== 'on' && $f_active !== 'off' ) {
		$t_response['error'] = 'Invalid parameter';
		echo json_encode( $t_response );
		form_security_purge( 'plugin_ModoDaltonico_toggle' );
		exit;
	}

	$t_response['ok']     = true;
	$t_response['active'] = ( $f_active === 'on' );

	form_security_purge( 'plugin_ModoDaltonico_toggle' );
} catch( Exception $e ) {
	$t_response['error'] = 'Security validation failed';
}

echo json_encode( $t_response );
exit;
