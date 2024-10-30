<?php

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class cardflip_cls_registerhook {
	public static function cardflip_activation() {
	
		global $wpdb;

		add_option('card-flip-image-slideshow', "1.0");

		$charset_collate = '';
		$charset_collate = $wpdb->get_charset_collate();
	
		$cardflip_default_tables = "CREATE TABLE {$wpdb->prefix}cardflip_slideshow (
										cf_id INT unsigned NOT NULL AUTO_INCREMENT,
										cf_image VARCHAR(1024) NOT NULL default '',
										cf_link VARCHAR(1024) NOT NULL default '',
										cf_title VARCHAR(1024) NOT NULL default '',
										cf_target VARCHAR(10) NOT NULL default '_blank',
										cf_order int(11) NOT NULL default '0',
										cf_group VARCHAR(10) NOT NULL default 'Group1',
										cf_status VARCHAR(3) NOT NULL default 'Yes',
										cf_updated TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP, 
										PRIMARY KEY (cf_id)
										) $charset_collate;";
	
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta( $cardflip_default_tables );
		
		$cardflip_default_tablesname = array( 'cardflip_slideshow' );
	
		$cardflip_has_errors = false;
		$cardflip_missing_tables = array();
		foreach($cardflip_default_tablesname as $table_name) {
			if(strtoupper($wpdb->get_var("SHOW TABLES like  '". $wpdb->prefix.$table_name . "'")) != strtoupper($wpdb->prefix.$table_name)) {
				$cardflip_missing_tables[] = $wpdb->prefix.$table_name;
			}
		}
		
		if($cardflip_missing_tables) {
			$errors[] = __( 'These tables could not be created on installation ' . implode(', ',$cardflip_missing_tables), 'horizontal-scrolling-announcements' );
			$cardflip_has_errors = true;
		}
		
		if($cardflip_has_errors) {
			wp_die( __( $errors[0] , 'card-flip-image-slideshow' ) );
			return false;
		} 
		else {
			cardflip_cls_dbquery::cardflip_default();
		}
		
		if ( ! is_network_admin() && ! isset( $_GET['activate-multi'] ) ) {
			set_transient( '_cardflip_activation_redirect', 1, 30 );
		}
		
		return true;
	}

	public static function cardflip_deactivation() {
		// do not generate any output here
	}

	public static function cardflip_adminoptions() {
	
		global $wpdb;
		$current_page = isset($_GET['ac']) ? $_GET['ac'] : '';
		
		switch($current_page) {
			case 'edit':
				require_once(CARDFLIP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-edit.php');
				break;
			case 'add':
				require_once(CARDFLIP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-add.php');
				break;
			default:
				require_once(CARDFLIP_DIR . 'pages' . DIRECTORY_SEPARATOR . 'image-management-show.php');
				break;
		}
	}
	
	public static function cardflip_frontscripts() {
		if (!is_admin()) {
			wp_enqueue_script( 'jquery');
			wp_enqueue_style( 'card-flip-image-slideshow',  plugin_dir_url( __DIR__ ) . '/card-flip-image-slideshow.css','','','');
			wp_enqueue_script( 'card-flip-image-slideshow', plugin_dir_url( __DIR__ ) . '/card-flip-image-slideshow.js');
		}	
	}

	public static function cardflip_addtomenu() {
	
		if (is_admin()) {
			add_options_page( __('Card flip slideshow', 'card-flip-image-slideshow'), 
								__('Card flip slideshow', 'card-flip-image-slideshow'), 'manage_options', 
									'card-flip-image-slideshow', array( 'cardflip_cls_registerhook', 'cardflip_adminoptions' ) );
		}
	}
	
	public static function cardflip_adminscripts() {
	
		if(!empty($_GET['page'])) {
			switch ($_GET['page']) {
				case 'card-flip-image-slideshow':
					wp_register_script( 'cardflip-adminscripts', plugin_dir_url( __DIR__ ) . '/pages/setting.js', '', '', true );
					wp_enqueue_script( 'cardflip-adminscripts' );
					$cardflip_select_params = array(
						'cardflip_image'  		=> __( 'Please enter the image path.', 'cardflip-select', 'card-flip-image-slideshow' ),
						'cardflip_group'  		=> __( 'Please enter the image group.', 'cardflip-select', 'card-flip-image-slideshow' ),
						'cardflip_order'  		=> __( 'Please enter the display order.', 'cardflip-select', 'card-flip-image-slideshow' ),
						'cardflip_numletters'  	=> __( 'Please input numeric and letters only.', 'cardflip-select', 'card-flip-image-slideshow' ),
						'cardflip_delete'  		=> __( 'Do you want to delete this record?', 'cardflip-select', 'card-flip-image-slideshow' ),
					);
					wp_localize_script( 'cardflip-adminscripts', 'cardflip_adminscripts', $cardflip_select_params );
					break;
			}
		}
	}
	
	public static function cardflip_widgetloading() {
		register_widget( 'cardflip_widget_register' );
	}
}

class cardflip_widget_register extends WP_Widget 
{
	function __construct() {
		$widget_ops = array('classname' => 'widget_text cardflip-widget', 'description' => __('Card flip image slideshow', 'card-flip-image-slideshow'), 'card-flip-image-slideshow');
		parent::__construct('card-flip-image-slideshow', __('Card flip image slideshow', 'card-flip-image-slideshow'), $widget_ops);
	}
	
	function widget( $args, $instance ) {
		extract( $args, EXTR_SKIP );
		
		$cf_title 		= apply_filters( 'widget_title', empty( $instance['cf_title'] ) ? '' : $instance['cf_title'], $instance, $this->id_base );
		$cf_group		= $instance['cf_group'];
		$cf_width		= $instance['cf_width'];
		$cf_height		= $instance['cf_height'];
		$cf_autoplay	= $instance['cf_autoplay'];
		$cf_hinge		= $instance['cf_hinge'];
		$cf_duration	= $instance['cf_duration'];
		$cf_pause		= $instance['cf_pause'];
	
		echo $args['before_widget'];
		if (!empty($cf_title)) {
			echo $args['before_title'] . $cf_title . $args['after_title'];
		}
		
		$rand = rand();
		
		$data = array(
			'group' 		=> $cf_group,
			'width' 		=> $cf_width,
			'height' 		=> $cf_height,
			'autoplay' 		=> $cf_autoplay,
			'hingepoint' 	=> $cf_hinge,
			'duration' 		=> $cf_duration,
			'id' 			=> 'cardflip' . $rand,
			'persist' 		=> 'false',
			'cycles' 		=> '2',
			'pause' 		=> $cf_pause,
			'activeclass' 	=> 'string'
		);
		
		cardflip_cls_shortcode::cardflip_render($data);
		
		echo $args['after_widget'];
	}
	
	function update( $new_instance, $old_instance ) {		
		$instance 					= $old_instance;
		$instance['cf_title'] 		= ( ! empty( $new_instance['cf_title'] ) ) ? strip_tags( $new_instance['cf_title'] ) : '';
		$instance['cf_group'] 		= ( ! empty( $new_instance['cf_group'] ) ) ? strip_tags( $new_instance['cf_group'] ) : '';
		$instance['cf_width'] 		= ( ! empty( $new_instance['cf_width'] ) ) ? strip_tags( $new_instance['cf_width'] ) : '';
		$instance['cf_height'] 		= ( ! empty( $new_instance['cf_height'] ) ) ? strip_tags( $new_instance['cf_height'] ) : '';
		$instance['cf_autoplay'] 	= ( ! empty( $new_instance['cf_autoplay'] ) ) ? strip_tags( $new_instance['cf_autoplay'] ) : '';
		$instance['cf_hinge'] 		= ( ! empty( $new_instance['cf_hinge'] ) ) ? strip_tags( $new_instance['cf_hinge'] ) : '';
		$instance['cf_duration'] 	= ( ! empty( $new_instance['cf_duration'] ) ) ? strip_tags( $new_instance['cf_duration'] ) : '';
		$instance['cf_pause'] 		= ( ! empty( $new_instance['cf_pause'] ) ) ? strip_tags( $new_instance['cf_pause'] ) : '';
		return $instance;
	}
	
	function form( $instance ) {
		$defaults = array(
			'cf_title' 		=> '',
		    'cf_group' 		=> '',
			'cf_width' 		=> '200',
			'cf_height' 	=> '150',
			'cf_autoplay' 	=> '',
			'cf_hinge' 		=> '',
			'cf_duration' 	=> '1000',
			'cf_pause' 		=> '2000'
        );
		
		$instance 		= wp_parse_args( (array) $instance, $defaults);
		$cf_title 		= $instance['cf_title'];
        $cf_group 		= $instance['cf_group'];
        $cf_width 		= $instance['cf_width'];
		$cf_height 		= $instance['cf_height'];
		$cf_autoplay 	= $instance['cf_autoplay'];
		$cf_hinge 		= $instance['cf_hinge'];
		$cf_duration 	= $instance['cf_duration'];
		$cf_pause 		= $instance['cf_pause'];
		
		?>
		<p>
			<label for="<?php echo $this->get_field_id('cf_title'); ?>"><?php _e('Title', 'card-flip-image-slideshow'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cf_title'); ?>" name="<?php echo $this->get_field_name('cf_title'); ?>" type="text" value="<?php echo $cf_title; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_group'); ?>"><?php _e('Image group', 'card-flip-image-slideshow'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('cf_group'); ?>" name="<?php echo $this->get_field_name('cf_group'); ?>">
			<option value="">Select</option>
			<?php
			$groups = array();
			$groups = cardflip_cls_dbquery::cardflip_group();
			if(count($groups) > 0) {
				foreach ($groups as $group) {
					?>
					<option value="<?php echo $group['cf_group']; ?>" <?php $this->cf_selected($group['cf_group'] == $cf_group); ?>>
					<?php echo $group['cf_group']; ?>
					</option>
					<?php
				}
			}
			?>
			</select>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_width'); ?>"><?php _e('Width', 'card-flip-image-slideshow'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cf_width'); ?>" name="<?php echo $this->get_field_name('cf_width'); ?>" type="text" value="<?php echo $cf_width; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_height'); ?>"><?php _e('Height', 'card-flip-image-slideshow'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cf_height'); ?>" name="<?php echo $this->get_field_name('cf_height'); ?>" type="text" value="<?php echo $cf_height; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_autoplay'); ?>"><?php _e('Auto play', 'card-flip-image-slideshow'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('cf_autoplay'); ?>" name="<?php echo $this->get_field_name('cf_autoplay'); ?>">
				<option value="true" <?php $this->cf_selected($cf_autoplay == 'true'); ?>>Yes</option>
				<option value="false" <?php $this->cf_selected($cf_autoplay == 'false'); ?>>No</option>
			</select>
        </p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cf_hinge'); ?>"><?php _e('Hinge position', 'card-flip-image-slideshow'); ?></label>
			<select class="widefat" id="<?php echo $this->get_field_id('cf_hinge'); ?>" name="<?php echo $this->get_field_name('cf_hinge'); ?>">
				<option value="top right" <?php $this->cf_selected($cf_hinge == 'top right'); ?>>Top Right</option>
				<option value="top left" <?php $this->cf_selected($cf_hinge == 'top left'); ?>>Top Left</option>
				<option value="bottom right" <?php $this->cf_selected($cf_hinge == 'bottom right'); ?>>Bottom Right</option>
				<option value="bottom left" <?php $this->cf_selected($cf_hinge == 'bottom left'); ?>>Bottom Left</option>
				<option value="50% 60%" <?php $this->cf_selected($cf_hinge == '50% 60%'); ?>>50% 50%</option>
			</select>
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_duration'); ?>"><?php _e('Duration', 'card-flip-image-slideshow'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cf_duration'); ?>" name="<?php echo $this->get_field_name('cf_duration'); ?>" type="text" value="<?php echo $cf_duration; ?>" />
        </p>
		<p>
			<label for="<?php echo $this->get_field_id('cf_pause'); ?>"><?php _e('Pause', 'card-flip-image-slideshow'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cf_pause'); ?>" name="<?php echo $this->get_field_name('cf_pause'); ?>" type="text" value="<?php echo $cf_pause; ?>" />
        </p>
		<?php
	}
	
	function cf_selected($var) {
		if ($var==1 || $var==true) {
			echo 'selected="selected"';
		}
	}
}

