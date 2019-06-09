<?php

/*
  IsoPost : CUSTOM POST TYPE
*/
class isopost_Shortcode_CusotmPostType {
	
	public function __construct()
	{
		$this->register_post_type();
	}

	public function register_post_type()
	{
		$args = array();

		// isopost Shortcode
		$args['post-type-shortcode'] = array(
			'labels' => array(
				'name' => __( 'IsoPost Shortcodes', 'isopost' ),
				'singular_name' => __( 'isopost Shortcode', 'isopost' ),
				'all_items' => 'Shortcodes',
				'add_new' => __( 'Add New', 'isoposts' ),
				'add_new_item' => __( 'Add New isopost Shortcode', 'isopost' ),
				'edit_item' => __( 'Edit isopost Shortcode', 'isopost' ),
				'new_item' => __( 'New isopost Shortcode', 'isopost' ),
				'view_item' => __( 'View isopost Shortcode', 'isopost' ),
				'search_items' => __( 'Search Through isopost Shortcode Items', 'isopost' ),
				'not_found' => __( 'No shortcodes found', 'isopost' ),
				'not_found_in_trash' => __( 'No shortcodes found in Trash', 'isopost' ),
				'parent_item_colon' => __( 'Parent isopost Shortcode :', 'isopost' ),
				'menu_name' => __( 'IsoCode', 'isocode' ),
			),	
				'public' => false,  
				'publicly_queriable' => true,  
				'show_ui' => true, 
				'exclude_from_search' => true,  
				'show_in_nav_menus' => false, 
				'has_archive' => false, 
				'rewrite' => false, 
				'hierarchical' => false,
				'can_export' => false,
				'menu_position' => 9,
				'description' => __( 'Add a Shortcode item', 'isopost' ),
				'supports' => array( 'title'),
				'menu_icon' =>  'dashicons-screenoptions',
				'show_in_menu'  =>  'edit.php?post_type=isopost',
			);

		register_post_type('isopost-scode', $args['post-type-shortcode']);
	}
}




function isopost_init_shortcodecpt() { new isopost_Shortcode_CusotmPostType(); }
add_action( 'init', 'isopost_init_shortcodecpt' );

#-----------------------------------------------------------------#
# SORTABLE (ID) COLUMN
#-----------------------------------------------------------------#

add_filter('manage_edit-isopost-scode_columns', 'isopost_register_isopostscode_columns');
function isopost_register_isopostscode_columns($columns){

	$new_columns = array(
		'cb'     => '<input type="checkbox" />',
		'title'  => 'Name',
		'_isopost_shcode' => 'Shortcode'
	);
	return array_merge($new_columns,$columns);
}

add_action('manage_posts_custom_column', 'isopost_handle_isopostscode_columns', 10, 2 );

function isopost_handle_isopostscode_columns( $column ){

	global $post;
	if( $post->post_type != 'isopost-scode' ) return;	
	elseif( $column == '_isopost_shcode' ){
		$_isopost_shcode = get_post_meta( $post->ID, '_isopost_shcode', true );
		echo "<input type='text' class='isopost-textsc' onClick='this.select();' value='$_isopost_shcode' readonly='readonly'>";
	}
}

#-----------------------------------------------------------------#
# FETCH ALL REGISTRED IMAGE SIZES
#-----------------------------------------------------------------#

function isopost_image_sizes( $name, $value )
{
	global $_wp_additional_image_sizes;

	$sizes = array();

	foreach ( get_intermediate_image_sizes() as $_size ) {
		if ( in_array( $_size, array('thumbnail', 'medium', 'medium_large', 'large') ) ) {

			$width 		= get_option( "{$_size}_size_w" );
			$height 	= get_option( "{$_size}_size_h" );
			$crop 		= (bool) get_option( "{$_size}_crop" ) ? 'hard' : 'soft';

			$sizes[$_size]   = "{$_size} - {$width}x{$height}";

		} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {

			$width 		= $_wp_additional_image_sizes[ $_size ]['width'];
			$height 	= $_wp_additional_image_sizes[ $_size ]['height'];
			$crop 		= $_wp_additional_image_sizes[ $_size ]['crop'] ? 'hard' : 'soft';

			$sizes[$_size]   = "{$_size} - {$width}x{$height}";
		}
	}

	$sizes = array_merge($sizes, array('full' => 'original uploaded image'));

	$table = sprintf('<select name="%1$s" class="regular-text select2">', $name );
	foreach( $sizes as $key => $option ){
		$selected = ( $value == $key ) ? ' selected="selected"' : '';
		$table .= sprintf('<option value="%1$s" %3$s>%2$s</option>',$key, $option, $selected);
	}
	$table .= '</select>';

	return $table;
}

