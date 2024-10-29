<?php
/**
 * Plugin Name:       Banners for product categories
 * Description:       For adding banners to Woocommerce product categories. Separate banner for mobile; 
 * Version:           1.6.4
 * Author:            WEBPlugins
 * Author URI:        https://webplugins.nl/ 
 * Plugin URI:        https://webplugins.nl/

 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly 

if ( function_exists( 'webedz_banners' ) ) {
  webedz_banners()->set_basename( true, __FILE__ );
} 
else {
  // DO NOT REMOVE THIS IF, IT IS ESSENTIAL FOR THE `function_exists` CALL ABOVE TO PROPERLY WORK.
  if ( ! function_exists( 'webedz_banners' ) ) {
  // Create a helper function for easy SDK access.
  function webedz_banners() {
      global $webedz_banners;

      if ( ! isset( $webedz_banners ) ) {
          // Include Freemius SDK.
          require_once dirname(__FILE__) . '/freemius/start.php';

          $webedz_banners = fs_dynamic_init( array(
            'id'                  => '12462',
            'slug'                => 'banners-for-product-categories',
            'premium_slug'        => 'banners-for-product-categories-premium',
            'type'                => 'plugin',
            'public_key'          => 'pk_6de55fa38dea2b7a73d821b80e6e4',
            'is_premium'          => false,
            'is_premium_only'     => false,
            'premium_suffix'      => 'premium',
            // If your plugin is a serviceware, set this option to false.
            'has_premium_version' => true,
            'has_addons'          => false,
            'has_paid_plans'      => true,
            'menu'                => array(
                'slug'           => 'webedz_SlugMainMenu',
                'first-path'     => 'admin.php?page=webedz_SlugMainMenu',
                'contact'        => false,
                'support'        => false,
            ),
        ) );
      }

      return $webedz_banners;
    }

  // Init Freemius.
  webedz_banners();
  // Signal that SDK was initiated.
  do_action( 'webedz_banners_loaded' );
  }

function webedz_Info() {
  ?>
  
 
  <h1> How to use Product categories banners </h1>
  
  <h3> 
    This is the free version.  <BR><BR>
    This plugin is for setting banners for Woocommerce product categories. <BR><BR>
    So first set banners in the product category settings, one for desktop and one for phones. <BR><BR>
    The phone banner is only available in the premium version. 
    <input type="button" class="button button-primary" value="<?php esc_attr_e( 'Get premium', 'webedz_banner' ); ?>" 
        onclick="window.location.href='/wp-admin/admin.php?page=webedz_SlugMainMenu-pricing';" />
    <BR><BR>
    Then use the shortcode [banner-prod-cat-webs] in your product category template or page. <BR><BR>
    <BR>
    For more information about this plugin, see
    <a href="https://webplugins.nl/" target="_blank" rel="noreferrer noopener" >
      webplugins.nl</a> <BR><BR>
    For a working example of the premium version see: 
    <a href="https://webplugins.nl/product-categorie/balloons/" target="_blank" rel="noreferrer noopener" >
      preview premium version</a> <BR><BR>
    <BR>

    In case you find a bug (purely hypothetical 🙂), please contact    
        <a href = "mailto: support@webplugins.nl">support@webplugins.nl</a>
    and I will quickly solve it.

  </h3>
 
  <?php
}


add_action( 'admin_menu', function () {

  add_menu_page(
  /*  'edit.php?post_type=product', */
    'Categories banners 1',
     'Categories banners',
     'manage_options',
     'webedz_SlugMainMenu',
     'webedz_Info',
     'dashicons-align-full-width'
    ); 
    
   
} );
 

function webedz_banners_custom_connect_message(
  $message,
  $user_first_name,
  $plugin_title,
  $user_login,
  $site_link,
  $freemius_link
) {
  return sprintf(
      __( 'Hello %1$s' ) . ',<br>' .
      __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'woocommerce-category-banners' ),
      $user_first_name,
      '<b>' . $plugin_title . '</b>',
      '<b>' . $user_login . '</b>',
      $site_link,
      $freemius_link
  );
}
webedz_banners()->add_filter('connect_message', 'webedz_banners_custom_connect_message', 10, 6);


