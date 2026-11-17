<?php
require_api( 'authentication_api.php' );
require_api( 'form_api.php' );
require_api( 'helper_api.php' );
require_api( 'print_api.php' );

auth_reauthenticate();
access_ensure_global_level( config_get( 'manage_plugin_threshold' ) );

$t_enabled       = plugin_config_get( 'enabled' );
$t_contrast_type = plugin_config_get( 'contrast_type' );

if( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
	form_security_validate( 'plugin_ModoDaltonico_config' );

	$f_enabled       = gpc_get_int( 'enabled', ON );
	$f_contrast_type = gpc_get_string( 'contrast_type', 'protanopia' );

	$t_valid_types = array( 'protanopia', 'deuteranopia', 'tritanopia' );
	if( !in_array( $f_contrast_type, $t_valid_types ) ) {
		$f_contrast_type = 'protanopia';
	}

	plugin_config_set( 'enabled', $f_enabled );
	plugin_config_set( 'contrast_type', $f_contrast_type );

	form_security_purge( 'plugin_ModoDaltonico_config' );

	print_header_redirect( plugin_page( 'config', true ) );
}

layout_page_header( plugin_lang_get( 'nombre' ) );
layout_page_begin();
?>

<div class="col-md-12 col-xs-12">
	<div class="space-10"></div>
	<div class="form-container">
		<form id="md-config-form" method="post" action="<?php echo plugin_page( 'config' ) ?>">
			<?php echo form_security_field( 'plugin_ModoDaltonico_config' ) ?>
			<div class="widget-box widget-color-blue2">
				<div class="widget-header widget-header-small">
					<h4 class="widget-title">
						<?php print_icon( 'fa-universal-access', 'ace-icon' ) ?>
						<?php echo plugin_lang_get( 'configuracion' ) ?>
					</h4>
				</div>
				<div class="widget-body">
					<div class="widget-main no-padding">
						<div class="table-responsive">
							<table class="table table-bordered table-condensed table-striped">
								<tr>
									<td class="category" width="30%">
										<?php echo plugin_lang_get( 'activar' ) ?> / <?php echo plugin_lang_get( 'desactivar' ) ?>
									</td>
									<td>
										<label>
											<input type="radio" name="enabled" value="1"
												<?php echo ( $t_enabled == ON ) ? 'checked="checked"' : '' ?> />
											<?php echo plugin_lang_get( 'activado' ) ?>
										</label>
										<label class="padding-left-8">
											<input type="radio" name="enabled" value="0"
												<?php echo ( $t_enabled == OFF ) ? 'checked="checked"' : '' ?> />
											<?php echo plugin_lang_get( 'desactivado' ) ?>
										</label>
									</td>
								</tr>
								<tr>
									<td class="category">
										<?php echo plugin_lang_get( 'tipo_contraste' ) ?>
									</td>
									<td>
										<select name="contrast_type" class="input-sm">
											<option value="protanopia"
												<?php echo ( $t_contrast_type === 'protanopia' ) ? 'selected="selected"' : '' ?>>
												<?php echo plugin_lang_get( 'protanopia' ) ?>
											</option>
											<option value="deuteranopia"
												<?php echo ( $t_contrast_type === 'deuteranopia' ) ? 'selected="selected"' : '' ?>>
												<?php echo plugin_lang_get( 'deuteranopia' ) ?>
											</option>
											<option value="tritanopia"
												<?php echo ( $t_contrast_type === 'tritanopia' ) ? 'selected="selected"' : '' ?>>
												<?php echo plugin_lang_get( 'tritanopia' ) ?>
											</option>
										</select>
									</td>
								</tr>
							</table>
						</div>
					</div>
					<div class="widget-toolbox padding-8 clearfix">
						<input type="submit" class="btn btn-primary btn-white btn-round"
							value="<?php echo plugin_lang_get( 'guardar' ) ?>" />
					</div>
				</div>
			</div>
		</form>
	</div>
</div>

<?php
layout_page_end();
