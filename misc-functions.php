<?php
	function get_sites( $license_id = 0 ) {
		$sites = get_post_meta( $license_id, '_edd_sl_sites', true );
		if( empty( $sites ) ) {
			$sites = array();
		}
		return array_unique( apply_filters( 'edd_sl_get_sites', $sites, $license_id ) );
	}

	function get_site_count( $license_id = 0 ) {
		if( force_increase() ) {
			$count = absint( get_post_meta( $license_id, '_edd_sl_activation_count', true ) );
		} else {
			$count = count( get_sites( $license_id ) );
		}
		return apply_filters( 'edd_sl_get_site_count', $count, $license_id );
	}

	function force_increase( $license_id = 0 ) {
	  global $edd_options;
	  $ret = isset( $edd_options['edd_sl_force_increase'] );
	  return (bool) apply_filters( 'edd_sl_force_activation_increase', $ret, $license_id );
	}