class cardflip_cls_shortcode {
	public function __construct() {
	}
	
	public static function cardflip_shortcode( $atts ) {
		ob_start();
		if (!is_array($atts)) {
			return '';
		}
		
		//[cardflip group="Group1" width="200" height="150" autoplay="true" hingepoint="top right" duration="1000" cycles="2" pause="2000"]
		$atts = shortcode_atts( array(
				'group'			=> '',
				'width'			=> '',
				'height'		=> '',
				'autoplay'		=> '',
				'hingepoint'	=> '',
				'cycles'		=> '',
				'duration'		=> ''
			), $atts, 'card-flip-image-slideshow' );

		$group 		= isset($atts['group']) ? $atts['group'] : 'Group1';
		$width 		= isset($atts['width']) ? $atts['width'] : '250';
		$height 	= isset($atts['height']) ? $atts['height'] : '100';
		$autoplay 	= isset($atts['autoplay']) ? $atts['autoplay'] : 'true';
		$hingepoint = isset($atts['hingepoint']) ? $atts['hingepoint'] : 'top right';
		$duration 	= isset($atts['duration']) ? $atts['duration'] : '1000';
		$cycles 	= isset($atts['cycles']) ? $atts['cycles'] : '2';
		$pause 		= isset($atts['pause']) ? $atts['pause'] : '2000';
		
		$rand = rand();
		
		$data = array(
			'group' 		=> $group,
			'width' 		=> $width,
			'height' 		=> $height,
			'autoplay' 		=> $autoplay,
			'hingepoint' 	=> $hingepoint,
			'duration' 		=> $duration,
			'id' 			=> 'cardflip' . $rand,
			'persist' 		=> 'false',
			'cycles' 		=> $cycles,
			'pause' 		=> $pause,
			'activeclass' 	=> 'string'
		);
		
		self::cardflip_render( $data );

		return ob_get_clean();
	}
	
