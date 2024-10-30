<?php
/**
 * frontend class
 */
namespace DevryptCheckoutFieldEditorManager;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once DCFEM_ADMIN . 'includes/class-settings.php';
class DCFEM_Checkout_Form_Builder {

	public function __construct() {
        $this->define_public_hooks();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
	}

	/**
	 * Loading required scripts
	 * @param
	 * @return void
	 * @since 1.0.0
	 */	
	public function enqueue_admin_scripts( $hook ) {
        if (is_page('checkout')){
            wp_enqueue_style( 'dcfem-frontend-css', DCFEM_URL . 'assets/css/frontend.css', array(), DCFEM_VER );
            wp_enqueue_script( 'dcfem-public-js', DCFEM_URL . 'assets/js/dcfem-public.js', array( 'jquery' ), DCFEM_VER, true );
            wp_enqueue_script( 'dcfem-frontend-js', DCFEM_URL . 'assets/js/frontend.js', array( 'jquery' ), DCFEM_VER, true );
        }
	}

	public function define_public_hooks(){

		add_filter('woocommerce_enable_order_notes_field', array($this, 'enable_order_notes_field'), 1000);

		add_filter('woocommerce_billing_fields', array($this, 'billing_fields'), 1000, 2);
		add_filter('woocommerce_shipping_fields', array($this, 'shipping_fields'), 1000, 2);
		add_filter('woocommerce_checkout_fields', array($this, 'checkout_fields'), 1000, 2);
		add_action('woocommerce_after_checkout_validation', array($this, 'checkout_fields_validation'), 10, 2);
		add_action('woocommerce_checkout_update_order_meta', array($this, 'checkout_update_order_meta'), 10, 2);

		add_filter('woocommerce_email_order_meta_fields', array($this, 'display_custom_fields_in_emails'), 10, 3);
		add_action('woocommerce_order_details_after_order_table', array($this, 'order_details_after_customer_details'), 20, 1);

		add_filter('woocommerce_form_field_checkboxgroup', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_checkbox', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_datetime_local', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_date', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_time', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_month', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_week', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_url', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_multiselect', array($this, 'dcfem_woo_form_field'), 10, 4);
		add_filter('woocommerce_form_field_hidden', array($this, 'dcfem_woo_form_field_hidden'), 10, 4);
		add_filter('woocommerce_form_field_heading', array($this, 'dcfem_woo_form_field_heading'), 10, 4);
		add_filter('woocommerce_form_field_paragraph', array($this, 'dcfem_woo_form_field_paragraph'), 10, 4);

	}

	/**
	 * Hide Additional Fields title if no fields available.
	 */
	public function enable_order_notes_field() {
		$additional_fields = get_option('wc_fields_additional');
		if(is_array($additional_fields)){
            $enabled = 0;
			foreach($additional_fields as $field){
                if($field['enabled']){
                    $enabled++;
				}
			}
			return $enabled > 0 ? true : false;
		}
		return true;
	}
	
	public function billing_fields($fields, $country){
		if(is_wc_endpoint_url('edit-address')){
			$fields = $this->prepare_address_fields(get_option('wc_fields_billing'), $country, $fields, 'billing');
			foreach ($fields as $key => $field) {
				$value = get_user_meta(get_current_user_id(), $key , true);
				if(isset($value) && !empty($value)){
					$field['value'] = $value;
					if(isset($field['type']) && ($field['type'] == 'checkbox')){
						$field['checked'] = $value;
					}
				}else{
					if(isset($field['default'])){
						$field['value'] = $field['default'];
					}else{
						$field['value'] = '';
					}
					if(isset($field['type']) && ($field['type'] == 'checkbox')){
						$field['checked'] = $value;
					}
				}
				$fields[$key] = $field;
				if( (isset($field['custom'])&& $field['custom']) && apply_filters('dcfem_show_custom_field_my_account',false)){
					unset($fields[$key]);
				}
			}
			return $fields;
		}else{
			return $this->prepare_address_fields(get_option('wc_fields_billing'), $country, $fields, 'billing');	
			
		}
	}

