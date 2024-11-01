<?php
defined('ABSPATH') or die("Cannot access pages directly.");


class SliderCatMenuPage
{
    private $options;

	public function __construct()
	{
		add_action( 'admin_menu', array( $this, 'sc_add_general_settings' ) );
		add_action( 'admin_init', array( $this, 'sc_general_settings_init' ) );
		add_action( 'admin_init', array( $this, 'sc_general_defaults' ) );
		if ( function_exists( 'add_image_size' ) ) {
		add_action( 'admin_init', array( $this, 'sc_add_thumb_settings') );
		}
		add_action( 'add_meta_boxes', array( $this, 'sc_add_post_meta' ) );
		add_action( 'save_post', array( $this, 'sc_save_post_meta' ) );
		add_action( 'slidercat_add_form_fields', array( $this, 'sc_add_tax_meta', ) );
		add_action( 'slidercat_edit_form_fields', array( $this, 'sc_edit_tax_meta' ) );
        add_action( 'edited_slidercat', array( $this, 'sc_save_tax_meta' ), 10, 2 );
        add_action( 'create_slidercat', array( $this, 'sc_save_tax_meta' ), 10, 2 );
		add_action( 'admin_footer-edit-tags.php', array( $this, 'sc_remove_cat_tag' ), 10, 1 );
		add_action( 'admin_footer-post.php', array( $this, 'sc_remove_space' ), 10, 1 );
		add_action( 'admin_footer-post-new.php', array( $this, 'sc_remove_space' ), 10, 1 );
		add_action('admin_head', array( $this, 'unused_meta_boxes'));

			
    }
	
/****************
	* General Settings
	*/
	public function sc_add_general_settings()
	{		
		add_submenu_page(
			'edit.php?post_type=post_type_slider',
			'settings',
			'General Settings',
			'manage_options',
			'slidercat-settings', 
			 array(&$this,'sc_create_general_settings_page'));
	}

	public function sc_create_general_settings_page()
	{
		$this->options = get_option( 'slidercat_options');
		?>
		<div class="wrap">
			<?php screen_icon(); ?>
			<h2><?php _e('sliderCat Settings', 'slidercat'); ?></h2>       
			<form method="post" action="options.php">
			<?php
				settings_fields( 'slidercat_option' );   
				do_settings_sections( 'slidercat-settings' );
				submit_button(); 
			?>
			</form>
			</div>
		<?php
	}

	public function sc_general_settings_init()
	{   
		load_plugin_textdomain('slidercat', FALSE, dirname(plugin_basename(__FILE__)).'/languages/');
		global $id, $title, $callback, $page;     
		register_setting(
			'slidercat_option',
			'slidercat_options',
			array( $this, 'sanitize' )
		);
		
		add_settings_field( $id, $title, $callback, $page, $section = 'default', $args = array() );
		
		add_settings_section('setting_section_id', __("sliderCat Scripts", 'slidercat'), array( $this, 'print_section_info' ), 'slidercat-settings');
		add_settings_field('sc_enable_css', __("Enable Slidercat CSS", 'slidercat'), array( $this, 'sc_css_cb' ), 'slidercat-settings', 'setting_section_id');
		add_settings_field('sc_enable_anim', __("Enable Animate CSS", 'slidercat'), array( $this, 'sc_anim_cb' ), 'slidercat-settings', 'setting_section_id');
		add_settings_field('sc_enable_flex', __("Enable FlexSlider Script", 'slidercat'), array( $this, 'sc_flex_cb' ), 'slidercat-settings', 'setting_section_id');
		add_settings_field('sc_enable_ease', __("Enable Easing  Script", 'slidercat'), array( $this, 'sc_ease_cb' ), 'slidercat-settings', 'setting_section_id');	
		add_settings_field('sc_enable_mw', __("Enable Mousewheel  Script", 'slidercat'), array( $this, 'sc_mw_cb' ), 'slidercat-settings', 'setting_section_id');

		add_settings_section('setting_section_thumbs', __("sliderCat Image Sizes", 'slidercat'), array( $this, 'print_section_thumbs' ), 'slidercat-settings');
		add_settings_field('sc_thumb_wdth', __("Thumb Image size", 'slidercat'), array( $this, 'sc_thumb_cb' ), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_thumb_hght', __("Thumbnail Height", 'slidercat'), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_thumb_crop', __("Thumbnail Crop", 'slidercat'), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_medium_wdth', __("Medium Image size", 'slidercat'), array( $this, 'sc_medium_cb' ), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_medium_hght', __("Thumbnail Height", 'slidercat'), 'slidercat-settings','setting_section_thumbs');
		add_settings_field('sc_medium_crop', __("Thumbnail Crop", 'slidercat'), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_large_wdth', __("Large Image size", 'slidercat'), array( $this, 'sc_large_cb' ), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_large_hght', __("Thumbnail Height", 'slidercat'), 'slidercat-settings', 'setting_section_thumbs');
		add_settings_field('sc_large_crop', __("Thumbnail Crop", 'slidercat'), 'slidercat-settings', 'setting_section_thumbs');
	}

	public function sanitize( $input )
	{
		$new_input = array();
		
		if( isset( $input['sc_enable_css'] ) )
			$new_input['sc_enable_css'] = sanitize_text_field( $input['sc_enable_css'] );
		if( isset( $input['sc_enable_anim'] ) )
			$new_input['sc_enable_anim'] = sanitize_text_field( $input['sc_enable_anim'] );
		if( isset( $input['sc_enable_flex'] ) )
			$new_input['sc_enable_flex'] = sanitize_text_field( $input['sc_enable_flex'] );
		if( isset( $input['sc_enable_mw'] ) )
			$new_input['sc_enable_mw'] = sanitize_text_field( $input['sc_enable_mw'] );
		if( isset( $input['sc_enable_ease'] ) )
			$new_input['sc_enable_ease'] = sanitize_text_field( $input['sc_enable_ease'] );
		if( isset( $input['sc_thumb_wdth'] ) )
			$new_input['sc_thumb_wdth'] = absint( $input['sc_thumb_wdth'] );
		if( isset( $input['sc_thumb_hght'] ) )
			$new_input['sc_thumb_hght'] = absint( $input['sc_thumb_hght'] );
		if( isset( $input['sc_thumb_crop'] ) )
			$new_input['sc_thumb_crop'] = sanitize_text_field( $input['sc_thumb_crop'] );
		if( isset( $input['sc_medium_wdth'] ) )
			$new_input['sc_medium_wdth'] = absint( $input['sc_medium_wdth'] );
		if( isset( $input['sc_medium_hght'] ) )
			$new_input['sc_medium_hght'] = absint( $input['sc_medium_hght'] );
		if( isset( $input['sc_medium_crop'] ) )
			$new_input['sc_medium_crop'] = sanitize_text_field( $input['sc_medium_crop'] );
		if( isset( $input['sc_large_wdth'] ) )
			$new_input['sc_large_wdth'] = absint( $input['sc_large_wdth'] );
		if( isset( $input['sc_large_hght'] ) )
			$new_input['sc_large_hght'] = absint( $input['sc_large_hght'] );
		if( isset( $input['sc_large_crop'] ) )
			$new_input['sc_large_crop'] = sanitize_text_field( $input['sc_large_crop'] );	
		
		return $new_input;
	}
	
	public function print_section_info()
	{
		echo __("Enable or Disable CSS and jQuery Scripts. jQuery scripts will not be loaded if shortcode is not present. However CSS will be loaded on all pages. If you plan to use slider only on few pages you can disable it here and include it manually in your template.", 'slidercat');
    }
	
