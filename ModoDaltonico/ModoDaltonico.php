<?php
/**
 * Plugin Modo Daltonico para MantisBT
 */
class ModoDaltonicoPlugin extends MantisPlugin {

	/**
	 * Registro del plugin.
	 * @return void
	 */
	public function register() {
		$this->name        = plugin_lang_get( 'nombre' );
		$this->description = plugin_lang_get( 'descripcion' );
		$this->version     = '1.0.0';
		$this->requires    = array( 'MantisCore' => '2.0.0' );
		$this->author      = 'Quanam';
		$this->contact     = 'miguel.rojas@quanam.com';
		$this->url         = '';
		$this->page        = 'config';
	}

	/**
	 * Configuracion por defecto del plugin.
	 * @return array
	 */
	public function config() {
		return array(
			'enabled'       => ON,
			'contrast_type' => 'protanopia',
		);
	}

	/**
	 * Eventos declarados por el plugin.
	 * @return array
	 */
	public function events() {
		return array();
	}

	/**
	 * Hooks del plugin.
	 * @return array
	 */
	public function hooks() {
		return array(
			'EVENT_MENU_MAIN'       => 'menu_main',
			'EVENT_LAYOUT_RESOURCES' => 'layout_resources',
		);
	}

	/**
	 * Agrega enlace al menu principal.
	 * @return array
	 */
	public function menu_main() {
		$t_links = array();
		$t_links[] = '<a href="' . plugin_page( 'config' ) . '">'
			. plugin_lang_get( 'configuracion' )
			. '</a>';
		return $t_links;
	}

	/**
	 * Inyecta CSS, JS y configuracion en el layout.
	 * @return void
	 */
	public function layout_resources() {
		$t_enabled       = plugin_config_get( 'enabled' );
		$t_contrast_type = plugin_config_get( 'contrast_type' );

		echo '<link rel="stylesheet" href="' . plugin_file( 'modoDaltonico.css' ) . '" />' . "\n";

		$t_config = array(
			'initialActive' => ( $t_enabled == ON ),
			'contrastType'  => $t_contrast_type,
			'activeLabel'   => plugin_lang_get( 'modo_activo' ),
			'inactiveLabel' => plugin_lang_get( 'modo_inactivo' ),
			'strings'       => array(
				'indicador_activo' => plugin_lang_get( 'indicador_activo' ),
				'protanopia'       => plugin_lang_get( 'protanopia' ),
				'deuteranopia'     => plugin_lang_get( 'deuteranopia' ),
				'tritanopia'       => plugin_lang_get( 'tritanopia' ),
				'activar'          => plugin_lang_get( 'activar' ),
				'desactivar'       => plugin_lang_get( 'desactivar' ),
			),
		);

		echo '<script>window.ModoDaltonicoConfig = '
			. json_encode( $t_config )
			. ';</script>' . "\n";
		echo '<script src="' . plugin_file( 'modoDaltonico.js' ) . '"></script>' . "\n";
	}

	/**
	 * Acciones de instalacion.
	 * @return bool
	 */
	public function install() {
		return true;
	}

	/**
	 * Acciones de desinstalacion.
	 * @return void
	 */
	public function uninstall() {
	}
}
