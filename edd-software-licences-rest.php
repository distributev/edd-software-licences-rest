<?php
/**
* Plugin Name: EDD Software Licences REST
* Plugin URI: http://sample.com
* Author: Bogdan M
* Author URI: http://www.mlb.ro
* Version: 1.0.0
* License: GPLv2
*/

function eslr_edd_software_licences_rest($data) {
  echo "hello";
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

      $_license['_edd_sl_name']    = get_the_title( get_the_ID() );
      $_license['_edd_sl_status']  = get_post_meta( get_the_ID(), '_edd_sl_status', true );
      $_license['_edd_sl_key']     = get_post_meta( get_the_ID(), '_edd_sl_key', true );
      $_edd_sl_user_id = get_post_meta( get_the_ID(), '_edd_sl_user_id', true);
      $_license['_edd_sl_user_name'] = get_userdata( $_edd_sl_user_id )->display_name;
      $_license['_edd_sl_site_count'] = get_post_meta( get_the_ID(), '_edd_sl_site_count', true );
      $_license['_edd_sl_limit'] = get_post_meta( get_the_ID(), '_edd_sl_limit', true );
      $_license['_edd_sl_expiration'] = mysql2date('M j, Y', get_post_meta( get_the_ID(), '_edd_sl_expiration', true ) );
      $_license['_edd_completed_date'] = get_post_meta( get_the_ID(), '_edd_sl_completed_date', true );

      array_push($licenses_array, $_license);
    }
  }

  return $licenses_array;
}

add_action( 'rest_api_init', function () {
	register_rest_route( 'eslr', '/page/(?P<paged>\d+)', array(
		'methods' => 'GET',
		'callback' => 'eslr_edd_software_licences_rest',
	) );
} );
