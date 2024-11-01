<?php

/**
 * WooCommerce Change Guest Owner Order
 *
 * @package WooCommerce Change Guest Owner Order
 * @author Iván Barreda
 * @copyright 2019 Iván Barreda
 * @license GPL-3.0+
 *
 * Plugin Name: WooCommerce Change Guest Owner Order
 * Plugin URI: https://ivanbarreda.es/plugins/woocommerce-change-guest-owner-order
 * Description: Extends WooCommerce with bulk action for change order owner from customer to guest
 * Version: 1.1.1
 * Author: cuxaro
 * Author URI: https://ivanbarreda.es/
 * Tested up to: 5.2.1
 * WC requires at least: 3.0
 * WC tested up to: 3.5
 * Text Domain: woo-guest-owner
 * Domain Path: /languages/
 * Copyright: (C) 2019 Iván Barreda
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 */

define( 'BUZZ_CHANGE_GUEST_OWNER_VERSION', '1.1.1' );
define( 'BUZZ_CHANGE_GUEST_OWNER_PLUGIN_URL', plugin_dir_url( __FILE__ ) );



add_filter( 'bulk_actions-edit-shop_order', 'buzz_change_guest_owner_woocommerce_order_bulk', 20, 1 );

function buzz_change_guest_owner_woocommerce_order_bulk( $actions ) {

	$actions['change_guest_owner'] = __( 'Change Guest Owner', 'woocommerce' );
	return $actions;
}


// Filto para añadir lo que se debe hacer con las acciones anteriores cuando se seleccionan en el listado de entradas (post)

add_filter( 'handle_bulk_actions-edit-shop_order', 'buzz_change_guest_owner_woocommerce_order_handler', 10, 3 );
function buzz_change_guest_owner_woocommerce_order_handler( $redirect_to, $action, $ids ) {

  //Solo continúa si son las acciones que hemos creado nosotros
	if ( $action !== 'change_guest_owner' ) {
		return $redirect_to;
	}

	foreach( $ids as $id ) {

		update_post_meta( $id, '_customer_user', 0 );
	}

	return $redirect_to = add_query_arg( array(
		'post_type'	=>	'shop_order',
		'change_guest_owner' => '1',
		'processed_count' => count( $ids ),
		'processed_ids' => implode( ',', $ids ),
	), $redirect_to );

	//$redirect_to = add_query_arg( 'change_guest_owner', count( $ids ), $redirect_to );
	//return $redirect_to;

}

// The results notice from bulk action on orders
add_action( 'admin_notices', 'buzz_change_guest_owner_woocommerce_order_notice' );

function buzz_change_guest_owner_woocommerce_order_notice() {


    if ( empty( $_REQUEST['change_guest_owner'] ) ) return; // Exit

    $count = intval( $_REQUEST['processed_count'] );

    printf( '<div id="message" class="updated fade"><p>' .
    	_n( 'Changed %s Order to Guest Owner',
    		'Changed %s Orders to Guest Owner',
    		$count,
    		'change_guest_owner'
    	) . '</p></div>', $count );
}

