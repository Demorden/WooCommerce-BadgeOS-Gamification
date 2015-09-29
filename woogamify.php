<?php

/*
*Plugin Name: Woocommerce BadgeOS Gamification
*Description: Simple Plugin for Woocommerce Gamification through BadgeOS, grants buyers achievements based on purchases: select awarded badge ID (must exist already) for products if/when needed
*Plugin URI:
* Author: Demorden
* Version: 1.0
* Author URI: http://fantazine.eu/
* License: GNU AGPL
*/

/*
THIS PLUGIN ADDS A CUSTOM FIELD TO WOOCOMMERCE PRODUCTS, "ACHIEVEMENT AWARDED"
YOU MUST INSERT THE ID OF THE BADGE YOU WANT TO GIVE WHEN A USER PURCHASES THE PRODUCT

SO "IF YOU BUY PRODUCT A, THEN YOU GET ACHIEVEMENT B"

WARNING: THE ACHIEVEMENT MUST EXIST

This simple plugin just creates rules to grant *EXISTING* Badgeos achievements when goods are bought in Woocommerce (you can set the granted achievement - if any in the product page).
It uses and links pre-existing stuff. Nothing else.

Based on the code snippets found at:
http://www.remicorson.com/mastering-woocommerce-products-custom-fields/
*/


// WOOCOMMERCE ADD CUSTOM PRODUCT FIELD: RELATED BADGE

add_action( 'woocommerce_product_options_general_product_data', 'woo_add_custom_general_fields' );
add_action( 'woocommerce_process_product_meta', 'woo_add_custom_general_fields_save' );

function woo_add_custom_general_fields() {

global $woocommerce, $post;
echo '<div class="options_group">';

woocommerce_wp_text_input(
array(
'id' => '_achievement_field',
'label' => __( 'Achievement Granted', 'woocommerce' ),
'placeholder' => '',
'description' => __( 'Enter the Badge ID here.', 'woocommerce' ),
'type' => 'number',
'custom_attributes' => array(
'step' => 'any',
'min' => '0'
)
)
);


echo '</div>';
} 

function woo_add_custom_general_fields_save( $post_id ){

$achievement_field = $_POST['_achievement_field'];
if( !empty( $achievement_field ) )
update_post_meta( $post_id, '_achievement_field', esc_attr( $achievement_field ) );

}

//
// BUY PRODUCT = GAIN RELATED ACHIEVEMENT
//

add_action('woocommerce_order_status_completed', 'custom_process_order_completed', 10, 1);
function custom_process_order_completed($order_id) {
    $order = new WC_Order( $order_id );
    $myuser_id = (int)$order->user_id;
    $items = $order->get_items();
    foreach ($items as $item) {
    	$product_id = $item['product_id'];
    	$badge_id = get_post_meta( $product_id, '_achievement_field', true );
	badgeos_award_achievement_to_user( $badge_id, $myuser_id );    
		}
	    return $order_id;
    	    }