<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-11
 * Time: 오후 2:02
 */

namespace ideapeople\util\wp;


use PasswordHash;

class PasswordUtils {
	public static function action_password( $password, $type = 'post' ) {
		$key = 'wp-' . $type . 'pass_' . COOKIEHASH;

		if ( ! $password ) {
			wp_safe_redirect( wp_get_referer() );
			exit();
		}

		require_once ABSPATH . WPINC . '/class-phpass.php';
		$hasher  = new PasswordHash( 8, true );
		$expire  = apply_filters( 'idea_' . $type . '_password_expires', time() + 10 * DAY_IN_SECONDS );
		$referer = wp_get_referer();
		if ( $referer ) {
			$secure = ( 'https' === parse_url( $referer, PHP_URL_SCHEME ) );
		} else {
			$secure = false;
		}
		setcookie( $key, $hasher->HashPassword( wp_unslash( $password ) ), $expire, COOKIEPATH, COOKIE_DOMAIN, $secure );
		wp_safe_redirect( wp_get_referer() );
		exit();
	}

	private static function password_required( $password, $type = 'post' ) {
		if ( empty( $password ) ) {
			return false;
		}

		$key = 'wp-' . $type . 'pass_' . COOKIEHASH;

		if ( ! isset( $_COOKIE[ $key ] ) ) {
			return true;
		}

		require_once ABSPATH . WPINC . '/class-phpass.php';

		$hasher = new PasswordHash( 8, true );

		$stored_hash = wp_unslash( $_COOKIE[ $key ] );

		if ( 0 !== strpos( $stored_hash, '$P$B' ) ) {
			return true;
		}

		if ( strlen( $password ) > 4096 ) {
			return false;
		}

		$hash = $hasher->crypt_private( $password, $stored_hash );

		if ( $hash[ 0 ] == '*' ) {
			$hash = crypt( $password, $stored_hash );
		}

		$check = $hash === $stored_hash;

		return ! apply_filters( 'idea_' . $type . '_check_password', $check, $password, $hash );
	}

	public static function comment_password_required( $password ) {
		return self::password_required( $password, 'comment' );
	}

	/**
	 * @param $password
	 *
	 * @return bool
	 */
	public static function post_password_required( $password ) {
		return self::password_required( $password );
	}
}