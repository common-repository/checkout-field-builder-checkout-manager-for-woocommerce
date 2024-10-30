jQuery(document).ready(function ($) {
    function field_is_required(field, is_required) {
        is_required ? (field.find("label .optional").remove(), field.addClass("validate-required"), field_label_white_space_fix(field), 0 === field.find("label .required").length && field.find("label").append(' <abbr class="required" title="' + wc_address_i18n_params.i18n_required_text + '">*</abbr>')) : (field.find("label .required").remove(), field.removeClass("validate-required woocommerce-invalid woocommerce-invalid-required-field"), field_label_white_space_fix(field), 0 === field.find("label .optional").length && field.find("label").append(' <span class="optional">(' + wc_address_i18n_params.i18n_optional_text + ")</span>"))
    }
    
    function address_fields_required_validation_fix() {
        var thisform = $(".woocommerce-checkout"),
        locale_fields = $.parseJSON(wc_address_i18n_params.locale_fields);
       locale_fields && $.each(locale_fields, function (key, value) {
          var fids = value.split(",");
          $.each(fids, function (index, fid) {
             var field = thisform.find(fid.trim());
             field.hasClass("dcfem-required") ? field_is_required(field, !0) : field.hasClass("dcfem-optional") && field_is_required(field, !1)
          })
       })
    }
 
    function field_label_white_space_fix(field) {
       var label = field.find("label").html();
       label && (label = label.replace(/(?:^(?: )+)|(?:(?: )+$)/g, ""), field.find("label").html(label.trim()))
    }
    $('.select2-validation').select2({
        tags: true,
        width: '100%',
        allowClear: true
    }).addClass("enhanced"), $(document.body).bind("country_to_state_changing", function (event, country, wrapper) {
        setTimeout(address_fields_required_validation_fix, 500)
    })
 });