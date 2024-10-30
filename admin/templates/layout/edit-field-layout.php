<?php
namespace DevryptCheckoutFieldEditorManager;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="modal fade dcfem-modal" id="edit_form" tabindex="-1" role="dialog" aria-labelledby="editModalLabel" aria-hidden="true">
    <form method="post" id="dcfem_edit_field_form" action="">    
        <div class="modal-dialog" role="document">
            <div class="modal-content dcfem-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editModalLabel"><?php echo esc_html__( 'Edit Field', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></h5>
                    <button type="button" class="close dcfem-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body dcfem-modal-body">
                    <div id="message"></div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" name="field_type" class="field_type" value="" />
                    <input type="hidden" name="modal_type" class="modal_type" value="edit" />
                    <button type="button" class="dcfem-close-btn dcfem-normal-btn" data-bs-dismiss="modal"><?php echo esc_html__( 'Close', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                    <button class="dcfem_update_btn"><?php echo esc_html__( 'Update', 'checkout-field-builder-checkout-manager-for-woocommerce' ); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>