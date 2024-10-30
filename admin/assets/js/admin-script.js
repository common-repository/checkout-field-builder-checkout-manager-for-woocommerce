jQuery(document).ready(function ($) {
  
  $('select#input_type, select#section_type').select2();
  $("#dcfem-tabs").tabs();

  $("#section_type").on("change", function () {
    var selectFieldValue = $(this).val();
    if (selectFieldValue == "") {
      var inputFieldValue = "";
    } else {
      var inputFieldValue = selectFieldValue + "_";
    }

    $("#name").val(inputFieldValue);
  });

  $(".add_new_data").on("click", function () {

    var errorClass = "has-error"; 
    var errorCount = 0;

    const blockedFieldType = ["radio", "select", "multiselect", "checkboxgroup"];

    if( !blockedFieldType.includes( $(".field_type").val() ) ) {
      var errorCount = 0;
      var errorClass = "";
    } else {
      $( '.dcfem-option-field-value' ).each(function() {
        if( $(this).val() == "" ) {
          errorCount = 1;
          $(this).addClass(errorClass);
        } else {
          errorCount = 0;
          $(this).removeClass(errorClass);
        }
      })
  
      $( '.dcfem-option-field-text' ).each(function() {
        if( $(this).val() == "" ) {
          errorCount = 1;
          $(this).addClass(errorClass);
        } else {
          errorCount = 0;
          $(this).removeClass(errorClass);
        }
      })
    }

    if(!errorCount) {
      $.ajax({
        type: "POST",
        url: dcfemAjax.ajaxurl,
        data: {
          data: $("#dcfem_add_field_form").serialize(),
          action: "addDataAjax",
          nonce: dcfemAjax.nonce,
        },
        success: function (response) {
          console.log(response);
          if (response.status == 1) {
            window.location.reload();
          }
        },
      });
    }
    
  });

  $(".dcfem_edit_btn").on("click", function () {
    var nameField = $(this).data("value");
    $.ajax({
      type: "POST",
      url: dcfemAjax.ajaxurl,
      data: {
        variable: nameField,
        action: "showEditForm",
        nonce: dcfemAjax.nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.error == 0) {
			$("#dcfem_edit_field_form .field_type").val(response.fieldType);
          $("#message").append(response.html);
          $('select#input_type, select#section_type').select2();
        }
      },
    });
  });

  $(".dcfem_update_btn").on("click", function (e) {
    e.preventDefault();

	var errorClass = "has-error"; 
    var errorCount = 0;

    const blockedFieldType = ["radio", "select", "multiselect", "checkboxgroup"];

    if( !blockedFieldType.includes( $("#dcfem_edit_field_form .field_type").val() ) ) {
      var errorCount = 0;
      var errorClass = "";
    } else {
      $( '.dcfem-option-field-value' ).each(function() {
        if( $(this).val() == "" ) {
          errorCount = 1;
          $(this).addClass(errorClass);
        } else {
          errorCount = 0;
          $(this).removeClass(errorClass);
        }
      })
  
      $( '.dcfem-option-field-text' ).each(function() {
        if( $(this).val() == "" ) {
          errorCount = 1;
          $(this).addClass(errorClass);
        } else {
          errorCount = 0;
          $(this).removeClass(errorClass);
        }
      })
    }

	if(!errorCount) {
		$.ajax({
		type: "POST",
		url: dcfemAjax.ajaxurl,
		data: {
			data: $("#dcfem_edit_field_form").serialize(),
			action: "updateDataAjax",
			nonce: dcfemAjax.nonce,
		},
		success: function (response) {
			console.log(response);
			if (response.status == 1) {
			window.location.reload();
			}
		},
		});
	}

  });

  $(".dcfem_delete_btn").on("click", function () {
    var name_field = $(this).data("value");
    $.ajax({
      type: "POST",
      url: dcfemAjax.ajaxurl,
      data: {
        variable: name_field,
        action: "showDeleteForm",
        nonce: dcfemAjax.nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.error == 0) {
          $("#delete_message").append(response.html);
        }
      },
    });
  });

  $(".dcfem_delete_field_btn").on("click", function () {
    $.ajax({
      type: "POST",
      url: dcfemAjax.ajaxurl,
      data: {
        data: $("#dcfem_delete_field_form").serialize(),
        action: "deleteDataAjax",
        nonce: dcfemAjax.nonce,
      },
      success: function (response) {
        console.log(response);
        if (response.status == 1) {
          window.location.reload();
        }
      },
    });
  });

  //   option show or hide
  $(document).on("change", "#input_type", function (e) {
    console.log($(this).val());
    if (
      $(this).val() == "radio" ||
      $(this).val() == "select" ||
      $(this).val() == "multiselect" ||
      $(this).val() == "checkboxgroup"
    ) {
		$(".field_type").val($(this).val());
		if($("#dcfem_edit_field_form .modal_type").val() == "add") {
			if ($(".dcfem-option-item").length > 1) {
				$(".dcfem-option-item").slice(1).remove();
				$(".dcfem-option-item input").val("");
			}
		}
		$(".dcfem-option-wrapper").show();
		$(".dcfem-option-item input").attr("required", true);
    } else {
      $(".field_type").val("");
      $(".dcfem-option-wrapper").hide();
      $(".dcfem-option-item input").attr("required", false);
    }
  });
  // Function to add a new option item
  $(document).on("click", ".dcfem-option-add", function (e) {
    e.preventDefault();

    var newOptionItem = $(this).closest(".dcfem-option-item").clone();
    newOptionItem.find("input").val("");

    $(this).closest(".dcfem-option-item").after(newOptionItem);
  });

  $(document).on("click", ".dcfem-option-remove", function (e) {
    e.preventDefault();

    if ($(".dcfem-option-item").length > 1) {
      $(this).closest(".dcfem-option-item").remove();
    }
  });

  $(".dcfem-close-btn").on("click", function () {
    $("#message").children().remove();
    $("#delete_message").children().remove();
  });

  $(document).on("change", ".required-field input", function () {
    if ($(this).is(":checked")) {
      $(this).val("1");
    } else {
      $(this).val("0");
    }
  });

  $(document).on("change", ".display-in-email-field input", function () {
    if ($(this).is(":checked")) {
      $(this).val("1");
    } else {
      $(this).val("0");
    }
  });

  $(document).on("change", ".display-in-order-field input", function () {
    if ($(this).is(":checked")) {
      $(this).val("1");
    } else {
      $(this).val("0");
    }
  });

});

function enabledisable(param) {
  var checkedKeys = [];
  jQuery(".dcfem-checkbox-select:checked").each(function () {
    let key = jQuery(this).data("key");
    checkedKeys.push(key);
  });
  jQuery.ajax({
    type: "POST",
    url: dcfemAjax.ajaxurl,
    data: {
      data: checkedKeys,
      action: "enableDataAjax",
      nonce: dcfemAjax.nonce,
      action_type: param,
    },
    success: function (response) {
      console.log(response);
      if (response.status == 1) {
        window.location.reload();
      }
    },
  });
}
