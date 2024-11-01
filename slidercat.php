<?php
/*
Plugin Name: sliderCat
Plugin URI: http://wordpress.transformnews.com
Description: Multiple shortcode slideshows with bunch of animated text effects and styles...
Version: 1.0
Author: m.r.d.a
Author URI: http://wordpress.transformnews.com
Text domain: slidercat
Domain Path: /languages
License: GPLv2 or later
*/

defined('ABSPATH') or die("Cannot access pages directly.");

if( is_admin() )
	include_once 'slidercat-admin.php';

   class SlidercatFront
{
	
	static $sc_add_script;

	static function scinit() {
		
		add_action( 'init', array( __CLASS__, 'slidercat_taxonomy'));
		add_action( 'init', array( __CLASS__, 'slidercat_post_type'));
		add_shortcode( 'slidercat', array( __CLASS__, 'slidercat_shortcode' ));
		add_action('init', array(__CLASS__, 'slidercat_register_script'));
		add_action('wp_footer', array(__CLASS__, 'slidercat_print_script'));
		add_action('wp_enqueue_scripts', array(__CLASS__, 'slidercat_enqueue_style'));
		
	}
	
	static function slidercat_taxonomy() {

	$labels = array(
		'name'                       => _x( 'Slideshows', 'Taxonomy General Name', 'slidercat' ),
		'singular_name'              => _x( 'Slideshow', 'Taxonomy Singular Name', 'slidercat' ),
		'menu_name'                  => __( 'Slideshows', 'slidercat' ),
		'all_items'                  => __( 'All Slides', 'slidercat' ),
		'parent_item'                => __( 'Parent Slide', 'slidercat' ),
		'parent_item_colon'          => __( 'Parent Slide:', 'slidercat' ),
		'new_item_name'              => __( 'New Slide Name', 'slidercat' ),
		'add_new_item'               => __( 'Add New Slideshow', 'slidercat' ),
		'edit_item'                  => __( 'Edit Slideshow', 'slidercat' ),
		'update_item'                => __( 'Update Slide', 'slidercat' ),
		'separate_items_with_commas' => __( '', 'slidercat' ),
		'search_items'               => __( 'Search Slides', 'slidercat' ),
		'add_or_remove_items'        => __( 'Add or remove slides', 'slidercat' ),
		'choose_from_most_used'      => __( 'Choose from the most used slideshows', 'slidercat' ),
		'not_found'                  => __( 'Not Found', 'slidercat' ),
	);
	
	$args = array(
		'labels'                     => $labels,
		'hierarchical'               => false,
		'public'                     => false,
		'show_ui'                    => true,
		'show_admin_column'          => true,
		'show_in_nav_menus'          => false,
		'show_tagcloud'              => false,
		'rewrite'                    => false,
	);
	register_taxonomy( 'slidercat', array( 'slidercat_post_type' ), $args );
	
	}

	static function slidercat_post_type() {

	$labels = array(
		'name'                => _x( 'Slides', 'Post Type General Name', 'slidercat' ),
		'singular_name'       => _x( 'Slide', 'Post Type Singular Name', 'slidercat' ),
		'menu_name'           => __( 'sliderCat', 'slidercat' ),
		'parent_item_colon'   => __( 'Parent Item:', 'slidercat' ),
		'all_items'           => __( 'All Slides', 'slidercat' ),
		'view_item'           => __( 'View Slide', 'slidercat' ),
		'add_new_item'        => __( 'Add New Slide', 'slidercat' ),
		'add_new'             => __( 'Add New Slide', 'slidercat' ),
		'edit_item'           => __( 'Edit Slide', 'slidercat' ),
		'update_item'         => __( 'Update Slide', 'slidercat' ),
		'search_items'        => __( 'Search Slide', 'slidercat' ),
		'not_found'           => __( 'Not found', 'slidercat' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'slidercat' ),
	);
	
	$args = array(
		'label'               => __( 'post_type_slider', 'slidercat' ),
		'description'         => __( 'Create simple slider', 'slidercat' ),
		'labels'              => $labels,
		'supports'            => array( 'title', 'thumbnail' ),
		'taxonomies'          => array( 'slidercat' ),
		'hierarchical'        => false,
		'public'              => false,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => false,
		'show_in_admin_bar'   => true,
		'menu_position'       => 100,
		'can_export'          => true,
		'has_archive'         => false,
		'exclude_from_search' => true,
		'publicly_queryable'  => false,
		'rewrite'             => false,
		'capability_type'     => 'page',
	);
	register_post_type( 'post_type_slider', $args );
	
	}