	public function shipping_fields($fields, $country){
		if(is_wc_endpoint_url('edit-address')){
			$fields = $this->prepare_address_fields(get_option('wc_fields_shipping'), $country, $fields, 'shipping');
			foreach ($fields as $key => $field) {
				$value = get_user_meta(get_current_user_id(), $key , true);
				if(isset($value) && !empty($value)){
					$field['value'] = $value;
					if(isset($field['type']) && ($field['type'] == 'checkbox')){
						$field['checked'] = $value;
					}
				}else{
					if(isset($field['default'])){
						$field['value'] = $field['default'];
					}else{
						$field['value'] = '';
					}
					if(isset($field['type']) && ($field['type'] == 'checkbox')){
						$field['checked'] = $value;
					}
				}
				$fields[$key] = $field;
				if( (isset($field['custom'])&& $field['custom']) && apply_filters('dcfem_show_custom_field_my_account',false)){
					unset($fields[$key]);
				}
			}
			return $fields;
		}else{
			return $this->prepare_address_fields(get_option('wc_fields_shipping'), $country, $fields, 'shipping');
		}
	}
	
	public function checkout_fields($fields) {
		$additional_fields = get_option('wc_fields_additional');

		if(is_array($additional_fields)){
			if(isset($fields['order']) && is_array($fields['order'])){
				$fields['order'] = $additional_fields + $fields['order'];
			}
			if(isset($additional_fields['order_comments']['enabled']) && !$additional_fields['order_comments']['enabled']){
				unset($fields['order']['order_comments']);
			}
		}
				
		if(isset($fields['order']) && is_array($fields['order'])){
			$fields['order'] = $this->prepare_checkout_fields($fields['order'], false);
		}

		if(isset($fields['order']) && !is_array($fields['order'])){
			unset($fields['order']);
		}
		return $fields;
	}

	public function prepare_address_fields($fieldset, $country, $original_fieldset = false, $sname = 'billing'){
		if(is_array($fieldset) && !empty($fieldset)) {
			$fieldset = $this->prepare_checkout_fields($fieldset, $original_fieldset);
			return $fieldset;
		}else {
			return $original_fieldset;
		}
	}

	public function prepare_checkout_fields($fields, $original_fields) {
		if(is_array($fields) && !empty($fields)) {

			foreach($fields as $name => $field) {
				if(DCFEM_Settings::is_enabled($field)) {
					$new_field = false;
					$allow_override = apply_filters('dcfem_allow_default_field_override_'.$name, false);
					
					if($original_fields && isset($original_fields[$name]) && !$allow_override){
						$new_field = $original_fields[$name];

						$class     = isset($field['class']) && is_array($field['class']) ? $field['class'] : array();
						$required  = isset($field['required']) ? $field['required'] : 0;
						$is_hidden = isset($field['hidden']) && $field['hidden'] ? true : false;

						if($is_hidden){
							$new_field['hidden'] = $field['hidden'];
							$new_field['required'] = false;
						}

                        if($required){
                            $class[] = 'dcfem-required';
                        }else{
                            $class[] = 'dcfem-optional';
                        }
						
						$new_field['label'] = isset($field['label']) ? $field['label'] : '';
						$new_field['default'] = isset($field['default']) ? $field['default'] : '';
						$new_field['placeholder'] = isset($field['placeholder']) ? $field['placeholder'] : '';
						$new_field['class'] = $class;
						$new_field['label_class'] = isset($field['label_class']) && is_array($field['label_class']) ? $field['label_class'] : array();
						$new_field['validate'] = isset($field['validate']) && is_array($field['validate']) ? $field['validate'] : array();
						$new_field['priority'] = isset($field['priority']) ? $field['priority'] : '';
					} else {
						$new_field = $field;
					}

					$type = isset($new_field['type']) ? $new_field['type'] : 'text';

                    if ( ! isset( $new_field['class'] ) || ! is_array( $new_field['class'] ) ) {
                        $new_field['class'] = []; // Initialize as an empty array if not already an array
                    }
                    
                    $new_field['class'][] = 'dcfem-field-wrapper';
                    $new_field['class'][] = 'dcfem-field-' . $type;
					
					if($type === 'select' || $type === 'radio'){
						if(isset($new_field['options'])){
							$options_arr = DCFEM_Settings::prepare_field_options($new_field['options']);
							$options = array();
							foreach($options_arr as $key => $value) {
								$options[$key] = $value;
							}
							$new_field['options'] = $options;
						}
					}

					if(($type === 'select' || $type === 'multiselect') && apply_filters('dcfem_enable_select2_for_select_fields', true)){
						$new_field['input_class'][] = 'dcfem-enhanced-select';
					}
					
					if(isset($new_field['label'])){
						$new_field['label'] = $new_field['label'];
					}

					if(isset($new_field['placeholder'])){
						$new_field['placeholder'] = $new_field['placeholder'];
					}
					
					$fields[$name] = $new_field;
				}else{
					unset($fields[$name]);
				}
			}
			return $fields;
		}else {
			return $original_fields;
		}
	}

