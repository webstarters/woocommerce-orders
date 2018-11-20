<?php 
function wpblog_wc_register_post_statuses() {
	register_post_status( 'wc-rest-order', array(
		'label' => _x( 'Restorder', 'WooCommerce Order status', 'text_domain' ),
		'public' => true,
		'exclude_from_search' => false,
		'show_in_admin_all_list' => true,
		'show_in_admin_status_list' => true,
		'label_count' => _n_noop( 'Restorder (%s)', 'Restordere (%s)', 'text_domain' )
	) );
}
add_filter( 'init', 'wpblog_wc_register_post_statuses' );

function wpblog_wc_add_order_statuses( $order_statuses ) {
$order_statuses['wc-rest-order'] = _x( 'Restorder', 'WooCommerce Order status', 'text_domain' );
return $order_statuses;
}
add_filter( 'wc_order_statuses', 'wpblog_wc_add_order_statuses' );


/*
 * Bulk action handler
 */
add_action( 'admin_action_add_to_restordre', 'misha_bulk_process_custom_status' ); // admin_action_{action name}
 
function misha_bulk_process_custom_status() {
 
	// if an array with order IDs is not presented, exit the function
	if( !isset( $_REQUEST['post'] ) && !is_array( $_REQUEST['post'] ) )
		return;
 
	foreach( $_REQUEST['post'] as $order_id ) {
 
		$order = new WC_Order( $order_id );
		$order_note = 'Ordre status er blevet ændret til:';
		$order->update_status( 'wc-rest-order', $order_note, true ); 
 
	}
 
	$location = add_query_arg( array(
    		'post_type' => 'shop_order',
		'add_to_restordre' => 1, 
		'changed' => count( $_REQUEST['post'] ), 
		'ids' => join( $_REQUEST['post'], ',' ),
		'post_status' => 'all'
	), 'edit.php' );
 
	wp_redirect( admin_url( $location ) );
	exit;
 
}
 
/*
 * Notices
 */
add_action('admin_notices', 'misha_custom_order_status_notices');
 
function misha_custom_order_status_notices() {
 
	global $pagenow, $typenow;
 
	if( $typenow == 'shop_order' 
	 && $pagenow == 'edit.php'
	 && isset( $_REQUEST['add_to_restordre'] )
	 && $_REQUEST['add_to_restordre'] == 1
	 && isset( $_REQUEST['changed'] ) ) {
 
		$message = sprintf( _n( 'Order status changed.', '%s order statuses changed.', $_REQUEST['changed'] ), number_format_i18n( $_REQUEST['changed'] ) );
		echo "<div class=\"updated\"><p>{$message}</p></div>";
 
	}
 
}

