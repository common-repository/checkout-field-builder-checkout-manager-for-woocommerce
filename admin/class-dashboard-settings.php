<?php
/**
 * Dashboard Settings Page
 */

namespace DevryptCheckoutFieldEditorManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DCFEM_Dashboard {

    
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'create_admin_menu' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );

        // show edit form ajax 
        add_action( 'wp_ajax_showEditForm', array( $this, 'showEditForm' ) );
		add_action( 'wp_ajax_nopriv_showEditForm', array( $this, 'showEditForm' ) );

        // show Delete form ajax 
        add_action( 'wp_ajax_showDeleteForm', array( $this, 'showDeleteForm' ) );
		add_action( 'wp_ajax_nopriv_showDeleteForm', array( $this, 'showDeleteForm' ) );

        // get delete ajax 
        add_action( 'wp_ajax_getDeleteAjax', array( $this, 'getDeleteAjax' ) );
		add_action( 'wp_ajax_nopriv_getDeleteAjax', array( $this, 'getDeleteAjax' ) );

        // add data form ajax 
		add_action( 'wp_ajax_addDataAjax', array( $this, 'addDataAjax' ) );
		add_action( 'wp_ajax_nopriv_addDataAjax', array( $this, 'addDataAjax' ) );

        // get update ajax 
		add_action( 'wp_ajax_updateDataAjax', array( $this, 'updateDataAjax' ) );
		add_action( 'wp_ajax_nopriv_updateDataAjax', array( $this, 'updateDataAjax' ) );

        // get delete ajax 
		add_action( 'wp_ajax_deleteDataAjax', array( $this, 'deleteDataAjax' ) );
		add_action( 'wp_ajax_nopriv_deleteDataAjax', array( $this, 'deleteDataAjax' ) );

        // get enable ajax 
		add_action( 'wp_ajax_enableDataAjax', array( $this, 'enableDataAjax' ) );
		add_action( 'wp_ajax_nopriv_enableDataAjax', array( $this, 'enableDataAjax' ) );
	}
	

	public function enqueue_admin_scripts( $hook ) {
        if( isset( $hook ) && $hook == 'toplevel_page_dcfem-settings' ) {
			wp_enqueue_style( 'dcfem-bootstrap-css', DCFEM_ADMIN_URL . 'assets/css/bootstrap.css', array(), DCFEM_VER );
			wp_enqueue_style( 'dcfem-jquery-ui-css', DCFEM_ADMIN_URL . 'assets/css/jquery-ui.css', array(), DCFEM_VER );
			wp_enqueue_style( 'dcfem-admin-css', DCFEM_ADMIN_URL . 'assets/css/admin-style.css', array(), DCFEM_VER );
            wp_enqueue_style( 'dcfem-select2-css', DCFEM_ADMIN_URL . 'assets/css/select2.css', array(), DCFEM_VER );
			wp_enqueue_script( 'jquery-ui-tabs');
			wp_enqueue_script( 'dcfem-bootstrap-js', DCFEM_ADMIN_URL . 'assets/js/bootstrap.bundle.js', array( 'jquery' ), DCFEM_VER, true );
            wp_enqueue_script( 'dcfem-select2-js', DCFEM_ADMIN_URL . 'assets/js/select2.js', array( 'jquery' ), DCFEM_VER, true );
			wp_enqueue_script( 'dcfem-admin-js', DCFEM_ADMIN_URL . 'assets/js/admin-script.js', array( 'jquery' ), DCFEM_VER, true );

            
            wp_localize_script(
                'dcfem-admin-js',
                'dcfemAjax',
                array(
                    'ajaxurl' => admin_url( 'admin-ajax.php' ),
                    'nonce'   => wp_create_nonce( 'dcfem-ajax-nonce' ),
                )
            );

            // Inline script to initialize Select2
            wp_add_inline_script('select2-js', "
                jQuery(document).ready(function($) {
                    $('.select2-validation').select2({
                        tags: true,
                        width: '100%',
                        allowClear: true
                    });
                });
            ");
		}
	}

	public function create_admin_menu() {

		$title = __( 'Checkout Field Builder', 'checkout-field-builder-checkout-manager-for-woocommerce' );
		add_menu_page( 
			$title, 
			$title, 
			'manage_options', 
			'dcfem-settings', 
			array( $this, 'admin_settings_page' ), 
			'dashicons-admin-page',
			59 
		);
	}

	public function admin_settings_page() { 
        ?>
        <div class="dcfem-dashboard-wrapper">
            <h1 class="dcfem-dashboard-title"><?php echo esc_html__('Checkout Field Builder Dashboard', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></h1>
            <div id="dcfem-tabs">
                <ul>
                    <li><a href="#tabs-1"><?php echo esc_html__('Billing Form', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></a></li>
                    <li><a href="#tabs-2"><?php echo esc_html__('Shipping Form', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></a></li>
                    <li><a href="#tabs-3"><?php echo esc_html__('Additional Form', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></a></li>
                </ul>
                
                <?php include_once DCFEM_ADMIN . 'templates/billing-table.php'; ?>
                <?php include_once DCFEM_ADMIN . 'templates/shipping-table.php'; ?>
                <?php include_once DCFEM_ADMIN . 'templates/additional-table.php'; ?>
          
            </div>
        </div>
        <?php
	}

    public function showEditForm(){
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

        DCFEM_Settings::show_edit_form_data();
    }

    public function showDeleteForm(){
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

        DCFEM_Settings::show_delete_form_data();
    }

    public function getDeleteAjax(){
        if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

        $deleteField = DCFEM_Settings::delete_form_field();
        exit();
    }

	public function addDataAjax(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

		$postData = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_STRING );
		wp_parse_str( $postData, $requestData );
		$requestData = $this->recursive_sanitize_text_field( $requestData );

		if (is_array($requestData)) {
			DCFEM_Settings::add_popup_form_field($requestData);
		}
	}

	public function updateDataAjax(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

		if (isset($_POST['data'])) {

			$postData = filter_input( INPUT_POST, 'data', FILTER_SANITIZE_STRING );
			wp_parse_str( $postData, $requestData );
			$requestData = $this->recursive_sanitize_text_field( $requestData );
	
			if (is_array($requestData)) {
				DCFEM_Settings::update_popup_form_field($requestData);
			}
		}
	}

	public function recursive_sanitize_text_field( $data ) {
		if ( is_array( $data ) ) {
			foreach ( $data as $key => $value ) {
				$data[ $key ] = $this->recursive_sanitize_text_field( $value );
			}
		} else {
			$data = sanitize_text_field( $data );
		}
		return $data;
	}

	public function deleteDataAjax(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

        if (isset($_POST['data'])) {
            $sanitized_data = sanitize_text_field( wp_unslash( $_POST['data'] ) );
            parse_str($sanitized_data, $requestData);
            if (is_array($requestData)) {
                DCFEM_Settings::delete_popup_form_field($requestData);
            }
        }
	}

	public function enableDataAjax(){
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            wp_die( 'Something went wrong!' );
        }

        DCFEM_Settings::enable_form_field();
	}

}

new DCFEM_Dashboard();