	static function slidercat_shortcode( $atts ) {
		
		self::$sc_add_script = true;
		global $post;
    	ob_start();
	
    extract( shortcode_atts( array (
	    'ids' => '',
        'type' => 'post',
        'order' => '',// date asc desc
        'orderby' => '', // rand
        'posts' => -1,
        'cat' => '',
		
    ), $atts ) );
 	
    $options = array(
		'scid' => $ids,
        'post_type' => 'post_type_slider',
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
        'slidercat' => $cat,
    );
	
    $query = new WP_Query( $options );
	$cat = explode(',', $atts['cat']);
	
	if ( $query->have_posts() ) { 
    
	$term = get_term_by('slug', $cat[0], 'slidercat'); 
	
	if ( isset( $term->term_id ) ) { 
	
	$t_id = $term->term_id; 
	$term_meta = get_option( "slidercat_$t_id" );
	
	$sc_trans_arr = array( 
    'taxid_str' => $term->term_id,
	'taxfx_str' => $term_meta['tax_fx'],
	'taxanimspd_str' => $term_meta['tax_animspd'],
	'taxvideo_str' => $term_meta['tax_video'],
	'taxdirect_str' => $term_meta['tax_direction'],
	'taxrevordr_str' => $term_meta['tax_revordr'],
	'taxanimloop_str' => $term_meta['tax_animloop'],
	'taxpause_str' => $term_meta['tax_pause'],
	'taxsmoothh_str' => $term_meta['tax_smoothh'],
	'taxrandom_str' => $term_meta['tax_random'],
	'taxcarwdth_str' => $term_meta['tax_carwdth'],
	'taxcarmin_str' => $term_meta['tax_carmin'],
	'taxcarmax_str' => $term_meta['tax_carmax'],
	'taxmousew_str' => $term_meta['tax_mousew'],
	'taxease_str' => $term_meta['tax_ease'],
	'taxasnavfor_str' => $term_meta['tax_asnavfor'],
	'taxsync_str' => $term_meta['tax_sync'],
			);
			 wp_localize_script( 'schelper', 'slidercat_' . $t_id, $sc_trans_arr );
		}
	
	wp_reset_postdata();

	echo '<div id="slidercat_'. $t_id .'" class="slidercat loading" data-token="'. $t_id .'">';
	echo '<div class="slides">';
	while ( $query->have_posts() ) : $query->the_post();	
                
	$data_arr = get_post_meta( $post->ID, "_slidercat", true);
	if ( isset( $data_arr['sc_dd'] ) ) $sc_dd = esc_attr( $data_arr['sc_dd']);
	if ( isset( $data_arr['sc_url'] ) ) $sc_url = esc_attr( $data_arr['sc_url']);
	if ( isset( $data_arr['sc_url_targ'] ) ) $sc_url_targ = esc_attr( $data_arr['sc_url_targ']);
	if ( isset( $data_arr['sc_yv'] ) ) $sc_yv = esc_attr( $data_arr['sc_yv']);
	if ( isset( $data_arr['sc_t_enable'] ) ) $sc_t_enable = esc_attr( $data_arr['sc_t_enable']);
	if ( isset( $data_arr['sc_t_url'] ) ) $sc_t_url = esc_attr( $data_arr['sc_t_url']);
	if ( isset( $data_arr['sc_t_url_targ'] ) ) { $sc_t_url_targ = esc_attr( $data_arr['sc_t_url_targ']);}
	if ( isset( $data_arr['sc_t_pos'] ) ) $sc_t_pos = esc_attr( $data_arr['sc_t_pos']);
	if ( isset( $data_arr['sc_t_col'] ) ) $sc_t_col = esc_attr( $data_arr['sc_t_col']);
	if ( isset( $data_arr['sc_t_size'] ) ) $sc_t_size = esc_attr( $data_arr['sc_t_size']);
	if ( isset( $data_arr['sc_t_wrap'] ) ) $sc_t_wrap = esc_attr( $data_arr['sc_t_wrap']);
	if ( isset( $data_arr['sc_t_maxw'] ) ) $sc_t_maxw = esc_attr( $data_arr['sc_t_maxw']);
	if ( isset( $data_arr['sc_t_class'] ) ) $sc_t_class = esc_attr( $data_arr['sc_t_class']);
	if ( isset( $data_arr['sc_t_infx'] ) ) $sc_t_infx = esc_attr( $data_arr['sc_t_infx']);
	if ( isset( $data_arr['sc_t_infx_d'] ) ) $sc_t_infx_d = esc_attr( $data_arr['sc_t_infx_d']); $sc_t_infx_d_dev = $sc_t_infx_d / 1000;
	if ( isset( $data_arr['sc_t_outfx'] ) ) $sc_t_outfx =  esc_attr( $data_arr['sc_t_outfx']);
	if ( isset( $data_arr['sc_t_outfx_d'] ) ) $sc_t_outfx_d = esc_attr( $data_arr['sc_t_outfx_d']); $sc_t_outfx_d_dev = $sc_t_infx_d / 1000;
	if ( isset( $data_arr['sc_c'] ) ) $sc_c = html_entity_decode( $data_arr['sc_c']);
	if ( isset( $data_arr['sc_c_pos'] ) ) $sc_c_pos = esc_attr( $data_arr['sc_c_pos']);
	if ( isset( $data_arr['sc_c_col'] ) ) $sc_c_col = esc_attr( $data_arr['sc_c_col']);
	if ( isset( $data_arr['sc_c_size'] ) ) $sc_c_size = esc_attr( $data_arr['sc_c_size']);
	if ( isset( $data_arr['sc_c_wrap'] ) ) $sc_c_wrap = esc_attr( $data_arr['sc_c_wrap']);
	if ( isset( $data_arr['sc_c_maxw'] ) ) $sc_c_maxw = esc_attr( $data_arr['sc_c_maxw']);
	if ( isset( $data_arr['sc_c_class'] ) ) $sc_c_class = esc_attr( $data_arr['sc_c_class']);
	if ( isset( $data_arr['sc_c_infx'] ) ) $sc_c_infx = esc_attr( $data_arr['sc_c_infx']);
	if ( isset( $data_arr['sc_c_infx_d'] ) ) $sc_c_infx_d = esc_attr( $data_arr['sc_c_infx_d']); $sc_c_infx_d_dev = $sc_c_infx_d / 1000;
	if ( isset( $data_arr['sc_c_outfx'] ) ) $sc_c_outfx = esc_attr( $data_arr['sc_c_outfx']);
	if ( isset( $data_arr['sc_c_outfx_d'] ) ) $sc_c_outfx_d = esc_attr( $data_arr['sc_c_outfx_d']); $sc_c_outfx_d_dev = $sc_c_outfx_d / 1000;
	if ( isset( $data_arr['sc_e'] ) ) $sc_e =  html_entity_decode($data_arr['sc_e']) ;
	if ( isset( $data_arr['sc_e_pos'] ) ) $sc_e_pos = esc_attr( $data_arr['sc_e_pos']);
	if ( isset( $data_arr['sc_e_col'] ) ) $sc_e_col = esc_attr( $data_arr['sc_e_col']);
	if ( isset( $data_arr['sc_e_size'] ) ) $sc_e_size = esc_attr( $data_arr['sc_e_size']);
	if ( isset( $data_arr['sc_e_wrap'] ) ) $sc_e_wrap = esc_attr( $data_arr['sc_e_wrap']);
	if ( isset( $data_arr['sc_e_maxw'] ) ) $sc_e_maxw = esc_attr( $data_arr['sc_e_maxw']);
	if ( isset( $data_arr['sc_e_class'] ) ) $sc_e_class = esc_attr( $data_arr['sc_e_class']);
	if ( isset( $data_arr['sc_e_infx'] ) ) $sc_e_infx = esc_attr( $data_arr['sc_e_infx']);
	if ( isset( $data_arr['sc_e_infx_d'] ) ) $sc_e_infx_d = esc_attr( $data_arr['sc_e_infx_d']); $sc_e_infx_d_dev = $sc_e_infx_d / 1000;
	if ( isset( $data_arr['sc_e_outfx'] ) ) $sc_e_outfx =  esc_attr( $data_arr['sc_e_outfx']);
	if ( isset( $data_arr['sc_e_outfx_d'] ) ) $sc_e_outfx_d = esc_attr( $data_arr['sc_e_outfx_d']); $sc_e_outfx_d_dev = $sc_e_outfx_d / 1000;

	echo '<div class="slidercatitem" data-duration="'. $sc_dd .'">';
		
	if ($sc_url !== ''){
		echo '<a href="'.$sc_url.'" target="' . $sc_url_targ . '">';
		the_post_thumbnail($term_meta['tax_image']);
		echo '</a>';
		} else {
		the_post_thumbnail($term_meta['tax_image']);
		}

	if ($sc_yv) {
		echo '<div class="sc_yv">';
		// Echo the embed code via oEmbed
		echo wp_oembed_get( $sc_yv ); 
		echo '</div>';
		}
       
	if ($sc_t_enable !== 'false'){
		echo '<div class="slidercatcontent '. $sc_t_infx .' '. $sc_t_pos . ' ' . $sc_t_col . ' ' . $sc_t_size . ' ' . $sc_t_wrap . ' ' . $sc_t_maxw . ' ' . $sc_t_class . ' animated" data-animation-delay="'. $sc_t_infx_d_dev .'" style="animation-delay:'. $sc_t_infx_d .'ms; -moz-animation-delay: '. $sc_t_infx_d .'ms; -webkit-animation-delay: '. $sc_t_infx_d .'ms;">';
                    
        echo '<div class="slidercatcontent '. $sc_t_outfx .' animated" data-animation-delay=" '. $sc_t_outfx_d_dev .'" style="animation-delay: '. $sc_t_outfx_d .'ms; -moz-animation-delay: '. $sc_t_outfx_d.'ms; -webkit-animation-delay: '. $sc_t_outfx_d.'ms;">';
              
		echo '<h1><p>';
               
	if ( $sc_t_url) { 
        
        echo '<a href="'. $sc_t_url .'" target="' . $sc_t_url_targ . '">';
		the_title();
        echo '</a>'; 

		} else { 
		the_title(); 
		}  

		echo '</p></h1></div></div>';
		}
		
	if ($sc_c != "") {

		echo '<div class="slidercatcontent '. $sc_c_infx .' '. $sc_c_pos .' '. $sc_c_col .' '. $sc_c_size . ' ' . $sc_c_wrap . ' ' . $sc_c_maxw . ' ' . $sc_c_class.' animated" data-animation-delay="'. $sc_c_infx_d_dev .'" style="animation-delay: '. $sc_c_infx_d .'ms; -moz-animation-delay: '. $sc_c_infx_d .'ms; -webkit-animation-delay: '. $sc_c_infx_d .'ms;">';
                    
		echo '<div class="slidercatcontent '. $sc_c_outfx .' animated" data-animation-delay="'.$sc_c_outfx_d_dev.'" style="animation-delay: '. $sc_c_outfx_d .'ms; -moz-animation-delay: '. $sc_c_outfx_d .'ms; -webkit-animation-delay: '. $sc_c_outfx_d .'ms;">';

		echo '<h2><p>';
		echo $sc_c;
		echo '</p></h2></div></div>';
		}

	if ($sc_e != "") {
		  
		echo '<div class="slidercatcontent ' . $sc_e_infx . ' ' . $sc_e_pos . ' ' . $sc_e_col . ' ' . $sc_e_size . ' ' . $sc_e_wrap . ' ' . $sc_e_maxw . ' ' . $sc_e_class . ' animated" data-animation-delay="' . $sc_e_infx_d_dev .'" style="animation-delay:' . $sc_e_infx_d . 'ms; -moz-animation-delay:'. $sc_e_infx_d.'ms; -webkit-animation-delay:' . $sc_e_infx_d . 'ms;">';
		  
		echo '<div class="slidercatcontent ' . $sc_e_outfx . ' animated" data-animation-delay="' . $sc_e_outfx_d_dev .'" style="animation-delay:'.$sc_e_outfx_d.'ms; -moz-animation-delay:'.$sc_e_outfx_d.'ms; -webkit-animation-delay:'.$sc_e_outfx_d.'ms;">';
		
		echo '<h2><p>';
		//the_excerpt(FALSE,TRUE);
		
		echo $sc_e;
		echo '</p></h2>';
		echo '</div></div>'; }   
		echo '</div>';
		endwhile; 
		wp_reset_postdata(); 
		echo '</div></div>'; 
    	$cleanobvar = ob_get_clean();
   		return $cleanobvar;                    
		}
	}
	
