<?php
/*
Plugin Name: custom_wp_links
Plugin URI: https://schipbreukeling.nl
Description:
Version: 1.1.1
Author: Jaap Marcus
Author URI: https://schipbreukeling.nl
Text Domain: -
*/

if (!defined('ABSPATH')) {
	exit();
}

class Custom_WP_links
{
	public const NAME = 'custom_wp_links';
	public const VERSION = '1.1.1';

	private static $instance = null;
	public static $plugin_basename = null;
	public static $is_configured = false;

	public static function get_instance()
	{
		if (!self::$instance) {
			self::$instance = new self();
		}
	
		return self::$instance;
	}
	
	function __construct(){
			add_action('init', array($this, 'init'));			
	}
	
	function init(){
		add_filter('wp_link_pages', array($this,'my_page_nav'));	
		add_action('wp_head',array($this, 'addHeader'));
		$this::$is_configured = true;
		
		if (is_admin()) {
			require_once __DIR__ . '/includes/admin.php';
			$admin = new Custom_Wp_Links_Admin();
		}

	}
	
	public function addHeader(){
			wp_enqueue_style('wp-pagenav', plugins_url("custom_wp_links/style.css"), array(),$this::VERSION, false);
			$option = get_option('custom_wp_links');
			echo "<style>";
			if(!empty($option['nextcolor'])){
				echo ".nextpostslink, .lastpostlink{ background-color: ".$option['nextcolor']." !important;}\n";
			}
			if(!empty($option['prevcolor'])){
				echo ".previouspostslink{ background-color: ".$option['prevcolor']."  !important;;}\n";
			}
			if(!empty($option['textcolor'])){
				echo ".previouspostslink, .nextpostslink, .lastpostlink{ color: ".$option['textcolor']." !important;}\n";
			}

			echo "</style>"; 
	}
	
	function create_link($i){
		global $wp_rewrite;
		$post       = get_post();
		$query_args = array();
		
		if (1 == $i) {
				$url = get_permalink();
		} else {
				if (! get_option('permalink_structure') || in_array($post->post_status, array( 'draft', 'pending' ), true)) {
						$url = add_query_arg('page', $i, get_permalink());
				} elseif ('page' === get_option('show_on_front') && get_option('page_on_front') == $post->ID) {
						$url = trailingslashit(get_permalink()) . user_trailingslashit("$wp_rewrite->pagination_base/" . $i, 'single_paged');
				} else {
						$url = trailingslashit(get_permalink()) . user_trailingslashit($i, 'single_paged');
				}
		}
		
		if (is_preview()) {
				if (('draft' !== $post->post_status) && isset($_GET['preview_id'], $_GET['preview_nonce'])) {
						$query_args['preview_id']    = wp_unslash($_GET['preview_id']);
						$query_args['preview_nonce'] = wp_unslash($_GET['preview_nonce']);
				}
		
				$url = get_preview_post_link($post, $query_args, $url);
		}
		return esc_url($url);
	}
	
	function my_page_nav($args = '')
	{
			global $page, $page_number, $numpages, $multipage, $more;
			$defaults = array(
					'before'           => '<aside class="page-links pagination-wrap ">',
					'after'            => '</aside>',
					'link_before'      => '',
					'link_after'       => '',
					'aria_current'     => 'page',
					'next_or_number'   => 'number',
					'separator'        => ' ',
					'nextpagelink'     => __('Next page'),
					'previouspagelink' => __('Previous page'),
					'pagelink'         => '%',
					'echo'             => 1,
			);
			
			$option = get_option('custom_wp_links');
			
			$default = array('lastlink' => '/trending/', 'lastlinktext' => 'Meer Artikelen' , 'prevpage' => '<<', 'nextpagelong' => 'Volgende Pagina >>', 'nextpageshort' => '>>');
			$option = array_merge($default, $option);
			$parsed_args = wp_parse_args($args, $defaults);
			$my_current_lang = apply_filters( 'wpml_current_language', NULL );
			
			if (get_post_type() != 'page') {
				if($numpages > 1){
?>

			<div class="wp-page-nav" role="navigation">
					<?php if ($page > 1) {
							?>
					<a class="previouspostslink" rel="prev" aria-label="Previous Page" href="<?php echo $this ->create_link($page - 1); ?>"><?=$option['prevpage'];?></a>
					<?php
					}
					if ($page < $numpages) {
							?>
					<a class="nextpostslink large-link" rel="next" aria-label="Next Page" href="<?php echo $this ->create_link ($page +1); ?>"><?=$option['nextpagelong'];?></a>
					<a class="nextpostslink small-link"  rel="next" aria-label="Next Page" href="<?php echo $this ->create_link($page +1); ?>"><?=$option['nextpageshort'];?></a>
					<?php
					}else{
					$category = get_the_category();
					$cat_id = $category[0]->cat_ID;
					if(in_array($cat_id, $option['categorylastlink'])){
					?>

<a class="nextpostslink large-link" rel="next" aria-label="Next Page" href="<?php echo $option['lastlink'];?>"><?=$option['lastlinktext'];?></a>
<a class="nextpostslink small-link"  rel="next" aria-label="Next Page" href="<?php echo $option['lastlink'];?>"><?=$option['lastlinktext'];?></a>
					<?php }
		}
		 ?>
			</div>
	<?php
	
	}else{
		$category = get_the_category();
					$cat_id = $category[0]->cat_ID;
					if(in_array($cat_id, $option['categorylastlink'])){

					?>
				<div class="wp-page-nav" role="navigation">
				<a class="lastpostlink large-link" rel="next" aria-label="Next Page" href="<?php echo $option['lastlink'];?>"><?=$option['lastlinktext'];?></a>
				<a class="lastpostlink small-link"  rel="next" aria-label="Next Page" href="<?php echo $option['lastlink'];?>"><?=$option['lastlinktext'];?></a>
				</div>
					<?php }
	}	
	}
	}
	
	
}

Custom_WP_links::get_instance();


?>