	public function checkout_fields_validation($posted, $errors){
		$checkout_fields = WC()->checkout->checkout_fields;
		
		foreach($checkout_fields as $fieldset_key => $fieldset){
			if($this->maybe_skip_fieldset($fieldset_key, $posted)){
				continue;
			}
			
			foreach($fieldset as $key => $field) {
				if(isset($posted[$key]) && !DCFEM_Settings::is_blank($posted[$key])){
					$this->validate_custom_field($key, $field, $posted, $errors);
				}
			}
		}
	}

	public function validate_custom_field($key, $field, $posted, $errors=false, $return=false){
		$err_msgs = array();
		$value = isset($posted[$key]) ? $posted[$key] : '';
		$validators = isset($field['validate']) ? $field['validate'] : '';

		if($value && is_array($validators) && !empty($validators)){
			foreach($validators as $vname){
				$err_msg = '';
				$flabel = isset($field['label']) ? $field['label'] : $key;

				if($vname === 'number'){
					if(!is_numeric($value)){
                        // Translators: 1: value
						$err_msg = sprintf( __( '<strong>%s</strong> is not a valid number.', 'checkout-field-builder-checkout-manager-for-woocommerce' ), $flabel );
					}
				}else if($vname === 'url'){
					if (!filter_var($value, FILTER_VALIDATE_URL)) {
                        // Translators: 1: value
						$err_msg = sprintf( __( '<strong>%s</strong> is not a valid url.', 'checkout-field-builder-checkout-manager-for-woocommerce' ), $flabel );
					}
				}
				if($err_msg){
					if($errors || !$return){
						$this->add_validation_error($err_msg, $errors);
					}
					$err_msgs[] = $err_msg;
				}
			}
		}
		return !empty($err_msgs) ? $err_msgs : false;
	}

	public function add_validation_error($msg, $errors=false){
		if($errors){
			$errors->add('validation', $msg);
		}else {
			WC()->add_error($msg);
		}
	}

	public function checkout_update_order_meta($order_id, $posted){
		$types = array('billing', 'shipping', 'additional');

		$order = wc_get_order( $order_id );

		foreach($types as $type){
			if($this->maybe_skip_fieldset($type, $posted)){
				continue;
			}

			$fields = DCFEM_Settings::get_fields($type);
			
			foreach($fields as $name => $field){
				if(DCFEM_Settings::is_active_custom_field($field) && isset($posted[$name]) && !DCFEM_Settings::is_wc_handle_custom_field($field)){
					$value = null;
					$type = isset($field['type']) ? $field['type'] : 'text';

					if($type == 'textarea'){
						$value =  isset($posted[$name]) ? sanitize_textarea_field($posted[$name]) : '';
					}else if($type == 'email'){
						$value =  isset($posted[$name]) ? sanitize_email($posted[$name]) : '';
					}else if(($type == 'select') || ($type == 'radio')){
						$options = isset($field['options']) ? $field['options'] : array();
						$value =  isset($posted[$name]) ? sanitize_text_field($posted[$name]) : '';
						$value = array_key_exists($value, $options) ? $value : '';
					}else if($type == 'checkboxgroup' || $type == 'multiselect'){
						$options = isset($field['options']) ? $field['options'] : array();
						$submitted_options =  isset($posted[$name]) ? $posted[$name] : array();
						if(! is_array($submitted_options)){
							$submitted_options = explode(", ", $submitted_options);
						}						
						$options_key = array_keys($options);
						if(!empty($submitted_options)){
							foreach($submitted_options as $key => $single_option){
								if(!in_array ($single_option, $options_key)){
									unset ($submitted_options[$key]);
								}
							}
						}
						if(!empty($submitted_options)){
							$value  = implode(",", $submitted_options);
						}
					}else if($type == 'checkbox'){
						$value =  isset($posted[$name]) ? sanitize_text_field($posted[$name]) : '';
						if($value){
							$value = !empty($field['default']) ? $field['default'] : $value;
						}else{
							$value = apply_filters('dcfem_checkbox_field_off_value', $value , $name);
						}
					}else{
						$value =  isset($posted[$name]) ? sanitize_text_field($posted[$name]) : '';						
					}
					if($value){
						// $result = update_post_meta($order_id, $name, $value);
						$order->update_meta_data( $name, $value );
					}
				}
			}
			$order->save();
		}
	}

