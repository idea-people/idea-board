<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-24
 * Time: 오후 10:54
 */

namespace ideapeople\board;


use ideapeople\board\setting\Setting;
use ideapeople\util\html\Form;
use ideapeople\util\wp\CustomField;
use ideapeople\util\wp\PostUtils;
use ideapeople\util\wp\RoleUtils;

class CommonUtils {
	public static function role_as_select_box( $name, $selected = null, $args = array() ) {
		$roles = new Roles();

		$args = wp_parse_args( $args, array(
			'multiple' => true,
			'class'    => "chosen-select"
		) );

		$options = array();

		foreach ( $roles->get_roles() as $role => $role_name ) {
			$options[ $role ] = $role_name;
		}

		return Form::select( $name, $options, $selected, $args );
	}

	public static function start_no() {
		global $wp_query;

		return $wp_query->start_no;
	}

	public static function get_category_form( $board_term, $page_permalink = false ) {
		$categories     = Setting::get_categories_array( $board_term );
		$query_category = get_query_var( 'idea_board_category' );

		if ( empty( $categories ) ) {
			return false;
		}
		?>
		<div class="idea-board-category idea-board-reset">
			<label for="idea-board-category" class="idea-board-hidden"><?php _e_idea_board( 'Category' ) ?></label>
			<ul id="idea-board-category">
				<li <?php echo $query_category == 'all' || empty( $query_category ) ? 'class="active"' : '' ?>>
					<a href="<?php echo add_query_arg( array( 'idea_board_category' => null ), $page_permalink ); ?>"><?php _e_idea_board( 'All' ) ?></a>
				</li>
				<?php foreach ( $categories as $value ) : ?>
					<li <?php echo $value == $query_category ? 'class="active"' : '' ?>>
						<a href="<?php echo add_query_arg( array( 'idea_board_category' => $value ), $page_permalink ); ?>"><?php echo $value ?></a>
					</li>
				<?php endforeach ?>
			</ul>
		</div>
		<?php
	}

	public static function get_post_page() {
		/**
		 * @var $wp_the_query \WP_Query
		 */
		global $wp_the_query;

		if ( $wp_the_query->query_vars[ 'page_id' ] ) {
			return get_post( $wp_the_query->query_vars[ 'page_id' ] );
		} else if ( $wp_the_query->query_vars[ 'pagename' ] ) {
			return get_page_by_path( $wp_the_query->query_vars[ 'pagename' ] );
		}

		return false;
	}

	public static function get_post_page_id() {
		return self::get_post_page()->ID;
	}

	public static function get_post_page_link() {
		return get_permalink( self::get_post_page_id() );
	}
}