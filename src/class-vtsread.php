<?php
/**
 * VTSRead Core Class
 */
namespace VTS;

class VTSRead {

	protected $plugin_slug;
	protected $version;
	protected $loader;
	protected $cpt;
	protected $admin;
	protected $ajax;
	protected $shortcodes;
	protected $acf;

	/**
	 * VTSRead constructor.
	 */
	public function __construct() {
		$this->plugin_slug = 'vtsread';
		$this->version = '0.0.2';

		$this->plugin_init();

		//Initiate admin functions only after plugins are loaded
		add_action('init', array($this, 'admin_init'));

		
		add_action('admin_enqueue_scripts', array($this, 'load_admin_scripts'));
		add_action('wp_head', array($this, 'load_styles'));
		add_action('wp_enqueue_scripts', array($this, 'load_frontend_scripts'));

		$this->load_dependencies();

	}

	/**
	 * Classes that should be instantiated upon plugin init.
	 */
	private function plugin_init() {

		//Instantiate Custom Post Types
		$this->cpt = new Custom_Posts();

		//Register AJAX methods
		$this->ajax = new VTS_Ajax();

		//Register Shortcodes
		$this->shortcodes = new VTSRead_Shortcodes();

		//Register ACF Fields
		$this->acf = new ACF_Fields();
	}

	/**
	 * Initiating that admin class separately.
	 */
	public function admin_init() {
		$this->admin = new VTSRead_Admin();
	}

	/**
	 * Load plugin dependencies
	 */
	private function load_dependencies() {
		$this->loader = new VTS_Loader();
	}

	/**
	 * Getter for plugin version.
	 * @return string
	 */
	public function get_version() {
		return $this->version;
	}

	/**
	 * Loader for admin-facing scripts.
	 * @param $hook
	 */
	public function load_admin_scripts($hook) {
		wp_register_script('admin', plugins_url('vtsread/public/js/admin.js'), array('jquery'),null, true);
		wp_enqueue_script('admin');
	}

	/**
	 * Loader for front-facing scripts.
	 * @param $hook
	 */
	public function load_frontend_scripts($hook) {
		wp_register_script('isotope',plugins_url('vtsread/public/js/isotope.min.js'),array('jquery'),null, false);
		wp_register_script('fancybox',plugins_url('vtsread/public/js/jquery.fancybox.min.js'),array('jquery'),null, true);
		wp_register_script('front',plugins_url('vtsread/public/js/front.js'),array('jquery'),null, true);
		wp_register_script('images-loaded', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js');
		wp_register_script('lazyload', 'https://cdnjs.cloudflare.com/ajax/libs/jquery.lazyload/1.9.1/jquery.lazyload.min.js',array('jquery'),null, false);

		wp_enqueue_script('fancybox');
		wp_enqueue_script('isotope');
		wp_enqueue_script('images-loaded');
		wp_enqueue_script('front');

	}

	/**
	 * Loader for front-facing styles.
	 */
	public function load_styles(){

		wp_register_style( 'vts-ie', plugins_url( 'vtsread/public/css/ie.css' ) );
		wp_register_style( 'vts-fancybox', plugins_url( 'vtsread/public/css/jquery.fancybox.min.css' ) );
		wp_register_style( 'vts-screen', plugins_url( 'vtsread/public/css/screen.css' ) );
		wp_register_style( 'vts-print', plugins_url( 'vtsread/public/css/print.css' ) );
		wp_register_style( 'vts-style', plugins_url( 'vtsread/public/css/style.css' ) );

		if (is_singular('listing')) {
			wp_enqueue_style( 'vts-ie' );
			wp_enqueue_style( 'vts-fancybox' );
			wp_enqueue_style( 'vts-screen' );
			wp_enqueue_style( 'vts-print' );
			wp_enqueue_style( 'vts-style' );
		}

	}

	/**
	 * Run the plugin.
	 */
	public function run() {
		$this->loader->run();
	}

}