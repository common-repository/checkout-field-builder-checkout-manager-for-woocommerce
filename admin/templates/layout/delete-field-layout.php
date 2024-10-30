<?php
namespace DevryptCheckoutFieldEditorManager;
// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="modal fade dcfem-modal" id="delete_form" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <form method="post" id="dcfem_delete_field_form" action="">    
        <div class="modal-dialog" role="document">
            <div class="modal-content dcfem-delete-modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel"><?php echo esc_html__('Delete Field', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></h5>
                    <button type="button" class="close dcfem-close-btn" data-bs-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div id="delete_message"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="dcfem-close-btn dcfem-normal-btn" data-bs-dismiss="modal"><?php echo esc_html__('Close', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></button>
                    <button class="dcfem_delete_field_btn"><?php echo esc_html__('Delete Field', 'checkout-field-builder-checkout-manager-for-woocommerce'); ?></button>
                </div>
            </div>
        </div>
    </form>
</div>