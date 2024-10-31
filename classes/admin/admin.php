<?php
namespace SP_ByAlpha\Admin;

class Admin {

	protected $mismatch = [];

	public function hooks() {
		add_filter( 'views_plugins', [ $this, 'plugins_view' ] );
		add_filter( 'views_plugins-network', [ $this, 'plugins_view' ] );
		add_filter( 'all_plugins', [ $this, 'plugins_list' ] );
		add_action( 'admin_notices', [ $this, 'admin_notices' ] );
		add_action( 'network_admin_notices', [ $this, 'admin_notices' ] );
	}

	/**
	 * @author Julien Maury
	 */
	public function admin_notices() {
		if ( $this->is_search_by_letter() && ! $this->is_letter_in_plugin_list() && $this->is_page_plugins() ) {
			echo '<div class="error"><p>' . sprintf( __( 'You have no plugin with a name that starts with "%s"', 'spby-alpha' ), strtoupper( $_GET['letter'] ) ) . '</p></div>';
		}
	}

	/**
	 * @return bool
	 * @author Julien Maury
	 */
	public function is_page_plugins() {
		return 'plugins.php' === $GLOBALS['pagenow'];
	}

	/**
	 * Filter results
	 *
	 * @param $all_plugins
	 *
	 * @return mixed
	 * @author Julien Maury
	 */
	public function plugins_list( $all_plugins ) {

		if ( $this->is_search_by_letter() ) {

			foreach ( $all_plugins as $path => $plugin_data ) {

				if ( ! $this->is_letter_in_plugin_list() ) {
					continue;
				}

				if ( 0 !== (int) strcasecmp( substr( $plugin_data['Name'], 0, 1 ), $_GET['letter'] ) ) {
					unset( $all_plugins[ $path ] );
				}
			}
		}

		return $all_plugins;
	}

	/**
	 * Get only letters available
	 * we don't need to sort it alphabetically, it's already done by WP
	 * @author Julien Maury
	 * @return array
	 */
	protected function get_plugin_list() {
		$plugins = get_plugins(); //use built in function to have cache

		if ( empty( $plugins ) ) {
			return [];
		}

		$by_name = array_values( wp_list_pluck( $plugins, 'Name' ) );
		return array_map( [ $this, 'get_first_letter' ], $by_name );
	}

	/**
	 * @return bool
	 * @author Julien Maury
	 */
	public function is_letter_in_plugin_list() {
		return in_array( strtoupper( $_GET['letter'] ), $this->get_plugin_list(), true );
	}

	/**
	 * @return bool
	 * @author Julien Maury
	 */
	public function is_search_by_letter() {
		return ! empty( $_GET['letter'] ) && ctype_alpha( $_GET['letter'] );
	}

	/**
	 * @param $a
	 *
	 * @return string
	 * @author Julien Maury
	 */
	protected function get_first_letter( $a ) {
		return substr( $a, 0, 1 );
	}

	/**
	 * Add our special index list
	 *
	 * @param $views
	 *
	 * @return mixed
	 * @author Julien Maury
	 */
	public function plugins_view( $views ) {

		foreach ( range( 'A', 'Z' ) as $letter ) {
			$lower           = strtolower( $letter );
			$link            = ( in_array( $letter, $this->get_plugin_list(), true ) ) ? sprintf( '<a href="%s" %s>%s</a>',
				esc_url_raw( add_query_arg( 'letter', $lower ) ),
				( $this->is_search_by_letter() && $lower === strtolower( $_GET['letter'] ) )
					? 'class="current"' : '',
				$letter
			) : strtoupper( $letter );
			$views[ $lower ] = $link;
		}

		$views['el'] = 'el';

		return $views;
	}
}