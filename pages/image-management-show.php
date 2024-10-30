<?php if(preg_match('#' . basename(__FILE__) . '#', $_SERVER['PHP_SELF'])) { die('You are not allowed to call this page directly.'); } ?>
<?php
if (isset($_POST['frm_cf_display']) && $_POST['frm_cf_display'] == 'yes') {
	$did = isset($_GET['did']) ? intval($_GET['did']) : '0';
	if(!is_numeric($did)) { 
		die('<p>Are you sure you want to do this?</p>'); 
	}
	
	$cf_success = '';
	$cf_success_msg = false;
	$result = cardflip_cls_dbquery::cardflip_count($did);
	
	if ($result != '1') {
		?><div class="error fade"><p><strong><?php _e('Oops, selected details doesnt exist', 'card-flip-image-slideshow'); ?></strong></p></div><?php
	}
	else {
		if (isset($_GET['ac']) && $_GET['ac'] == 'del' && isset($_GET['did']) && $_GET['did'] != '') {
			check_admin_referer('cf_form_show');
			cardflip_cls_dbquery::cardflip_delete($did);
			$cf_success_msg = true;
			$cf_success = __('Selected record was successfully deleted.', 'card-flip-image-slideshow');
		}
	}
	
	if ($cf_success_msg == true) {
		?><div class="updated fade"><p><strong><?php echo $cf_success; ?></strong></p></div><?php
	}
}
?>
<div class="wrap">
    <h2><?php _e('Card flip image slideshow', 'card-flip-image-slideshow'); ?>
	<a class="add-new-h2" href="<?php echo CARDFLIP_ADMIN_URL; ?>&amp;ac=add"><?php _e('Add New', 'card-flip-image-slideshow'); ?></a></h2>
    <div class="tool-box">
	<?php
	$myData = array();
	$myData = cardflip_cls_dbquery::cardflip_select_bygroup("");
	?>
	<form name="frm_cf_display" method="post">
      <table width="100%" class="widefat" id="straymanage">
        <thead>
          <tr>
		  	<th scope="col"><?php _e('Image', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Title', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Open Link', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Order', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Group', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Status', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Action', 'card-flip-image-slideshow'); ?></th>
          </tr>
        </thead>
		<tfoot>
          <tr>
		  	<th scope="col"><?php _e('Image', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Title', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Open Link', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Order', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Group', 'card-flip-image-slideshow'); ?></th>
            <th scope="col"><?php _e('Status', 'card-flip-image-slideshow'); ?></th>
			<th scope="col"><?php _e('Action', 'card-flip-image-slideshow'); ?></th>
          </tr>
        </tfoot>
		<tbody>
		<?php 
		$i = 0;
		if(count($myData) > 0 ) {
			foreach ($myData as $data) {
				?>
				<tr class="<?php if ($i&1) { echo'alternate'; } else { echo ''; }?>">
					<td>
						<a href="<?php echo $data['cf_image']; ?>" target="_blank"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/image-icon.png"  /></a>
						<?php if($data['cf_link'] <> '') { ?>
						<a href="<?php echo $data['cf_link']; ?>" target="_blank"><img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/link-icon.gif"  /></a>
						<?php } ?>
					</td>
					<td><?php echo $data['cf_title']; ?></td>
					<td><?php echo cardflip_cls_dbquery::cardflip_common_text($data['cf_target']); ?></td>
					<td><?php echo $data['cf_order']; ?></td>
					<td><?php echo $data['cf_group']; ?></td>
					<td><?php echo cardflip_cls_dbquery::cardflip_common_text($data['cf_status']); ?></td>
					<td>
						<a title="Edit" href="<?php echo CARDFLIP_ADMIN_URL; ?>&amp;ac=edit&did=<?php echo $data['cf_id']; ?>">
							<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/edit.gif" alt="Edit" />
						</a>
						&nbsp;
						<a title="Delete" onClick="javascript:_cf_delete('<?php echo $data['cf_id']; ?>')" href="javascript:void(0);">
							<img src="<?php echo plugin_dir_url( __DIR__ ); ?>/inc/delete.gif" alt="Delete" />
						</a>
					</td>
				</tr>
				<?php 
				$i = $i+1; 
			} 
		}
		else {
			?><tr><td colspan="7" align="center"><?php _e('No records available', 'card-flip-image-slideshow'); ?></td></tr><?php 
		}
		?>
		</tbody>
        </table>
		<?php wp_nonce_field('cf_form_show'); ?>
		<input type="hidden" name="frm_cf_display" value="yes"/>
      </form>	
	  <div class="tablenav bottom">
	  <a href="<?php echo CARDFLIP_ADMIN_URL; ?>&amp;ac=add">
	  <input class="button button-primary" type="button" value="<?php _e('Add New', 'card-flip-image-slideshow'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2019/12/15/card-flip-image-slideshow-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Short Code', 'card-flip-image-slideshow'); ?>" /></a>
	  <a target="_blank" href="http://www.gopiplus.com/work/2019/12/15/card-flip-image-slideshow-wordpress-plugin/">
	  <input class="button button-primary" type="button" value="<?php _e('Help', 'card-flip-image-slideshow'); ?>" /></a>
	  </div>
	</div>
</div>