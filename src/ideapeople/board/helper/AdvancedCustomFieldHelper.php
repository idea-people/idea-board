<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-07
 * Time: 오후 1:03
 */

namespace ideapeople\board\helper;


use ideapeople\board\helper\core\AbstractHelper;
use ideapeople\board\setting\Setting;
use ideapeople\board\helper\core\Helper;
use ideapeople\util\wp\CustomField;

class AdvancedCustomFieldHelper extends AbstractHelper {
	public function run() {
		add_action( 'idea_board_nonce', array( $this, 'add_nonce' ) );
		add_filter( 'idea_board_custom_fields', array( $this, 'get_edit_page_meta_fields' ), 10, 2 );
	}

	public function add_nonce() {
		wp_nonce_field( 'input', 'acf_nonce' );
	}

	public function get_edit_page_meta_fields( $board, $post ) {
		$field_groups = $this->get_board_field_groups( $board );
		$rows         = array();

		foreach ( $field_groups as $group ) {
			$fields = apply_filters( 'acf/field_group/get_fields', array(), $group[ 'id' ] );

			foreach ( $fields as $field ) {
				$rows[] = $field;
			}
		}

		$results = array();

		foreach ( $rows as $row ) {
			$value = null;

			if ( $post ) {
				$value = get_field( $row[ 'name' ], $post->ID );
			}
			$f = new CustomField( array(
				'name'       => 'fields[' . $row[ 'key' ] . ']',
				'label'      => $row[ 'label' ],
				'field_type' => $row[ 'type' ],
				'require'    => $row[ 'required' ],
				'value'      => $value
			) );

			switch ( $row[ 'type' ] ) {
				case 'select':
					$f->default_value = $row[ 'choices' ];
					break;
			}

			$results[] = $f;
		}

		return $results;
	}

	public function get_board_field_groups( $board ) {
		$acfs         = apply_filters( 'acf/get_field_groups', array() );
		$field_groups = array();

		if ( $acfs ) {
			foreach ( $acfs as $acf ) {
				$acf[ 'location' ] = apply_filters( 'acf/field_group/get_location', array(), $acf[ 'id' ] );

				foreach ( $acf[ 'location' ] as $group_id => $group ) {
					if ( is_array( $group ) ) {
						foreach ( $group as $rule_id => $rule ) {
							if ( $rule[ 'param' ] == 'taxonomy' && $rule[ 'value' ] == $board->term_id ) {
								$field_groups[] = $acf;
							}
						}
					}
				}
			}
		}

		return $field_groups;
	}

	public function get_name() {
		return 'advanced-custom-fields/acf.php';
	}
}