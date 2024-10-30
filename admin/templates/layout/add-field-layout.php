<?php
namespace DevryptCheckoutFieldEditorManager;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="modal fade dcfem-modal" id="add_form" tabindex="-1" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form method="post" id="dcfem_add_field_form" action="">
            <div class="modal-content dcfem-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addModalLabel"><?php echo esc_html__( 'Add New Field', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body dcfem-modal-body">
                    <div class="form-group">
                        <label for="section_type"><?php echo esc_html__( 'Section Type', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></label>
                        <select class="form-control" id="section_type" name="section_type">
                            <option value=""><?php echo esc_html__( 'Select', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="billing" ><?php echo esc_html__( 'Billing', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="shipping"><?php echo esc_html__( 'Shipping', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="additional"><?php echo esc_html__( 'Additional', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name"><?php echo esc_html__( 'Name', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></label>
                        <input type="text" class="form-control" id="name" value="" name="name">
                    </div>
                    <div class="form-group">
                        <label for="input_type"><?php echo esc_html__( 'Type', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></label>
                        <select class="form-control" id="input_type" name="type">
                            <option value="text"><?php echo esc_html__( 'Text', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="email"><?php echo esc_html__( 'Email', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="number"><?php echo esc_html__( 'Number', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="country"><?php echo esc_html__( 'Country', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="state"><?php echo esc_html__( 'State', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="tel"><?php echo esc_html__( 'Telephone', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="hidden"><?php echo esc_html__( 'Hidden', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="password"><?php echo esc_html__( 'Password', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="textarea"><?php echo esc_html__( 'Textarea', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="radio"><?php echo esc_html__( 'Radio', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="checkbox"><?php echo esc_html__( 'Checkbox', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="select"><?php echo esc_html__( 'Select', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="multiselect"><?php echo esc_html__( 'Multiselect', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="checkboxgroup"><?php echo esc_html__( 'Checkbox Group', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="heading"><?php echo esc_html__( 'Heading', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="paragraph"><?php echo esc_html__( 'Paragraph', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="url"><?php echo esc_html__( 'URL', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="datetime-local"><?php echo esc_html__( 'Datetime Local', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="date"><?php echo esc_html__( 'Date', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="time"><?php echo esc_html__( 'Time', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="month"><?php echo esc_html__( 'Month', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></option>
                            <option value="week"><?php echo esc_html__('Week', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="name"><?php echo esc_html__( 'Label', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></label>
                        <input type="text" class="form-control" id="label" value="" name="label">
                    </div>
                    <div class="form-group">
                        <label for="name"><?php echo esc_html__('Placeholder', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                        <input type="text" class="form-control" id="placeholder" value="" name="placeholder">
                    </div>
                    <div class="form-group">
                        <label for="name"><?php echo esc_html__('Default Value', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                        <input type="text" class="form-control" id="default_value" value="" name="default">
                    </div>
                    <div class="form-group">
                        <label for="name"><?php echo esc_html__('Additional Classes', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                        <input type="text" class="form-control" id="add_class" value="" name="class[]">
                    </div>
                    <div class="form-check required-field">
                        <div class="required-blank-div"></div>
                        <input class="form-check-input dcfem-check-input" type="checkbox" value="0" name="required" id="required">
                        <label class="form-check-label" for="required"><?php echo esc_html__('Required', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                    </div>
                    <div class="form-check display-in-email-field">
                        <div class="required-blank-div"></div>
                        <input class="form-check-input dcfem-check-input" type="checkbox" value="0" name="show_in_email" id="show_in_email">
                        <label class="form-check-label" for="show_in_email"><?php echo esc_html__('Display on Email', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                    </div>
                    <div class="form-check display-in-order-field">
                        <div class="required-blank-div"></div>
                        <input class="form-check-input dcfem-check-input" type="checkbox" value="0" name="show_in_order" id="show_in_order">
                        <label class="form-check-label" for="show_in_order"><?php echo esc_html__('Display in Order Detail Pages', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></label>
                    </div>
                    <div class="dcfem-option-wrapper" style="display: none;">
                        <div>
                            <div class="sub-title"><?php echo esc_html__('Options', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></div>
                            <div></div>
                        </div>
                        <div>
                            <div colspan="3" class="p-0">
                                <div class="dcfem-option-item">
                                    <div class="key">
                                        <input type="text" class="dcfem-option-field-value" name="radio_options_value[]" placeholder="<?php echo esc_html__('Option Value', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?>">
                                    </div>
                                    <div class="value">
                                        <input type="text" class="dcfem-option-field-text" name="radio_options_text[]" placeholder="<?php echo esc_html__('Option Text', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?>">
                                    </div>
                                    <div class="action-cell">
                                        <a href="#" class="btn btn-tiny btn-primary dcfem-option-add"><?php echo esc_html__('+', 'checkout-field-builder-checkout-manager-for-woocommerce');?></a>
                                        <a href="#" class="btn btn-tiny btn-danger dcfem-option-remove"><?php echo esc_html__( 'x', 'checkout-field-builder-checkout-manager-for-woocommerce' );?></a>
                                    </div>
                                </div>        	
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="dcfem-normal-btn" data-bs-dismiss="modal"><?php echo esc_html__( 'Close', 'checkout-field-builder-checkout-manager-for-woocommerce' ) ?></button>
                    <input type="hidden" name="id" value="" />
                    <input type="hidden" name="field_type" class="field_type" value="" />
                    <input type="hidden" name="modal_type" class="modal_type" value="add" />
                    <input name="add_data_submit" type="button" value="Save Data" class="add_new_data">
                </div>
            </div>
        </form>
    </div>
</div>