	public function sc_css_cb()
	{
		printf('<select id="slidercat_options[sc_enable_css]" name="slidercat_options[sc_enable_css]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_enable_css'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_enable_css'] ), 'false').'>false</option>
				</select> ');
		echo __("This is mandatory style, set to false only if you plan to include it manually.", 'slidercat');
	}	
	
	public function sc_anim_cb()
	{
		printf('<select id="slidercat_options[sc_enable_anim]" name="slidercat_options[sc_enable_anim]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_enable_anim'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_enable_anim'] ), 'false').'>false</option>
				</select> ');
		echo __("False will disable animations on title, content and excerpt.", 'slidercat');
	}	
	
	public function sc_flex_cb()
	{
		printf('<select id="slidercat_options[sc_enable_flex]" name="slidercat_options[sc_enable_flex]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_enable_flex'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_enable_flex'] ), 'false').'>false</option>
				</select> ');
		echo __("This is mandatory script, set to false only if you plan to include it manually.", 'slidercat');
	}	
	
	public function sc_mw_cb()
	{
		printf('<select id="slidercat_options[sc_enable_mw]" name="slidercat_options[sc_enable_mw]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_enable_mw'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_enable_mw'] ), 'false').'>false</option>
				</select> ');
		echo __("Enable Mousewheel actions (start animation on scroll).", 'slidercat');
	}	
	
	public function sc_ease_cb()
	{
		printf('<select id="slidercat_options[sc_enable_ease]" name="slidercat_options[sc_enable_ease]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_enable_ease'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_enable_ease'] ), 'false').'>false</option>
				</select> ');
		 echo __("True will enable easing effects on slide images .", 'slidercat');
	}
	public function print_section_thumbs()
	{
		 $supportedTypes = get_theme_support( 'post-thumbnails' );

    if( $supportedTypes === false ) {
		echo __("Your theme does not supports thumbnails or does not use add_theme_support function. To enable image sizes please add this function to your theme functions.php ( add_theme_support('post-thumbnails'); ).", 'slidercat');
		}else {
		echo __("To disable any image size option set width to 0. This will work only for newly uploaded images.", 'slidercat');
		
		}
		
    }	
	
	public function sc_thumb_cb()
	{   
		printf('<label for="slidercat_options[sc_thumb_wdth]"></label>
				Width: <input id="slidercat_options[sc_thumb_wdth]" class="small-text" type="number" min="0" step="1"  name="slidercat_options[sc_thumb_wdth]" value="'. esc_attr( $this->options['sc_thumb_wdth'] ) .'" />
				Height: <input id="slidercat_options[sc_thumb_hght]" class="small-text" type="number" min="0" step="1" name="slidercat_options[sc_thumb_hght]" value="'. esc_attr( $this->options['sc_thumb_hght'] ) .'" />
				Crop: <select id="slidercat_options[sc_thumb_crop]" name="slidercat_options[sc_thumb_crop]" />' );
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_thumb_crop'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_thumb_crop'] ), 'false').'>false</option>
				</select>');
	}
    
	public function sc_medium_cb()
	{   
		printf('<label for="slidercat_options[sc_medium_wdth]"></label>
				Width: <input id="slidercat_options[sc_medium_wdth]" class="small-text" type="number" min="0" step="1"  name="slidercat_options[sc_medium_wdth]" value="'. esc_attr( $this->options['sc_medium_wdth'] ) .'" />
				Height: <input id="slidercat_options[sc_medium_hght]" class="small-text" type="number" min="0" step="1" name="slidercat_options[sc_medium_hght]" value="'. esc_attr( $this->options['sc_medium_hght'] ) .'" />
				Crop: <select id="slidercat_options[sc_medium_crop]" name="slidercat_options[sc_medium_crop]" />');
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_medium_crop'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_medium_crop'] ), 'false').'>false</option>
				</select>');
	}
        
    public function sc_large_cb()
	{   
	    printf('<label for="slidercat_options[sc_large_wdth]"></label>
				Width: <input id="slidercat_options[sc_large_wdth]" class="small-text" type="number" min="0" step="1"  name="slidercat_options[sc_large_wdth]" value="'. esc_attr( $this->options['sc_large_wdth'] ) .'" />
				Height: <input id="slidercat_options[sc_large_hght]" class="small-text" type="number" min="0" step="1" name="slidercat_options[sc_large_hght]" value="'. esc_attr( $this->options['sc_large_hght'] ) .'" />
				Crop: <select id="slidercat_options[sc_large_crop]" name="slidercat_options[sc_large_crop]" />');
		printf('<option value="true" '.selected( esc_attr( $this->options['sc_large_crop'] ), 'true').'>true</option>
				<option value="false" '.selected( esc_attr( $this->options['sc_large_crop'] ), 'false').'>false</option>
				</select> ');
	}
	
	public function sc_add_thumb_settings()
	{		
		$this->options = get_option( 'slidercat_options');
		$width_thumb = esc_attr( $this->options['sc_thumb_wdth'] );
		if ($width_thumb != 0) {
			$height_thumb = esc_attr( $this->options['sc_thumb_hght'] );
			$crop_thumb = esc_attr( $this->options['sc_thumb_crop'] );
			add_image_size( 'slidercat-thumb', $width_thumb, $height_thumb, $crop_thumb );	
		}
		
		$width_medium = esc_attr( $this->options['sc_medium_wdth'] );
		if ($width_medium != 0) {
			$height_medium = esc_attr( $this->options['sc_medium_hght'] );
			$crop_medium = esc_attr( $this->options['sc_medium_crop'] );
			add_image_size( 'slidercat-medium', $width_medium, $height_medium, $crop_medium );
		}
		
		$width_large = esc_attr( $this->options['sc_large_wdth'] );
		if ($width_large != 0) {
			$height_large = esc_attr( $this->options['sc_large_hght'] );
			$crop_large = esc_attr( $this->options['sc_large_crop'] );
			add_image_size( 'slidercat-large', $width_large, $height_large, $crop_large );
		}
	}

	public function sc_general_defaults() {
	$slidercat_options = get_option( 'slidercat_options' );

		$default = array(
				'sc_enable_anim' => 'true',
				'sc_enable_css' => 'true',
				'sc_enable_flex' => 'true',
				'sc_enable_mw' => 'false',
				'sc_enable_ease' => 'false',
				'sc_thumb_wdth' => '1280',
				'sc_thumb_hght' => '640',
				'sc_thumb_crop' => 'true',
				'sc_medium_wdth' => '0',
				'sc_medium_hght' => '0',
				'sc_medium_crop' => 'false',
				'sc_large_wdth' => '0',
				'sc_large_hght' => '0',
				'sc_large_crop' => 'false',
			);

		if ( get_option('slidercat_options') == false ) {	
			update_option( 'slidercat_options', $default );		
		}
	}
	
	// End General Settings
	
	public function unused_meta_boxes() {
	remove_meta_box('slugdiv','post_type_slider','normal');
	}
	
	// remove tagcloud and description from category
	public function sc_remove_cat_tag(){
		
	if (  ($_GET['taxonomy'] == 'slidercat')  ) {
		
    ?>
    <script type="text/javascript">
    jQuery(document).ready( function($) {
        $('#tag-description').parent().remove();
		$('textarea#description').parent().parent().remove();
		$('.tagcloud').remove();
		$('p.description,span.description').remove();
		$('#tag-slug').parent().remove();
		$('#normal-sortables').remove();
		
    });
    </script>
    <?php
		}
	
	}
	public function sc_remove_space() {

		global $post;
	if ($post->post_type == "post_type_slider") {
		?>
    <script type="text/javascript">
    jQuery(document).ready( function($) {
        $('#normal-sortables').remove();
		
    });
    </script>
    <?php
		
	}	
		
	}
	
/**************
     * Slideshow Settings 
     */
    public function sc_add_tax_meta(){
	wp_nonce_field( 'sc_tax_meta', 'sc_tax_meta_nonce' );

	echo '<div class="form-field">
			Edit Slideshow to get shortcode and setup advanced options
			<input type="hidden" name="term_meta[tax_image]" id="term_meta[tax_image]" value="slidercat-thumb" />
			<input type="hidden" name="term_meta[tax_fx]" id="term_meta[tax_fx]" value="slide" />
			<input type="hidden" name="term_meta[tax_animspd]" id="term_meta[tax_animspd]" value="800" />
			<input type="hidden" name="term_meta[tax_direction]" id="term_meta[tax_direction]" value="horizontal" />
			<input type="hidden" name="term_meta[tax_ease]" id="term_meta[tax_ease]" value="swing" />
			<input type="hidden" name="term_meta[tax_animloop]" id="term_meta[tax_animloop]" value="true" />
		</div>';
    }
	
	public function sc_edit_tax_meta( $term ){
	wp_nonce_field( 'sc_tax_meta', 'sc_tax_meta_nonce' );

		$term_id = $term->term_id;
		$term_name = $term->slug;
		$term_meta = get_option( "slidercat_$term_id" );
		
		if ( isset( $term_meta['tax_image'] ) ) $scimage = esc_attr( $term_meta['tax_image']);
		if ( isset( $term_meta['tax_fx'] ) ) $scfx = esc_attr( $term_meta['tax_fx']);
		if ( isset( $term_meta['tax_animspd'] ) ) $scanimspd = esc_attr( $term_meta['tax_animspd']);
		if ( isset( $term_meta['tax_video'] ) ) $scvideo = esc_attr( $term_meta['tax_video']);
		if ( isset( $term_meta['tax_direction'] ) ) $scdirection = esc_attr( $term_meta['tax_direction']);
		if ( isset( $term_meta['tax_ease'] ) ) $scease = esc_attr( $term_meta['tax_ease']);
		if ( isset( $term_meta['tax_revordr'] ) ) $screvordr = esc_attr( $term_meta['tax_revordr']);
		if ( isset( $term_meta['tax_animloop'] ) ) $scanimloop = esc_attr( $term_meta['tax_animloop']);
		if ( isset( $term_meta['tax_pause'] ) ) $scpause = esc_attr( $term_meta['tax_pause']);
		if ( isset( $term_meta['tax_smoothh'] ) ) $scsmoothh = esc_attr( $term_meta['tax_smoothh']);
		if ( isset( $term_meta['tax_random'] ) ) $scrandom = esc_attr( $term_meta['tax_random']);
		if ( isset( $term_meta['tax_carwdth'] ) ) $sccarwdth = esc_attr( $term_meta['tax_carwdth']);
		if ( isset( $term_meta['tax_carmin'] ) ) $sccarmin = esc_attr( $term_meta['tax_carmin']);
		if ( isset( $term_meta['tax_carmax'] ) ) $sccarmax = esc_attr( $term_meta['tax_carmax']);
		if ( isset( $term_meta['tax_mousew'] ) ) $scmousew = esc_attr( $term_meta['tax_mousew']);
		if ( isset( $term_meta['tax_asnavfor'] ) ) $scasnavfor = esc_attr( $term_meta['tax_asnavfor']);
		if ( isset( $term_meta['tax_sync'] ) ) $scsync = esc_attr( $term_meta['tax_sync']);
		
		
    ?>
		<tr class="form-field">
			<th scope="row">
				<label for="term_meta[tax_image]">Image Size</label>
					<td>
					<?php 
					$slidercat_options = get_option( 'slidercat_options' );

					echo '<select type="text" name="term_meta[tax_image]" id="term_meta[tax_image]" />';
					if ( isset( $slidercat_options['sc_thumb_wdth'] ) ) { $width_thumb = esc_attr( $slidercat_options['sc_thumb_wdth']);}
					if ( isset( $slidercat_options['sc_medium_wdth'] ) ) { $width_medium = esc_attr( $slidercat_options['sc_medium_wdth']);}
					if ( isset( $slidercat_options['sc_large_wdth'] ) ) { $width_large = esc_attr( $slidercat_options['sc_large_wdth']);}
					if ($width_thumb != 0) { echo '<option value="slidercat-thumb" '.selected( esc_attr( $scimage ), 'slidercat-thumb').'>slidercat-thumb</option>'; }
					if ($width_medium != 0) { echo '<option value="slidercat-medium" '.selected( esc_attr( $scimage ), 'slidercat-medium').'>slidercat-medium</option>'; }
					if ($width_large != 0) { echo '<option value="slidercat-large" '.selected( esc_attr( $scimage ), 'slidercat-large').'>slidercat-large</option>';}					
     	  			echo '</select> Define size in General Settings';

					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_fx]">Animation Transition Effect</label>
                <td>
                    <?php echo '<select type="text" name="term_meta[tax_fx]" id="term_meta[tax_fx]" />';
					 echo '<option value="slide" '.selected( esc_attr( $scfx ), 'slide').'>slide</option>';
					 echo '<option value="fade" '.selected( esc_attr( $scfx ), 'fade').'>fade</option>';
     	  			 echo '</select>';
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr>
            <th>
                <label for="term_meta[tax_animspd]">Animation Transition Speed</label>
                <td>
                    <input class="small-text" type="number" min="0" step="1" name="term_meta[tax_animspd]" id="term_meta[tax_animspd]" value="<?php echo esc_attr( $scanimspd ); ?>" /> in mS.
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_direction]">Slidercat Direction</label>
                <td>
                	<?php echo '<select type="text" name="term_meta[tax_direction]" id="term_meta[tax_direction]" />';
					 echo '<option value="horizontal" '.selected( esc_attr( $scdirection ), 'horizontal').'>horizontal</option>';
					 echo '<option value="vertical" '.selected( esc_attr( $scdirection ), 'vertical').'>vertical</option>';
     	  			 echo '</select> It make sense only if slide is selected.';
					 ?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_ease]">Easing</label>
                <td>
                   <?php
					 echo '<select type="text" name="term_meta[tax_ease]" id="term_meta[tax_ease]" />';
					 echo '<option value="swing" '.selected( esc_attr( $scease ), 'swing').'>swing</option>';
					
					if ( $slidercat_options['sc_enable_ease'] == 'true' )  {
					 echo '<option value="jswing" '.selected( esc_attr( $scease ), 'jswing').'>jswing</option>';
					 echo '<option value="easeInQuad" '.selected( esc_attr( $scease ), 'easeInQuad').'>easeInQuad</option>';
					 echo '<option value="easeOutQuad" '.selected( esc_attr( $scease ), 'easeOutQuad').'>easeOutQuad</option>';
					 echo '<option value="easeInOutQuad" '.selected( esc_attr( $scease ), 'easeInOutQuad').'>easeInOutQuad</option>';
					 echo '<option value="easeInCubic" '.selected( esc_attr( $scease ), 'easeInCubic').'>easeInCubic</option>';
					 echo '<option value="easeOutCubic" '.selected( esc_attr( $scease ), 'easeOutCubic').'>easeOutCubic</option>';
					 echo '<option value="easeInOutCubic" '.selected( esc_attr( $scease ), 'easeInOutCubic').'>easeInOutQuad</option>';
					 echo '<option value="easeInQuart" '.selected( esc_attr( $scease ), 'easeInQuart').'>easeInQuart</option>';
					 echo '<option value="easeOutQuart" '.selected( esc_attr( $scease ), 'easeOutQuart').'>easeOutQuart</option>';
					 echo '<option value="easeInOutQuart" '.selected( esc_attr( $scease ), 'easeInOutQuart').'>easeInOutQuart</option>';
					 echo '<option value="easeInQuint" '.selected( esc_attr( $scease ), 'easeInQuint').'>easeInQuint</option>';
					 echo '<option value="easeOutQuint" '.selected( esc_attr( $scease ), 'easeOutQuint').'>easeOutQuint</option>';
					 echo '<option value="easeInOutQuint" '.selected( esc_attr( $scease ), 'easeInOutQuint').'>easeInOutQuint</option>';
					 echo '<option value="easeInSine" '.selected( esc_attr( $scease ), 'easeInSine').'>easeInSine</option>';
					 echo '<option value="easeOutSine" '.selected( esc_attr( $scease ), 'easeOutSine').'>easeOutSine</option>';
					 echo '<option value="easeInOutSine" '.selected( esc_attr( $scease ), 'easeInOutSine').'>easeInOutSine</option>';
					 echo '<option value="easeInExpo" '.selected( esc_attr( $scease ), 'easeInExpo').'>easeInExpo</option>';
					 echo '<option value="easeOutExpo" '.selected( esc_attr( $scease ), 'easeOutExpo').'>easeOutExpo</option>';
					 echo '<option value="easeInOutExpo" '.selected( esc_attr( $scease ), 'easeInOutExpo').'>easeInOutExpo</option>';
					 echo '<option value="easeInCirc" '.selected( esc_attr( $scease ), 'easeInCirc').'>easeInCirc</option>';
					 echo '<option value="easeOutCirc" '.selected( esc_attr( $scease ), 'easeOutCirc').'>easeOutCirc</option>';
					 echo '<option value="easeInOutCirc" '.selected( esc_attr( $scease ), 'easeInOutCirc').'>easeInOutCirc</option>';
					 echo '<option value="easeInElastic" '.selected( esc_attr( $scease ), 'easeInElastic').'>easeInElastic</option>';
					 echo '<option value="easeOutElastic" '.selected( esc_attr( $scease ), 'easeOutElastic').'>easeOutElastic</option>';
					 echo '<option value="easeInOutElastic" '.selected( esc_attr( $scease ), 'easeInOutElastic').'>easeInOutElastic</option>';
					 echo '<option value="easeInBack" '.selected( esc_attr( $scease ), 'easeInBack').'>easeInBack</option>';
					 echo '<option value="easeOutBack" '.selected( esc_attr( $scease ), 'easeOutBack').'>easeOutBack</option>';
					 echo '<option value="easeInOutBack" '.selected( esc_attr( $scease ), 'easeInOutBack').'>easeInOutBack</option>';
					 echo '<option value="easeInBounce" '.selected( esc_attr( $scease ), 'easeInBounce').'>easeInBounce</option>';
					 echo '<option value="easeOutBounce" '.selected( esc_attr( $scease ), 'easeOutBounce').'>easeOutBounce</option>';
					 echo '<option value="easeInOutBounce" '.selected( esc_attr( $scease ), 'easeInOutBounce').'>easeInOutBounce</option>';
					 }
     	  			 echo '</select> To enable more effects enable Easing Script in General settings, and select "slide" transition.';
					
					 
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_revordr]">Reverse</label>
                <td>
                	 <?php echo '<select type="text" name="term_meta[tax_revordr]" id="term_meta[tax_revordr]" />';
					 echo '<option value="true" '.selected( esc_attr( $screvordr ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $screvordr ), '').'>false</option>';
     	  			 echo '</select>';
					 ?>
                </td>
            </th>
        </tr><!-- /.form-field -->
         <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_animloop]">Animation Loop</label>
                <td>
                	 <?php echo '<select type="text" name="term_meta[tax_animloop]" id="term_meta[tax_animloop]" />';
					 echo '<option value="true" '.selected( esc_attr( $scanimloop ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scanimloop ), '').'>false</option>';
     	  			 echo '</select>';
					 ?>
                </td>
            </th>
        </tr><!-- /.form-field -->
         <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_pause]">Pause Button</label>
                <td>
                   <?php echo '<select type="text" name="term_meta[tax_pause]" id="term_meta[tax_pause]" />';
					 echo '<option value="true" '.selected( esc_attr( $scpause ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scpause ), '').'>false</option>';
     	  			 echo '</select>';
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
         <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_smoothh]">Smooth Height</label>
                <td>
                   <?php echo '<select type="text" name="term_meta[tax_smoothh]" id="term_meta[tax_smoothh]" />';
					 echo '<option value="true" '.selected( esc_attr( $scsmoothh ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scsmoothh ), '').'>false</option>';
     	  			 echo '</select>';
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_random]">Randomize</label>
                <td>
                   <?php echo '<select type="text" name="term_meta[tax_random]" id="term_meta[tax_random]" />';
					 echo '<option value="true" '.selected( esc_attr( $scrandom ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scrandom ), '').'>false</option>';
     	  			 echo '</select>';
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
                <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_video]">Slidercat Video</label>
                <td>
                	 <?php echo '<select type="text" name="term_meta[tax_video]" id="term_meta[tax_video]" />';
					 echo '<option value="true" '.selected( esc_attr( $scvideo ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scvideo ), '').'>false</option>';
     	  			 echo '</select>';
					 ?>
                </td>
            </th>
        </tr><!-- /.form-field -->
         <tr>
            <th>
                <label for="term_meta[tax_carwdth]">Carousel Item Width</label>
                <td>
                   <p><input class="small-text" type="number" min="0" step="1" name="term_meta[tax_carwdth]" id="term_meta[tax_carwdth]"  value="<?php echo esc_attr( $sccarwdth ); ?>" />
                     0 or empty to disable carousel, width in pixels for single item in carousel view. Slidercat Transition Effect must be set to "slide".</p>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <tr>
            <th>
                <label for="term_meta[tax_carmin]">Carousel min / max Items</label>
				<td>
                   <input class="small-text" type="number" min="0" step="1" name="term_meta[tax_carmin]" id="term_meta[tax_carmin]" size="3" value="<?php echo esc_attr( $sccarmin ); ?>" /> / 
                   <input class="small-text" type="number" min="0" step="1" name="term_meta[tax_carmax]" id="term_meta[tax_carmax]" size="3" value="<?php echo esc_attr( $sccarmax ); ?>" />
                    
				</td>
            </th>
        </tr><!-- /.form-field -->
        <?php if ( $slidercat_options['sc_enable_mw'] == 'true' )  { ?>
         <tr class="form-field">
            <th scope="row">
                <label for="term_meta[tax_mousew]">Enable MouseWheel</label>
                <td>
                   <?php echo '<p><select type="text" name="term_meta[tax_mousew]" id="term_meta[tax_mousew]" />';
					 echo '<option value="true" '.selected( esc_attr( $scmousew ), 'true').'>true</option>';
					 echo '<option value="" '.selected( esc_attr( $scmousew ), '').'>false</option>';
     	  			 echo '</select> ';
					 echo 'In order to work enable mousewheel in General Settings';
					 echo '</p>';
					?>
                </td>
            </th>
        </tr><!-- /.form-field -->
        <?php } ?>
         <tr>
            <th>
                <label for="term_meta[tax_asnavfor]">As Navigation For</label>
                <td>
                   <input type="text" name="term_meta[tax_asnavfor]" id="term_meta[tax_asnavfor]" size="10" value="<?php echo esc_attr( $scasnavfor ); ?>" /> Enter Sync Slidercat element ID if used as navigation for other slideshow. Carousel must be enabled. This element ID is: #slidercat_<?php echo $term_id; ?> and that is the ID that should be entered in a Sync field of an element which ID is used here in As Nav For field.
                </td>
            </th>
        </tr><!-- /.form-field -->
         <tr>
            <th>
                <label for="term_meta[tax_sync]">Sync</label>
                <td>
                   <input type="text" name="term_meta[tax_sync]" id="term_meta[tax_sync]" size="10" value="<?php echo esc_attr( $scsync ); ?>" /> Enter As Navigation For Slidercat element ID if used as slideshow controled by As Navigation For element
                </td>
            </th>
        </tr><!-- /.form-field -->
       Here is your slideshow shortcode: [slidercat cat="<?php echo $term_name; ?>"]
    <?php
    } 
	
    public function sc_save_tax_meta( $term_id ){
		
		if ( ! isset( $_POST['sc_tax_meta_nonce'] ) )
			return $term_id;
			
		$nonce = $_POST['sc_tax_meta_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'sc_tax_meta' ) )
			return $term_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $term_id;

		if ( 'page' == $_POST['term_meta'] ) {

			if ( ! current_user_can( 'edit_page', $term_id ) )
				return $term_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $term_id ) )
				return $term_id;
		}

        if ( isset( $_POST['term_meta'] ) ) {
            $t_id = $term_id;
			
            $term_meta = array(
			'tax_id' => $t_id,
            'tax_image' => isset ( $_POST['term_meta']['tax_image'] ) ? sanitize_text_field( $_POST['term_meta']['tax_image'] ) : '',
			'tax_fx' => isset ( $_POST['term_meta']['tax_fx'] ) ? sanitize_text_field( $_POST['term_meta']['tax_fx'] ) : '',
			'tax_animspd' => isset ( $_POST['term_meta']['tax_animspd'] ) ? absint( $_POST['term_meta']['tax_animspd'] ) : '',
			'tax_video' => isset ( $_POST['term_meta']['tax_video'] ) ? sanitize_text_field( $_POST['term_meta']['tax_video'] ) : '',
			'tax_direction' => isset ( $_POST['term_meta']['tax_direction'] ) ? sanitize_text_field( $_POST['term_meta']['tax_direction'] ) : '',
			'tax_ease' => isset ( $_POST['term_meta']['tax_ease'] ) ? sanitize_text_field( $_POST['term_meta']['tax_ease'] ) : '',
			'tax_revordr' => isset ( $_POST['term_meta']['tax_revordr'] ) ? sanitize_text_field( $_POST['term_meta']['tax_revordr'] ) : '',
			'tax_animloop' => isset ( $_POST['term_meta']['tax_animloop'] ) ? sanitize_text_field( $_POST['term_meta']['tax_animloop'] ) : '',
			'tax_pause' => isset ( $_POST['term_meta']['tax_pause'] ) ? sanitize_text_field( $_POST['term_meta']['tax_pause'] ) : '',
			'tax_smoothh' => isset ( $_POST['term_meta']['tax_smoothh'] ) ? sanitize_text_field( $_POST['term_meta']['tax_smoothh'] ) : '',
			'tax_random' => isset ( $_POST['term_meta']['tax_random'] ) ? sanitize_text_field( $_POST['term_meta']['tax_random'] ) : '',
			'tax_carwdth' => isset ( $_POST['term_meta']['tax_carwdth'] ) ? absint( $_POST['term_meta']['tax_carwdth'] ) : '',
			'tax_carmin' => isset ( $_POST['term_meta']['tax_carmin'] ) ? absint( $_POST['term_meta']['tax_carmin'] ) : '',
			'tax_carmax' => isset ( $_POST['term_meta']['tax_carmax'] ) ? absint( $_POST['term_meta']['tax_carmax'] ) : '',
			'tax_mousew' => isset ( $_POST['term_meta']['tax_mousew'] ) ? sanitize_text_field( $_POST['term_meta']['tax_mousew'] ) : '',
			'tax_asnavfor' => isset ( $_POST['term_meta']['tax_asnavfor'] ) ? sanitize_text_field( $_POST['term_meta']['tax_asnavfor'] ) : '',
			'tax_sync' => isset ( $_POST['term_meta']['tax_sync'] ) ? sanitize_text_field( $_POST['term_meta']['tax_sync'] ) : '',
			);
			
            update_option( "slidercat_$t_id", $term_meta );
        } 
    } 
	