#-----------------------------------------------------------------#
# isopost METABOXES & META FIELDS
#-----------------------------------------------------------------#

add_action( 'add_meta_boxes', 'isopost_add_scodeopts_metabox' );
add_action( 'add_meta_boxes', 'isopost_add_settings_metabox' );
add_action( 'add_meta_boxes', 'isopost_add_right_side_custom_metabox' );
add_action( 'save_post', 'isopost_save_meta_fields' );

function isopost_add_scodeopts_metabox() {
    add_meta_box('isopost_metabox', __( 'Post Settings', 'isopost' ),'isopost_scopts_meta_fields','isopost-scode','normal','default');
}

function isopost_add_settings_metabox() {
    add_meta_box('isopost_set_metabox', __( 'Appearance Settings', 'isopost' ),'isopost_settings_meta_fields','isopost-scode','normal','default');
}

function isopost_add_right_side_custom_metabox() {
    add_meta_box('isopost_rs_metabox', __( 'Shortcode', 'isopost' ),'isopost_scode_meta_field','isopost-scode','side','default');
}

// Create Meta Fields Function
function isopost_scopts_meta_fields( $post ) 
{
	wp_nonce_field( plugin_basename( __FILE__ ), 'isopost_noncename' );
	$_isopost_selpost  = get_post_meta( $post->ID, '_isopost_selpost', true );
	$_isopost_seltax   = get_post_meta( $post->ID, '_isopost_seltax', true );
	$_isopost_taxterms = get_post_meta( $post->ID, '_isopost_taxterms', true );
	
	// Sanitiz the meta fields
	$_isopost_selpost  = !empty($_isopost_selpost) ? $_isopost_selpost : '';
	$_isopost_seltax   = !empty($_isopost_seltax) ? $_isopost_seltax : '';
	$_isopost_taxterms = !empty($_isopost_taxterms) ? unserialize($_isopost_taxterms) : array();
	
	$args = array('public' => true);
	$output   = 'names'; 
	$operator = 'and'; 
	$inner_Html ='';
	
	$taxonomies = get_taxonomies( $args, $output, $operator ); 
	if ( $taxonomies ) {
		foreach ( $taxonomies  as $taxonomy ) {
			if(is_taxonomy_hierarchical( $taxonomy ) ){
				$taxObject = get_taxonomy($taxonomy);
				$postTypesArray[] = $taxObject->object_type[0];
			}
		}
	}
	$uniquePtypes = array_unique($postTypesArray);
	
	$post_ddown  = '<select name="_isopost_selpost" id="isopost_selpost"><option value="">Select</option>';
	foreach($uniquePtypes as $ptype){
		$post_ddown .= '<option '.selected($_isopost_selpost, $ptype, 0).' value='.$ptype.'>'.ucwords($ptype).'</option>';
	}
	$post_ddown .= '<select>';
	
	if(!empty($_isopost_selpost))
	{
		$taxonomy_obj = get_object_taxonomies( $_isopost_selpost );
		$taxonomy_Arr = (array) $taxonomy_obj;
		$taxo_ddown   = '<select name="_isopost_seltax" id="isopost_seltax"><option value="">Select</option>';
		
		foreach ( $taxonomy_Arr as $term ) { 
			if(is_taxonomy_hierarchical( $term ) ){
				$taxo_ddown .= '<option '.selected($_isopost_seltax, $term, 0).' value="' . $term . '" >' . ucwords($term) . '</option>'; 
			}
		} 
		
		$taxo_ddown .= '<select>';
		
	}else{
		$taxo_ddown  = '<select name="_isopost_seltax" id="isopost_seltax"><option value="">Select</option></select>';
	}
	
	if(!empty($_isopost_seltax))
	{
		$taxonomy  = $_isopost_seltax;
        $taxterms  = get_terms( $taxonomy, 'orderby=count&offset=1&hide_empty=0&fields=all');
		
		foreach ( $taxterms as $term ) { 
		
			if( in_array( $term->term_id, $_isopost_taxterms) ){ 
				$checked = 'checked="checked"';
			}else{
				$checked = '';
			}
		
			$inner_Html .= '<label><input type="checkbox" '.$checked. ' name="_isopost_taxterms[]" value="' . $term->term_id . '" />' . ucwords($term->name) . '</label>'; 
		} 
		
		$terms_html  = '<div id="isopost_selterms">'.$inner_Html.'</div>';
		
	}else{
		$terms_html  = '<div id="isopost_selterms"></div>';
	}
	
	// Add Ajax Loader
	echo '<div class="isopost_loader"></div>';

	// Select Post Type Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Select Post Type',$post_ddown);
	
	// Select Category/Taxonomy Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Select Category / Taxonomy',$taxo_ddown);
	
	// Choose Category/Taxonomy Items
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Select Category / Taxonomy Terms',$terms_html);

	// Create Security Nonce Data
	echo '<input type="hidden" id="isopoststring" value="'.wp_create_nonce( 'isopost-security-data' ).'" />';
}