	static function slidercat_register_script() {
		
		$slidercat_options = get_option( 'slidercat_options' );
		
		if ( $slidercat_options ['sc_enable_flex'] == 'true' )
			wp_register_script( 'slidercat', WP_PLUGIN_URL. '/slidercat/js/jquery.flexslider-min.js', array( 'jquery' ) );

		if ( $slidercat_options ['sc_enable_ease'] == 'true' )
			wp_register_script( 'sceasing', WP_PLUGIN_URL. '/slidercat/js/jquery.easing.1.3.min.js', array( 'jquery' ), '1.0', true  );

		if ( $slidercat_options ['sc_enable_mw'] == 'true' )
			wp_register_script( 'mousewheel', WP_PLUGIN_URL. '/slidercat/js/jquery.mousewheel.min.js', array( 'jquery' ), '1.0', true );

			wp_register_script('schelper', WP_PLUGIN_URL. '/slidercat/js/jquery.flexslider.helper.js', false,'1.0.0', true); 
	}

	static function slidercat_print_script() {
		
		if ( ! self::$sc_add_script )
			return;
			
		$slidercat_options = get_option( 'slidercat_options' );
		if ( $slidercat_options ['sc_enable_flex'] == 'true' ) wp_print_scripts('slidercat');
		if ( $slidercat_options ['sc_enable_ease'] == 'true' ) wp_print_scripts('sceasing');
		if ( $slidercat_options ['sc_enable_mw'] == 'true' ) wp_print_scripts('mousewheel');
		wp_print_scripts('schelper');
	}
	
	static function slidercat_enqueue_style() {		
	
		$slidercat_options = get_option( 'slidercat_options' );
		if ( $slidercat_options ['sc_enable_css'] == 'true' ) wp_enqueue_style('slidercat', WP_PLUGIN_URL.'/slidercat/css/slidercat.css',false,'2.2.0');
		if( file_exists(plugin_dir_path(__FILE__) . '/css/slidercat-custom.css') ) wp_enqueue_style ('slidercustom', WP_PLUGIN_URL.'/slidercat/css/slidercat-custom.css', false,'1.0.0');
		if ( $slidercat_options ['sc_enable_anim'] == 'true' ) wp_enqueue_style('animation', WP_PLUGIN_URL.'/slidercat/css/animate.min.css', false, '1.0.0');
	}
	

}

SlidercatFront::scinit();
?>