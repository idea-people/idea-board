<?php
/**
 * Created by PhpStorm.
 * User: ideapeople
 * Date: 2016-09-16
 * Time: 오후 4:35
 */

namespace ideapeople\board;


class Seo {
	/**
	 * @var \WP_Post
	 */
	public $post;

	/**
	 * @var int
	 */
	public $post_ID;

	public function active_seo() {
		global $idea_board_page_mode, $idea_board_pid;

		if ( $idea_board_page_mode && $idea_board_pid ) {
			$this->post    = get_post( $idea_board_pid );
			$this->post_ID = $this->post->ID;

			add_action( 'wp_head', array( $this, 'seo' ) );
		}
	}

	public function seo() {
		$this->clear_other_seo();

		add_filter( 'wpseo_title', array( $this, 'get_seo_title' ) );
		add_filter( 'wpseo_metadesc', array( $this, 'get_seo_desc' ) );
		add_filter( 'wpseo_canonical', array( $this, 'get_seo_url' ) );

		add_filter( 'aioseop_title_page', array( &$this, 'get_seo_title' ) );
		add_filter( 'aioseop_description', array( &$this, 'get_seo_desc' ) );
		add_filter( 'aioseop_canonical_url', array( &$this, 'get_seo_url' ) );

		$this->print_seo();
	}

	public function print_seo() {
		$title = $this->get_seo_title();
		$desc  = $this->get_seo_desc();
		$url   = $this->get_seo_url();
		?>
		<meta name="title" content="<?php echo $title; ?>">
		<meta name="description" content="<?php echo $desc; ?>">
		<meta property="og:type" content="article">
		<meta property="og:title" content="<?php echo $title; ?>">
		<meta property="og:description" content="<?php echo $desc; ?>">
		<meta property="og:url" content="<?php echo $url; ?>">
		<?php
	}

	public function get_seo_url() {
		$url = get_permalink( $this->post_ID );

		return $url;
	}

	public function get_seo_title() {
		$title = get_the_title( $this->post->ID );

		return $title;
	}

	public function get_seo_desc() {
		$desc = esc_html( $this->post->post_content );

		return $desc;
	}

	public function clear_other_seo() {
		remove_action( 'wp_head', 'wpseo_head', 20 );
		remove_action( 'wp_head', 'wpseo_opengraph', 20 );
		remove_action( 'wp_head', 'rel_canonical' );
		remove_action( 'wp_head', 'wp_shortlink_wp_head' );

		add_filter( 'jetpack_enable_open_graph', '__return_false' );
		add_filter( 'wpseo_canonical', '__return_false' );
		add_filter( 'wpseo_title', '__return_false' );
		add_filter( 'wpseo_metadesc', '__return_false' );
		add_filter( 'wpseo_author_link', '__return_false' );
		add_filter( 'wpseo_metakey', '__return_false' );
		add_filter( 'wpseo_locale', '__return_false' );
		add_filter( 'wpseo_opengraph_type', '__return_false' );
		add_filter( 'wpseo_opengraph_image', '__return_false' );
		add_filter( 'wpseo_opengraph_image_size', '__return_false' );
		add_filter( 'wpseo_opengraph_site_name', '__return_false' );
		add_filter( 'wpseo_whitelist_permalink_vars', '__return_false' );
		add_filter( 'wpseo_prev_rel_link', '__return_false' );
		add_filter( 'wpseo_next_rel_link', '__return_false' );
		add_filter( 'wpseo_xml_sitemap_img_src', '__return_false' );
		add_filter( 'wp_seo_get_bc_title', '__return_false' );
		add_filter( 'wp_seo_get_bc_ancestors', '__return_false' );
		
		if ( defined( 'AIOSEOP_VERSION' ) ) {
			global $aiosp;
			remove_action( 'wp_head', array( $aiosp, 'wp_head' ) );
		}
	}
}