	private function maybe_skip_fieldset( $fieldset_key, $data ) {
        $ship_to_different_address = isset($data['ship_to_different_address']) ? $data['ship_to_different_address'] : false;
        $ship_to_destination = get_option( 'woocommerce_ship_to_destination' );

        if ( 'shipping' === $fieldset_key && ( ! $ship_to_different_address || ! WC()->cart->needs_shipping_address() ) ) {
            return  $ship_to_destination != 'billing_only' ? true : false;
        }
        return false;
    }
	/**
	 * Display custom fields in emails
	 */
	public function display_custom_fields_in_emails($ofields, $sent_to_admin, $order){
		$custom_fields = array();
		$fields = DCFEM_Settings::get_checkout_fields();

		$order_id = DCFEM_Settings::get_order_id($order);
		$order = wc_get_order( $order_id );
		if(!$order){
			return $ofields;
		}

		// Loop through all custom fields to see if it should be added
		foreach( $fields as $key => $field ) {
			if(isset($field['show_in_email']) && $field['show_in_email'] && !DCFEM_Settings::is_wc_handle_custom_field($field)){
				
				// $value = get_post_meta( $order_id, $key, true );
				$value = $order->get_meta( $key, true );
				
				if($value){
					$label = isset($field['label']) && $field['label'] ? $field['label'] : $key;
					//$label = esc_attr($label);
					$value = DCFEM_Settings::get_option_text($field, $value);

					$f_type = isset($field['type']) ? $field['type'] : 'text';
					// $value = $value;
					if($f_type == 'textarea'){
						$value =  nl2br($value);
					}
					
					$custom_field = array();
					$custom_field['label'] = wp_kses_post($label);
					$custom_field['value'] = $value;
					
					$custom_fields[$key] = $custom_field;
				}
			}
		}

		return array_merge($ofields, $custom_fields);
	}	
	
