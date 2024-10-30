<?php
/**
 * Dashboard Settings Page
 */
namespace DevryptCheckoutFieldEditorManager;
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class DCFEM_Settings {

	public function __construct() {
		
	}

	public static function is_address_field($name){
		$address_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
		);

		if($name && in_array($name, $address_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field($name){
		$default_fields = array(
			'billing_address_1', 'billing_address_2', 'billing_state', 'billing_postcode', 'billing_city',
			'shipping_address_1', 'shipping_address_2', 'shipping_state', 'shipping_postcode', 'shipping_city',
			'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_default_field_name($field_name){
		$default_fields = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);

		if($name && in_array($name, $default_fields)){
			return true;
		}
		return false;
	}

	public static function is_reserved_field_name( $field_name ){
		$reserved_names = array(
			'billing_first_name', 'billing_last_name', 'billing_company', 'billing_address_1', 'billing_address_2', 
			'billing_city', 'billing_state', 'billing_country', 'billing_postcode', 'billing_phone', 'billing_email',
			'shipping_first_name', 'shipping_last_name', 'shipping_company', 'shipping_address_1', 'shipping_address_2', 
			'shipping_city', 'shipping_state', 'shipping_country', 'shipping_postcode', 'customer_note', 'order_comments'
		);
		
		if($name && in_array($name, $reserved_names)){
			return true;
		}
		return false;
	}

	public static function is_valid_field($field){
		$return = false;
		if(is_array($field)){
			$return = true;
		}
		return $return;
	}

	public static function is_enabled($field){
		$enabled = false;
		if(is_array($field)){
			$enabled = isset($field['enabled']) && $field['enabled'] == false ? false : true;
		}
		return $enabled;
	}

	public static function is_custom_field($field){
		$return = false;
		if(isset($field['custom']) && $field['custom']){
			$return = true;
		}
		return $return;
	}

	public static function is_active_custom_field($field){
		$return = false;
		if(self::is_valid_field($field) && self::is_enabled($field) && self::is_custom_field($field)){
			$return = true;
		}
		return $return;
	}

	public static function is_wc_handle_custom_field($field){
		$name = isset($field['name']) ? $field['name'] : '';
		$special_fields = array();
		
		if($name && in_array($name, $special_fields)){
			return true;
		}
		return false;
	}	

	public static function update_fields($key, $fields){
		$result = update_option('wc_fields_' . $key, $fields, 'no');
		return $result;
	}

	public static function get_fields($key){
		$fields = get_option('wc_fields_'. $key, array());
		$fields = is_array($fields) ? array_filter($fields) : array();
		
		if(empty($fields) || sizeof($fields) == 0){
			if($key === 'billing' || $key === 'shipping'){
				$fields = WC()->countries->get_address_fields(WC()->countries->get_base_country(), $key . '_');

			} else if($key === 'additional'){
				$fields = array(
					'order_comments' => array(
						'type'        => 'textarea',
						'class'       => array('notes'),
						'label'       => __('Order Notes', 'checkout-field-builder-checkout-manager-for-woocommerce'),
						'placeholder' => _x('Notes about your order, e.g. special notes for delivery.', 'placeholder', 'checkout-field-builder-checkout-manager-for-woocommerce')
					)
				);
			}
			$fields = self::prepare_default_fields($fields);
		}
		return $fields;
	}

	private static function prepare_default_fields($fields){
		foreach ($fields as $key => $value) {
			$fields[$key]['custom'] = 0;
			$fields[$key]['enabled'] = 1;
			$fields[$key]['show_in_email'] = 1;
			$fields[$key]['show_in_order'] = 1;
		}
		return $fields;
	}

    public static function get_checkout_fields($order=false){
		$fields = array();
		$needs_shipping = true;

		if($order){
			$needs_shipping = !wc_ship_to_billing_address_only() && $order->needs_shipping_address() ? true : false;
		}
		
		if($needs_shipping){
			$fields = array_merge(self::get_fields('billing'), self::get_fields('shipping'), self::get_fields('additional'));
		}else{
			$fields = array_merge(self::get_fields('billing'), self::get_fields('additional'));
		}

		return $fields;
	}

	public static function prepare_field_options($options){
		if(is_string($options)){
			$options = array_map('trim', explode('|', $options));
		}
		return is_array($options) ? $options : array();
	}

	public static function prepare_options_array($options_json, $type = 'radio'){
		$options_json = rawurldecode($options_json);
		$options_arr = json_decode($options_json, true);
		$options = array();
		
		if($options_arr){
			$i = 0;
			foreach($options_arr as $option){
				$okey = isset($option['key']) ? $option['key'] : '';
				$otext = isset($option['text']) ? $option['text'] : '';
				if($i == 0 && $type == 'select'){
					$okey = $okey ? $okey : '';
				}else{
					$okey = $okey ? $okey : sanitize_key($otext);
				}
				$i++;
				$options[$okey] = $otext;
			}
		}
		return $options;
	}

    public static function get_order_id($order){
		$order_id = false;
		if(self::woo_version_check()){
			$order_id = $order->get_id();
		}else{
			$order_id = $order->id;
		}
		return $order_id;
	}

	public static function woo_version_check( $version = '3.0' ) {
	  	if(function_exists( 'is_woocommerce_active' ) && is_woocommerce_active() ) {
			global $woocommerce;
			if( version_compare( $woocommerce->version, $version, ">=" ) ) {
		  		return true;
			}
	  	}
	  	return false;
	}

    public static function get_option_text($field, $value){
		$type = isset($field['type']) ? $field['type'] : false;

		if($type === 'select' || $type === 'radio'){
			$options = isset($field['options']) ? $field['options'] : array();

			if(isset($options[$value]) && !empty($options[$value])){
				$value = $options[$value];
			}
		}elseif($type === 'checkboxgroup' || $type === 'multiselect'){
			$options = isset($field['options']) ? $field['options'] : array();

			$value_arr = explode(',', $value);
			if(is_array($value_arr)){
				$new_value = array();
				foreach($value_arr as $single_value){
					if(isset($options[$single_value]) && !empty($options[$single_value])){
						$new_value[] = $options[$single_value];
					}else{
						$new_value[] = $single_value;
					}
				}
				$value = implode(', ', $new_value);
			}elseif(isset($options[$value]) && !empty($options[$value])){
				$value = $options[$value];
			}
				
		}

		return $value;
	}

    public static function add_popup_form_field($request){

		$inserted_data = array();
		$existingData = self::get_fields($request['section_type']);

        $required = $request['required'] ? 1 : 0;
        $show_in_email = $request['show_in_email'] ? 1 : 0;
        $show_in_order = $request['show_in_order'] ? 1 : 0;
        $default = $request['default'] ? $request['default'] : '';

        $options = array();
        $radioOptionsValue = $request['radio_options_value'];
        $radioOptionsText = $request['radio_options_text'];
        for ($i = 0; $i < count($radioOptionsValue); $i++) {
            if (isset($radioOptionsValue[$i]) && isset($radioOptionsText[$i])) {
                $key = $radioOptionsValue[$i];
                $value = $radioOptionsText[$i];
                $options[$key] = $value;
            }
        }
                
		$inserted_data[$request['name']] = array(
			'label' => sanitize_text_field($request['label']),
			'placeholder' => sanitize_text_field($request['placeholder']),
			'class' => $request['class'],
			'type' => $request['type'],
			'required' => $required,
            'default' => $default,
			'priority' => 120,
			'custom' => 0,
            'options' => $options,
			'enabled' => 1,
			'show_in_email' => $show_in_email,
			'show_in_order' => $show_in_order,
            'custom' => 1
		);

		$new_data = array_merge($existingData, $inserted_data);

		self::update_fields($request['section_type'], $new_data);

		$reurnData = array(
			'status' => 1,
			'msg'    => 'success',
			'add_field' => $inserted_data,
		);
		wp_send_json( $reurnData );
    }

	public static function show_edit_form_data()
	{
		$json_result = array('error' => 0, 'msg' => '', 'html' => '');
        if ( isset( $_POST['variable'] ) && isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
            $variableData = sanitize_text_field( wp_unslash( $_POST['variable'] ) );
            $sectionTypeArray = explode("_",$variableData);
            $sectionType = $sectionTypeArray[0];
            $existingData = self::get_fields($sectionType);
            $label = sanitize_text_field($existingData[$variableData]['label']);
            $type = $existingData[$variableData]['type'];
            $placeholder = sanitize_text_field($existingData[$variableData]['placeholder']);
            $default = sanitize_text_field($existingData[$variableData]['default']);
            $required = $existingData[$variableData]['required'] ? 1 : 0;
            $show_in_email = $existingData[$variableData]['show_in_email'] ? 1 : 0;
            $show_in_order = $existingData[$variableData]['show_in_order'] ? 1 : 0;
            $classArray = $existingData[$variableData]['class'];
            $class = implode(', ', $classArray);
            $options = $existingData[$variableData]['options'];
            $html = '';
            $statusBlock = "";
            if($type == 'radio' || $type == 'checkboxgroup' || $type == 'select' || $type == 'multiselect'){
                $statusBlock = "display:block";
            } else {
                $statusBlock = "display:none";
            }
		
			$html = '<div class="edit-form-wrapper">';
            $html .= '<div class="form-group">';
            $html .= '<label for="section_type">Section Type</label>';
            $html .= '<select class="form-control" id="section_type" name="section_type">';
            $html .= '<option value="">' . esc_html__( 'Select', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($sectionType, 'billing', false) . ' value="billing">' . esc_html__( 'Billing', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($sectionType, 'shipping', false) . ' value="shipping">' . esc_html__( 'Shipping', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($sectionType, 'additional', false) . ' value="additional">' . esc_html__( 'Additional', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="name">' . esc_html__( 'Name', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<input type="text" class="form-control" id="name" value="' . esc_attr($variableData) . '" name="name" disabled="disabled">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="input_type">' . esc_html__( 'Type', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<select class="form-control" id="input_type" name="type">';
            $html .= '<option ' . selected($type, 'text', false) . ' value="text">' . esc_html__( 'Text', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'email', false) . ' value="email">' . esc_html__( 'Email', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'number', false) . ' value="number">' . esc_html__( 'Number', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'country', false) . ' value="country">' . esc_html__( 'Country', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'state', false) . ' value="state">' . esc_html__( 'State', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'tel', false) . ' value="tel">' . esc_html__( 'Telephone', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'hidden', false) . ' value="hidden">' . esc_html__( 'Hidden', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'password', false) . ' value="password">' . esc_html__( 'Password', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'textarea', false) . ' value="textarea">' . esc_html__( 'Textarea', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'radio', false) . ' value="radio">' . esc_html__( 'Radio', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'checkbox', false) . ' value="checkbox">' . esc_html__( 'Checkbox', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'select', false) . ' value="select">' . esc_html__( 'Select', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'multiselect', false) . ' value="multiselect">' . esc_html__( 'Multiselect', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'checkboxgroup', false) . ' value="checkboxgroup">' . esc_html__( 'Checkbox Group', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'heading', false) . ' value="heading">' . esc_html__( 'Heading', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'paragraph', false) . ' value="paragraph">' . esc_html__( 'Paragraph', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'url', false) . ' value="url">' . esc_html__( 'URL', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'datetime-local', false) . ' value="datetime-local">' . esc_html__( 'Datetime Local', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'date', false) . ' value="date">' . esc_html__( 'Date', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'time', false) . ' value="time">' . esc_html__( 'Time', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'month', false) . ' value="month">' . esc_html__( 'Month', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '<option ' . selected($type, 'week', false) . ' value="week">' . esc_html__( 'Week', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</option>';
            $html .= '</select>';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="label">' . esc_html__( 'Label', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<input type="text" class="form-control" id="label" value="' . esc_attr($label) . '" name="label">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="placeholder">' . esc_html__( 'Placeholder', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<input type="text" class="form-control" id="placeholder" value="' . esc_attr($placeholder) . '" name="placeholder">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="default_value">' . esc_html__( 'Default', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<input type="text" class="form-control" id="default_value" value="' . esc_attr($default) . '" name="default">';
            $html .= '</div>';
            $html .= '<div class="form-group">';
            $html .= '<label for="class">' . esc_html__( 'Additional Classes', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '<input type="text" class="form-control" id="add_class" value="' . esc_attr($class) . '" name="class[]">';
            $html .= '</div>';
            $html .= '<div class="form-check required-field">';
            $html .= '<div class="required-blank-div"></div>';
            $html .= '<input class="form-check-input dcfem-check-input" type="checkbox" value="1" name="required" id="required" ' . checked($required, true, false) . '>';
            $html .= '<label class="form-check-label" for="required">' . esc_html__( 'Required', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '</div>';
            $html .= '<div class="form-check display-in-email-field">';
            $html .= '<div class="required-blank-div"></div>';
            $html .= '<input class="form-check-input dcfem-check-input" type="checkbox" value="1" name="show_in_email" id="show_in_email" ' . checked($show_in_email, true, false) . '>';
            $html .= '<label class="form-check-label" for="show_in_email">' . esc_html__( 'Display on Email', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '</div>';
            $html .= '<div class="form-check display-in-order-field">';
            $html .= '<div class="required-blank-div"></div>';
            $html .= '<input class="form-check-input dcfem-check-input" type="checkbox" value="1" name="show_in_order" id="show_in_order" ' . checked($show_in_order, true, false) . '>';
            $html .= '<label class="form-check-label" for="show_in_order">' . esc_html__( 'Display in Order Details Page', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</label>';
            $html .= '</div>';
            $html .= '<div class="dcfem-option-wrapper" style="' . esc_attr($statusBlock) . '">';
            $html .= '<div>';
            $html .= '<div class="sub-title">' . esc_html__( 'Options', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</div>';
            $html .= '<div></div>';
            $html .= '</div>';
            $html .= '<div>';
            $html .= '<div colspan="3" class="p-0">';
            if (!empty($options)) {
                foreach ($options as $key => $value) {
                    $html .= '<div class="dcfem-option-item">';
                    $html .= '<div class="key"><input type="text" class="dcfem-option-field-value" name="radio_options_value[]" placeholder="' . esc_html__( 'Option Value', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '" value="' . esc_attr($key) . '"></div>';
                    $html .= '<div class="value"><input type="text" class="dcfem-option-field-text" name="radio_options_text[]" placeholder="' . esc_html__( 'Option Text', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '" value="' . esc_attr($value) . '"></div>';
                    $html .= '<div class="action-cell">';
                    $html .= '<a href="#" class="btn btn-tiny btn-primary dcfem-option-add">' . esc_html__( '+', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</a>';
                    $html .= '<a href="#" class="btn btn-tiny btn-danger dcfem-option-remove">' . esc_html__( 'x', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</a>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
            } else {
                $html .= '<div class="dcfem-option-item">';
                $html .= '<div class="key"><input type="text" class="dcfem-option-field-value" name="radio_options_value[]" placeholder="' . esc_html__( 'Option Value', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '" value=""></div>';
                $html .= '<div class="value"><input type="text" class="dcfem-option-field-text" name="radio_options_text[]" placeholder="' . esc_html__( 'Option Text', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '" value=""></div>';
                $html .= '<div class="action-cell">';
                $html .= '<a href="#" class="btn btn-tiny btn-primary dcfem-option-add">' . esc_html__( '+', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</a>';
                $html .= '<a href="#" class="btn btn-tiny btn-danger dcfem-option-remove">' . esc_html__( 'x', 'checkout-field-builder-checkout-manager-for-woocommerce' ) . '</a>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';
            $html .= '</div>';

            $json_result['error'] = 0;
            $json_result['msg'] = 'Successfully fetched data';
            $json_result['html'] = $html;
			$json_result['fieldType'] = $type;
		} else {
			$json_result['error'] = 1;
			$json_result['msg'] = 'Error fetch data';
			$json_result['html'] = '';
			$json_result['fieldType'] = '';
		}

		wp_send_json( $json_result );
	}

    public static function update_popup_form_field($request) {

		$section_type_array = explode("_",$request['name']);
		$section_type = $section_type_array[0];
		$existingData = self::get_fields($section_type);
        $options = array();

        $radioOptionsValue = $request['radio_options_value'] ? $request['radio_options_value'] : "";
        $radioOptionsText = $request['radio_options_text'] ? $request['radio_options_text'] : "";
		if(!empty($radioOptionsValue) && !empty($radioOptionsText)) {
			for ($i = 0; $i < count($radioOptionsValue); $i++) {
				if (isset($radioOptionsValue[$i]) && isset($radioOptionsText[$i])) {
					$key = $radioOptionsValue[$i];
					$value = $radioOptionsText[$i];
					$options[$key] = $value;
				}
			}
		}
        $required = $request['required'];
        $show_in_email = $request['show_in_email'];
        $show_in_order = $request['show_in_order'];
        $default = $request['default'];
		
		$billing_get_data[$request['name']] = array(
			'label' => sanitize_text_field($request['label']),
			'placeholder' => $request['placeholder'],
			'class' => $request['class'],
			'type' => $request['type'],
			'required' => $required,
            'default' => $default,
			'priority' => 120,
			'custom' => 0,
            'options' => $options,
			'enabled' => 1,
			'show_in_email' => $show_in_email,
			'show_in_order' => $show_in_order,
            'custom' => 1
		);
		foreach ($billing_get_data as $key => $value) {
			if (array_key_exists($key, $existingData)) {
				$existingData[$key] = $value;
			}
		}
		
		self::update_fields($section_type, $existingData);

		$reurnData = array(
			'status' => 1,
			'msg'    => 'success',
			'update_field' => $billing_get_data,
		);
		wp_send_json( $reurnData );

	}

    public static function show_delete_form_data(){
        $json_result = array('error' => 0, 'msg' => '', 'html' => '');
        if(isset($_POST['variable']) && isset( $_POST['nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' )){
            $variableData = sanitize_text_field( wp_unslash( $_POST['variable'] ) );
            $sectionTypeArray = explode("_",$variableData);
            $sectionType = $sectionTypeArray[0];
            $existingData = self::get_fields($sectionType);
            $label = sanitize_text_field($existingData[$variableData]['label']);
            $html = '';
        
            $html .= '<div class="edit-form-wrapper">';
            $html .= '<input type="hidden" class="form-control" id="name" value="'.$variableData.'" name="name">';
            $html .= 'Do you want to delete "'.$variableData.'" field?';
            $html .= '</div>';
            
            $json_result['error'] = 0;
            $json_result['msg'] = 'Successfully fetch data';
            $json_result['html'] = $html;

        } else {
            $json_result['error'] = 1;
            $json_result['msg'] = 'Error fetch data';
            $json_result['html'] = '';
        }

        wp_send_json( $json_result );
    }

    public static function delete_popup_form_field($request){

        $section_type_array = explode("_",$request['name']);
        $section_type = $section_type_array[0];
        $existingData = self::get_fields($section_type);
        
        $key = $request['name'];
        if (array_key_exists($key, $existingData)) {
            unset($existingData[$key]);
        }
        self::update_fields($section_type, $existingData);

        $reurnData = array(
			'status' => 1,
			'msg'    => 'success',
			'update_field' => $existingData,
		);
		wp_send_json( $reurnData );
    }

    public static function reset_to_default(){
        delete_option('wc_fields_billing');
        delete_option('wc_fields_shipping');
        delete_option('wc_fields_additional');
    }

	public static function enable_form_field() {
		if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'dcfem-ajax-nonce' ) ) {
			wp_send_json_error( 'Invalid nonce' );
		} else {
            $actionType = isset( $_POST['action_type'] ) ? sanitize_text_field( wp_unslash( $_POST['action_type'] ) ) : '';	
            $sanitizedDataArray = isset($_POST['data']) ? array_map('sanitize_text_field', wp_unslash($_POST['data'])) : array();
	
			foreach ($sanitizedDataArray as $data) {
				// Process each sanitized data item
				$section_type_array = explode("_", $data);
				$section_type = sanitize_text_field( $section_type_array[0] ); // sanitize section_type
	
				// Retrieve existing data
				$existingData = self::get_fields($section_type);
	
				if (array_key_exists($data, $existingData)) {
					// Update enabled status based on actionType
					if ($actionType == 'enable') {
						$existingData[$data]['enabled'] = 1;
					} else {
						$existingData[$data]['enabled'] = 0;
					}
				}
				// Update the fields with the new enabled status
				self::update_fields($section_type, $existingData);
			}
	
			// Prepare return data
			$returnData = array(
				'status' => 1,
				'msg'    => 'success',
				'update_field' => $existingData,
			);
	
			// Send JSON response
			wp_send_json($returnData);
		}
	}

    public static function is_blank($value) {
		return empty($value) && !is_numeric($value);
	}
		
}

new DCFEM_Settings();