function webedz_banners_custom_connect_message_on_update(
  $message,
  $user_first_name,
  $plugin_title,
  $user_login,
  $site_link,
  $freemius_link
) {
  return sprintf(
      __( 'Hello %1$s' ) . ',<br>' .
      __( 'Please help us improve %2$s! If you opt-in, some data about your usage of %2$s will be sent to %5$s. If you skip this, that\'s okay! %2$s will still work just fine.', 'woocommerce-category-banners' ),
      $user_first_name,
      '<b>' . $plugin_title . '</b>',
      '<b>' . $user_login . '</b>',
      $site_link,
      $freemius_link
  );
}
webedz_banners()->add_filter('connect_message_on_update', 'webedz_banners_custom_connect_message_on_update', 10, 6);


  
add_action( 'admin_init', function () {

  /* nice for later: foreach ( get_taxonomies( array( 'public' => true ) ) as $taxonomy )  */
  
  // $taxoList = ["category", "post_tag", "product_cat", "product_tag"];
  $taxoList = ["product_cat"]; // only this one works good
  foreach ( $taxoList as $taxonomy ) {
    add_action( "{$taxonomy}_add_form_fields", 'webedz_taxonomy_add_banners', 10, 0 );
    add_action( "{$taxonomy}_edit_form_fields", 'webedz_taxonomy_edit_banners', 10, 1);

    add_action("create_{$taxonomy}", 'webedz_updated_banners', 10, 1);
    add_action("edited_{$taxonomy}", 'webedz_updated_banners', 10, 1 ); 

    // en ook tonen in grid:
    //Displaying Additional Columns
    add_filter( "manage_edit-{$taxonomy}_columns", 'webedz_customFieldsListTitle' ); //Register Function
    add_action( "manage_{$taxonomy}_custom_column", 'webedz_customFieldsListDisplay' , 10, 3); //Populating the Columns
  }
} );

function webedz_taxonomy_add_banners() {
  ?>   
  <div class="form-field">
    <label for="cf_prod_cat_banner"><?php esc_attr_e( 'Banner desktop', 'webedz_banner' ); ?></label>
    <input type="hidden" id="cf_prod_cat_banner" name="cf_prod_cat_banner" value="<?php echo esc_html($image_id); ?>">
      <div id="category-image-wrapper">
         <?php if ( $image_id ) { ?>
           <?php echo wp_get_attachment_image ( esc_html($image_id), array('3000', '130') ); ?>
         <?php } ?>
       </div>
       <p>
         <input type="button" class="button button-secondary" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php esc_attr_e( 'Edit', 'webedz_banner' ); ?>" />
         <input type="button" class="button button-secondary" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove', 'webedz_banner' ); ?>" />
       </p>
  </div>
  
  <div class="form-field">
    <label for="cf_prod_cat_banner_mob"><?php esc_attr_e( 'Banner mobiel', 'webedz_banner' ); ?></label>
    
       <p>
         <input type="button" disabled class="button button-secondary" id="ct_tax_media_button_mob" name="ct_tax_media_button_mob" value="<?php esc_attr_e( 'Edit', 'webedz_banner' ); ?>" />
         <input type="button" disabled class="button button-secondary" id="ct_tax_media_remove_mob" name="ct_tax_media_remove_mob" value="<?php esc_attr_e( 'Remove', 'webedz_banner' ); ?>" />
         <input type="button" class="button button-primary" value="<?php esc_attr_e( 'Get premium version', 'webedz_banner' ); ?>" onclick="window.location.href='/wp-admin/admin.php?page=webedz_SlugMainMenu-pricing';" />
       </p>
  </div>
  <?php

  webedz_add_script();
}



function webedz_taxonomy_edit_banners ( $term) { 
	
	$image_id = get_term_meta($term -> term_id, 'cf_prod_cat_banner', true);
	$image_mob_id = get_term_meta($term -> term_id, 'cf_prod_cat_banner_mob', true);
	
	?>
 
	<tr class="form-field term-group-wrap">
     <th scope="row">
       <label for="cf_prod_cat_banner"><?php esc_attr_e( 'Banner desktop', 'webedz_banner' ); ?></label>
     </th>
     <td>
       <input type="hidden" id="cf_prod_cat_banner" name="cf_prod_cat_banner" value="<?php echo esc_html($image_id); ?>">
       <div id="category-image-wrapper">
         <?php if ( $image_id ) { ?>
           <?php echo wp_get_attachment_image ( esc_html($image_id), array('3000', '130') ); ?>
         <?php } ?>
       </div>
       <p>
         <input type="button" class="button button-secondary" id="ct_tax_media_button" name="ct_tax_media_button" value="<?php esc_attr_e( 'Edit', 'webedz_banner' ); ?>" />
         <input type="button" class="button button-secondary" id="ct_tax_media_remove" name="ct_tax_media_remove" value="<?php esc_attr_e( 'Remove', 'webedz_banner' ); ?>" />
       </p>
     </td>
   </tr>

   <tr class="form-field term-group-wrap">
     <th scope="row">
       <label for="cf_prod_cat_banner_mob"><?php esc_attr_e( 'Banner mobile', 'webedz_banner' ); ?></label>
     </th>
     <td>
       <p>
         <input type="button" disabled class="button button-secondary" id="ct_tax_media_button_mob" name="ct_tax_media_button_mob" value="<?php esc_attr_e( 'Edit', 'webedz_banner' ); ?>" />
         <input type="button" disabled class="button button-secondary" id="ct_tax_media_remove_mob" name="ct_tax_media_remove_mob" value="<?php esc_attr_e( 'Remove', 'webedz_banner' ); ?>" />
         <input type="button" class="button button-primary" value="<?php esc_attr_e( 'Get premium version', 'webedz_banner' ); ?>" onclick="window.location.href='/wp-admin/admin.php?page=webedz_SlugMainMenu-pricing';" />
       
        </p>
     </td>
   </tr>


 <?php
  
  webedz_add_script();
 
}

