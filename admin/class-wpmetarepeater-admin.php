<?php
class Wpmetarepeater_Admin {
	
	private $plugin_name;
	private $version;
	
	public function __construct( $plugin_name, $version ) {
		$this->plugin_name = $plugin_name;
		$this->version = $version;
		
		add_action( 'admin_menu', array( $this,'wpmr_register_setting_page' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpmr_add_meta_box' ) );
		add_action( 'save_post', array( $this, 'wpmr_save' ) );		
		add_action('wp_ajax_nopriv_wpmetarepeater_ajax', array( $this, 'wpmetarepeater_ajax' ));
		add_action('wp_ajax_wpmetarepeater_ajax', array( $this, 'wpmetarepeater_ajax' ));		
		
	}
	
	//Admin menu
	public function wpmr_register_setting_page(){
		add_menu_page( 
			__( 'Wpmr setting', 'wpmetarepeater' ),
			'WPMR setting'
			,'manage_options'
			,'wpmr post types'
			,array( $this, 'wpmr_post_typeslist' )
			,'dashicons-admin-plugins',
			15
		); 
	}
	
	//Admin menu calback
	public function wpmr_post_typeslist(){
		$all_items_post_types = get_post_types();
		$exist_posttypes = array(
								'page'
								,'attachment'
								,'revision'
								,'nav_menu_item'
								,'custom_css'
								,'customize_changeset'
							); 
							
		$post_types = array_diff($all_items_post_types,$exist_posttypes);
		
		//Options save
		$is_valid_nonce_option = ( isset( $_POST['save_wpmr_post_typeslist_nonce'] )  && wp_verify_nonce( $_POST['fade_slider_nonce_option'], basename(__FILE__) ) ) ? 'true' : 'false';
		
		if( !$is_valid_nonce_option ){		
		 return;	 
		}
		if( isset( $_POST['wpmr_submit'] ) ){

			$selected_posts = $_POST['wpmr_posts'];
			if( !empty( $selected_posts ) ){
				foreach( $selected_posts as $selected_post ){
					$wpmr_posts[] = sanitize_text_field( $selected_post );
				}
				update_option( 'wpmr_posts', $wpmr_posts );
			}else{
				update_option( 'wpmr_posts', ' '  );
			}
		}
	?>
		<div class="wpmr_posts_list wrap">
			<h1 class="wp-heading-inline"><?php _e( 'Choose Post Types', 'wpmetarepeater' ); ?></h1>
			<form method="POST">
				<ul class="wpmr_list">
					<?php
						$wpmr_get_posts = get_option('wpmr_posts');
						wp_nonce_field( 'save_wpmr_post_typeslist', 'save_wpmr_post_typeslist_nonce' );
						$j = 0;
						foreach( $post_types as $post_type ){ 
							?>
							<li><input type="checkbox" <?php if( is_array( $wpmr_get_posts ) ){ if( in_array( $post_type, $wpmr_get_posts ) ){ ?>checked="checked"<?php } } ?> id="<?php echo $post_type.'-'.$j; ?>" name="wpmr_posts[]" value="<?php echo $post_type; ?>"><label for="<?php echo $post_type.'-'.$j; ?>"><?php _e( $post_type, 'wpmetarepeater' ); ?></label></li>
						<?php $j++; } ?>
				</ul>
				<input type="submit" value="Save" name="wpmr_submit" class="button button-primary">				
			</form>
		</div>
	<?php	
	}
	
	//Add Meta
	public function wpmr_add_meta_box( $post_type ) {
		
		$post_types = get_option('wpmr_posts');
		if( is_array( $post_types ) ){
			if ( in_array( $post_type, $post_types )) {
				add_meta_box(
					'wpmetarepeater_metabox_information'
					,__( 'Add Custom Fields', 'wpmetarepeater' )
					,array( $this, 'wpmetarepeater_meta_box_content' )
					,$post_type
					,'advanced'
					,'default'
				);
			}
		}
	}
	
	//Get Meta Loop	
	public function get_wpmr_meta( $wpmr_label_key, $wpmr_input_type, $wpmr_post_id ){
		
		foreach( $wpmr_label_key as $key=>$mkey ){ 
		
			$m_value = get_post_meta($wpmr_post_id,'wpmr_'.sanitize_title($mkey),true);
			$wp_sani_meta = sanitize_key($mkey);
			
			if( $wpmr_input_type[$key] == 3 ){ 
				$wpmr_form_info = "<div id='wpmr_field-$key' class='wpmr-defaultfield-row'><span class='remover' onclick='wpmr_remove_field(this);' data-wpmr_post_id='$wpmr_post_id' data-key='$mkey'>Remove</span><div class='col-label'><label>$mkey</label></div><div class='col-input'><textarea name='wpmr_areafield[$wp_sani_meta]' class='edd-input' rows='4'>$m_value</textarea></div></div>";
			}else if( $wpmr_input_type[$key] == 1 ){
				$wpmr_form_info = "<div id='wpmr_field-$key' class='wpmr-defaultfield-row'><span class='remover' onclick='wpmr_remove_field(this);' data-wpmr_post_id='$wpmr_post_id' data-key='$mkey'>Remove</span><div class='col-label'><label>$mkey </label></div><div class='col-input'><input type='text' name='wpmr_txtfield[$wp_sani_meta]' class='edd-input' value='$m_value'/></div></div>";
			}else{
				$wpmr_form_info = "<div id='wpmr_field-$key' class='wpmr-defaultfield-row'><span class='remover' onclick='wpmr_remove_field(this);' data-wpmr_post_id='$wpmr_post_id' data-key='$mkey'>Remove</span><div class='col-label'><label>$mkey </label></div><div class='col-input'><input type='text' name='wpmr_urlfield[$wp_sani_meta]' class='edd-input' value='$m_value'/></div></div>";
			}
			echo $wpmr_form_info; 
		}	
	}
	
	//Meta box callback
	public function wpmetarepeater_meta_box_content( $post ){
		
		wp_nonce_field( 'wpmetarepeater_meta_box', 'wpmetarepeater_meta_box_nonce' );
		$wpmr_input_type = get_post_meta($post->ID,'wpmr_input_type',true);
		$wpmr_label_key = get_post_meta($post->ID,'wpmr_label_key',true);		
		$wpmr_post_id = $post->ID;		
		?>
		<div id="wpmr-info" class="wpmr-wrapper">
			<div class="wpmr-addfield-row">
				<div class="col-first">
					<input type="hidden" name="wpmr_post_id" id="wpmr_post_id" value="<?php echo $post->ID; ?>" />
					<input type="text" name="wpmr_label" id="wpmr_label_key" placeholder="Name" class="edd-input" />
				</div>
				<div class="col-md">
					<select name="wpmr_type" id="wpmr_input_type" class="edd-input">
						<option value=""><?php _e( 'Select Type', 'wpmetarepeater' );?></option>
						<option value="1"><?php _e( 'Text', 'wpmetarepeater' );?></option>
						<option value="2"><?php _e( 'URL', 'wpmetarepeater' );?></option>
						<option value="3"><?php _e( 'Text area' ,'wpmetarepeater' );?></option>
					</select>
				</div>
				<div class="col-last">
					<input type="button" id="label_add" class="button-secondary edd_add_repeatable" value="<?php _e( 'Add', 'wpmetarepeater' ); ?>" />
				</div>
			</div>				
			<div id="wpmr_newfields">
			
				<?php if( $wpmr_label_key && $wpmr_input_type ){ ?>
						<div class="wpmr_newfields_wrapper">
							<?php $this->get_wpmr_meta( $wpmr_label_key, $wpmr_input_type, $wpmr_post_id ); // Meta box loop ?>
						</div>
				<?php } ?>
				
			</div>
		<?php 
	}
	
	//Meta Save
	public function wpmr_save( $post_id ) {

		if ( ! isset( $_POST['wpmetarepeater_meta_box_nonce'] ) )
			return $post_id;

		$nonce = $_POST['wpmetarepeater_meta_box_nonce'];
		if ( ! wp_verify_nonce( $nonce, 'wpmetarepeater_meta_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;
		
		if( isset( $_POST['wpmr_numberfield'] ) ){
			foreach( $_POST['wpmr_numberfield'] as $key=>$wpmr_numbers ){
				$wpmr_number_value = absint($wpmr_numbers);
				update_post_meta( $post_id, 'wpmr_'.$key, $wpmr_number_value );
			}			
		}
		
		if( isset( $_POST['wpmr_txtfield'] ) ){
			foreach( $_POST['wpmr_txtfield'] as $key=>$wpmr_titles ){
				$wpmr_titles_value = sanitize_text_field($wpmr_titles);
				update_post_meta( $post_id, 'wpmr_'.$key, $wpmr_titles_value );
			}			
		}
		if( isset( $_POST['wpmr_urlfield'] ) ){
			foreach( $_POST['wpmr_urlfield'] as $key=>$wpmr_urls ){
				$wpmr_urls_value = esc_url($wpmr_urls,array('http', 'https'));
				update_post_meta( $post_id, 'wpmr_'.$key, $wpmr_urls_value );
			}			
		}
		if( isset( $_POST['wpmr_areafield'] ) ){
			foreach( $_POST['wpmr_areafield'] as $key=>$wpmr_textarea ){
				$wpmr_textarea_value = sanitize_text_field($wpmr_textarea);
				update_post_meta( $post_id, 'wpmr_'.$key, $wpmr_textarea_value );
			}			
		}		
	}
	
	//Enqueue scripts
	public function wpmr_enqueue_scripts() {
		
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/wpmetarepeater-admin.css', array(), $this->version, 'all' );
		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/wpmetarepeater-admin.js', array( 'jquery' ), $this->version, false );
		wp_localize_script($this->plugin_name, 'ajax_var', array(
			'ajax_url' => admin_url('admin-ajax.php'),
			'nonce' => wp_create_nonce('ajax-nonce')
		));	
	}

	//Ajax
	public function wpmetarepeater_ajax( ) {
		
		if( $_POST['mode'] == 'wpmr_meta_add' ){	//Add Meta			
		
			$wpmr_label_key = strtolower($_POST['wpmr_label_key']);
			$wpmr_input_type = $_POST['wpmr_input_type'];
			$wpmr_post_id = $_POST['wpmr_post_id'];
			
			$get_wpmr_input_types = get_post_meta($wpmr_post_id,'wpmr_input_type',true);
			$get_wpmr_label_keys = get_post_meta($wpmr_post_id,'wpmr_label_key',true);
			
			if( !empty( $get_wpmr_input_types ) && !empty( $get_wpmr_label_keys ) ){
				if( in_array($wpmr_label_key ,$get_wpmr_label_keys) ){
					
					echo $label_error = '<p>Label already exist !</p>';
					$this->get_wpmr_meta( $get_wpmr_label_keys, $get_wpmr_input_types, $wpmr_post_id ); // Meta box loop	
				}else{					
					$wpmr_input_type_array = array($wpmr_input_type);
					$wpmr_label_key_array = array($wpmr_label_key);
					$wpmr_save_input = array_merge( $get_wpmr_input_types,$wpmr_input_type_array );
					$wpmr_save_key = array_merge( $get_wpmr_label_keys,$wpmr_label_key_array );
					
					$update_wpmr_input = update_post_meta($wpmr_post_id,'wpmr_input_type',$wpmr_save_input);
					$update_wpmr_key = update_post_meta($wpmr_post_id,'wpmr_label_key',$wpmr_save_key);
					
					if( $update_wpmr_input && $update_wpmr_key ){						
						$get_wpmr_input_types = get_post_meta($wpmr_post_id,'wpmr_input_type',true);
						$get_wpmr_label_keys = get_post_meta($wpmr_post_id,'wpmr_label_key',true);						
						$this->get_wpmr_meta( $get_wpmr_label_keys, $get_wpmr_input_types, $wpmr_post_id ); // Meta box loop						
					}				
				}							
			}else{
				// If No Data then add meta directly no merge					
				$wpmr_input_type_array = array($wpmr_input_type);
				$wpmr_label_key_array = array($wpmr_label_key);				
				$add_wpmr_input = update_post_meta( $wpmr_post_id,'wpmr_input_type',$wpmr_input_type_array );
				$add_wpmr_key = update_post_meta( $wpmr_post_id,'wpmr_label_key',$wpmr_label_key_array );
				
				if( $add_wpmr_input && $add_wpmr_key ){					
					$get_wpmr_input_types = get_post_meta($wpmr_post_id,'wpmr_input_type',true);
					$get_wpmr_label_keys = get_post_meta($wpmr_post_id,'wpmr_label_key',true);
					$this->get_wpmr_meta( $get_wpmr_label_keys, $get_wpmr_input_types, $wpmr_post_id ); // Meta box loop					
				}
			}			
		}elseif( $_POST['mode'] == 'wpmr_meta_clear' ){	//Delete or Remove
		
			$wpmr_post_id = $_POST['wpmr_post_id'];
			
			$get_inputs = get_post_meta($wpmr_post_id,'wpmr_input_type',true);
			$get_mkeys = get_post_meta($wpmr_post_id,'wpmr_label_key',true);
			
			$combain_meta = array_combine($get_mkeys, $get_inputs);			
			unset($combain_meta[$_POST['delete_key']]);
			
			$wpmr_delete = delete_post_meta($wpmr_post_id,'wpmr_'.$_POST['delete_key']); //Delete Meta Field				
			$remains = $combain_meta;
			foreach( $remains as $key=>$remain ){
				$wpr_input[] = $remain;
				$wpr_key[] = $key;
			}
			
			if( !empty( $wpr_key ) ){
				update_post_meta($wpmr_post_id,'wpmr_input_type',$wpr_input);
				update_post_meta($wpmr_post_id,'wpmr_label_key',$wpr_key);
			}else{																	//Delete master meta fields
				delete_post_meta( $wpmr_post_id, 'wpmr_input_type' );
				delete_post_meta( $wpmr_post_id, 'wpmr_label_key' );
			}
			
			$get_wpmr_input_types = get_post_meta($wpmr_post_id,'wpmr_input_type',true);
			$get_wpmr_label_keys = get_post_meta($wpmr_post_id,'wpmr_label_key',true);
			$this->get_wpmr_meta( $get_wpmr_label_keys, $get_wpmr_input_types, $wpmr_post_id ); // Meta box loop	
		}
		die();
	}
}