<?php
/**
* Plugin Name: EDD Software Licences REST
* Plugin URI: http://sample.com
* Author: Bogdan M
* Author URI: http://www.mlb.ro
* Version: 1.0.0
* License: GPLv2
*/

// prevent direct access in wordpress
if ( ! defined( 'ABSPATH' ) ) die( 'Error!' );

include_once(dirname(__FILE__) . '/misc-functions.php');

function eslr_edd_software_licences_rest($data) {
  if( !class_exists('Easy_Digital_Downloads') || !class_exists('EDD_Software_Licensing') ) {
    return;
  }

  if( $data['key'] != 9637264772642634672636747 ) {
    exit('{"error": "You are not authorized to view this page"}');
  }

  if( !!$data['paged'] ) {
    $data['posts_per_page'] = 10;
  } else {
    $data['posts_per_page'] = -1;
  }

  // custom posts query with pagination
  $licenses = new WP_Query(array(
    'post_type'   => 'edd_license',
    'post_status' => 'publish',
    'posts_per_page' => $data['posts_per_page'],
    'paged' => $data['paged']
  ));

  $licenses_array = [];
  $_license = [];

  if( $licenses->have_posts() ) {
    while( $licenses->have_posts() ) {
      $licenses->the_post();

      // custom post meta filds
      $_license['_edd_sl_name']    = get_the_title( get_the_ID() );
      $_license['_edd_sl_status']  = get_post_meta( get_the_ID(), '_edd_sl_status', true );
      $_license['_edd_sl_key']     = get_post_meta( get_the_ID(), '_edd_sl_key', true );
      $_edd_sl_user_id = get_post_meta( get_the_ID(), '_edd_sl_user_id', true);
      $_license['_edd_sl_user_name'] = (get_userdata( $_edd_sl_user_id )->display_name != "") ?  get_userdata( $_edd_sl_user_id )->display_name : get_userdata( $_edd_sl_user_id )->user_login;
      // $_license['_edd_sl_site_count'] = get_post_meta( get_the_ID(), '_edd_sl_site_count', true );
      $_license['_edd_sl_site_count'] = get_site_count( get_the_ID() );
      $_license['_edd_sl_limit'] = (get_post_meta( get_the_ID(), '_edd_sl_limit', true ) != null) ?  get_post_meta( get_the_ID(), '_edd_sl_limit', true ) : 1;
      $_edd_sl_exp_length = get_post_meta( get_the_ID(), '_edd_sl_exp_length', true);
      $_license['_edd_completed_date'] = get_the_date( 'M j, Y', get_the_ID() );

      $_license['_edd_sl_expiration'] = ( get_post_meta( get_the_ID(), '_edd_sl_expiration', true ) ) ? date_i18n('M j, Y', get_post_meta( get_the_ID(), '_edd_sl_expiration', true ) ) : "Lifetime";

      // created entry pushed to the licenses array
      array_push($licenses_array, $_license);
    }
  }
  return $licenses_array;
}

// register new routes for wp-json
add_action( 'rest_api_init', function () {

  // route returning all the results - /wp-json/wp/v2/licenses
  register_rest_route( 'wp/v2', '/licenses/key/(?P<key>\d+)', array(
    'methods' => 'GET',
    'callback' => 'eslr_edd_software_licences_rest'
  ));

  // route returning results with pagination - /wp-json/wp/v2/licenses/page/1
	register_rest_route( 'wp/v2', '/licenses/page/(?P<paged>\d+)/key/(?P<key>\d+)', array(
		'methods' => 'GET',
		'callback' => 'eslr_edd_software_licences_rest'
	));
});
