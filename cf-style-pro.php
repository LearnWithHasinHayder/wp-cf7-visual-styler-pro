<?php
/**
 * Plugin Name: CF7 Visual Styler Pro
 * Description: Premium themes and advanced features for Contact Form 7.
 * Version: 1.0.0
 * Author: Hasin Hayder
 * Text Domain: cf7-visual-styler-pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CF7_Visual_Styler_Pro {

	private static $instance = null;

	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {
		// Always register settings (needed for license page form)
		add_action( 'admin_init', array( $this, 'register_settings' ) );
		
		// Check if license is valid before loading pro features
		if ( $this->is_license_valid() ) {
			add_filter( 'cf7_styler_register_themes', array( $this, 'register_pro_themes' ) );
			add_action( 'cf7_styler_enqueue_theme_styles', array( $this, 'enqueue_pro_theme_styles' ) );
		} else {
			// Show license notice
			add_action( 'admin_notices', array( $this, 'license_notice' ) );
		}
		
		// Always add all menu pages (independent of CF7 and license status)
		add_action( 'admin_menu', array( $this, 'add_license_menu' ), 25 );
		add_action( 'admin_menu', array( $this, 'add_pricing_menu' ), 26 );
		add_action( 'admin_menu', array( $this, 'add_admin_menu' ), 27 );
		
		// Deactivation hook
		// register_deactivation_hook( __FILE__, array( $this, 'on_deactivation' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_deactivation_scripts' ) );
		add_action( 'wp_ajax_cf7_styler_pro_deactivation_reason', array( $this, 'handle_deactivation_reason' ) );
	}

	/**
	 * Check if license is valid
	 */
	public function is_license_valid() {
		$license_key = get_option( 'cf7_styler_pro_license_key', '' );
		return ! empty( $license_key );
	}

	/**
	 * License notice
	 */
	public function license_notice() {
		?>
		<div class="notice notice-warning">
			<p><?php _e( '<strong>CF7 Visual Styler Pro:</strong> Please activate your license key to use premium themes.', 'cf7-visual-styler-pro' ); ?> 
			<a href="<?php echo admin_url( 'admin.php?page=cf7-styler-pro-license' ); ?>"><?php _e( 'Activate License', 'cf7-visual-styler-pro' ); ?></a></p>
		</div>
		<?php
	}

	/**
	 * Add license menu
	 */
	public function add_license_menu() {
		add_menu_page(
			__( 'CF7 Styler Pro', 'cf7-visual-styler-pro' ),
			__( 'CF7 Styler Pro', 'cf7-visual-styler-pro' ),
			'manage_options',
			'cf7-styler-pro-license',
			array( $this, 'render_license_page' ),
			'dashicons-star-filled',
			100
		);
	}

	/**
	 * Add pricing menu
	 */
	public function add_pricing_menu() {
		add_submenu_page(
			'cf7-styler-pro-license',
			__( 'Pro Pricing', 'cf7-visual-styler-pro' ),
			__( 'Pro Pricing', 'cf7-visual-styler-pro' ),
			'manage_options',
			'cf7-styler-pro-pricing',
			array( $this, 'render_pricing_page' )
		);
	}

	/**
	 * Add admin menu for pro features
	 */
	public function add_admin_menu() {
		add_submenu_page(
			'cf7-styler-pro-license',
			__( 'Pro Features', 'cf7-visual-styler-pro' ),
			__( 'Pro Features', 'cf7-visual-styler-pro' ),
			'manage_options',
			'cf7-styler-pro',
			array( $this, 'render_pro_page' )
		);
	}

	/**
	 * Register settings
	 */
	public function register_settings() {
		register_setting( 'cf7_styler_pro_settings', 'cf7_styler_pro_license_key' );
	}

	/**
	 * Render license page
	 */
	public function render_license_page() {
		// Handle deactivate license action
		if ( isset( $_POST['cf7_styler_pro_deactivate_license'] ) && check_admin_referer( 'cf7_styler_pro_deactivate_license' ) ) {
			delete_option( 'cf7_styler_pro_license_key' );
			echo '<div class="notice notice-success"><p>' . __( 'License deactivated successfully!', 'cf7-visual-styler-pro' ) . '</p></div>';
		}

		$license_key = get_option( 'cf7_styler_pro_license_key', '' );
		?>
		<div class="wrap">
			<h1><?php _e( 'CF7 Visual Styler Pro - License Activation', 'cf7-visual-styler-pro' ); ?></h1>
			<div class="card" style="max-width: 600px; margin-top: 20px;">
				<h2><?php _e( 'Activate Your License', 'cf7-visual-styler-pro' ); ?></h2>
				<p><?php _e( 'Enter your license key to unlock premium themes and features.', 'cf7-visual-styler-pro' ); ?></p>
				<form method="post" action="options.php">
					<?php
					settings_fields( 'cf7_styler_pro_settings' );
					?>
					<table class="form-table">
						<tr>
							<th scope="row">
								<label for="cf7_styler_pro_license_key"><?php _e( 'License Key', 'cf7-visual-styler-pro' ); ?></label>
							</th>
							<td>
								<input type="text" 
									id="cf7_styler_pro_license_key" 
									name="cf7_styler_pro_license_key" 
									value="<?php echo esc_attr( $license_key ); ?>" 
									class="regular-text"
									placeholder="<?php _e( 'Enter your license key', 'cf7-visual-styler-pro' ); ?>">
							</td>
						</tr>
					</table>
					<?php submit_button( __( 'Activate License', 'cf7-visual-styler-pro' ), 'primary' ); ?>
				</form>
				<?php if ( ! empty( $license_key ) ) : ?>
					<div class="notice notice-success inline" style="margin-top: 20px;">
						<p><?php _e( '<strong>License Active!</strong> Premium themes are now available.', 'cf7-visual-styler-pro' ); ?></p>
					</div>
					<form method="post" style="margin-top: 20px;">
						<?php wp_nonce_field( 'cf7_styler_pro_deactivate_license' ); ?>
						<input type="submit" 
							name="cf7_styler_pro_deactivate_license" 
							class="button" 
							value="<?php _e( 'Deactivate License', 'cf7-visual-styler-pro' ); ?>">
					</form>
				<?php endif; ?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render pro features page
	 */
	public function render_pro_page() {
		$license_key = get_option( 'cf7_styler_pro_license_key', '' );
		?>
		<div class="wrap">
			<h1><?php _e( 'CF7 Visual Styler Pro', 'cf7-visual-styler-pro' ); ?></h1>
			<?php if ( ! empty( $license_key ) ) : ?>
				<div class="card" style="max-width: 800px; margin-top: 20px;">
					<h2><?php _e( 'Premium Themes', 'cf7-visual-styler-pro' ); ?></h2>
					<p><?php _e( 'Your license is active! You now have access to premium themes. Go to <a href="admin.php?page=cf7-styler">Form Styles</a> to select a premium theme.', 'cf7-visual-styler-pro' ); ?></p>
					
					<h3><?php _e( 'Available Premium Themes:', 'cf7-visual-styler-pro' ); ?></h3>
					<ul style="margin-top: 10px;">
						<li><strong><?php _e( 'Neon Glow', 'cf7-visual-styler-pro' ); ?></strong> - <?php _e( 'Futuristic neon effects with vibrant colors', 'cf7-visual-styler-pro' ); ?></li>
						<li><strong><?php _e( 'Gradient Flow', 'cf7-visual-styler-pro' ); ?></strong> - <?php _e( 'Smooth gradient backgrounds and modern aesthetics', 'cf7-visual-styler-pro' ); ?></li>
						<li><strong><?php _e( 'Cyberpunk', 'cf7-visual-styler-pro' ); ?></strong> - <?php _e( 'Dark theme with neon accents and tech-inspired design', 'cf7-visual-styler-pro' ); ?></li>
					</ul>
				</div>

				<div class="card" style="max-width: 800px; margin-top: 20px;">
					<h2><?php _e( 'License Information', 'cf7-visual-styler-pro' ); ?></h2>
					<p><?php _e( 'License Key:', 'cf7-visual-styler-pro' ); ?> <code><?php echo esc_html( $license_key ); ?></code></p>
					<p><a href="<?php echo admin_url( 'admin.php?page=cf7-styler-pro-license' ); ?>"><?php _e( 'Manage License', 'cf7-visual-styler-pro' ); ?></a></p>
				</div>
			<?php else : ?>
				<div class="card" style="max-width: 800px; margin-top: 20px;">
					<h2><?php _e( 'License Required', 'cf7-visual-styler-pro' ); ?></h2>
					<p><?php _e( 'Please activate your license to access premium themes. Go to <a href="admin.php?page=cf7-styler-pro-license">License Activation</a> to enter your license key.', 'cf7-visual-styler-pro' ); ?></p>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}

	/**
	 * Render pricing page
	 */
	public function render_pricing_page() {
		?>
		<div class="wrap">
			<h1><?php _e( 'CF7 Visual Styler Pro - Pricing', 'cf7-visual-styler-pro' ); ?></h1>
			<p style="max-width: 800px; margin-bottom: 30px;"><?php _e( 'Choose the perfect plan for your needs. All plans include access to premium themes and priority support.', 'cf7-visual-styler-pro' ); ?></p>
			
			<div style="display: flex; gap: 20px; flex-wrap: wrap; margin-top: 30px;">
				<!-- Personal Plan -->
				<div class="card" style="flex: 1; min-width: 280px; max-width: 350px; padding: 30px; text-align: center; border: 2px solid #e0e0e0;">
					<h2 style="color: #666;"><?php _e( 'Personal', 'cf7-visual-styler-pro' ); ?></h2>
					<div style="font-size: 48px; font-weight: bold; margin: 20px 0; color: #333;">
						$29<span style="font-size: 18px; font-weight: normal; color: #666;">/year</span>
					</div>
					<ul style="text-align: left; margin: 20px 0; padding-left: 20px; line-height: 2;">
						<li>✓ <?php _e( '1 Website License', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'All Premium Themes', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'Email Support', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( '1 Year Updates', 'cf7-visual-styler-pro' ); ?></li>
					</ul>
					<a href="<?php echo admin_url( 'admin.php?page=cf7-styler-pro-license' ); ?>" class="button button-primary" style="width: 100%; padding: 12px;">
						<?php _e( 'Get Started', 'cf7-visual-styler-pro' ); ?>
					</a>
				</div>

				<!-- Business Plan -->
				<div class="card" style="flex: 1; min-width: 280px; max-width: 350px; padding: 30px; text-align: center; border: 3px solid #0073aa; position: relative;">
					<div style="position: absolute; top: -15px; left: 50%; transform: translateX(-50%); background: #0073aa; color: white; padding: 5px 20px; border-radius: 20px; font-size: 14px; font-weight: bold;">
						<?php _e( 'POPULAR', 'cf7-visual-styler-pro' ); ?>
					</div>
					<h2 style="color: #0073aa;"><?php _e( 'Business', 'cf7-visual-styler-pro' ); ?></h2>
					<div style="font-size: 48px; font-weight: bold; margin: 20px 0; color: #0073aa;">
						$79<span style="font-size: 18px; font-weight: normal; color: #666;">/year</span>
					</div>
					<ul style="text-align: left; margin: 20px 0; padding-left: 20px; line-height: 2;">
						<li>✓ <?php _e( '5 Website Licenses', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'All Premium Themes', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'Priority Support', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( '1 Year Updates', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'Theme Customization', 'cf7-visual-styler-pro' ); ?></li>
					</ul>
					<a href="<?php echo admin_url( 'admin.php?page=cf7-styler-pro-license' ); ?>" class="button button-primary" style="width: 100%; padding: 12px; background: #0073aa;">
						<?php _e( 'Get Started', 'cf7-visual-styler-pro' ); ?>
					</a>
				</div>

				<!-- Agency Plan -->
				<div class="card" style="flex: 1; min-width: 280px; max-width: 350px; padding: 30px; text-align: center; border: 2px solid #e0e0e0;">
					<h2 style="color: #666;"><?php _e( 'Agency', 'cf7-visual-styler-pro' ); ?></h2>
					<div style="font-size: 48px; font-weight: bold; margin: 20px 0; color: #333;">
						$199<span style="font-size: 18px; font-weight: normal; color: #666;">/year</span>
					</div>
					<ul style="text-align: left; margin: 20px 0; padding-left: 20px; line-height: 2;">
						<li>✓ <?php _e( 'Unlimited Websites', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'All Premium Themes', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( '24/7 Priority Support', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'Lifetime Updates', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'White Label Option', 'cf7-visual-styler-pro' ); ?></li>
						<li>✓ <?php _e( 'Custom Theme Development', 'cf7-visual-styler-pro' ); ?></li>
					</ul>
					<a href="<?php echo admin_url( 'admin.php?page=cf7-styler-pro-license' ); ?>" class="button button-primary" style="width: 100%; padding: 12px;">
						<?php _e( 'Get Started', 'cf7-visual-styler-pro' ); ?>
					</a>
				</div>
			</div>

			<div class="card" style="max-width: 800px; margin-top: 40px; padding: 20px;">
				<h3><?php _e( '30-Day Money Back Guarantee', 'cf7-visual-styler-pro' ); ?></h3>
				<p><?php _e( 'Not satisfied? Get a full refund within 30 days, no questions asked.', 'cf7-visual-styler-pro' ); ?></p>
			</div>
		</div>
		<?php
	}

	/**
	 * Register pro themes
	 */
	public function register_pro_themes( $themes ) {
		$pro_themes = array(
			'neon-glow' => array(
				'label' => __( 'Neon Glow (Pro)', 'cf7-visual-styler-pro' ),
				'desc'  => __( 'Futuristic neon effects with vibrant colors.', 'cf7-visual-styler-pro' ),
				'is_pro' => true,
			),
			'gradient-flow' => array(
				'label' => __( 'Gradient Flow (Pro)', 'cf7-visual-styler-pro' ),
				'desc'  => __( 'Smooth gradient backgrounds and modern aesthetics.', 'cf7-visual-styler-pro' ),
				'is_pro' => true,
			),
			'cyberpunk' => array(
				'label' => __( 'Cyberpunk (Pro)', 'cf7-visual-styler-pro' ),
				'desc'  => __( 'Dark theme with neon accents and tech-inspired design.', 'cf7-visual-styler-pro' ),
				'is_pro' => true,
			),
		);

		return array_merge( $themes, $pro_themes );
	}

	/**
	 * Enqueue deactivation scripts
	 */
	public function enqueue_deactivation_scripts() {
		global $pagenow;
		if ( 'plugins.php' === $pagenow ) {
			wp_enqueue_script( 'cf7-styler-pro-deactivation', plugin_dir_url( __FILE__ ) . 'assets/js/deactivation.js', array( 'jquery' ), '1.0.0', true );
			wp_localize_script( 'cf7-styler-pro-deactivation', 'cf7StylerProDeactivation', array(
				'ajax_url' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'cf7_styler_pro_deactivation_nonce' ),
			) );
		}
	}

	/**
	 * Handle deactivation via plugin link
	 */
	public function on_deactivation() {
		// Plugin will be deactivated after this function
	}

	/**
	 * Handle deactivation reason submission
	 */
	public function handle_deactivation_reason() {
		// check_ajax_referer( 'cf7_styler_pro_deactivation_nonce' );
		// check_ajax_referer()

		$reason = isset( $_POST['reason'] ) ? sanitize_text_field( $_POST['reason'] ) : 'unknown';
		$details = isset( $_POST['details'] ) ? sanitize_text_field( $_POST['details'] ) : '';

		// Store the deactivation reason
		$reasons = get_option( 'cf7_styler_pro_deactivation_reasons', array() );
		$reasons[] = array(
			'reason'    => $reason,
			'details'   => $details,
			'timestamp' => current_time( 'mysql' ),
		);
		update_option( 'cf7_styler_pro_deactivation_reasons', $reasons );

		wp_send_json_success( array( 'message' => 'Thank you for your feedback!' ) );
	}

	/**
	 * Enqueue pro theme styles
	 */
	public function enqueue_pro_theme_styles( $theme ) {
		$pro_themes = array( 'neon-glow', 'gradient-flow', 'cyberpunk' );

		if ( in_array( $theme, $pro_themes ) ) {
			$theme_url = plugin_dir_url( __FILE__ ) . 'assets/css/' . $theme . '.css';
			$theme_path = plugin_dir_path( __FILE__ ) . 'assets/css/' . $theme . '.css';

			if ( file_exists( $theme_path ) ) {
				wp_enqueue_style( 'cf7-styler-pro-' . $theme, $theme_url, array(), '1.0.0' );
			}
		}
	}
}

// Initialize the plugin
CF7_Visual_Styler_Pro::get_instance();
