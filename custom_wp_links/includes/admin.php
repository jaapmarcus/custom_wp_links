<?php 
if (!defined('ABSPATH')) {
	exit();
}

class Custom_Wp_Links_Admin{
		private $plugin = null;
		
		public function __construct()
		{
			$this->plugin = Custom_WP_links::get_instance();
		
			$options = get_option($this->plugin::NAME);
		
		
			// Add settings link to plugin actions.
			add_filter('plugin_action_links_' . $this->plugin::$plugin_basename, array($this, 'settings_link'));
			
			// Add settings page.
			add_action('admin_init', array($this, 'register_settings'));
			add_action('admin_menu', array($this, 'add_settings_page'));
			
			
		}
		
		function settings_link($links)
		{
			$url = esc_url(add_query_arg(
				'page',
				$this->plugin::NAME,
				get_admin_url() . 'options-general.php'
			));
		
			array_push($links, "<a href='$url'>" . __('Settings', $this->plugin::NAME) . '</a>');
			return $links;
		}
		
		public function register_settings()
		{
			register_setting(
				$this->plugin::NAME,
				$this->plugin::NAME,
				array('sanitize_callback' => array($this, 'validate_options'))
			);
		
			add_settings_section('custom_wp_link_settings', 'Custom WP Link Settings', '', $this->plugin::NAME);
			add_settings_field('custom_wp_link_text_next_page', 'Volgende pagina (Web)', array($this, 'custom_wp_link_text_next_page'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_text_next_page_short', 'Volgende pagina (Mob)', array($this, 'custom_wp_link_text_next_page_short'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_text_prev_page', 'Vorige pagina', array($this, 'custom_wp_link_text_prev_page'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_last_link', 'URL Laatste link', array($this, 'custom_wp_link_last_link'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_text_last_link', 'Text "Lees meer"', array($this, 'custom_wp_link_lastlinktext'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_category_last_link', 'Laats "Lees" meer alleen zien op Categorie', array($this, 'custom_wp_link_categorylastlink'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_next_page_color', 'Volgende pagina Achterground Kleur', array($this, 'custom_wp_link_next_page_color'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_prev_page_color', 'Vorige pagina Achterground Kleur', array($this, 'custom_wp_link_prev_page_color'), $this->plugin::NAME, 'custom_wp_link_settings');
			add_settings_field('custom_wp_link_text_color', 'Vorige pagina Tekst Kleur', array($this, 'custom_wp_link_text_color'), $this->plugin::NAME, 'custom_wp_link_settings');
		}
		
		public function custom_wp_link_categorylastlink(){
			$options = get_option($this->plugin::NAME);
			$lastlink = $this->plugin::$is_configured ? $options["categorylastlink"] : array();
			if($lastlink == "")
				$lastlink = array();

			$categories = get_categories(array('hide_empty' => 0));
			echo '<select id="custom_wp_link_category_last_link" name="' . $this->plugin::NAME . '[categorylastlink][]" multiple>';	
			foreach($categories as $category){
				$selected = in_array( $category->term_id, $lastlink,) ? 'selected' : '';
				echo '<option value="' . $category->term_id . '" ' . $selected . '>' . $category->name . '</option>';
			}
		}
		public function custom_wp_link_last_link(){
			$options = get_option($this->plugin::NAME);
			$lastlink = $this->plugin::$is_configured ? esc_attr($options["lastlink"]) : "";
			echo '<input id="custom_wp_link_last_link" name="' . $this->plugin::NAME . '[lastlink]" type="text" value="' . $lastlink . '" />';
		}
		
		public function custom_wp_link_text_next_page(){
			$options = get_option($this->plugin::NAME);
			$nextpagelong = $this->plugin::$is_configured ? esc_attr($options["nextpagelong"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[nextpagelong]" type="text" value="' . $nextpagelong . '" />';
		}
		
		public function custom_wp_link_text_next_page_short(){
			$options = get_option($this->plugin::NAME);
			$nextpageshort = $this->plugin::$is_configured ? esc_attr($options["nextpageshort"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[nextpageshort]" type="text" value="' . $nextpageshort . '" />';
		}
		public function custom_wp_link_text_prev_page(){
			$options = get_option($this->plugin::NAME);
			$prevpage = $this->plugin::$is_configured ? esc_attr($options["prevpage"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[prevpage]" type="text" value="' . $prevpage . '" />';
		}
		
		public function custom_wp_link_lastlinktext(){
			$options = get_option($this->plugin::NAME);
			$lastlinktext = $this->plugin::$is_configured ? esc_attr($options["lastlinktext"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[lastlinktext]" type="text" value="' . $lastlinktext . '" />';
		}

		public function custom_wp_link_next_page_color(){
			$options = get_option($this->plugin::NAME);
			$nextcolor = $this->plugin::$is_configured ? esc_attr($options["nextcolor"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[nextcolor]" type="text" value="' . $nextcolor . '" />';
		}
		
		public function custom_wp_link_prev_page_color(){
			$options = get_option($this->plugin::NAME);
			$prevcolor = $this->plugin::$is_configured ? esc_attr($options["prevcolor"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[prevcolor]" type="text" value="' . $prevcolor . '" />';
		}
		
		public function custom_wp_link_text_color(){
			$options = get_option($this->plugin::NAME);
			$textcolor = $this->plugin::$is_configured ? esc_attr($options["textcolor"]) : "";
			echo '<input id="custom_wp_link_text_next_page" name="' . $this->plugin::NAME . '[textcolor]" type="text" value="' . $textcolor . '" />';
		}

		
		public function add_settings_page()
		{
			add_options_page(
				__('Custom WP Links', $this->plugin::NAME),
				__('Custom WP Links', $this->plugin::NAME),
				'manage_options',
				$this->plugin::NAME,
				array($this, 'render_settings_page')
			);
		}
		
		public function validate_options($input)
		{
			$options = get_option($this->plugin::NAME);	
				
			return $input;
		}
		
		public function render_settings_page()
			{
		?>
				<div class="wrap">
					<h1><?php esc_html_e('Custom WP Links', $this->plugin::NAME); ?></h1>
		
					<form method="post" action="options.php">
						<?php
						settings_fields($this->plugin::NAME);
						do_settings_sections($this->plugin::NAME);
						submit_button();
						?>
					</form>
				</div>
		<?php
		}
		
}