<?php
/**
 * Plugin Name: tMPCo Hotels
 * Plugin URI: https://github.com/gerrytucker/tmpco-hotels
 * Description: The Hotel Management WordPress plugin for the Mayfair Printing Co.
 * Version: 1.1.19
 * Author: Gerry Tucker
 * Author URI: http://gerrytucker.co.uk/
 * Text-Domain: tmpco-hotels
 * GitHub Plugin URI: https://github.com/gerrytucker/tmpco-hotels
 */

if ( ! class_exists( 'TMPCOHotels' ) ) {

	class TMPCOHotels {

		/**
		 * Initialize the class
		 */
		public function __construct() {

			// Add hooks
			add_action( 'init', array( &$this, 'hotels_custom_post_type'), 0 );

			if ( is_admin() ) {
				add_action( 'admin_enqueue_scripts', array( &$this, 'hotels_admin_enqueue_scripts') );
				add_action( 'add_meta_boxes', array( &$this, 'hotels_add_meta_box' ) );
				add_action( 'save_post', array( &$this, 'hotels_save_meta_box_data' ) );
			}

		}

		/**
		 * Enqueue styles
		 */
		public function hotels_admin_enqueue_scripts() {

			wp_register_style( 'hotels_admin_style', plugins_url( 'css/tmpco-hotels.css', __FILE__ ) );
			wp_enqueue_style( 'hotels_admin_style' );

		}

		/**
		 * Register Custom Post Type
		 */
		function hotels_custom_post_type() {

			$labels = array(
				'name'                => _x( 'Hotels', 'Post Type General Name', 'tmpco-hotels' ),
				'singular_name'       => _x( 'Hotel', 'Post Type Singular Name', 'tmpco-hotels' ),
				'menu_name'           => __( 'Hotels', 'tmpco-hotels' ),
				'parent_item_colon'   => __( 'Parent Hotel:', 'tmpco-hotels' ),
				'all_items'           => __( 'All Hotels', 'tmpco-hotels' ),
				'view_item'           => __( 'View Hotel', 'tmpco-hotels' ),
				'add_new_item'        => __( 'Add New Hotel', 'tmpco-hotels' ),
				'add_new'             => __( 'Add New', 'tmpco-hotels' ),
				'edit_item'           => __( 'Edit Hotel', 'tmpco-hotels' ),
				'update_item'         => __( 'Update Hotel', 'tmpco-hotels' ),
				'search_items'        => __( 'Search Hotels', 'tmpco-hotels' ),
				'not_found'           => __( 'Not found', 'tmpco-hotels' ),
				'not_found_in_trash'  => __( 'Not found in Trash', 'tmpco-hotels' ),
			);
			$rewrite = array(
				'slug'                => 'hotel',
				'with_front'          => true,
				'pages'               => true,
				'feeds'               => false,
			);
			$args = array(
				'label'               => __( 'hotel', 'tmpco-hotels' ),
				'description'         => __( 'Hotels', 'tmpco-hotels' ),
				'labels'              => $labels,
				'supports'            => array( 'title', ),
				'hierarchical'        => false,
				'public'              => true,
				'show_ui'             => true,
				'show_in_menu'        => true,
				'show_in_nav_menus'   => true,
				'show_in_admin_bar'   => true,
				'menu_position'       => 25,
				'can_export'          => true,
				'has_archive'         => false,
				'exclude_from_search' => false,
				'publicly_queryable'  => true,
				'rewrite'             => $rewrite,
				'capability_type'     => 'page',
				'menu_icon' => ''
			);
			register_post_type( 'hotel', $args );

		}


		/**
		 * Add Hotels Meta Box
		 */
		function hotels_add_meta_box()
		{

			add_meta_box(
				'hotel_meta_box_id',
				__('Hotel Map URL', 'tmpco-hotels'),
				array( &$this, 'hotels_add_meta_box_callback' ),
				'hotel'
			);

		}


		/**
		 * Hotels Meta Box Callback
		 */
		function hotels_add_meta_box_callback( $post )
		{
			// Add a nonce field for later
			wp_nonce_field(
				'hotels_meta_box',
				'hotels_meta_box_nonce'
			);

			$hotel_uri = get_post_meta( $post->ID, '_tmpco_hotel_uri', true );
?>

			<div style="margin: 10px;">
				<label for="hotel_uri">Map URL: </label>
				<input type="url" id="hotel_uri" name="hotel_uri" value="<?php echo $hotel_uri; ?>" required>
			</div>

<?php
		}


		/**
		 * Save Hotel Map URI Meta Box Data
		 */
		function hotels_save_meta_box_data( $post_id ) {
			if ( ! isset( $_POST['hotels_meta_box_nonce'] ) )
				return;

			if ( ! wp_verify_nonce( $_POST['hotels_meta_box_nonce'], 'hotels_meta_box' ) )
				return;

			if ( defined ( 'DOING_AUTOSAVE') && DOING_AUTOSAVE )
				return;

			if ( isset( $_POST['post_type'] ) && 'hotel' == $_POST['post_type'] ) {
				if ( ! current_user_can( 'edit_page', $post_id ) )
					return;
			}
			else {
				if ( ! current_user_can( 'edit_post', $post_id ) )
					return;
			}

			if ( ! isset( $_POST['hotel_uri'] ) )
				return;

			$hotel_uri = $_POST['hotel_uri'];

			update_post_meta( $post_id, '_tmpco_hotel_uri', $hotel_uri );

		}

	}

}

$tmpcohotels = new TMPCOHotels();
