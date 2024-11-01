<?php
class Wpmetarepeater {
	
	protected $loader;
	protected $plugin_name;
	protected $version;

	public function __construct() {

		$this->plugin_name = 'wpmetarepeater';
		$this->version = '1.0.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		//$this->define_public_hooks();

	}
	
	private function load_dependencies() {	
	
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpmetarepeater-loader.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-wpmetarepeater-i18n.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-wpmetarepeater-admin.php';
		
		$this->loader = new Wpmetarepeater_Loader();
	}
	
	private function set_locale() {
		$plugin_i18n = new Wpmetarepeater_i18n();
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );
	}
	
	private function define_admin_hooks() {
		$plugin_admin = new Wpmetarepeater_Admin( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'wpmr_enqueue_scripts' );
	}
	
	public function run() {
		$this->loader->run();
	}

	public function get_plugin_name() {
		return $this->plugin_name;
	}

	public function get_loader() {
		return $this->loader;
	}

	public function get_version() {
		return $this->version;
	}
}