// Create Meta Fields Function
function isopost_settings_meta_fields( $post ) 
{
	wp_nonce_field( plugin_basename( __FILE__ ), 'isopost_noncename' );
	$_isopost_st_dcol = get_post_meta( $post->ID, '_isopost_st_dcol', true );
	$_isopost_st_tcol = get_post_meta( $post->ID, '_isopost_st_tcol', true );
	$_isopost_st_pcol = get_post_meta( $post->ID, '_isopost_st_pcol', true );
	$_isopost_st_size = get_post_meta( $post->ID, '_isopost_st_size', true );
	
	// Sanitiz the meta fields
	$_isopost_st_dcol = !empty($_isopost_st_dcol) ? $_isopost_st_dcol : '';
	$_isopost_st_tcol = !empty($_isopost_st_tcol) ? $_isopost_st_tcol : '';
	$_isopost_st_pcol = !empty($_isopost_st_pcol) ? $_isopost_st_pcol : '';
	$_isopost_st_size = !empty($_isopost_st_size) ? $_isopost_st_size : '';
	
	$column_desk_dd = '<select name="_isopost_st_dcol">
		<option '.selected($_isopost_st_dcol, 'md12', 0).' value="md12">1 Column</option>
		<option '.selected($_isopost_st_dcol, 'md6', 0).' value="md6">2 Columns</option>
		<option '.selected($_isopost_st_dcol, 'md4', 0).' value="md4">3 Columns</option>
		<option '.selected($_isopost_st_dcol, 'md3', 0).' value="md3">4 Columns</option>
	</select>&nbsp;<span class="description">The number of items you want to see on large devices, Tablet Landscape / Desktop.</span>';
	
	$column_tablet_dd = '<select name="_isopost_st_tcol">
		<option '.selected($_isopost_st_tcol, 'sm12', 0).' value="sm12">1 Column</option>
		<option '.selected($_isopost_st_tcol, 'sm6', 0).' value="sm6">2 Columns</option>
		<option '.selected($_isopost_st_tcol, 'sm4', 0).' value="sm4">3 Columns</option>
		<option '.selected($_isopost_st_tcol, 'sm3', 0).' value="sm3">4 Columns</option>
	</select>&nbsp;<span class="description">The number of items you want to see on medium devices, Tablet Portrait.</span>';
	
	$column_phone_dd = '<select name="_isopost_st_pcol">
		<option '.selected($_isopost_st_pcol, 'xs12', 0).' value="xs12">1 Column</option>
		<option '.selected($_isopost_st_pcol, 'xs6', 0).' value="xs6">2 Columns</option>
		<option '.selected($_isopost_st_pcol, 'xs4', 0).' value="xs4">3 Columns</option>
		<option '.selected($_isopost_st_pcol, 'xs3', 0).' value="xs3">4 Columns</option>
	</select>&nbsp;<span class="description">The number of items you want to see on small devices, Phones.</span>';
	
	
	// Template	
	/*$column_template_dd = '<select name="_isopost_st_pcol">
		<option '.selected($_isopost_st_pcol, 'isopost-shortcode.php', 0).' value="xs12">Equal Height</option>
		<option '.selected($_isopost_st_pcol, 'xs6', 0).' value="xs6">isopost</option>
		<option '.selected($_isopost_st_pcol, 'xs4', 0).' value="xs4">Overlay Equal Height</option>
		<option '.selected($_isopost_st_pcol, 'xs3', 0).' value="xs3">Overlay isopost</option>
	</select>&nbsp;<span class="description">Select your Template</span>';*/
	
	$imageSizes_dd = isopost_image_sizes('_isopost_st_size',$_isopost_st_size);	

	// Select Columns : Desktop Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Columns : Large Devices',$column_desk_dd);
	
	// Select Columns : Tablet Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Columns : Medium Devices',$column_tablet_dd);
	
	// Select Columns : Phone Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Columns : Small Devices',$column_phone_dd);
	
	// Select Image Size Dropdown
	echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Image Size',$imageSizes_dd);

	// Select Template
	/*echo sprintf('<div class="isopost-item"><label class="isopost-label">%s</label><div class="isopost-field">%s</div></div>','Template',$column_template_dd);*/


}

