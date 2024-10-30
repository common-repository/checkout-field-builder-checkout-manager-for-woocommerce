<?php
namespace DevryptCheckoutFieldEditorManager;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$fields = DCFEM_Settings::get_fields('billing');

if ( isset( $_POST['reset_fields'] ) ) {
    if ( isset( $_POST['save_reset_fields_nonce'] ) && wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['save_reset_fields_nonce'] ) ), 'save_reset_fields_action' ) ) {
        $reset_default = DCFEM_Settings::reset_to_default();
        echo esc_html( $reset_default );
    } else {
        wp_die( 'Security check failed.' );
    }
}
?>
<div id="tabs-1" class="dcfem-tab">
    <div class="dcfem-form-wrapper">
        <table class="dcfem_checkout_fields" cellspacing="0">
            <thead>
                <tr>
                    <th colspan="4">
                        <button type="button" class="dcfem-add-new-btn" data-bs-toggle="modal" data-bs-target="#add_form"><?php echo esc_html__( 'Add Field', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                        <button type="button" class="dcfem-enable-btn" onclick="enabledisable('enable')"><?php echo esc_html__( 'Enable', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                        <button type="button" class="dcfem-disable-btn" onclick="enabledisable('disable')"><?php echo esc_html__( 'Disable', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                    </th>
                    <th colspan="5">
                        <form method="post" action="">
                            <?php wp_nonce_field( 'save_reset_fields_action', 'save_reset_fields_nonce' ); ?>
                            <input type="submit" name="save_fields" class="save-changes-btn" value="Save changes" style="float:right">
                            <input type="submit" name="reset_fields" class="reset-defalt-btn" value="Reset to default fields" style="float:right; margin-right: 5px;" onclick="return confirm('Are you sure you want to reset to default fields? all your changes will be deleted.')">
                        </form>
                    </th>
                </tr>
                <tr>
                    <th class="check-column"><input type="checkbox" class="dcfem-checkbox-select" value="0"></th>
                    <th class="name"><?php echo esc_html__( 'Name', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="id"><?php echo esc_html__( 'Type', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="label"><?php echo esc_html__( 'Label', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="placeholder"><?php echo esc_html__( 'Placeholder', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="status"><?php echo esc_html__( 'Required', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="action"><?php echo esc_html__( 'Edit', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                    <th class="action"><?php echo esc_html__( 'Remove', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></th>
                </tr>
            </thead>
            <tbody class="ui-sortable">
                <?php foreach($fields as $key => $field){
                    $field_type = isset($field['type']) ? $field['type'] : '';
                    $field_label = isset($field['label']) ? $field['label'] : '';
                    $field_placeholder = isset($field['placeholder']) ? $field['placeholder'] : '';
                    $default = isset($field['default']) ? $field['default'] : '';
                    $field_validate = isset($field['validate']) ? $field['validate'] : '';
                    $field_required = isset($field['required']) && $field['required'] ? 1 : 0;
                    $field_enabled = isset($field['enabled']) && $field['enabled'] ? 1 : 0;
                    $custom = isset($field['custom']) && $field['custom'] ? 1 : 0;
                    if($field_required){
                        $required_status = '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-check"><polyline points="20 6 9 17 4 12"></polyline></svg></span>';
                    } else {
                        $required_status = '<span><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-minus"><line x1="5" y1="12" x2="19" y2="12"></line></svg></span>';
                    }
                    $allowed_html = array(
                        'span' => array(),
                        'svg' => array(
                            'xmlns' => array(),
                            'width' => array(),
                            'height' => array(),
                            'viewBox' => array(),
                            'fill' => array(),
                            'stroke' => array(),
                            'stroke-width' => array(),
                            'stroke-linecap' => array(),
                            'stroke-linejoin' => array(),
                            'class' => array(),
                        ),
                        'polyline' => array(
                            'points' => array(),
                        ),
                        'line' => array(
                            'x1' => array(),
                            'y1' => array(),
                            'x2' => array(),
                            'y2' => array(),
                        ),
                    );
                    $field_custom = isset($field['custom']) && $field['custom'] ? 1 : 0;
                    if($field_enabled){
                        $enabled_status = 'dcfem-enable-row';
                    } else {
                        $enabled_status = 'dcfem-disable-row';
                    }
                ?>
                <tr class="row_0 <?php echo esc_attr($enabled_status); ?>">
                    <td class="td_select">
                        <input data-key="<?php echo esc_attr( $key ); ?>" type="checkbox" class="dcfem-checkbox-select" value="0">
                    </td>
                    <td class="td_name"><?php echo esc_html( $key ); ?></td>
                    <td class="td_type"><?php echo esc_html( $field_type ); ?></td>
                    <td class="td_label"><?php echo esc_html( $field_label ); ?></td>
                    <td class="td_placeholder"><?php echo esc_html( $field_placeholder ); ?></td>
                    <td class="td_required"><?php echo wp_kses( $required_status, $allowed_html ); ?></td>
                    <td class="td_edit action">
                        <button class="button action-btn dcfem_edit_btn" data-value="<?php echo esc_attr( $key ); ?>" data-bs-toggle="modal" data-bs-target="#edit_form"><?php echo esc_html__( 'Edit', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                    </td>
                    <td class="td_remove action">
                        <button class="button action-btn dcfem_delete_btn" data-value="<?php echo esc_attr( $key ); ?>" data-bs-toggle="modal" data-bs-target="#delete_form"><?php echo esc_html__( 'Remove', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <div class="dcfem-preview-box dcfem-preview-billing-form">
            <h3 class="dcfem-preview-box-title"><?php echo esc_html__('Billing Details', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></h3>
            <div class="dcfem-preview-box-content">
                <?php
                $checkout = \wc()->checkout();
                $checkout_billing_fields = $checkout->get_checkout_fields('billing');
                foreach($fields as $key => $value){
                    woocommerce_form_field( $key, $value, $checkout->get_value( $key ) );
                }
                ?>
            </div>
        </div>
    </div>
</div>
<?php include_once DCFEM_ADMIN . 'templates/layout/add-field-layout.php'; ?>
<?php include_once DCFEM_ADMIN . 'templates/layout/edit-field-layout.php'; ?>
<?php include_once DCFEM_ADMIN . 'templates/layout/delete-field-layout.php'; ?>