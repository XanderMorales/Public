// @author: Alexander Morales - December 14th 2007
// @desc: Form validation using the jQuery Framework vs 1.2.1
// updated function check_phone_number on april 22nd 2008 - us phone number proper format validation.

$(document).ready(function()
{
	//declare all possible validation types and methods
	// # 'validation-type' : 'validation-method'
	var validation_types = {
		'validate-empty'			: 'empty',
		'validate-email'			: 'check_email',
		'validate-phone-number'		: 'check_phone_number',
		'validate-digits'			: 'digits_only',
		'validate-date'				: 'date_format',
		'validate-number'			: 'number_format',
		'validate-url'				: 'url_format',
		'validate-credit-card'		: 'cc_check',
		'validate-select-one'		: 'make_selection',
		'validate-is-checked'		: 'is_checked',
		'maxLength'					: 'length_max',
		'minLength'					: 'length_min',
		'equalTo'					: 'is_equal_to'
	};
	// Loop through the form and associate the types and method for events
	for (form_name in validation_objects) {
		var form_id = form_name;
		var parent_element = '';
		for (sub_name in validation_objects[form_name]) {
			if (sub_name.match(/^sub_val/)) {
				for (sub_validation_type in validation_objects[form_name][sub_name]) {
					validate_secondary_elements(form_id, sub_validation_type, parent_element, validation_types[sub_validation_type], validation_objects[form_name][sub_name][sub_validation_type]);
				}
			}
			else {
				parent_element = sub_name;
				validate_primary_elements(form_id, validation_objects[form_name][sub_name], sub_name, validation_types[validation_objects[form_name][sub_name]]);
			}
		}
	}
	// Loop through the form on submit and associate the types and method for events
	$("form").submit(function() {
		var found_error = 'no';
		for (form_name in validation_objects) {
			var form_id = form_name;
			var parent_element = '';
			for (sub_name in validation_objects[form_name])	{
				if (sub_name.match(/^sub_val/)) {
					for (sub_validation_type in validation_objects[form_name][sub_name]) {
						var x = submit_secondary_elements(form_id, sub_validation_type, parent_element, validation_types[sub_validation_type], validation_objects[form_name][sub_name][sub_validation_type]);
						if(x == false) { found_error = 'yes'; /* break; */ }
					}
				}
				else {
					parent_element = sub_name;
					var x = submit_primary_elements(form_id, validation_objects[form_name][sub_name], sub_name, validation_types[validation_objects[form_name][sub_name]]);
					if(x == false) { found_error = 'yes'; /* break; */ }
				}
			}
		}
		if(found_error == 'yes') { return false; }
	});
	// Validate parent level validation methods
	function validate_primary_elements(form_id, validation_type, form_element_id, val_func) {
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').focus(function() { setTo('note', $(this).attr('id'), 'visible'); });
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').blur(function() {
			if(validation_type == 'validate-empty') {
				if ($(this).val().length > 0) { setTo('note', $(this).attr('id'), 'hidden'); } 
				else { setTo('error', $(this).attr('id'), 'visible'); }
			}
			else {
				var msg = eval(val_func + "($(this))");
				if(msg == 'pass') { setTo('note', $(this).attr('id'), 'hidden'); }
				else { setTo('error', $(this).attr('id'), 'visible'); }
			}
		});
	}
	// Validate child level validation methods
	function validate_secondary_elements(form_id, validation_type, form_element_id, val_func, arg) {
		count_error_for_parent = 0;
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').focus(function() { setTo('note', $(this).attr('id'), 'visible'); });
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').blur(function() {
			var sub_msg = eval(val_func + "( $(this), arg)");
			if(sub_msg == 'pass') {
				if(count_error_for_parent == 0)
					setTo('note', $(this).attr('id'), 'hidden');
			}
			else {
				count_error_for_parent++;
				setTo('error', $(this).attr('id'), 'visible');
			}
		});
	}
	// On submit - validate parent level validation methods
	function submit_primary_elements(form_id, validation_type, form_element_id, val_func) {
		var need_correction = 'no';
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').each(function() {
			if(validation_type == 'validate-empty') {
				if ($(this).val().length == 0) { setTo('error', $(this).attr('id'), 'visible'); need_correction = 'yes'; } 
			}
			else {
				var msg = eval(val_func + "($(this))");
				if(msg == 'error') { setTo('error', $(this).attr('id'), 'visible');  need_correction = 'yes';  }
			}
		});
		if(need_correction == 'yes') { return false; }
	}
	// On submit - validate child level validation methods
	function submit_secondary_elements(form_id, validation_type, form_element_id, val_func , arg) {
		var need_correction = 'no';
		count_error_for_parent = 0;
		$('#' + form_id + ' ' + '[id=' + form_element_id + ']').each(function() {
			var sub_msg = eval(val_func + "( $(this), arg)");
			if(sub_msg == 'error') {
				if(count_error_for_parent == 0) {
					setTo('error', $(this).attr('id'), 'visible');
					need_correction = 'yes';
					count_error_for_parent ++;
				}
			}
		});
		if(need_correction == 'yes') { return false; }
	}
	// Show or hide a specific note, error or success message to user ( set id div.form-note )
	function setTo(type, id, show, txt) {
		$('#note-' + id).removeClass('form-note form-error form-success');
		$('#note-' + id).addClass('form-' + type);	    
		$('#note-' + id).text(eval(type + '[' + 'id' + ']'));
		$('#note-' + id).css('visibility', show );
	}
	/*
	/*===========================
	   - validation functions - 
	=============================*/
	// validate-email
	// Please use a real email address as we need to email you to confirm your account
	function check_email(email_addy) {
		if(/^[^\s,;]+@([^\s.,;]+\.)+[\w-]{2,}$/.test($(email_addy).val())) { return "pass"; }
		else { return "error"; }
	}
	// validate-phone-number
	// This field validate us proper hone number formats
	function check_phone_number(number) {
		if(/^\(?(\d{3})\)?[- ]?(\d{3})[- ]?(\d{4})$/.test($(number).val())) { return "pass"; }
		else { return "error"; }
	}
	// validate-digits
	// Please use numbers only in this field - avoid spaces or other characters such as dots or commas.
	function digits_only(number) {
		if(/^\d+$/.test( $(number).val() )){ return "pass"; }
		else { return "error"; }
	}
	// validate-date
	// Date must be in following format: ("yyyy/mm/dd") or ("yyyy-mm-dd") or ("mm.dd.yyy")
	function date_format(date_in) {
		//if(/^\d{4}[\/-]\d{1,2}[\/-]\d{1,2}$/.test($(date_in).val())) { return "pass"; } // ("yyyy/mm/dd") or ("yyyy-mm-dd") or ("mm.dd.yyy")
		if(/^\d{4}[\-]\d{2}[\-]\d{2}$/.test($(date_in).val())) { return "pass"; } // ("yyyy-mm-dd") for dbase
		else { return "error"; }
	}
	// validate-number-format
	// Please use international number format, eg. 100,000.59
	function number_format(obj) {
		if(/^-?(?:\d+|\d{1,3}(?:,\d{3})+)(?:\.\d+)?$/.test($(obj).val())) { return "pass"; }
		else { return "error"; }
	}
	// validate-url
	// Please enter a valid http(s) or ftp url.
	function url_format(url_in) {
		if(/^(https?|ftp):\/\/(((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:)*@)?(((\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5])\.(\d|[1-9]\d|1\d\d|2[0-4]\d|25[0-5]))|((([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|\d|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.)*(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])*([a-z]|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])))\.?)(:\d*)?)(\/((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)+(\/(([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)*)*)?)?(\?((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|[\uE000-\uF8FF]|\/|\?)*)?(\#((([a-z]|\d|-|\.|_|~|[\u00A0-\uD7FF\uF900-\uFDCF\uFDF0-\uFFEF])|(%[\da-f]{2})|[!\$&'\(\)\*\+,;=]|:|@)|\/|\?)*)?$/i.test( $(url_in).val() )){ return "pass"; }
		else { return "error"; }
	}
	// validate-credit-card
	// Please enter a valid card number.
	function cc_check(cc_in) {
		if( $(cc_in).val() == '') { return "error"; }
		this.ar = new Array($(cc_in).val().length);
		this.sum = 0;
		for(i = 0; i < $(cc_in).val().length; ++i){ this.ar[i] = parseInt($(cc_in).val().charAt(i)); }
		for(i = this.ar.length -2; i >= 0; i-=2) {
			this.ar[i] *= 2;
			if(this.ar[i] > 9){ this.ar[i]-=9; }
		}
		for(i = 0; i < this.ar.length; ++i){ this.sum += this.ar[i]; }
		if(this.sum%10 != 0){ return "error"; }
		else {
			// test cards
			// visa, mc, amex, diners club, discover
			if($(cc_in).val() == '4111111111111111' || $(cc_in).val() == '5500000000000004' || $(cc_in).val() == '340000000000009' || $(cc_in).val() == '30000000000004' || $(cc_in).val() == '6011000000000004'){
				return "error";
			}
			return "pass";
		}
	}
	// maxLength
	// you can not have this value greater than arg
	function length_max(val_in, arg) {
		if( $(val_in).val().length > arg) { return "error"; }
		else { return "pass"; }
	}
	// minLength
	// you can not have this value less than arg
	function length_min(val_in, arg) {
		if($(val_in).val().length < arg) { return "error"; }
		else { return "pass"; }
	}
	// validat a selected item
	// check against <option value="empty" selected="selected">please choose</option>
	function make_selection(val_in) {
		if($(val_in).val() == 'empty') {return "error"; }
		else { return "pass"; }
	}
	// validate-is-checked
	// Insure a check box is checked: Perfect for a user who need to accept an agreement
	function is_checked(val_in) {
		err = "error";
		$(val_in).each(function(){ if(this.checked) { err = "pass"; } });
		return err;
	}
	// validate-is-equal_to
	// Check the value of one form field against the value of another form field: Both values must be the same or return error
	function is_equal_to(val_in, arg) {
		err = "error";
		if($(val_in).val() == eval(arg)) { err = "pass"; }
		return err;
	}
	// To do:
	// Test Ajax function for server query of validation methods
	// Posibilities - build validation methods in PHP.
	function jquery_ajax(endPoint, params) {
		$.ajax({
			url			: '/ajax/validate/' + endPoint,
			type		: 'POST',
			dataType	: 'json',
			data		: params,
			timeout		: 2000,
			error		: function() { alert('Error loading script /ajax/validate/' + endPoint); },
			success		: function(json) { return json; }
		});
	}
});