function webedz_add_script() {

 ?>

  <script>

    //alert('eb in add script');
    window.addEventListener('load', function () {
      
      function webedz_media_upload(extScreenType) {

        let _orig_send_attachment = wp.media.editor.send.attachment;
        let btnAddMedia;
        let imageWrapper;
                     
        btnAddMedia = document.getElementById("ct_tax_media_button" + extScreenType);
        btnAddMedia.onclick = function() {
          let send_attachment_bkp = wp.media.editor.send.attachment;
          wp.media.editor.send.attachment = function(props, attachment){
            document.getElementById("cf_prod_cat_banner" + extScreenType).value = attachment.id;

            imageWrapper = document.getElementById("category-image-wrapper" + extScreenType);
            while (imageWrapper.firstChild)
              imageWrapper.removeChild(imageWrapper.firstChild);

            imageWrapper.insertAdjacentHTML("afterbegin", '<img id="id_banner_image' + extScreenType + '"'
                                                                              + 'src="" style="margin:0;padding:0;height:130px;float:none;display:block" />');
            document.getElementById("id_banner_image" + extScreenType).setAttribute('src',attachment.url);
 		   
            /* else {
              return _orig_send_attachment.apply( "ct_tax_media_button", [props, attachment] ); 
            } */
          }
          wp.media.editor.open(btnAddMedia);
          return false;
        }
      };
      
      webedz_media_upload(''); 
      
      function removeBanner (extScreenType) {
        document.getElementById("cf_prod_cat_banner" + extScreenType).value = '';
        
        imageWrapper = document.getElementById("category-image-wrapper" + extScreenType);
        while (imageWrapper.firstChild)
          imageWrapper.removeChild(imageWrapper.firstChild);
        imageWrapper.insertAdjacentHTML("afterbegin", '<img src="" style="margin:0;padding:0;height:0px;float:none;" />');
      }

      document.getElementById("ct_tax_media_remove").onclick = function() {
        removeBanner('');
      };    
      
    }, false);
 </script>
 <?php }

function webedz_updated_banners ( $term_id) {
    $cf_prod_cat_banner = sanitize_key(filter_input(INPUT_POST, 'cf_prod_cat_banner'));
    update_term_meta($term_id, 'cf_prod_cat_banner', $cf_prod_cat_banner);
}


/**
 * Meta Title and Description column added to category admin screen.
 *
 * @param mixed $columns
 * @return array
 */
function webedz_customFieldsListTitle( $columns ) {
    $columns['webedz_meta_banner'] = __( 'Banner', 'woocommerce' );
    $columns['webedz_meta_banner_mob'] = __( 'Banner mob', 'woocommerce' );

    return $columns;
}
/**
 * Meta Title and Description column value added to product category admin screen.
 *
 * @param string $columns
 * @param string $column
 * @param int $id term ID
 *
 * @return string
 */
function webedz_customFieldsListDisplay( $columns, $column, $id ) {
    if ( 'webedz_meta_banner' == $column ) {
		if (get_term_meta($id, 'cf_prod_cat_banner', true) != '')
        	$columns = 'V';
    }
    elseif ( 'webedz_meta_banner_mob' == $column ) {
		if (get_term_meta($id, 'cf_prod_cat_banner_mob', true) != '')
        	$columns = 'V';
    } 
    return $columns;
}



function webedz_maak_cat_banner($atts){

  $_imageId;
	// Get the current term ID, e.g. if we're on a archive page
	$term = get_queried_object(); 
	$termID = $term->term_id;
 //   $termID = get_queried_object_id(); // sometimes wrong id
  
  ?>
  
  <div class="webedz-banner">
  </div>

  <script>
 
  KiesBanner();

  function KiesBanner() {
  
      let jsBanner = document.querySelector('.webedz-banner');
    
      // Get the image ID for the category
        
      while (jsBanner.firstChild)
        jsBanner.removeChild(jsBanner.firstChild);
        
      jsBanner.insertAdjacentHTML("afterbegin", '<?php  
              $_imageId = get_term_meta ( $termID, 'cf_prod_cat_banner', true );
              echo wp_get_attachment_image ( esc_html($_imageId), 'full'); 
              ?>');
  }

  </script>

  <?php
    	
}

add_shortcode('banner-prod-cat-webs', 'webedz_maak_cat_banner'); 

}  //end webedz_banners

?>