	/**
	 * Display custom checkout fields on view order pages
	 */
	public function order_details_after_customer_details($order){
        $order_id = $order->get_id(); 
		$fields = DCFEM_Settings::get_checkout_fields($order);
		if(is_array($fields) && !empty($fields)){
			$fields_html = '';
			// Loop through all custom fields to see if it should be added
			foreach($fields as $key => $field){	
				if(DCFEM_Settings::is_active_custom_field($field) && isset($field['show_in_order']) && $field['show_in_order'] && !DCFEM_Settings::is_wc_handle_custom_field($field)){
					$order = wc_get_order( $order_id );
				
					$value = $order->get_meta( $key, true );

					if($value || (($value !== '') && ($value == 0) && apply_filters( 'dcfem_accept_value_zero',false))){
						$label = isset($field['label']) && $field['label'] ? $field['label'] : $key;
                        $label = wp_kses_post($label);
						$value = DCFEM_Settings::get_option_text($field, $value);

						$f_type = isset($field['type']) ? $field['type'] : 'text';
                        $value = esc_html($value);
						if($f_type == 'textarea'){
							$value =  nl2br($value);
						}
						
						if(is_account_page()){
							if(apply_filters( 'dcfem_view_order_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. $label .':</dt><dd>'. $value .'</dd>';
							}
						}else{
							if(apply_filters( 'dcfem_thankyou_customer_details_table_view', true )){
								$fields_html .= '<tr><th>'. $label .':</th><td>'. $value .'</td></tr>';
							}else{
								$fields_html .= '<br/><dt>'. $label .':</dt><dd>'. $value .'</dd>';
							}
						}
					}
				}
			}
			
			if($fields_html){
				do_action( 'dcfem_order_details_before_custom_fields_table', $order ); 
				?>
				<table class="woocommerce-table woocommerce-table--custom-fields shop_table custom-fields">
					<?php
						echo wp_kses_post($fields_html);
					?>
				</table>
				<?php
				do_action( 'dcfem_order_details_after_custom_fields_table', $order ); 
			}
		}	
    
	}

	public function dcfem_woo_form_field($field, $key, $args, $value = null){

		if(is_admin() || ! in_array("dcfem-field-wrapper", $args['class'])){
			return $field;
		}
		$field = '';

		if ( $args['required'] ) {
			$args['class'][] = 'validate-required';
			$required        = '&nbsp;<abbr class="required" title="' . esc_attr__( 'required', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '">*</abbr>';
		} else {
			$required = '&nbsp;<span class="optional">(' . esc_html__( 'optional', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . ')</span>';
		}

		if (is_string($args['label_class'])) {
			$args['label_class'] = array($args['label_class']);
		}

		if(is_null($value)){
			$value = $args['default'];
		}

		// Custom attribute handling.
		$custom_attributes = array();
		$args['custom_attributes'] = array_filter((array) $args['custom_attributes'], 'strlen');

		if ($args['maxlength']) {
			$args['custom_attributes']['maxlength'] = absint($args['maxlength']);
		}

		if (!empty($args['autocomplete'])) {
			$args['custom_attributes']['autocomplete'] = $args['autocomplete'];
		}

		if (true === $args['autofocus']) {
			$args['custom_attributes']['autofocus'] = 'autofocus';
		}

		if ($args['description']) {
			$args['custom_attributes']['aria-describedby'] = $args['id'] . '-description';
		}

		if (!empty($args['custom_attributes']) && is_array($args['custom_attributes'])) {
			foreach ($args['custom_attributes'] as $attribute => $attribute_value) {
				$custom_attributes[] = esc_attr($attribute) . '="' . esc_attr($attribute_value) . '"';
			}
		}

        if (!empty($args['validate'])) {
			foreach ($args['validate'] as $validate) {
				$args['class'][] = 'validate-' . $validate;
			}
		}

		$label_id = $args['id'];
		$sort = $args['priority'] ? $args['priority'] : '';
		$field_container = '<p class="form-row %1$s" id="%2$s" data-priority="' . esc_attr($sort) . '">%3$s</p>';

		switch ($args['type']) {

			case 'multiselect':

				$field = '';

				$value = is_array($value) ? $value : array_map('trim', (array) explode(',', $value));

				if (!empty($args['options'])) {
					$field .= '<select name="' . esc_attr($key) . '[]" id="' . esc_attr($key) . '" class="select ' . esc_attr(implode(' ', $args['input_class'])) . '" multiple="multiple" ' . esc_attr(implode(' ', $custom_attributes)) . ' data-placeholder="' . $args['placeholder'] .'" >';
					foreach ($args['options'] as $option_key => $option_text) {
						$field .= '<option value="' . esc_attr($option_key) . '" ' . selected(in_array($option_key, $value), 1, false) . '>' . $option_text . '</option>';
					}
					$field .= ' </select>';
				}

			break;

			case 'checkbox' :
				$field = '';
				if(isset($args['checked']) && $args['checked']){
					$value = 1;
				}else{
					$value = 0;
				}
				$default_value = !empty($args['default']) ? esc_attr($args['default']) : 1; 

				$field .= '<label class="checkbox ' . implode( ' ', $args['label_class'] ) . '" ' . implode( ' ', $custom_attributes ) . '>
						<input type="' . esc_attr( $args['type'] ) . '" class="input-checkbox ' . esc_attr( implode( ' ', $args['input_class'] ) ) . '" name="' . esc_attr( $key ) . '" id="' . esc_attr( $args['id'] ) . '" value="'.$default_value.'" ' . checked( $value, 1, false ) . ' /> ' . $args['label'] . $required . '</label>';
			break;

			case 'checkboxgroup':
				$field = '';

				$value = is_array($value) ? $value : array_map('trim', (array) explode(',', $value));

				if (!empty($args['options'])) {

					$field .= ' <span class="woocommerce-multicheckbox-wrapper" ' . esc_attr(implode(' ', $custom_attributes)) . '>';

					foreach ($args['options'] as $option_key => $option_text) {
						$field .= '<label><input type="checkbox" name="' . esc_attr($key) . '[]" value="' . esc_attr($option_key) . '"' . checked(in_array($option_key, $value), 1, false) . ' /> ' . $option_text . '</label>';
					}

					$field .= '</span>';
				}
			break;

			case 'datetime_local':
				$field = '';

				$field .= '<input type="datetime-local" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" value="' . esc_attr( $value) . '" />';
			break;

			case 'date':

				$field = '';

				$field .= '<input type="date" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" value="' . esc_attr( $value) . '" />';
			break;
			case 'time':

				$field = '';

				$field .= '<input type="time" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" value="' . esc_attr( $value) . '" />';
			break;
			case 'month':

				$field = '';

				$field .= '<input type="month" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" value="' . esc_attr( $value) . '" />';
			break;
			case 'week':

				$field = '';

				$field .= '<input type="week" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" value="' . esc_attr( $value) . '" />';
			break;

			case 'url':

				$field = '';

				$field .= '<input type="url" name="' . esc_attr( $key ) . '"  id="' . esc_attr( $key ) . '" placeholder ="'.esc_attr($args['placeholder']). '" value="' . esc_attr( $value) . '" />';
			break;

			case 'file':

				$field = '';

			break;
		}

		if (!empty($field)) {
			$field_html = '';

			if ($args['label'] && 'checkbox' !== $args['type']) {
				$field_html .= '<label for="' . esc_attr($label_id) . '" class="' . esc_attr(implode(' ', $args['label_class'])) . '">' . $args['label'] . $required . '</label>';
			}

			$field_html .= '<span class="woocommerce-input-wrapper">' . $field;

			if ($args['description']) {
				$field_html .= '<span class="description" id="' . esc_attr($args['id']) . '-description" aria-hidden="true">' . wp_kses_post($args['description']) . '</span>';
			}

			$field_html .= '</span>';

			$container_class = esc_attr(implode(' ', $args['class']));
			$container_id = esc_attr($args['id']) . '_field';
			$field = sprintf($field_container, $container_class, $container_id, $field_html);
		}
		return $field;
	}

	public function dcfem_woo_form_field_hidden($field, $key, $args, $value){
		if(is_null($value) || (is_string($value) && $value === '')){
            $value = $args['default'];
        }

		$field  = '<input type="hidden" id="'. esc_attr($key) .'" name="'. esc_attr($key) .'" value="'. esc_attr( $value ) .'" class="'.esc_attr(implode(' ', $args['class'])).'" />';
		return $field;
	}

	public function dcfem_woo_form_field_paragraph($field, $key, $args, $value){
		$args['class'][] = 'dcfem-field-wrapper dcfem-field-paragraph';
		
		if(isset($args['label']) && !empty($args['label'])){
			$field  = '<p class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" name="'.esc_attr($key).'" >'. $args['label'] .'</ p >';
		}

		return $field;
	}

	public function dcfem_woo_form_field_heading($field, $key, $args, $value = null){
    	$args['class'][] = 'dcfem-field-wrapper dcfem-field-heading';
		
		$heading_html = '';
		$field  = '';

		if(isset($args['label']) && !empty($args['label'])){
			$title_type  = isset($args['title_type']) && !empty($args['title_type']) ? $args['title_type'] : 'label';

			$heading_html .= '<'. esc_attr($title_type) .' class="'. esc_attr(implode(' ', $args['label_class'])) .'" >'. $args['label'] .'</'. $title_type .'>';
		}

		if(!empty($heading_html)){
			$field .= '<div class="form-row '.esc_attr(implode(' ', $args['class'])).'" id="'.esc_attr($key).'_field" data-name="'.esc_attr($key).'" >'. $heading_html .'</div>';
		}
		return $field;		
	}
}

new DCFEM_Checkout_Form_Builder();