// Create Meta Fields Function
function isopost_scode_meta_field( $post ) 
{
	wp_nonce_field( plugin_basename( __FILE__ ), 'isopost_noncename' );
	
	$_isopost_selpost  = get_post_meta( $post->ID, '_isopost_selpost', true );
	$_isopost_seltax   = get_post_meta( $post->ID, '_isopost_seltax', true );
	$_isopost_taxterms = get_post_meta( $post->ID, '_isopost_taxterms', true );
	$_isopost_shcode   = get_post_meta( $post->ID, '_isopost_shcode', true );
	
	// Sanitiz the meta fields
	$_isopost_selpost  = !empty($_isopost_selpost) ? $_isopost_selpost : '';
	$_isopost_seltax   = !empty($_isopost_seltax) ? $_isopost_seltax : '';
	$_isopost_taxterms = !empty($_isopost_taxterms) ? unserialize($_isopost_taxterms) : array();
	$_isopost_shcode   = !empty($_isopost_shcode) ? $_isopost_shcode : '';
	
	echo "<label>Copy shortcode after saving isopost Item.</label>";
	echo "<input type='text' name='_isopost_shcode' class='isopost-textsc' onClick='this.select();' value='$_isopost_shcode' readonly='readonly'>";
}



// Save Meta Fields Function
function isopost_save_meta_fields( $post_id ) 
{
	if ( !empty($_POST['post_type']) && 'isopost-scode' == $_POST['post_type'] ) {
		if ( ! current_user_can( 'edit_page', $post_id ) )
		return;
	} else {
		if ( ! current_user_can( 'edit_post', $post_id ) )
		return;
	}

	if ( ! isset( $_POST['isopost_noncename'] ) || ! wp_verify_nonce( $_POST['isopost_noncename'], plugin_basename( __FILE__ ) ) )
	return;

	$post_ID = $_POST['post_ID'];
	$_isopost_selpost  = !empty($_POST['_isopost_selpost']) ? $_POST['_isopost_selpost'] : '';
	$_isopost_seltax   = !empty($_POST['_isopost_seltax']) ? $_POST['_isopost_seltax'] : '';
	$_isopost_taxterms = !empty($_POST['_isopost_taxterms']) ? $_POST['_isopost_taxterms'] : '';
	$_isopost_st_dcol  = !empty($_POST['_isopost_st_dcol']) ? $_POST['_isopost_st_dcol'] : '';
	$_isopost_st_tcol  = !empty($_POST['_isopost_st_tcol']) ? $_POST['_isopost_st_tcol'] : '';
	$_isopost_st_pcol  = !empty($_POST['_isopost_st_pcol']) ? $_POST['_isopost_st_pcol'] : '';
	$_isopost_st_size  = !empty($_POST['_isopost_st_size']) ? $_POST['_isopost_st_size'] : '';
	
	update_post_meta($post_ID, '_isopost_selpost', $_isopost_selpost);
	update_post_meta($post_ID, '_isopost_seltax', $_isopost_seltax);
	update_post_meta($post_ID, '_isopost_st_dcol', $_isopost_st_dcol);
	update_post_meta($post_ID, '_isopost_st_tcol', $_isopost_st_tcol);
	update_post_meta($post_ID, '_isopost_st_pcol', $_isopost_st_pcol);
	update_post_meta($post_ID, '_isopost_st_size', $_isopost_st_size);
	
	if(isset($_POST['_isopost_taxterms'])){
		$data = serialize($_POST['_isopost_taxterms']);
		update_post_meta($post_ID, '_isopost_taxterms', $data);
	}else{
		update_post_meta($post_ID, '_isopost_taxterms', '');
	}
	
	if(!empty($_isopost_taxterms)){
		$_isopost_taxterms = implode(",",$_isopost_taxterms);
	}else{
		$_isopost_taxterms = '';
	}
	
	$_generate_scode = '[isopost id="'.$post_ID.'"]';
	update_post_meta($post_ID, '_isopost_shcode', $_generate_scode);
}

?>