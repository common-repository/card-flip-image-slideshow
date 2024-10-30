<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<div class="wrap">
<?php
$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
if(!is_numeric($did)) { 
	die('<p>Are you sure you want to do this?</p>'); 
}

$result = cardflip_cls_dbquery::cardflip_count($did);
if ($result != '1') {
	?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'card-flip-image-slideshow'); ?></strong></p></div><?php
}
else {
	$cf_errors = array();
	$cf_success = '';
	$cf_error_found = false;
	
	$data = array();
	$data = cardflip_cls_dbquery::cardflip_select_byid($did);
	
	$form = array(
		'cf_id' => $data['cf_id'],
		'cf_image' => $data['cf_image'],
		'cf_link' => $data['cf_link'],
		'cf_title' => $data['cf_title'],
		'cf_target' => $data['cf_target'],
		'cf_order' => $data['cf_order'],
		'cf_status' => $data['cf_status'],
		'cf_group' => $data['cf_group']
	);
}

if (isset($_POST['cf_form_submit']) && $_POST['cf_form_submit'] == 'yes') {
	check_admin_referer('cf_form_edit');
	
	$form['cf_image'] = isset($_POST['cf_image']) ? esc_url_raw($_POST['cf_image']) : '';
	if ($form['cf_image'] == '') {
		$cf_errors[] = __('Please enter the image path.', 'card-flip-image-slideshow');
		$cf_error_found = true;
	}

	$form['cf_link'] = isset($_POST['cf_link']) ? esc_url_raw($_POST['cf_link']) : '';
	$form['cf_title'] = isset($_POST['cf_title']) ? sanitize_text_field($_POST['cf_title']) : '';
	$form['cf_target'] = isset($_POST['cf_target']) ? sanitize_text_field($_POST['cf_target']) : '';
	$form['cf_order'] = isset($_POST['cf_order']) ? intval($_POST['cf_order']) : '0';
	$form['cf_group'] = isset($_POST['cf_group']) ? sanitize_text_field($_POST['cf_group']) : '';
	if ($form['cf_group'] == '') {
		$form['cf_group'] = isset($_POST['cf_group_txt']) ? sanitize_text_field($_POST['cf_group_txt']) : '';
	}
	if ($form['cf_group'] == '') {
		$cf_errors[] = __('Please enter the image group.', 'card-flip-image-slideshow');
		$cf_error_found = true;
	}
	$form['cf_status'] = isset($_POST['cf_status']) ? sanitize_text_field($_POST['cf_status']) : '';
	$form['cf_id'] = isset($_POST['cf_id']) ? sanitize_text_field($_POST['cf_id']) : '';

	if ($cf_error_found == FALSE)
	{	
		$status = cardflip_cls_dbquery::cardflip_action_ins($form, "update");
		if($status == 'update') {
			$cf_success = __('Image details was successfully updated.', 'card-flip-image-slideshow');
		}
		else {
			$cf_errors[] = __('Oops, something went wrong. try again.', 'card-flip-image-slideshow');
			$cf_error_found = true;
		}
	}
}

if ($cf_error_found == true && isset($cf_errors[0]) == true) {
	?><div class="error fade"><p><strong><?php echo $cf_errors[0]; ?></strong></p></div><?php
}

if ($cf_error_found == false && strlen($cf_success) > 0) {
	?><div class="updated fade"><p><strong><?php echo $cf_success; ?>
	<a href="<?php echo CARDFLIP_ADMIN_URL; ?>"><?php _e('Click here', 'card-flip-image-slideshow'); ?></a> <?php _e('to view the details', 'card-flip-image-slideshow'); ?>
	</strong></p></div><?php
}