	public static function cardflip_render( $data = array() ) {	
		
		$cf = "";
		
		if(count($data) == 0) {
			return $cf;
		}

		$group 		= $data['group'];
		$width 		= $data['width'];
		$height 	= $data['height'];
		$autoplay 	= $data['autoplay'];
		$hingepoint	= $data['hingepoint'];
		$duration	= $data['duration'];
		$id			= $data['id'];
		$persist	= $data['persist'];
		$cycles		= $data['cycles'];
		$pause		= $data['pause'];
		$activeclass= $data['activeclass'];
		
		$datas = cardflip_cls_dbquery::cardflip_select_bygroup($group);
		
		if(count($datas) > 0 ) {
			$url = plugin_dir_url( __DIR__ ) . '/inc';
			$prev = "'prev'";
			$next = "'next'";
			
			$cf = '<div id="' . $id . '" class="stackcontainer" style="width:' . $width . 'px;height:' . $height . 'px;max-width:100%;min-height:100px;">';
			foreach ($datas as $data) {
				$cf .= '<div class="inner"> <img src="' . $data['cf_image'] . '" /> </div>';
			}
			$cf .= '</div>';
			$cf .= '<div style="padding-top:5px;padding-bottom:10px;clear:both;width:' . $width . 'px;max-width:100%;">';
				$cf .= '<span style="float:left;">';
					$cf .= '<img onClick="javascript: ' . $id . '.navigate(' . $prev . ')" name="Previous" id="Previous" style="cursor:pointer;" src="' . $url . '/left.jpg"" />';
				$cf .= '</span>';
				$cf .= '<span style="float:right;">';
					$cf .= '<img onClick="javascript: ' . $id . '.navigate(' . $next . ')" name="Next" id="Next" style="cursor:pointer;" src="' . $url . '/right.jpg"" />';
				$cf .= '</span>';
			$cf .= '</div>';
			
			$cf .= '<script>';
			$cf .= 'var ' . $id . ' = new carddeckslideshow({';
				$cf .= "id: '" . $id . "',";
				$cf .= 'autoplay: ' . $autoplay . ',';
				$cf .= "hingepoint: '" . $hingepoint . "',";
				$cf .= 'persist: false,';
				$cf .= 'cycles: 2,';
				$cf .= 'fxduration: ' . $duration. ',';
				$cf .= 'pause: ' . $pause . ',';
				$cf .= 'activeclass: "' . $id . '"';
			$cf .= '})';
			$cf .= '</script>';
			$cf .= '<br />';
		}
		echo $cf;
	}
}
?>