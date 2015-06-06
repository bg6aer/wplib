<?php

/**
 * Class WPLib_Theme
 *
 * Provide support for WordPress themes
 *
 */
class WPLib_Theme extends WPLib_Module_Base {

	/**
	 * Sets hooks required by all themes.
	 */
	static function on_load() {

		self::add_class_action( 'wp_enqueue_scripts', 0 );
		self::add_class_action( 'template_include', 999 );

	}

	/**
	 * Creates a JS variable WPLib.ajaxurl
	 *
	 *  Priority 0 ONLY so that this static function does not conflict with the instance method in child classes
	 */
	static function _wp_enqueue_scripts_0() {

		wp_localize_script( 'wplib-script', 'WPLib', array(

			'ajaxurl' => admin_url( 'admin-ajax.php' ),

		));

	}


	/**
	 * Hijack `template_include` so that we can ensure a $theme variable is defined.
	 *
	 * @param string $template;
	 *
	 * @return static
	 */
	static function _template_include_999( $template ) {

		if ( ! $template ) {

			$message = __( '<p>No template file found. You may have deleted the current theme or renamed the theme directory?</p>' .
				'<p>If you are a site admin <a href="%s">click here</a> to verify and possibly correct.</p>', 'wplib' );

			echo sprintf( $message, esc_url( site_url( '/wp-admin/themes.php') ) );

		} else {

			global $theme;

			/*
			 * Make $theme available inside the template.
			 */
			$theme = self::instance();

			include( $template );

		}
		return false;

	}

	/**
	 * Create an instance of get_called_class()
	 *
	 * @return static
	 */
	static function instance() {

		if ( ! WPLib::theme() ) {

			foreach( WPLib::app_classes() as $class_name ) {

				if ( is_subclass_of( $class_name, 'WPLib_Theme_Base' )  ) {

					/*
					 * Will create instance of FIRST class found that subclasses WPLib_Theme_Base.
					 * That means sites should ONLY have ONE subclass of WPLib_Theme_Base.
					 */
					WPLib::set_theme( new $class_name() );
					break;

				}

			}

		}

		return WPLib::theme();

	}

	/**
	 * Theme method for setting a theme isntance for unit test mocking.
	 *
	 * @param $theme
	 *
	 * @return mixed
	 */
	static function set_mock_theme( $theme ) {

		WPLib::set_theme( $theme );

	}
}

WPLib_Theme::on_load();