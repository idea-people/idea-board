<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-08-30
 * Time: 오후 12:18
 */

namespace ideapeople\board\helper\helpers\wordpress_popular_posts;

use ideapeople\board\helper\core\AbstractHelper;
use ideapeople\board\PluginConfig;

class WordpressPopularPostsHelper extends AbstractHelper {
	public function run() {
		add_action( 'idea_board_read', array( $this, 'register' ) );
		add_filter( 'wpp_query_posts', array( $this, 'wpp_query_posts' ), 10, 2 );
	}

	public function register( $post ) {
		add_filter( 'wpp_trackable_post_types', array( $this, 'wpp_query_posts' ) );
		$this->update_views( $post );
		remove_filter( 'wpp_trackable_post_types', array( $this, 'wpp_query_posts' ) );
	}

	public $javascript = 'var do_request=!1;if(sampling_active){var num=Math.floor(Math.random()*sampling_rate)+1;do_request=1===num}else do_request=!0;if(do_request){var xhr=window.XMLHttpRequest?new XMLHttpRequest:new ActiveXObject("Microsoft.XMLHTTP"),params="action=update_views_ajax&token="+token+"&wpp_id="+wpp_id;xhr.open("POST",_url,!0),xhr.setRequestHeader("Content-type","application/x-www-form-urlencoded"),xhr.onreadystatechange=function(){4===xhr.readyState&&200===xhr.status&&window.console&&window.console.log&&window.console.log(xhr.responseText)},xhr.send(params)}';

	public function update_views( $post ) {
		?>
		<script type="text/javascript">
			var _url = '<?php echo admin_url( 'admin-ajax.php', is_ssl() ? 'https' : 'http' ); ?>';
			var token = '<?php echo wp_create_nonce( 'wpp-token' ) ?>';
			var wpp_id = '<?php echo $post->ID; ?>';
			<?php echo $this->javascript;?>
		</script>
		<?php
	}