?>
<script type="text/javascript">
jQuery(document).ready(function($){
    $('#upload-btn').click(function(e) {
        e.preventDefault();
        var image = wp.media({ 
            title: 'Upload Image',
            // mutiple: true if you want to upload multiple files at once
            multiple: false
        }).open()
        .on('select', function(e){
            // This will return the selected image from the Media Uploader, the result is an object
            var uploaded_image = image.state().get('selection').first();
            // We convert uploaded_image to a JSON object to make accessing it easier
            // Output to the console uploaded_image
            console.log(uploaded_image);
            var img_imageurl = uploaded_image.toJSON().url;
			var img_imagetitle = uploaded_image.toJSON().title;
            // Let's assign the url value to the input field
            $('#cf_path').val(img_imageurl);
			$('#cf_title').val(img_imagetitle);
        });
    });
});
</script>
<?php
wp_enqueue_script('jquery');
wp_enqueue_media();
?>
<div class="form-wrap">
	<h1 class="wp-heading-inline"><?php _e('Update image', 'card-flip-image-slideshow'); ?></h1>
	<form name="cf_form" method="post" action="#" onsubmit="return _cf_submit()"  >
      
	  <label for="tag-image"><strong><?php _e('Image path (URL)', 'card-flip-image-slideshow'); ?></strong></label>
      <input name="cf_image" type="text" id="cf_image" value="<?php echo $form['cf_image']; ?>" size="60" />
	  <input type="button" name="upload-btn" id="upload-btn" class="button-secondary" value="Upload Image">
      <p><?php _e('Where is the image located on the internet.', 'card-flip-image-slideshow'); ?> <br />(ex: http://www.gopiplus.com/work/wp-content/uploads/sample.jpg)</p>
	  <p><img src="<?php echo $form['cf_image']; ?>" width="200"  /></p>
	  
	  <label for="tag-link"><strong><?php _e('Image title', 'card-flip-image-slideshow'); ?></strong></label>
      <input name="cf_title" type="text" id="cf_title" value="<?php echo $form['cf_title']; ?>" size="60" />
      <p><?php _e('Enter title for your image.', 'card-flip-image-slideshow'); ?></p>
	  
      <label for="tag-link"><strong><?php _e('Target link', 'card-flip-image-slideshow'); ?></strong></label>
      <input name="cf_link" type="text" id="cf_link" value="<?php echo $form['cf_link']; ?>" size="60" />
      <p><?php _e('When someone clicks on the picture, where do you want to send them.', 'card-flip-image-slideshow'); ?></p>
	  
      <label for="tag-target"><strong><?php _e('Target link window', 'card-flip-image-slideshow'); ?></strong></label>
      <select name="cf_target" id="cf_target">
        <option value='_blank' <?php if($form['cf_target'] == '_blank') { echo 'selected' ; } ?>>New Window</option>
        <option value='_self' <?php if($form['cf_target'] == '_self') { echo 'selected' ; } ?>>Same Window</option>
      </select>
      <p><?php _e('Do you want to open link in new window?', 'card-flip-image-slideshow'); ?></p>
	  
      <label for="tag-select-gallery-group"><strong><?php _e('Image group', 'card-flip-image-slideshow'); ?></strong></label>
		<select name="cf_group" id="cf_group">
			<option value=''><?php _e('Select', 'email-posts-to-subscribers'); ?></option>
			<?php
			$selected = "";
			$groups = array();
			$groups = cardflip_cls_dbquery::cardflip_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					if(strtoupper($form['cf_group']) == strtoupper($group["cf_group"])) { 
						$selected = "selected"; 
					}
					
					?>
					<option value="<?php echo stripslashes($group["cf_group"]); ?>" <?php echo $selected; ?>>
						<?php echo stripslashes($group["cf_group"]); ?>
					</option>
					<?php
					$selected = "";
				}
			}
			?>
		</select>
		(or) 
	   	<input name="cf_group_txt" type="text" id="cf_group_txt" value="" maxlength="10" onkeyup="return _cf_numericandtext(document.cf_form.cf_group_txt)" />
      <p><?php _e('This is to group the images. Select your slideshow group.', 'card-flip-image-slideshow'); ?></p>
	  
      <label for="tag-display-status"><strong><?php _e('Display', 'card-flip-image-slideshow'); ?></strong></label>
      <select name="cf_status" id="cf_status">
        <option value='Yes' <?php if($form['cf_status'] == 'Yes') { echo 'selected' ; } ?>>Yes</option>
        <option value='No' <?php if($form['cf_status'] == 'No') { echo 'selected' ; } ?>>No</option>
      </select>
      <p><?php _e('Do you want the image to show in your galler?', 'card-flip-image-slideshow'); ?></p>
	  
      <label for="tag-display-order"><strong><?php _e('Order', 'card-flip-image-slideshow'); ?></strong></label>
      <input name="cf_order" type="text" id="cf_order" size="10" value="<?php echo $form['cf_order']; ?>" maxlength="2" />
      <p><?php _e('What order should the image be played in. should it come 1st, 2nd, 3rd, etc.', 'card-flip-image-slideshow'); ?></p>	  
	  
      <input name="cf_id" id="cf_id" type="hidden" value="<?php echo $form['cf_id']; ?>">
      <input type="hidden" name="cf_form_submit" value="yes"/>
      <p class="submit">
        <input name="submit" class="button button-primary" value="<?php _e('Submit', 'card-flip-image-slideshow'); ?>" type="submit" />
        <input name="cancel" class="button button-primary" onclick="_cf_redirect()" value="<?php _e('Cancel', 'card-flip-image-slideshow'); ?>" type="button" />
        <input name="help" class="button button-primary" onclick="_cf_help()" value="<?php _e('Help', 'card-flip-image-slideshow'); ?>" type="button" />
      </p>
	  <?php wp_nonce_field('cf_form_edit'); ?>
    </form>
</div>
</div>