/**************
	 * Slide Settings
	 */
	public function sc_add_post_meta( $post_type ) {
		$post_types = array( 'post_type_slider');
		if ( in_array( $post_type, $post_types )) {
			
			add_meta_box(
			'slidercat_sectionc'
			,__( 'Content Boxes', 'slidercat' )
			,array( $this, 'sc_render_post_excerpt' )
			,$post_type
			,'advanced'
			,'high'
			);
			
			add_meta_box(
			'slidercat_sectionid'
			,__( 'Settings', 'slidercat' )
			,array( $this, 'sc_render_post_meta' )
			,$post_type
			,'advanced'
			,'high'
			);
		}
	}

	public function sc_save_post_meta( $post_id ) {

		if ( ! isset( $_POST['slidercat_meta_box_nonce'] ) )
			return $post_id;
			
		$nonce = $_POST['slidercat_meta_box_nonce'];

		if ( ! wp_verify_nonce( $nonce, 'slidercat_meta_box' ) )
			return $post_id;

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
			return $post_id;

		if ( 'page' == $_POST['post_type'] ) {

			if ( ! current_user_can( 'edit_page', $post_id ) )
				return $post_id;
	
		} else {

			if ( ! current_user_can( 'edit_post', $post_id ) )
				return $post_id;
		}

	 $data_arr = array( 
		'sc_dd' => absint( $_POST['sc_dd'] ),
		'sc_url' => sanitize_text_field( $_POST['sc_url'] ),
		'sc_url_targ' => sanitize_text_field( $_POST['sc_url_targ'] ),
		'sc_yv' => sanitize_text_field( $_POST['sc_yv'] ),
		'sc_t_enable' => sanitize_text_field( $_POST['sc_t_enable'] ),
		'sc_t_url' => sanitize_text_field( $_POST['sc_t_url'] ),
		'sc_t_url_targ' => sanitize_text_field( $_POST['sc_t_url_targ'] ),
		'sc_t_pos' => sanitize_text_field( $_POST['sc_t_pos'] ),
		'sc_t_col' => sanitize_text_field( $_POST['sc_t_col'] ),
		'sc_t_size' => sanitize_text_field( $_POST['sc_t_size'] ),
		'sc_t_wrap' => sanitize_text_field( $_POST['sc_t_wrap'] ),
		'sc_t_maxw' => sanitize_text_field( $_POST['sc_t_maxw'] ),
		'sc_t_class' => sanitize_text_field( $_POST['sc_t_class'] ),
		'sc_t_infx' => sanitize_text_field( $_POST['sc_t_infx'] ),
		'sc_t_infx_d' => absint( $_POST['sc_t_infx_d'] ),
		'sc_t_outfx' => sanitize_text_field( $_POST['sc_t_outfx'] ),
		'sc_t_outfx_d' => absint( $_POST['sc_t_outfx_d'] ),
		'sc_c' => wp_kses($_POST['sc_c'], 
			
			array( 
				'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array(),
						'rel' => array()
						),
				'br' => array(),
				'&nbsp;' => array(),
				'hr' => array(),
				'em' => array(),
				'strong' => array(),
				'img' => array(
						'src' => array(),
						'alt' => array(),
						'class' => array(),
						'width' => array(),
						'height' => array(),
						'rel' => array()
						)
				) 
			),
		'sc_c_pos' => sanitize_text_field( $_POST['sc_c_pos'] ),
		'sc_c_col' => sanitize_text_field( $_POST['sc_c_col'] ),
		'sc_c_size' => sanitize_text_field( $_POST['sc_c_size'] ),
		'sc_c_wrap' => sanitize_text_field( $_POST['sc_c_wrap'] ),
		'sc_c_maxw' => sanitize_text_field( $_POST['sc_c_maxw'] ),
		'sc_c_class' => sanitize_text_field( $_POST['sc_c_class'] ),
		'sc_c_infx' => sanitize_text_field( $_POST['sc_c_infx'] ),
		'sc_c_infx_d' => absint( $_POST['sc_c_infx_d'] ),
		'sc_c_outfx' => sanitize_text_field( $_POST['sc_c_outfx'] ),
		'sc_c_outfx_d' => absint( $_POST['sc_c_outfx_d'] ),
		'sc_e' => wp_kses($_POST['sc_e'], 
			
			array( 
				'a' => array(
						'href' => array(),
						'title' => array(),
						'target' => array(),
						'rel' => array()
						),
				'br' => array(),
				'&nbsp;' => array(),
				'hr' => array(),
				'em' => array(),
				'strong' => array(),
				'img' => array(
						'src' => array(),
						'alt' => array(),
						'class' => array(),
						'width' => array(),
						'height' => array(),
						'rel' => array()
						)
				) 
			),
		'sc_e_pos' => sanitize_text_field( $_POST['sc_e_pos'] ),
		'sc_e_col' => sanitize_text_field( $_POST['sc_e_col'] ),
		'sc_e_size' => sanitize_text_field( $_POST['sc_e_size'] ),
		'sc_e_wrap' => sanitize_text_field( $_POST['sc_e_wrap'] ),
		'sc_e_maxw' => sanitize_text_field( $_POST['sc_e_maxw'] ),
		'sc_e_class' => sanitize_text_field( $_POST['sc_e_class'] ),
		'sc_e_infx' => sanitize_text_field( $_POST['sc_e_infx'] ),
		'sc_e_infx_d' => absint( $_POST['sc_e_infx_d'] ),
		'sc_e_outfx' => sanitize_text_field( $_POST['sc_e_outfx'] ),
		'sc_e_outfx_d' => absint( $_POST['sc_e_outfx_d'] ),
	);
	update_post_meta( $post_id, '_slidercat', $data_arr );
	}
	
	public function sc_render_post_excerpt( $post) {
		
		wp_nonce_field( 'slidercat_meta_box', 'slidercat_meta_box_nonce' );
	$post_id = $post->ID;
		
		$data_arr = get_post_meta($post_id, '_slidercat', true);
		$supportedTypes = get_theme_support( 'post-thumbnails' );
		
  		if( $supportedTypes === false ) {
		echo __("Your theme does not supports thumbnails or does not use add_theme_support function. To enable Featured images (Slide Images) please add this function to your theme functions.php => add_theme_support('post-thumbnails');", 'slidercat');
		echo '<br /><br />';
		}
		if ( isset( $data_arr['sc_c'] ) ) $sc_c =  html_entity_decode($data_arr['sc_c']) ; else $sc_c = '';
		 
		wp_editor( $sc_c , 'sc_c', 
			 array(
			'textarea_name' => 'sc_c',
			'wpautop' => false,
			'teeny' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 4)
			)  
		);	
		
		if ( isset( $data_arr['sc_e'] ) ) $sc_e =  html_entity_decode($data_arr['sc_e']) ; else $sc_e = '';
		 
		wp_editor( $sc_e , 'sc_e', 
			 array(
			'textarea_name' => 'sc_e',
			'wpautop' => false,
			'teeny' => true,
			'textarea_rows' => get_option('default_post_edit_rows', 4)
			)  
		);	
		
	}
	
	public function sc_render_post_meta( $post) {
	wp_nonce_field( 'slidercat_meta_box', 'slidercat_meta_box_nonce' );
	$post_id = $post->ID;
	
	$data_arr = get_post_meta($post_id, '_slidercat', true);
	if ( isset( $data_arr['sc_dd'] ) ) $sc_dd = esc_attr( $data_arr['sc_dd'] ); else $sc_dd = 5000;
	if ( isset( $data_arr['sc_url'] ) ) $sc_url = esc_url( $data_arr['sc_url'] ); else $sc_url = '';
	if ( isset( $data_arr['sc_url_targ'] ) ) $sc_url_targ = esc_attr( $data_arr['sc_url_targ'] ); else $sc_url_targ = '_self';
	if ( isset( $data_arr['sc_yv'] ) ) $sc_yv = esc_attr( $data_arr['sc_yv'] ); else $sc_yv = '';
	if ( isset( $data_arr['sc_t_enable'] ) ) $sc_t_enable = esc_attr( $data_arr['sc_t_enable'] ); else $sc_t_enable = 'true';
	if ( isset( $data_arr['sc_t_url'] ) ) $sc_t_url = esc_url( $data_arr['sc_t_url'] ); else $sc_t_url = '';
	if ( isset( $data_arr['sc_t_url_targ'] ) ) $sc_t_url_targ = esc_attr( $data_arr['sc_t_url_targ'] ); else $sc_t_url_targ = '_self';
	if ( isset( $data_arr['sc_t_pos'] ) ) $sc_t_pos = esc_attr( $data_arr['sc_t_pos'] ); else $sc_t_pos = 'top-left';
	if ( isset( $data_arr['sc_t_col'] ) ) $sc_t_col = esc_attr( $data_arr['sc_t_col'] ); else $sc_t_col = 'white';
	if ( isset( $data_arr['sc_t_size'] ) ) $sc_t_size = esc_attr( $data_arr['sc_t_size'] ); else $sc_t_size = 'medium';
	if ( isset( $data_arr['sc_t_wrap'] ) ) $sc_t_wrap = esc_attr( $data_arr['sc_t_wrap'] ); else $sc_t_wrap = 'wraped-width-1040';
	if ( isset( $data_arr['sc_t_maxw'] ) ) $sc_t_maxw = esc_attr( $data_arr['sc_t_maxw'] ); else $sc_t_maxw = 'max-width-320';
	if ( isset( $data_arr['sc_t_class'] ) ) $sc_t_class = esc_attr( $data_arr['sc_t_class'] ); else $sc_t_class = '';
	if ( isset( $data_arr['sc_t_infx'] ) ) $sc_t_infx = esc_attr( $data_arr['sc_t_infx'] ); else $sc_t_infx = 'bounceInRight';
	if ( isset( $data_arr['sc_t_infx_d'] ) ) $sc_t_infx_d = esc_attr( $data_arr['sc_t_infx_d'] ); else $sc_t_infx_d = '1050';
	if ( isset( $data_arr['sc_t_outfx'] ) ) $sc_t_outfx =  esc_attr( $data_arr['sc_t_outfx'] ); else $sc_t_outfx = 'bounceOutRight';
	if ( isset( $data_arr['sc_t_outfx_d'] ) ) $sc_t_outfx_d = esc_attr( $data_arr['sc_t_outfx_d'] ); else $sc_t_outfx_d = '4700';
	if ( isset( $data_arr['sc_c_pos'] ) ) $sc_c_pos = esc_attr( $data_arr['sc_c_pos'] ); else $sc_c_pos = 'center-center';
	if ( isset( $data_arr['sc_c_col'] ) ) $sc_c_col = esc_attr( $data_arr['sc_c_col'] ); else $sc_c_col = 'white';
	if ( isset( $data_arr['sc_c_size'] ) ) $sc_c_size = esc_attr( $data_arr['sc_c_size'] ); else $sc_c_size = 'medium';
	if ( isset( $data_arr['sc_c_wrap'] ) ) $sc_c_wrap = esc_attr( $data_arr['sc_c_wrap'] ); else $sc_c_wrap = 'wraped-width-1040';
	if ( isset( $data_arr['sc_c_maxw'] ) ) $sc_c_maxw = esc_attr( $data_arr['sc_c_maxw'] ); else $sc_c_maxw = 'max-width-320';
	if ( isset( $data_arr['sc_c_class'] ) ) $sc_c_class = esc_attr( $data_arr['sc_c_class'] ); else $sc_c_class = '';
	if ( isset( $data_arr['sc_c_infx'] ) ) $sc_c_infx = esc_attr( $data_arr['sc_c_infx'] ); else $sc_c_infx = 'zoomIn';
	if ( isset( $data_arr['sc_c_infx_d'] ) ) $sc_c_infx_d = esc_attr( $data_arr['sc_c_infx_d'] ); else $sc_c_infx_d = '1200';
	if ( isset( $data_arr['sc_c_outfx'] ) ) $sc_c_outfx = esc_attr( $data_arr['sc_c_outfx'] ); else $sc_c_outfx = 'zoomOut';
	if ( isset( $data_arr['sc_c_outfx_d'] ) ) $sc_c_outfx_d = esc_attr( $data_arr['sc_c_outfx_d'] ); else $sc_c_outfx_d = '4800';
	if ( isset( $data_arr['sc_e_pos'] ) ) $sc_e_pos = esc_attr( $data_arr['sc_e_pos'] ); else $sc_e_pos = 'bottom-right';
	if ( isset( $data_arr['sc_e_col'] ) ) $sc_e_col = esc_attr( $data_arr['sc_e_col'] ); else $sc_e_col = 'red';
	if ( isset( $data_arr['sc_e_size'] ) ) $sc_e_size = esc_attr( $data_arr['sc_e_size'] ); else $sc_e_size = 'small';
	if ( isset( $data_arr['sc_e_wrap'] ) ) $sc_e_wrap = esc_attr( $data_arr['sc_e_wrap'] ); else $sc_e_wrap = 'wraped-width-1040';
	if ( isset( $data_arr['sc_e_maxw'] ) ) $sc_e_maxw = esc_attr( $data_arr['sc_e_maxw'] ); else $sc_e_maxw = 'max-width-320';
	if ( isset( $data_arr['sc_e_class'] ) ) $sc_e_class = esc_attr( $data_arr['sc_e_class'] ); else $sc_e_class = '';
	if ( isset( $data_arr['sc_e_infx'] ) ) $sc_e_infx = esc_attr( $data_arr['sc_e_infx'] ); else $sc_e_infx = 'bounceInLeft';
	if ( isset( $data_arr['sc_e_infx_d'] ) ) $sc_e_infx_d = esc_attr( $data_arr['sc_e_infx_d'] ); else $sc_e_infx_d = '1350';
	if ( isset( $data_arr['sc_e_outfx'] ) ) $sc_e_outfx =  esc_attr( $data_arr['sc_e_outfx'] ); else $sc_e_outfx = 'bounceOutLeft';
	if ( isset( $data_arr['sc_e_outfx_d'] ) ) $sc_e_outfx_d = esc_attr( $data_arr['sc_e_outfx_d'] ); else $sc_e_outfx_d = '4900';
	
	/******* Overall *******/
	echo '<label for="sc_dd">';
	_e( 'Overall Slide Duration', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_dd" name="sc_dd" value="'.$sc_dd.'" />mS<br /><br />';
	
	echo '<label for="sc_url">';
	_e( 'Slide Link URL (Leave empty if not used)', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_url" name="sc_url" value="'.$sc_url.'" size="25" /> ';
	
	echo '<label for="sc_url_targ">';
	_e( 'Link Target:', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_url_targ" name="sc_url_targ">';
    echo '<option value="_self" '.selected( $sc_url_targ, '_self').'>_self</option> 
          <option value="_blank" '.selected( $sc_url_targ, '_blank').'>_blank</option>
     	  </select>';  
	
	/******* Youtube / Vimeo *******/
	echo '<br /><br /><label for="sc_yv">';
	_e( 'Youtube / Vimeo URL', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_yv" name="sc_yv" value="' . $sc_yv .  '" size="25" /> Leave empty if not used, or if featured image is used.<br /><br />';
	
	/************ Title ***************/
	echo '<h3 class="hndle"><span>';
	_e( 'Title', 'slidercat' );
	echo '</span></h3>';
	
	echo '<br /><label for="sc_t_enable">';
	_e( 'Enable Title', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_enable" name="sc_t_enable">';
    echo '<option value="true" '.selected( $sc_t_enable, 'true').'>true</option> 
          <option value="false" '.selected( $sc_t_enable, 'false').'>false</option>
     	  </select> ';
	
	echo '<label for="sc_t_url">';
	_e( 'Link URL', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_t_url" name="sc_t_url" value="' . $sc_t_url .  '" size="35" /> ';
	
	echo '<label for="sc_t_url_targ">';
	_e( 'Link Target:', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_url_targ" name="sc_t_url_targ">';
    echo '<option value="_self" '.selected( $sc_t_url_targ, '_self').'>_self</option> 
          <option value="_blank" '.selected( $sc_t_url_targ, '_blank').'>_blank</option>
     	  </select><br /><br />';
	
	echo '<label for="sc_t_pos">';
	_e( 'Position', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_pos" name="sc_t_pos">';
    echo '<option value=""'.selected( $sc_t_pos, '').'>none</option>
		  <option value="top-left"'.selected( $sc_t_pos, 'top-left').'>top-left</option> 
          <option value="center-left" '.selected( $sc_t_pos, 'center-left').'>center-left</option>
          <option value="bottom-left" '.selected( $sc_t_pos, 'bottom-left').'>bottom-left</option>
		  <option value="center-top" '.selected( $sc_t_pos, 'center-top').'>center-top</option>
          <option value="center-center" '.selected( $sc_t_pos, 'center-center').'>center-center</option>
          <option value="center-bottom" '.selected( $sc_t_pos, 'center-bottom').'>center-bottom</option>
          <option value="top-right" '.selected( $sc_t_pos, 'top-right').'>top-right</option>
          <option value="center-right" '.selected( $sc_t_pos, 'center-right').'>center-right</option>
		  <option value="bottom-right" '.selected( $sc_t_pos, 'bottom-right').'>bottom-right</option>
     	  </select> ';	  
		  
	echo '<label for="sc_t_col">';
	_e( 'Font Colour', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_col" name="sc_t_col">';
    echo '<option value=""'.selected( $sc_t_col, '').'>none</option>
		  <option value="white"'.selected( $sc_t_col, 'white').'>white</option> 
          <option value="black" '.selected( $sc_t_col, 'black').'>black</option>
          <option value="blue" '.selected( $sc_t_col, 'blue').'>blue</option>
		  <option value="yellow" '.selected( $sc_t_col, 'yellow').'>yellow</option>
          <option value="red" '.selected( $sc_t_col, 'red').'>red</option>
          <option value="violet" '.selected( $sc_t_col, 'violet').'>violet</option>
     	  </select> ';
		  
	echo '<label for="sc_t_size">';
	_e( 'Size', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_size" name="sc_t_size">';
    echo '<option value=""'.selected( $sc_t_size, '').'>none</option>
		  <option value="small"'.selected( $sc_t_size, 'small').'>small</option> 
          <option value="medium" '.selected( $sc_t_size, 'medium').'>medium</option>
          <option value="big" '.selected( $sc_t_size, 'big').'>big</option>
		  <option value="xxl" '.selected( $sc_t_size, 'xxl').'>xxl</option>
     	  </select> ';	  
		  
	echo '<label for="sc_t_wrap">';
	_e( 'Wrap', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_wrap" name="sc_t_wrap">';
    echo '<option value=""'.selected( $sc_t_wrap, '').'>none</option>
		  <option value="full-width"'.selected( $sc_t_wrap, 'full-width').'>full-width</option> 
          <option value="wraped-width-1600" '.selected( $sc_t_wrap, 'wraped-width-1600').'>wraped-width-1600</option>
		  <option value="wraped-width-1300" '.selected( $sc_t_wrap, 'wraped-width-1300').'>wraped-width-1300</option>
		  <option value="wraped-width-1180" '.selected( $sc_t_wrap, 'wraped-width-1180').'>wraped-width-1180</option>
          <option value="wraped-width-1040" '.selected( $sc_t_wrap, 'wraped-width-1040').'>wraped-width-1040</option>
		  <option value="wraped-width-760" '.selected( $sc_t_wrap, 'wraped-width-760').'>wraped-width-760</option>
		  <option value="wraped-width-520" '.selected( $sc_t_wrap, 'wraped-width-520').'>wraped-width-520</option>
     	  </select> ';	  
		  
	echo '<label for="sc_t_maxw">';
	_e( 'Max Width', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_maxw" name="sc_t_maxw">';
    echo '<option value=""'.selected( $sc_t_maxw, '').'>none</option>
		  <option value="max-width-160" '.selected( $sc_t_maxw, 'max-width-160').'>max-width-160</option> 
          <option value="max-width-240" '.selected( $sc_t_maxw, 'max-width-240').'>max-width-240</option>
		  <option value="max-width-320" '.selected( $sc_t_maxw, 'max-width-320').'>max-width-320</option>
		  <option value="max-width-480" '.selected( $sc_t_maxw, 'max-width-480').'>max-width-480</option>
		  <option value="max-width-640" '.selected( $sc_t_maxw, 'max-width-640').'>max-width-640</option>
		  <option value="max-width-800" '.selected( $sc_t_maxw, 'max-width-800').'>max-width-800</option>
		  <option value="max-width-1024" '.selected( $sc_t_maxw, 'max-width-1024').'>max-width-1024</option>
     	  </select> ';		  
	
	echo '<label for="sc_t_class">';
	_e( 'Custom Class', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_t_class" name="sc_t_class" value="' . $sc_t_class . '" size="15" /><br /><br />';	  	  
	
	echo '<label for="sc_t_outfx">';
	_e( 'In Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_infx" name="sc_t_infx">';
	echo '<option value=""'.selected( $sc_t_infx, '').'>none</option>
		<optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_t_infx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_t_infx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_t_infx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_t_infx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_t_infx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_t_infx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_t_infx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_t_infx, 'wobble').'>wobble</option>
        </optgroup>
        <optgroup label="Bouncing Entrances">
          <option value="bounceIn" '.selected( $sc_t_infx, 'bounceIn').'>bounceIn</option>
          <option value="bounceInDown" '.selected( $sc_t_infx, 'bounceInDown').'>bounceInDown</option>
          <option value="bounceInLeft" '.selected( $sc_t_infx, 'bounceInLeft').'>bounceInLeft</option>
          <option value="bounceInRight" '.selected( $sc_t_infx, 'bounceInRight').'>bounceInRight</option>
          <option value="bounceInUp" '.selected( $sc_t_infx, 'bounceInUp').'>bounceInUp</option>
        </optgroup>
        <optgroup label="Fading Entrances">
          <option value="fadeIn" '.selected( $sc_t_infx, 'fadeIn').'>fadeIn</option>
          <option value="fadeInDown" '.selected( $sc_t_infx, 'fadeInDown').'>fadeInDown</option>
          <option value="fadeInDownBig" '.selected( $sc_t_infx, 'fadeInDownBig').'>fadeInDownBig</option>
          <option value="fadeInLeft" '.selected( $sc_t_infx, 'fadeInLeft').'>fadeInLeft</option>
          <option value="fadeInLeftBig" '.selected( $sc_t_infx, 'fadeInLeftBig').'>fadeInLeftBig</option>
          <option value="fadeInRight" '.selected( $sc_t_infx, 'fadeInRight').'>fadeInRight</option>
          <option value="fadeInRightBig" '.selected( $sc_t_infx, 'fadeInRightBig').'>fadeInRightBig</option>
          <option value="fadeInUp" '.selected( $sc_t_infx, 'fadeInUp').'>fadeInUp</option>
          <option value="fadeInUpBig" '.selected( $sc_t_infx, 'fadeInUpBig').'>fadeInUpBig</option>
        </optgroup>
        <optgroup label="Rotating Entrances">
          <option value="rotateIn" '.selected( $sc_t_infx, 'rotateIn').'>rotateIn</option>
          <option value="rotateInDownLeft" '.selected( $sc_t_infx, 'rotateInDownLeft').'>rotateInDownLeft</option>
          <option value="rotateInDownRight" '.selected( $sc_t_infx, 'rotateInDownRight').'>rotateInDownRight</option>
          <option value="rotateInUpLeft" '.selected( $sc_t_infx, 'rotateInUpLeft').'>rotateInUpLeft</option>
          <option value="rotateInUpRight" '.selected( $sc_t_infx, 'rotateInUpRight').'>rotateInUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_t_infx, 'flip').'>flip</option>
          <option value="flipInX" '.selected( $sc_t_infx, 'flipInX').'>flipInX</option>
          <option value="flipInY" '.selected( $sc_t_infx, 'flipInY').'>flipInY</option>
		  <option value="lightSpeedIn" '.selected( $sc_t_infx, 'lightSpeedIn').'>lightSpeedIn</option>
          <option value="hinge" '.selected( $sc_t_infx, 'hinge').'>hinge</option>
          <option value="rollIn" '.selected( $sc_t_infx, 'rollIn').'>rollIn</option>
        </optgroup>
        <optgroup label="Zoom Entrances">
          <option value="zoomIn" '.selected( $sc_t_infx, 'zoomIn').'>zoomIn</option>
          <option value="zoomInDown" '.selected( $sc_t_infx, 'zoomInDown').'>zoomInDown</option>
          <option value="zoomInLeft" '.selected( $sc_t_infx, 'zoomInLeft').'>zoomInLeft</option>
          <option value="zoomInRight" '.selected( $sc_t_infx, 'zoomInRight').'>zoomInRight</option>
          <option value="zoomInUp" '.selected( $sc_t_infx, 'zoomInUp').'>zoomInUp</option>
        </optgroup>
      </select> ';

	echo '<label for="sc_t_infx_d">';
	_e( 'In Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_t_infx_d" name="sc_t_infx_d" value="' . $sc_t_infx_d .  '" /> ';
	
	echo '<label for="sc_t_outfx">';
	_e( 'Out Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_t_outfx" name="sc_t_outfx">';
	echo '<option value=""'.selected( $sc_t_outfx, '').'>none</option>
		<optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_t_outfx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_t_outfx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_t_outfx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_t_outfx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_t_outfx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_t_outfx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_t_outfx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_t_outfx, 'wobble').'>wobble</option>
        </optgroup>
        <optgroup label="Bouncing Exits">
          <option value="bounceOut" '.selected( $sc_t_outfx, 'bounceOut').'>bounceOut</option>
          <option value="bounceOutDown" '.selected( $sc_t_outfx, 'bounceOutDown').'>bounceOutDown</option>
          <option value="bounceOutLeft" '.selected( $sc_t_outfx, 'bounceOutLeft').'>bounceOutLeft</option>
          <option value="bounceOutRight" '.selected( $sc_t_outfx, 'bounceOutRight').'>bounceOutRight</option>
          <option value="bounceOutUp" '.selected( $sc_t_outfx, 'bounceOutUp').'>bounceOutUp</option>
        </optgroup>
        <optgroup label="Fading Exits">
          <option value="fadeOut" '.selected( $sc_t_outfx, 'fadeOut').'>fadeOut</option>
          <option value="fadeOutDown" '.selected( $sc_t_outfx, 'fadeOutDown').'>fadeOutDown</option>
          <option value="fadeOutDownBig" '.selected( $sc_t_outfx, 'fadeOutDownBig').'>fadeOutDownBig</option>
          <option value="fadeOutLeft" '.selected( $sc_t_outfx, 'fadeOutLeft').'>fadeOutLeft</option>
          <option value="fadeOutLeftBig" '.selected( $sc_t_outfx, 'fadeOutLeftBig').'>fadeOutLeftBig</option>
          <option value="fadeOutRight" '.selected( $sc_t_outfx, 'fadeOutRight').'>fadeOutRight</option>
          <option value="fadeOutRightBig" '.selected( $sc_t_outfx, 'fadeOutRightBig').'>fadeOutRightBig</option>
          <option value="fadeOutUp" '.selected( $sc_t_outfx, 'fadeOutUp').'>fadeOutUp</option>
          <option value="fadeOutUpBig" '.selected( $sc_t_outfx, 'fadeOutUpBig').'>fadeOutUpBig</option>
        </optgroup>
        <optgroup label="Rotating Exits">
          <option value="rotateOut" '.selected( $sc_t_outfx, 'rotateOut').'>rotateOut</option>
          <option value="rotateOutDownLeft" '.selected( $sc_t_outfx, 'rotateOutDownLeft').'>rotateOutDownLeft</option>
          <option value="rotateOutDownRight" '.selected( $sc_t_outfx, 'rotateOutDownRight').'>rotateOutDownRight</option>
          <option value="rotateOutUpLeft" '.selected( $sc_t_outfx, 'rotateOutUpLeft').'>rotateOutUpLeft</option>
          <option value="rotateOutUpRight" '.selected( $sc_t_outfx, 'rotateOutUpRight').'>rotateOutUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_t_outfx, 'flip').'>flip</option>
          <option value="flipOutX" '.selected( $sc_t_outfx, 'flipOutX').'>flipOutX</option>
          <option value="flipOutY" '.selected( $sc_t_outfx, 'flipOutY').'>flipOutY</option>
		  <option value="lightSpeedOut" '.selected( $sc_t_outfx, 'lightSpeedOut').'>lightSpeedOut</option>
          <option value="hinge" '.selected( $sc_t_outfx, 'hinge').'>hinge</option>
          <option value="rollOut" '.selected( $sc_t_outfx, 'rollOut').'>rollOut</option>
        </optgroup>
        <optgroup label="Zoom Exits">
          <option value="zoomOut" '.selected( $sc_t_outfx, 'zoomOut').'>zoomOut</option>
          <option value="zoomOutDown" '.selected( $sc_t_outfx, 'zoomOutDown').'>zoomOutDown</option>
          <option value="zoomOutLeft" '.selected( $sc_t_outfx, 'zoomOutLeft').'>zoomOutLeft</option>
          <option value="zoomOutRight" '.selected( $sc_t_outfx, 'zoomOutRight').'>zoomOutRight</option>
          <option value="zoomOutUp" '.selected( $sc_t_outfx, 'zoomOutUp').'>zoomOutUp</option>
        </optgroup>
      </select> ';

	echo '<label for="sc_t_outfx_d">';
	_e( 'Out Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_t_outfx_d" name="sc_t_outfx_d" value="' . $sc_t_outfx_d .  '"/ ><br /><br />';
	
	/************ Content ***************/
	echo '<h3 class="hndle"><span>';
	_e( 'Content Box', 'slidercat' );
	echo '</span></h3>';
	
	echo '<br /><label for="sc_c_pos">';
	_e( 'Position', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_pos" name="sc_c_pos">';
    echo '<option value=""'.selected( $sc_c_pos, '').'>none</option>
		  <option value="top-left"'.selected( $sc_c_pos, 'top-left').'>top-left</option> 
          <option value="center-left" '.selected( $sc_c_pos, 'center-left').'>center-left</option>
          <option value="bottom-left" '.selected( $sc_c_pos, 'bottom-left').'>bottom-left</option>
		  <option value="center-top" '.selected( $sc_c_pos, 'center-top').'>center-top</option>
          <option value="center-center" '.selected( $sc_c_pos, 'center-center').'>center-center</option>
          <option value="center-bottom" '.selected( $sc_c_pos, 'center-bottom').'>center-bottom</option>
          <option value="top-right" '.selected( $sc_c_pos, 'top-right').'>top-right</option>
          <option value="center-right" '.selected( $sc_c_pos, 'center-right').'>center-right</option>
		  <option value="bottom-right" '.selected( $sc_c_pos, 'bottom-right').'>bottom-right</option>
     	  </select> ';
		  
	echo '<label for="sc_c_col">';
	_e( 'Font Colour', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_col" name="sc_c_col">';
    echo '<option value=""'.selected( $sc_c_col, '').'>none</option>
		  <option value="white"'.selected( $sc_c_col, 'white').'>white</option> 
          <option value="black" '.selected( $sc_c_col, 'black').'>black</option>
          <option value="blue" '.selected( $sc_c_col, 'blue').'>blue</option>
		  <option value="yellow" '.selected( $sc_c_col, 'yellow').'>yellow</option>
          <option value="red" '.selected( $sc_c_col, 'red').'>red</option>
          <option value="violet" '.selected( $sc_c_col, 'violet').'>violet</option>
     	  </select> ';
		  
	echo '<label for="sc_c_size">';
	_e( 'Size', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_size" name="sc_c_size">';
    echo '<option value=""'.selected( $sc_c_size, '').'>none</option>
		  <option value="small"'.selected( $sc_c_size, 'small').'>small</option> 
          <option value="medium" '.selected( $sc_c_size, 'medium').'>medium</option>
          <option value="big" '.selected( $sc_c_size, 'big').'>big</option>
		  <option value="xxl" '.selected( $sc_c_size, 'xxl').'>xxl</option>
     	  </select> ';	  
	
	echo '<label for="sc_c_wrap">';
	_e( 'Wrap', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_wrap" name="sc_c_wrap">';
    echo '<option value=""'.selected( $sc_c_wrap, '').'>none</option>
		  <option value="full-width"'.selected( $sc_c_wrap, 'full-width').'>full-width</option> 
		  <option value="wraped-width-1600" '.selected( $sc_c_wrap, 'wraped-width-1600').'>wraped-width-1600</option>
		  <option value="wraped-width-1300" '.selected( $sc_c_wrap, 'wraped-width-1300').'>wraped-width-1300</option>
		  <option value="wraped-width-1180" '.selected( $sc_c_wrap, 'wraped-width-1180').'>wraped-width-1180</option>
          <option value="wraped-width-1040" '.selected( $sc_c_wrap, 'wraped-width-1040').'>wraped-width-1040</option>
		  <option value="wraped-width-760" '.selected( $sc_c_wrap, 'wraped-width-760').'>wraped-width-760</option>
		  <option value="wraped-width-520" '.selected( $sc_c_wrap, 'wraped-width-520').'>wraped-width-520</option>
     	  </select> ';	  
		   
	echo '<label for="sc_c_maxw">';
	_e( 'Max Width', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_maxw" name="sc_c_maxw">';
    echo '<option value=""'.selected( $sc_c_maxw, '').'>none</option>
		  <option value="max-width-160" '.selected( $sc_c_maxw, 'max-width-160').'>max-width-160</option> 
          <option value="max-width-240" '.selected( $sc_c_maxw, 'max-width-240').'>max-width-240</option>
		  <option value="max-width-320" '.selected( $sc_c_maxw, 'max-width-320').'>max-width-320</option>
		  <option value="max-width-480" '.selected( $sc_c_maxw, 'max-width-480').'>max-width-480</option>
		  <option value="max-width-640" '.selected( $sc_c_maxw, 'max-width-640').'>max-width-640</option>
		  <option value="max-width-800" '.selected( $sc_c_maxw, 'max-width-800').'>max-width-800</option>
		  <option value="max-width-1024" '.selected( $sc_c_maxw, 'max-width-1024').'>max-width-1024</option>
     	  </select> ';	
		  
	echo '<label for="sc_c_class">';
	_e( 'Custom Class', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_c_class" name="sc_c_class" value="' . $sc_c_class .  '" size="15" /><br /><br />';	  	

	echo '<label for="sc_c_infx">';
	_e( 'In Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_infx" name="sc_c_infx">';
	echo '<option value=""'.selected( $sc_c_infx, '').'>none</option>
		<optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_c_infx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_c_infx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_c_infx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_c_infx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_c_infx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_c_infx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_c_infx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_c_infx, 'wobble').'>wobble</option>
		</optgroup>
        <optgroup label="Bouncing Entrances">
          <option value="bounceIn" '.selected( $sc_c_infx, 'bounceIn').'>bounceIn</option>
          <option value="bounceInDown" '.selected( $sc_c_infx, 'bounceInDown').'>bounceInDown</option>
          <option value="bounceInLeft" '.selected( $sc_c_infx, 'bounceInLeft').'>bounceInLeft</option>
          <option value="bounceInRight" '.selected( $sc_c_infx, 'bounceInRight').'>bounceInRight</option>
          <option value="bounceInUp" '.selected( $sc_c_infx, 'bounceInUp').'>bounceInUp</option>
        </optgroup>
        <optgroup label="Fading Entrances">
          <option value="fadeIn" '.selected( $sc_c_infx, 'fadeIn').'>fadeIn</option>
          <option value="fadeInDown" '.selected( $sc_c_infx, 'fadeInDown').'>fadeInDown</option>
          <option value="fadeInDownBig" '.selected( $sc_c_infx, 'fadeInDownBig').'>fadeInDownBig</option>
          <option value="fadeInLeft" '.selected( $sc_c_infx, 'fadeInLeft').'>fadeInLeft</option>
          <option value="fadeInLeftBig" '.selected( $sc_c_infx, 'fadeInLeftBig').'>fadeInLeftBig</option>
          <option value="fadeInRight" '.selected( $sc_c_infx, 'fadeInRight').'>fadeInRight</option>
          <option value="fadeInRightBig" '.selected( $sc_c_infx, 'fadeInRightBig').'>fadeInRightBig</option>
          <option value="fadeInUp" '.selected( $sc_c_infx, 'fadeInUp').'>fadeInUp</option>
          <option value="fadeInUpBig" '.selected( $sc_c_infx, 'fadeInUpBig').'>fadeInUpBig</option>
        </optgroup>
        <optgroup label="Rotating Entrances">
          <option value="rotateIn" '.selected( $sc_c_infx, 'rotateIn').'>rotateIn</option>
          <option value="rotateInDownLeft" '.selected( $sc_c_infx, 'rotateInDownLeft').'>rotateInDownLeft</option>
          <option value="rotateInDownRight" '.selected( $sc_c_infx, 'rotateInDownRight').'>rotateInDownRight</option>
          <option value="rotateInUpLeft" '.selected( $sc_c_infx, 'rotateInUpLeft').'>rotateInUpLeft</option>
          <option value="rotateInUpRight" '.selected( $sc_c_infx, 'rotateInUpRight').'>rotateInUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_c_infx, 'flip').'>flip</option>
          <option value="flipInX" '.selected( $sc_c_infx, 'flipInX').'>flipInX</option>
          <option value="flipInY" '.selected( $sc_c_infx, 'flipInY').'>flipInY</option>
		  <option value="lightSpeedIn" '.selected( $sc_c_infx, 'lightSpeedIn').'>lightSpeedIn</option>
          <option value="hinge" '.selected( $sc_c_infx, 'hinge').'>hinge</option>
          <option value="rollIn" '.selected( $sc_c_infx, 'rollIn').'>rollIn</option>
          <option value="rollOut" '.selected( $sc_c_infx, 'rollOut').'>rollOut</option>
        </optgroup>
        <optgroup label="Zoom Entrances">
          <option value="zoomIn" '.selected( $sc_c_infx, 'zoomIn').'>zoomIn</option>
          <option value="zoomInDown" '.selected( $sc_c_infx, 'zoomInDown').'>zoomInDown</option>
          <option value="zoomInLeft" '.selected( $sc_c_infx, 'zoomInLeft').'>zoomInLeft</option>
          <option value="zoomInRight" '.selected( $sc_c_infx, 'zoomInRight').'>zoomInRight</option>
          <option value="zoomInUp" '.selected( $sc_c_infx, 'zoomInUp').'>zoomInUp</option>
        </optgroup>
      </select> ';

	echo '<label for="sc_c_infx_d">';
	_e( 'In Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_c_infx_d" name="sc_c_infx_d" value="' . $sc_c_infx_d . '"/> ';
	
	echo '<label for="sc_c_outfx">';
	_e( 'Out Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_c_outfx" name="sc_c_outfx">';
	echo '<option value=""'.selected( $sc_c_outfx, '').'>none</option>
		<optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_c_outfx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_c_outfx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_c_outfx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_c_outfx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_c_outfx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_c_outfx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_c_outfx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_c_outfx, 'wobble').'>wobble</option>
        </optgroup>
        <optgroup label="Bouncing Exits">
          <option value="bounceOut" '.selected( $sc_c_outfx, 'bounceOut').'>bounceOut</option>
          <option value="bounceOutDown" '.selected( $sc_c_outfx, 'bounceOutDown').'>bounceOutDown</option>
          <option value="bounceOutLeft" '.selected( $sc_c_outfx, 'bounceOutLeft').'>bounceOutLeft</option>
          <option value="bounceOutRight" '.selected( $sc_c_outfx, 'bounceOutRight').'>bounceOutRight</option>
          <option value="bounceOutUp" '.selected( $sc_c_outfx, 'bounceOutUp').'>bounceOutUp</option>
        </optgroup>
        <optgroup label="Fading Exits">
          <option value="fadeOut" '.selected( $sc_c_outfx, 'fadeOut').'>fadeOut</option>
          <option value="fadeOutDown" '.selected( $sc_c_outfx, 'fadeOutDown').'>fadeOutDown</option>
          <option value="fadeOutDownBig" '.selected( $sc_c_outfx, 'fadeOutDownBig').'>fadeOutDownBig</option>
          <option value="fadeOutLeft" '.selected( $sc_c_outfx, 'fadeOutLeft').'>fadeOutLeft</option>
          <option value="fadeOutLeftBig" '.selected( $sc_c_outfx, 'fadeOutLeftBig').'>fadeOutLeftBig</option>
          <option value="fadeOutRight" '.selected( $sc_c_outfx, 'fadeOutRight').'>fadeOutRight</option>
          <option value="fadeOutRightBig" '.selected( $sc_c_outfx, 'fadeOutRightBig').'>fadeOutRightBig</option>
          <option value="fadeOutUp" '.selected( $sc_c_outfx, 'fadeOutUp').'>fadeOutUp</option>
          <option value="fadeOutUpBig" '.selected( $sc_c_outfx, 'fadeOutUpBig').'>fadeOutUpBig</option>
        </optgroup>
        <optgroup label="Rotating Exits">
          <option value="rotateOut" '.selected( $sc_c_outfx, 'rotateOut').'>rotateOut</option>
          <option value="rotateOutDownLeft" '.selected( $sc_c_outfx, 'rotateOutDownLeft').'>rotateOutDownLeft</option>
          <option value="rotateOutDownRight" '.selected( $sc_c_outfx, 'rotateOutDownRight').'>rotateOutDownRight</option>
          <option value="rotateOutUpLeft" '.selected( $sc_c_outfx, 'rotateOutUpLeft').'>rotateOutUpLeft</option>
          <option value="rotateOutUpRight" '.selected( $sc_c_outfx, 'rotateOutUpRight').'>rotateOutUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_c_outfx, 'flip').'>flip</option>
          <option value="flipOutX" '.selected( $sc_c_outfx, 'flipOutX').'>flipOutX</option>
          <option value="flipOutY" '.selected( $sc_c_outfx, 'flipOutY').'>flipOutY</option>
		  <option value="lightSpeedOut" '.selected( $sc_c_outfx, 'lightSpeedOut').'>lightSpeedOut</option>
          <option value="hinge" '.selected( $sc_c_outfx, 'hinge').'>hinge</option>
		  <option value="rollOut" '.selected( $sc_c_outfx, 'rollOut').'>rollOut</option>
        </optgroup>
        <optgroup label="Zoom Exits">
          <option value="zoomOut" '.selected( $sc_c_outfx, 'zoomOut').'>zoomOut</option>
          <option value="zoomOutDown" '.selected( $sc_c_outfx, 'zoomOutDown').'>zoomOutDown</option>
          <option value="zoomOutLeft" '.selected( $sc_c_outfx, 'zoomOutLeft').'>zoomOutLeft</option>
          <option value="zoomOutRight" '.selected( $sc_c_outfx, 'zoomOutRight').'>zoomOutRight</option>
          <option value="zoomOutUp" '.selected( $sc_c_outfx, 'zoomOutUp').'>zoomOutUp</option>
        </optgroup>
      </select> ';
	  
	echo '<label for="sc_c_outfx_d">';
	_e( 'Out Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_c_outfx_d" name="sc_c_outfx_d" value="' . $sc_c_outfx_d . '"/><br /><br />';
	
	/******* EXCERPT *******/
	echo '<h3 class="hndle"><span>';
	_e( 'Content Box', 'slidercat' );
	echo ' </span></h3>';
	echo '<br /><label for="sc_e_pos">';
	_e( 'Position', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_pos" name="sc_e_pos">';
    echo '<option value=""'.selected( $sc_e_pos, '').'>none</option>
	      <option value="top-left"'.selected( $sc_e_pos, 'top-left').'>top-left</option> 
          <option value="center-left" '.selected( $sc_e_pos, 'center-left').'>center-left</option>
          <option value="bottom-left" '.selected( $sc_e_pos, 'bottom-left').'>bottom-left</option>
		  <option value="center-top" '.selected( $sc_e_pos, 'center-top').'>center-top</option>
          <option value="center-center" '.selected( $sc_e_pos, 'center-center').'>center-center</option>
          <option value="center-bottom" '.selected( $sc_e_pos, 'center-bottom').'>center-bottom</option>
          <option value="top-right" '.selected( $sc_e_pos, 'top-right').'>top-right</option>
          <option value="center-right" '.selected( $sc_e_pos, 'center-right').'>center-right</option>
		  <option value="bottom-right" '.selected( $sc_e_pos, 'bottom-right').'>bottom-right</option>
     	  </select> ';
		  
	echo '<label for="sc_e_col">';
	_e( 'Font Colour', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_col" name="sc_e_col">';
    echo '<option value=""'.selected( $sc_e_col, '').'>none</option>
	      <option value="white"'.selected( $sc_e_col, 'white').'>white</option> 
          <option value="black" '.selected( $sc_e_col, 'black').'>black</option>
          <option value="blue" '.selected( $sc_e_col, 'blue').'>blue</option>
		  <option value="yellow" '.selected( $sc_e_col, 'yellow').'>yellow</option>
          <option value="red" '.selected( $sc_e_col, 'red').'>red</option>
          <option value="violet" '.selected( $sc_e_col, 'violet').'>violet</option>
     	  </select> ';
		  
	echo '<label for="sc_e_size">';
	_e( 'Size', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_size" name="sc_e_size">';
    echo '<option value=""'.selected( $sc_e_size, '').'>none</option>
	      <option value="small"'.selected( $sc_e_size, 'small').'>small</option> 
          <option value="medium" '.selected( $sc_e_size, 'medium').'>medium</option>
          <option value="big" '.selected( $sc_e_size, 'big').'>big</option>
		  <option value="xxl" '.selected( $sc_e_size, 'xxl').'>xxl</option>
     	  </select> ';	 
		  
	echo '<label for="sc_e_wrap">';
	_e( 'Wrap', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_wrap" name="sc_e_wrap">';
    echo '<option value=""'.selected( $sc_e_wrap, '').'>none</option>
		  <option value="full-width"'.selected( $sc_e_wrap, 'full-width').'>full-width</option> 
          <option value="wraped-width-1600" '.selected( $sc_e_wrap, 'wraped-width-1600').'>wraped-width-1600</option>
		  <option value="wraped-width-1300" '.selected( $sc_e_wrap, 'wraped-width-1300').'>wraped-width-1300</option>
		  <option value="wraped-width-1180" '.selected( $sc_e_wrap, 'wraped-width-1180').'>wraped-width-1180</option>
          <option value="wraped-width-1040" '.selected( $sc_e_wrap, 'wraped-width-1040').'>wraped-width-1040</option>
		  <option value="wraped-width-760" '.selected( $sc_e_wrap, 'wraped-width-760').'>wraped-width-760</option>
		  <option value="wraped-width-520" '.selected( $sc_e_wrap, 'wraped-width-520').'>wraped-width-520</option>
     	  </select> ';	  
		  
	echo '<label for="sc_e_maxw">';
	_e( 'Max Width', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_maxw" name="sc_e_maxw">';
    echo '<option value=""'.selected( $sc_e_maxw, '').'>none</option>
		  <option value="max-width-160" '.selected( $sc_e_maxw, 'max-width-160').'>max-width-160</option> 
          <option value="max-width-240" '.selected( $sc_e_maxw, 'max-width-240').'>max-width-240</option>
		  <option value="max-width-320" '.selected( $sc_e_maxw, 'max-width-320').'>max-width-320</option>
		  <option value="max-width-480" '.selected( $sc_e_maxw, 'max-width-480').'>max-width-480</option>
		  <option value="max-width-640" '.selected( $sc_e_maxw, 'max-width-640').'>max-width-640</option>
		  <option value="max-width-800" '.selected( $sc_e_maxw, 'max-width-800').'>max-width-800</option>
		  <option value="max-width-1024" '.selected( $sc_e_maxw, 'max-width-1024').'>max-width-1024</option>
     	  </select> ';	
		  
	echo '<label for="sc_e_class">';
	_e( 'Custom Class', 'slidercat' );
	echo '</label> ';
	echo '<input type="text" id="sc_e_class" name="sc_e_class" value="' . $sc_e_class .  '" size="15" /><br /><br />';	  	

	echo '<label for="sc_e_infx">';
	_e( 'In Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_infx" name="sc_e_infx">';
	echo '<option value=""'.selected( $sc_e_infx, '').'>none</option>
	    <optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_e_infx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_e_infx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_e_infx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_e_infx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_e_infx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_e_infx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_e_infx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_e_infx, 'wobble').'>wobble</option>
        </optgroup>
        <optgroup label="Bouncing Entrances">
          <option value="bounceIn" '.selected( $sc_e_infx, 'bounceIn').'>bounceIn</option>
          <option value="bounceInDown" '.selected( $sc_e_infx, 'bounceInDown').'>bounceInDown</option>
          <option value="bounceInLeft" '.selected( $sc_e_infx, 'bounceInLeft').'>bounceInLeft</option>
          <option value="bounceInRight" '.selected( $sc_e_infx, 'bounceInRight').'>bounceInRight</option>
          <option value="bounceInUp" '.selected( $sc_e_infx, 'bounceInUp').'>bounceInUp</option>
        </optgroup>
        <optgroup label="Fading Entrances">
          <option value="fadeIn" '.selected( $sc_e_infx, 'fadeIn').'>fadeIn</option>
          <option value="fadeInDown" '.selected( $sc_e_infx, 'fadeInDown').'>fadeInDown</option>
          <option value="fadeInDownBig" '.selected( $sc_e_infx, 'fadeInDownBig').'>fadeInDownBig</option>
          <option value="fadeInLeft" '.selected( $sc_e_infx, 'fadeInLeft').'>fadeInLeft</option>
          <option value="fadeInLeftBig" '.selected( $sc_e_infx, 'fadeInLeftBig').'>fadeInLeftBig</option>
          <option value="fadeInRight" '.selected( $sc_e_infx, 'fadeInRight').'>fadeInRight</option>
          <option value="fadeInRightBig" '.selected( $sc_e_infx, 'fadeInRightBig').'>fadeInRightBig</option>
          <option value="fadeInUp" '.selected( $sc_e_infx, 'fadeInUp').'>fadeInUp</option>
          <option value="fadeInUpBig" '.selected( $sc_e_infx, 'fadeInUpBig').'>fadeInUpBig</option>
        </optgroup>
        <optgroup label="Rotating Entrances">
          <option value="rotateIn" '.selected( $sc_e_infx, 'rotateIn').'>rotateIn</option>
          <option value="rotateInDownLeft" '.selected( $sc_e_infx, 'rotateInDownLeft').'>rotateInDownLeft</option>
          <option value="rotateInDownRight" '.selected( $sc_e_infx, 'rotateInDownRight').'>rotateInDownRight</option>
          <option value="rotateInUpLeft" '.selected( $sc_e_infx, 'rotateInUpLeft').'>rotateInUpLeft</option>
          <option value="rotateInUpRight" '.selected( $sc_e_infx, 'rotateInUpRight').'>rotateInUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_e_infx, 'flip').'>flip</option>
          <option value="flipInX" '.selected( $sc_e_infx, 'flipInX').'>flipInX</option>
          <option value="flipInY" '.selected( $sc_e_infx, 'flipInY').'>flipInY</option>
		  <option value="lightSpeedIn" '.selected( $sc_e_infx, 'lightSpeedIn').'>lightSpeedIn</option>
          <option value="hinge" '.selected( $sc_e_infx, 'hinge').'>hinge</option>
          <option value="rollIn" '.selected( $sc_e_infx, 'rollIn').'>rollIn</option>
        </optgroup>
        <optgroup label="Zoom Entrances">
          <option value="zoomIn" '.selected( $sc_e_infx, 'zoomIn').'>zoomIn</option>
          <option value="zoomInDown" '.selected( $sc_e_infx, 'zoomInDown').'>zoomInDown</option>
          <option value="zoomInLeft" '.selected( $sc_e_infx, 'zoomInLeft').'>zoomInLeft</option>
          <option value="zoomInRight" '.selected( $sc_e_infx, 'zoomInRight').'>zoomInRight</option>
          <option value="zoomInUp" '.selected( $sc_e_infx, 'zoomInUp').'>zoomInUp</option>
        </optgroup>
      </select> ';

	echo '<label for="sc_e_infx_d">';
	_e( 'In Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_e_infx_d" name="sc_e_infx_d" value="' . $sc_e_infx_d .  '"/> ';
	
	echo '<label for="sc_e_outfx">';
	_e( 'Out Animation', 'slidercat' );
	echo '</label> ';
	echo '<select type="text" id="sc_e_outfx" name="sc_e_outfx">';
	echo '<option value=""'.selected( $sc_e_outfx, '').'>none</option>
		<optgroup label="Attention Seekers">';
    echo '<option value="bounce"'.selected( $sc_e_outfx, 'bounce').'>bounce</option> 
          <option value="flash" '.selected( $sc_e_outfx, 'flash').'>flash</option>
          <option value="pulse" '.selected( $sc_e_outfx, 'pulse').'>pulse</option>
          <option value="rubberBand" '.selected( $sc_e_outfx, 'rubberBand').'>rubberBand</option>
          <option value="shake" '.selected( $sc_e_outfx, 'shake').'>shake</option>
          <option value="swing" '.selected( $sc_e_outfx, 'swing').'>swing</option>
          <option value="tada" '.selected( $sc_e_outfx, 'tada').'>tada</option>
          <option value="wobble" '.selected( $sc_e_outfx, 'wobble').'>wobble</option>
		</optgroup>
		<optgroup label="Bouncing Exits">
          <option value="bounceOut" '.selected( $sc_e_outfx, 'bounceOut').'>bounceOut</option>
          <option value="bounceOutDown" '.selected( $sc_e_outfx, 'bounceOutDown').'>bounceOutDown</option>
          <option value="bounceOutLeft" '.selected( $sc_e_outfx, 'bounceOutLeft').'>bounceOutLeft</option>
          <option value="bounceOutRight" '.selected( $sc_e_outfx, 'bounceOutRight').'>bounceOutRight</option>
          <option value="bounceOutUp" '.selected( $sc_e_outfx, 'bounceOutUp').'>bounceOutUp</option>
        </optgroup>
        <optgroup label="Fading Exits">
          <option value="fadeOut" '.selected( $sc_e_outfx, 'fadeOut').'>fadeOut</option>
          <option value="fadeOutDown" '.selected( $sc_e_outfx, 'fadeOutDown').'>fadeOutDown</option>
          <option value="fadeOutDownBig" '.selected( $sc_e_outfx, 'fadeOutDownBig').'>fadeOutDownBig</option>
          <option value="fadeOutLeft" '.selected( $sc_e_outfx, 'fadeOutLeft').'>fadeOutLeft</option>
          <option value="fadeOutLeftBig" '.selected( $sc_e_outfx, 'fadeOutLeftBig').'>fadeOutLeftBig</option>
          <option value="fadeOutRight" '.selected( $sc_e_outfx, 'fadeOutRight').'>fadeOutRight</option>
          <option value="fadeOutRightBig" '.selected( $sc_e_outfx, 'fadeOutRightBig').'>fadeOutRightBig</option>
          <option value="fadeOutUp" '.selected( $sc_e_outfx, 'fadeOutUp').'>fadeOutUp</option>
          <option value="fadeOutUpBig" '.selected( $sc_e_outfx, 'fadeOutUpBig').'>fadeOutUpBig</option>
        </optgroup>
        <optgroup label="Rotating Exits">
          <option value="rotateOut" '.selected( $sc_e_outfx, 'rotateOut').'>rotateOut</option>
          <option value="rotateOutDownLeft" '.selected( $sc_e_outfx, 'rotateOutDownLeft').'>rotateOutDownLeft</option>
          <option value="rotateOutDownRight" '.selected( $sc_e_outfx, 'rotateOutDownRight').'>rotateOutDownRight</option>
          <option value="rotateOutUpLeft" '.selected( $sc_e_outfx, 'rotateOutUpLeft').'>rotateOutUpLeft</option>
          <option value="rotateOutUpRight" '.selected( $sc_e_outfx, 'rotateOutUpRight').'>rotateOutUpRight</option>
        </optgroup>
        <optgroup label="Specials">
		  <option value="flip" '.selected( $sc_e_outfx, 'flip').'>flip</option>
          <option value="flipOutX" '.selected( $sc_e_outfx, 'flipOutX').'>flipOutX</option>
          <option value="flipOutY" '.selected( $sc_e_outfx, 'flipOutY').'>flipOutY</option>
		  <option value="lightSpeedOut" '.selected( $sc_e_outfx, 'lightSpeedOut').'>lightSpeedOut</option>
          <option value="hinge" '.selected( $sc_e_outfx, 'hinge').'>hinge</option>
          <option value="rollOut" '.selected( $sc_e_outfx, 'rollOut').'>rollOut</option>
        </optgroup>
		<optgroup label="Zoom Exits">
          <option value="zoomOut" '.selected( $sc_e_outfx, 'zoomOut').'>zoomOut</option>
          <option value="zoomOutDown" '.selected( $sc_e_outfx, 'zoomOutDown').'>zoomOutDown</option>
          <option value="zoomOutLeft" '.selected( $sc_e_outfx, 'zoomOutLeft').'>zoomOutLeft</option>
          <option value="zoomOutRight" '.selected( $sc_e_outfx, 'zoomOutRight').'>zoomOutRight</option>
          <option value="zoomOutUp" '.selected( $sc_e_outfx, 'zoomOutUp').'>zoomOutUp</option>
        </optgroup>
		</select> ';

	echo '<label for="sc_e_outfx_d">';
	_e( 'Out Delay', 'slidercat' );
	echo '</label> ';
	echo '<input class="small-text" type="number" min="0" step="1" id="sc_e_outfx_d" name="sc_e_outfx_d" value="' . $sc_e_outfx_d .  '"/>';
	}	
}
	if( is_admin() )
	$my_settings_page = new SliderCatMenuPage();
	?>