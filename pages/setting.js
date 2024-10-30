function _cf_submit() {
	if(document.cf_form.cf_image.value == "") {
		alert(cardflip_adminscripts.cardflip_image);
		document.cf_form.cf_image.focus();
		return false;
	}
	else if(document.cf_form.cf_group.value == "" && document.cf_form.cf_group_txt.value == "") {
		alert(cardflip_adminscripts.cardflip_group);
		document.cf_form.cf_group_txt.focus();
		return false;
	}
	else if(document.cf_form.cardflip_order.value=="" || isNaN(document.cf_form.cardflip_order.value)) {
		alert(cardflip_adminscripts.cardflip_order);
		document.cf_form.cf_order.focus();
		document.cf_form.cf_order.select();
		return false;
	}
}

function _cf_delete(id) {
	if(confirm(cardflip_adminscripts.cardflip_delete)) {
		document.frm_cf_display.action="options-general.php?page=card-flip-image-slideshow&ac=del&did="+id;
		document.frm_cf_display.submit();
	}
}	

function _cf_redirect() {
	window.location = "options-general.php?page=card-flip-image-slideshow";
}

function _cf_help() {
	window.open("http://www.gopiplus.com/work/2019/12/15/card-flip-image-slideshow-wordpress-plugin/");
}

function _cf_numericandtext(inputtxt) {  
	var numbers = /^[0-9a-zA-Z]+$/;  
	document.getElementById('cf_group').value = "";
	if(inputtxt.value.match(numbers)) {  
		return true;  
	}  
	else {  
		alert(cardflip_adminscripts.cardflip_numletters); 
		newinputtxt = inputtxt.value.substring(0, inputtxt.value.length - 1);
		document.getElementById('cf_group_txt').value = newinputtxt;
		return false;  
	}  
}