	public function wpp_query_posts( $result, $instance ) {
		if ( $instance[ 'post_type' ] == PluginConfig::$board_post_type ) {
			global $wpdb;
			$fields  = "p.ID AS 'id', p.post_title AS 'title', p.post_date AS 'date', p.post_author AS 'uid'";
			$from    = "";
			$where   = "WHERE 1 = 1";
			$orderby = "";
			$groupby = "";
			$limit   = "LIMIT {$instance['limit']}";
			$now     = current_time( 'mysql' );

			$prefix = $wpdb->prefix . "popularposts";

			// post filters
			// * freshness - get posts published within the selected time range only
			if ( $instance[ 'freshness' ] ) {
				switch ( $instance[ 'range' ] ) {
					case "daily":
						$where .= " AND p.post_date > DATE_SUB('{$now}', INTERVAL 1 DAY) ";
						break;

					case "weekly":
						$where .= " AND p.post_date > DATE_SUB('{$now}', INTERVAL 1 WEEK) ";
						break;

					case "monthly":
						$where .= " AND p.post_date > DATE_SUB('{$now}', INTERVAL 1 MONTH) ";
						break;

					default:
						$where .= "";
						break;
				}
			}

			// * post types - based on code seen at https://github.com/williamsba/WordPress-Popular-Posts-with-Custom-Post-Type-Support
			$types          = explode( ",", $instance[ 'post_type' ] );
			$sql_post_types = "";
			$join_cats      = true;

			// if we're getting just pages, why join the categories table?
			if ( 'page' == strtolower( $instance[ 'post_type' ] ) ) {

				$join_cats = false;
				$where .= " AND p.post_type = '{$instance['post_type']}'";

			} // we're listing other custom type(s)
			else {

				if ( count( $types ) > 1 ) {

					foreach ( $types as $post_type ) {
						$post_type = trim( $post_type ); // required in case user places whitespace between commas
						$sql_post_types .= "'{$post_type}',";
					}

					$sql_post_types = rtrim( $sql_post_types, "," );
					$where .= " AND p.post_type IN({$sql_post_types})";

				} else {
					$where .= " AND p.post_type = '{$instance['post_type']}'";
				}

			}

			// * posts exclusion
			if ( ! empty( $instance[ 'pid' ] ) ) {

				$ath = explode( ",", $instance[ 'pid' ] );

				$where .= ( count( $ath ) > 1 )
					? " AND p.ID NOT IN({$instance['pid']})"
					: " AND p.ID <> '{$instance['pid']}'";

			}

			// * categories
			if ( ! empty( $instance[ 'cat' ] ) && $join_cats ) {

				$cat_ids = explode( ",", $instance[ 'cat' ] );
				$in      = array();
				$out     = array();

				for ( $i = 0; $i < count( $cat_ids ); $i ++ ) {
					if ( $cat_ids[ $i ] >= 0 ) {
						$in[] = $cat_ids[ $i ];
					} else {
						$out[] = $cat_ids[ $i ];
					}
				}

				$in_cats  = implode( ",", $in );
				$out_cats = implode( ",", $out );
				$out_cats = preg_replace( '|[^0-9,]|', '', $out_cats );

				if ( $in_cats != "" && $out_cats == "" ) { // get posts from from given cats only
					$where .= " AND p.ID IN (
						SELECT object_id
						FROM {$wpdb->term_relationships} AS r
							 JOIN {$wpdb->term_taxonomy} AS x ON x.term_taxonomy_id = r.term_taxonomy_id
						WHERE x.taxonomy = 'category' AND x.term_id IN({$in_cats})
						)";
				} else if ( $in_cats == "" && $out_cats != "" ) { // exclude posts from given cats only
					$where .= " AND p.ID NOT IN (
						SELECT object_id
						FROM {$wpdb->term_relationships} AS r
							 JOIN {$wpdb->term_taxonomy} AS x ON x.term_taxonomy_id = r.term_taxonomy_id
						WHERE x.taxonomy = 'category' AND x.term_id IN({$out_cats})
						)";
				} else { // mixed
					$where .= " AND p.ID IN (
						SELECT object_id
						FROM {$wpdb->term_relationships} AS r
							 JOIN {$wpdb->term_taxonomy} AS x ON x.term_taxonomy_id = r.term_taxonomy_id
						WHERE x.taxonomy = 'category' AND x.term_id IN({$in_cats}) AND x.term_id NOT IN({$out_cats})
						) ";
				}

			}

			// * authors
			if ( ! empty( $instance[ 'author' ] ) ) {

				$ath = explode( ",", $instance[ 'author' ] );

				$where .= ( count( $ath ) > 1 )
					? " AND p.post_author IN({$instance['author']})"
					: " AND p.post_author = '{$instance['author']}'";

			}

			// All-time range
			if ( "all" == $instance[ 'range' ] ) {

				$fields .= ", p.comment_count AS 'comment_count'";

				// order by comments
				if ( "comments" == $instance[ 'order_by' ] ) {

					$from = "{$wpdb->posts} p";
					$where .= " AND p.comment_count > 0 ";
					$orderby = " ORDER BY p.comment_count DESC";

					// get views, too
					if ( $instance[ 'stats_tag' ][ 'views' ] ) {

						$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews'";
						$from .= " LEFT JOIN {$prefix}data v ON p.ID = v.postid";

					}

				} // order by (avg) views
				else {

					$from = "{$prefix}data v LEFT JOIN {$wpdb->posts} p ON v.postid = p.ID";

					// order by views
					if ( "views" == $instance[ 'order_by' ] ) {

						$fields .= ", v.pageviews AS 'pageviews'";
						$orderby = "ORDER BY pageviews DESC";

					} // order by avg views
					elseif ( "avg" == $instance[ 'order_by' ] ) {

						$fields .= ", ( v.pageviews/(IF ( DATEDIFF('{$now}', MIN(v.day)) > 0, DATEDIFF('{$now}', MIN(v.day)), 1) ) ) AS 'avg_views'";
						$groupby = "GROUP BY v.postid";
						$orderby = "ORDER BY avg_views DESC";

					}

				}

			} else { // CUSTOM RANGE
				switch ( $instance[ 'range' ] ) {
					case "daily":
						$interval = "1 DAY";
						break;

					case "weekly":
						$interval = "1 WEEK";
						break;

					case "monthly":
						$interval = "1 MONTH";
						break;

					default:
						$interval = "1 DAY";
						break;
				}

				// order by comments
				if ( "comments" == $instance[ 'order_by' ] ) {

					$fields .= ", COUNT(c.comment_post_ID) AS 'comment_count'";
					$from = "{$wpdb->comments} c LEFT JOIN {$wpdb->posts} p ON c.comment_post_ID = p.ID";
					$where .= " AND c.comment_date_gmt > DATE_SUB('{$now}', INTERVAL {$interval}) AND c.comment_approved = 1 ";
					$groupby = "GROUP BY c.comment_post_ID";
					$orderby = "ORDER BY comment_count DESC";

					if ( $instance[ 'stats_tag' ][ 'views' ] ) { // get views, too

						$fields .= ", IFNULL(v.pageviews, 0) AS 'pageviews'";
						$from .= " LEFT JOIN (SELECT postid, SUM(pageviews) AS pageviews FROM {$prefix}summary WHERE last_viewed > DATE_SUB('{$now}', INTERVAL {$interval}) GROUP BY postid) v ON p.ID = v.postid";

					}

				} // ordered by views / avg
				else {

					$from = "{$prefix}summary v LEFT JOIN {$wpdb->posts} p ON v.postid = p.ID";
					$where .= " AND v.last_viewed > DATE_SUB('{$now}', INTERVAL {$interval}) ";
					$groupby = "GROUP BY v.postid";

					// ordered by views
					if ( "views" == $instance[ 'order_by' ] ) {

						$fields .= ", SUM(v.pageviews) AS 'pageviews'";
						$orderby = "ORDER BY pageviews DESC";

					} // ordered by avg views
					elseif ( "avg" == $instance[ 'order_by' ] ) {

						$fields .= ", ( SUM(v.pageviews)/(IF ( DATEDIFF('{$now}', DATE_SUB('{$now}', INTERVAL {$interval})) > 0, DATEDIFF('{$now}', DATE_SUB('{$now}', INTERVAL {$interval})), 1) ) ) AS 'avg_views' ";
						$orderby = "ORDER BY avg_views DESC";

					}

					// get comments, too
					if ( $instance[ 'stats_tag' ][ 'comment_count' ] ) {

						$fields .= ", IFNULL(c.comment_count, 0) AS 'comment_count'";
						$from .= " LEFT JOIN (SELECT comment_post_ID, COUNT(comment_post_ID) AS 'comment_count' FROM {$wpdb->comments} WHERE comment_date_gmt > DATE_SUB('{$now}', INTERVAL {$interval}) AND comment_approved = 1 GROUP BY comment_post_ID) c ON p.ID = c.comment_post_ID";

					}

				}

			}

			// Build query
			$query = "SELECT {$fields} FROM {$from} {$where} {$groupby} {$orderby} {$limit};";

			$result = $wpdb->get_results( $query );
		}

		return $result;
	}

	public function wpp_trackable_post_types() {
		return true;
	}

	public function get_name() {
		return 'wordpress-popular-posts/wordpress-popular-posts.php';
	}
}