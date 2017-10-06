<?php
if ( version_compare( $GLOBALS['wp_version'], '4.7-alpha', '<' ) ) {
	require get_template_directory() . '/inc/back-compat.php';
	return;
}

function cao_setup() {
	
	load_theme_textdomain( 'twentyseventeen' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'title-tag' );

	add_theme_support( 'post-thumbnails' );
	add_image_size( 'twentyseventeen-featured-image', 2000, 1200, true );
	add_image_size( 'twentyseventeen-thumbnail-avatar', 100, 100, true );

	// Set the default content width.
	$GLOBALS['content_width'] = 525;

	register_nav_menus( array(
		'top' => __( 'Top Menu','twentyseventeen')
	) );

	add_theme_support( 'html5', array(
		'comment-form',
		'comment-list',
		'gallery',
		'caption',
	) );

	add_theme_support( 'post-formats', array(
		'aside',
		'image',
		'video',
		'quote',
		'link',
		'gallery',
		'audio',
	) );

	add_theme_support('custom-logo', array(
		'width'       => 250,
		'height'      => 250,
		'flex-width'  => true,
	));

	// Add theme support for selective refresh for widgets.
	add_theme_support( 'customize-selective-refresh-widgets' );

	/*
	 * This theme styles the visual editor to resemble the theme style,
	 * specifically font, colors, and column width.
 	 */
	//add_editor_style( array( 'assets/css/editor-style.css', twentyseventeen_fonts_url() ) );

}
add_action('after_setup_theme', 'cao_setup' );

function twentyseventeen_content_width() {

	$content_width = $GLOBALS['content_width'];

	// Get layout.
	$page_layout = get_theme_mod( 'page_layout' );

	// Check if layout is one column.
	if ( 'one-column' === $page_layout ) {
		if ( twentyseventeen_is_frontpage() ) {
			$content_width = 644;
		} elseif ( is_page() ) {
			$content_width = 740;
		}
	}

	// Check if is single post and there is no sidebar.
	if ( is_single() && ! is_active_sidebar( 'sidebar-1' ) ) {
		$content_width = 740;
	}

	/**
	 * Filter Twenty Seventeen content width of the theme.
	 *
	 * @since Twenty Seventeen 1.0
	 *
	 * @param int $content_width Content width in pixels.
	 */
	$GLOBALS['content_width'] = apply_filters( 'twentyseventeen_content_width', $content_width );
}
add_action( 'template_redirect', 'twentyseventeen_content_width', 0 );
/**
 * Add preconnect for Google Fonts.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array  $urls           URLs to print for resource hints.
 * @param string $relation_type  The relation type the URLs are printed.
 * @return array $urls           URLs to print for resource hints.
 */
function twentyseventeen_resource_hints( $urls, $relation_type ) {
	if ( wp_style_is( 'twentyseventeen-fonts', 'queue' ) && 'preconnect' === $relation_type ) {
		$urls[] = array(
			'href' => 'https://fonts.gstatic.com',
			'crossorigin',
		);
	}

	return $urls;
}
add_filter( 'wp_resource_hints', 'twentyseventeen_resource_hints', 10, 2 );

/**
 * Replaces "[...]" (appended to automatically generated excerpts) with ... and
 * a 'Continue reading' link.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $link Link to single post/page.
 * @return string 'Continue reading' link prepended with an ellipsis.
 */
function twentyseventeen_excerpt_more( $link ) {
	if ( is_admin() ) {
		return $link;
	}

	$link = sprintf( '<p class="link-more"><a href="%1$s" class="more-link">%2$s</a></p>',
		esc_url( get_permalink( get_the_ID() ) ),
		/* translators: %s: Name of current post */
		sprintf( __( 'Continue reading<span class="screen-reader-text"> "%s"</span>', 'twentyseventeen' ), get_the_title( get_the_ID() ) )
	);
	return ' &hellip; ' . $link;
}
add_filter( 'excerpt_more', 'twentyseventeen_excerpt_more' );

/**
 * Handles JavaScript detection.
 *
 * Adds a `js` class to the root `<html>` element when JavaScript is detected.
 *
 * @since Twenty Seventeen 1.0
 */
function twentyseventeen_javascript_detection() {
	echo "<script>(function(html){html.className = html.className.replace(/\bno-js\b/,'js')})(document.documentElement);</script>\n";
}
add_action( 'wp_head', 'twentyseventeen_javascript_detection', 0 );

/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function twentyseventeen_pingback_header() {
	if ( is_singular() && pings_open() ) {
		printf( '<link rel="pingback" href="%s">' . "\n", get_bloginfo( 'pingback_url' ) );
	}
}
add_action( 'wp_head', 'twentyseventeen_pingback_header' );

/**
 * Display custom color CSS.
 */
function twentyseventeen_colors_css_wrap() {
	if ( 'custom' !== get_theme_mod( 'colorscheme' ) && ! is_customize_preview() ) {
		return;
	}

	require_once( get_parent_theme_file_path( '/inc/color-patterns.php' ) );
	$hue = absint( get_theme_mod( 'colorscheme_hue', 250 ) );
?>
	<style type="text/css" id="custom-theme-colors" <?php if ( is_customize_preview() ) { echo 'data-hue="' . $hue . '"'; } ?>>
		<?php echo twentyseventeen_custom_colors_css(); ?>
	</style>
<?php }
add_action( 'wp_head', 'twentyseventeen_colors_css_wrap' );

/**
 * Enqueue scripts and styles.
 */
function twentyseventeen_scripts() {
	wp_enqueue_style( 'twentyseventeen-style', get_stylesheet_uri() );

	// Load the dark colorscheme.
	if ( 'dark' === get_theme_mod( 'colorscheme', 'light' ) || is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-colors-dark', get_theme_file_uri( '/assets/css/colors-dark.css' ), array( 'twentyseventeen-style' ), '1.0' );
	}

	// Load the Internet Explorer 9 specific stylesheet, to fix display issues in the Customizer.
	if ( is_customize_preview() ) {
		wp_enqueue_style( 'twentyseventeen-ie9', get_theme_file_uri( '/assets/css/ie9.css' ), array( 'twentyseventeen-style' ), '1.0' );
		wp_style_add_data( 'twentyseventeen-ie9', 'conditional', 'IE 9' );
	}

	// Load the Internet Explorer 8 specific stylesheet.
	wp_enqueue_style( 'twentyseventeen-ie8', get_theme_file_uri( '/assets/css/ie8.css' ), array( 'twentyseventeen-style' ), '1.0' );
	wp_style_add_data( 'twentyseventeen-ie8', 'conditional', 'lt IE 9' );

	// Load the html5 shiv.
	wp_enqueue_script( 'html5', get_theme_file_uri( '/assets/js/html5.js' ), array(), '3.7.3' );
	wp_script_add_data( 'html5', 'conditional', 'lt IE 9' );

	wp_enqueue_script( 'twentyseventeen-skip-link-focus-fix', get_theme_file_uri( '/assets/js/skip-link-focus-fix.js' ), array(), '1.0', true );


	$twentyseventeen_l10n = array(
		'quote'          => twentyseventeen_get_svg( array( 'icon' => 'quote-right' ) ),
	);

	if ( has_nav_menu( 'top' ) ) {
		
		$twentyseventeen_l10n['expand']         = __( 'Expand child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['collapse']       = __( 'Collapse child menu', 'twentyseventeen' );
		$twentyseventeen_l10n['icon']           = twentyseventeen_get_svg( array( 'icon' => 'angle-down', 'fallback' => true ) );
	}
	wp_localize_script( 'twentyseventeen-skip-link-focus-fix', 'twentyseventeenScreenReaderText', $twentyseventeen_l10n );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'twentyseventeen_scripts' );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for content images.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $sizes A source size value for use in a 'sizes' attribute.
 * @param array  $size  Image size. Accepts an array of width and height
 *                      values in pixels (in that order).
 * @return string A source size value for use in a content image 'sizes' attribute.
 */
function twentyseventeen_content_image_sizes_attr( $sizes, $size ) {
	$width = $size[0];

	if ( 740 <= $width ) {
		$sizes = '(max-width: 706px) 89vw, (max-width: 767px) 82vw, 740px';
	}

	if ( is_active_sidebar( 'sidebar-1' ) || is_archive() || is_search() || is_home() || is_page() ) {
		if ( ! ( is_page() && 'one-column' === get_theme_mod( 'page_options' ) ) && 767 <= $width ) {
			 $sizes = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
		}
	}

	return $sizes;
}
add_filter( 'wp_calculate_image_sizes', 'twentyseventeen_content_image_sizes_attr', 10, 2 );

/**
 * Filter the `sizes` value in the header image markup.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $html   The HTML image tag markup being filtered.
 * @param object $header The custom header object returned by 'get_custom_header()'.
 * @param array  $attr   Array of the attributes for the image tag.
 * @return string The filtered header image HTML.
 */
function twentyseventeen_header_image_tag( $html, $header, $attr ) {
	if ( isset( $attr['sizes'] ) ) {
		$html = str_replace( $attr['sizes'], '100vw', $html );
	}
	return $html;
}
add_filter( 'get_header_image_tag', 'twentyseventeen_header_image_tag', 10, 3 );

/**
 * Add custom image sizes attribute to enhance responsive image functionality
 * for post thumbnails.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param array $attr       Attributes for the image markup.
 * @param int   $attachment Image attachment ID.
 * @param array $size       Registered image size or flat array of height and width dimensions.
 * @return string A source size value for use in a post thumbnail 'sizes' attribute.
 */
function twentyseventeen_post_thumbnail_sizes_attr( $attr, $attachment, $size ) {
	if ( is_archive() || is_search() || is_home() ) {
		$attr['sizes'] = '(max-width: 767px) 89vw, (max-width: 1000px) 54vw, (max-width: 1071px) 543px, 580px';
	} else {
		$attr['sizes'] = '100vw';
	}

	return $attr;
}
add_filter( 'wp_get_attachment_image_attributes', 'twentyseventeen_post_thumbnail_sizes_attr', 10, 3 );

/**
 * Use front-page.php when Front page displays is set to a static page.
 *
 * @since Twenty Seventeen 1.0
 *
 * @param string $template front-page.php.
 *
 * @return string The template to be used: blank if is_home() is true (defaults to index.php), else $template.
 */
function twentyseventeen_front_page_template( $template ) {
	return is_home() ? '' : $template;
}
add_filter( 'frontpage_template',  'twentyseventeen_front_page_template' );

/**
 * Implement the Custom Header feature.
 */
require get_parent_theme_file_path( '/inc/custom-header.php' );

/**
 * Custom template tags for this theme.
 */
require get_parent_theme_file_path( '/inc/template-tags.php' );

/**
 * Additional features to allow styling of the templates.
 */
require get_parent_theme_file_path( '/inc/template-functions.php' );

/**
 * Customizer additions.
 */
require get_parent_theme_file_path( '/inc/customizer.php' );

/**
 * SVG icons functions and filters.
 */
require get_parent_theme_file_path( '/inc/icon-functions.php' );

/* CAO */
global $wpdb;
//Регистрация новых ролей пользователей
function cao_init(){
	//Less
	echo '<link rel="stylesheet/less" type="text/css" href="'.get_template_directory_uri().'/assets/dashboard.less" />';
	wp_enqueue_script( 'dashboard-admin-less', get_template_directory_uri(). '/assets/less.js');
	//
	wp_enqueue_style( 'portfolio-admin-style', get_stylesheet_directory_uri() . '/assets/dashboard.css' );
	wp_enqueue_script( 'dashboard-admin-jquery', get_template_directory_uri(). '/assets/jquery-3.2.1.min.js');
	wp_enqueue_script( 'dashboard-admin-script', get_template_directory_uri(). '/assets/dashboard.js');
}
//Регистрация новых разделов, пользователей
function post_init(){
    register_post_type('estate',
        array(
            'labels' => array(
                'name' => 'Объекты',
                'singular_name' => 'Объект',
                'add_new' => 'Добавить новый',
                'add_new_item' => 'Добавить новый объект',
                'edit' => 'Редактировать',
                'edit_item' => 'Редактировать объект',
                'new_item' => 'Новый объект',
                'view' => 'Просмотреть',
                'view_item' => 'Просмотреть объект',
                'search_items' => 'Найти объекты',
                'not_found' => 'Объектов не найдено',
                'not_found_in_trash' => 'Объекты не найдены в корзине',
                'parent' => 'Родитель'
            ),
            'public' => true,
            'menu_position' => 5,
            'supports' => array('title','editor','thumbnail'),
			'taxonomies' => array( '' ),
			'show_in_nav_menus' => true,
			'show_ui' => true,
            'menu_icon' => 'dashicons-admin-home',
			'has_archive' => true,
			'show_in_nav_menus' => true,
			'taxonomies' => array('estate_root')
        )
	);
	
	register_taxonomy('estate_root', array('estate'), array(
		'label'                 => 'Центр', // определяется параметром $labels->name
		'labels'                => array(
			'name'              => 'Центры',
			'singular_name'     => 'Раздел вопроса',
			'search_items'      => 'Найти центры',
			'all_items'         => 'Все центры',
			'parent_item'       => 'Родит. раздел вопроса',
			'parent_item_colon' => 'Родит. раздел вопроса:',
			'edit_item'         => 'Изменить информацию о центре',
			'update_item'       => 'Обновить информацию',
			'add_new_item'      => 'Добавить центр',
			'new_item_name'     => 'Новый центр',
			'menu_name'         => 'Центры',
		),
		'description'           => 'Рубрики для раздела вопросов', // описание таксономии
		'public'                => true,
		'show_in_nav_menus'     => true, // равен аргументу public
		'show_ui'               => true, // равен аргументу public
		'show_tagcloud'         => false, // равен аргументу show_ui
		'hierarchical'          => true,
		'rewrite'               => array('slug'=>'property', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
		'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
	));
	register_taxonomy('offer-type', array('estate'), array(
		'label'                 => 'Тип сделки', // определяется параметром $labels->name
		'labels'                => array(
			'name'              => 'Тип сделки',
			'singular_name'     => 'Тип сделки',
			'search_items'      => 'Найти',
			'all_items'         => 'Показать все',
			'parent_item'       => 'Родит. раздел вопроса',
			'parent_item_colon' => 'Родит. раздел вопроса:',
			'edit_item'         => 'Изменить информацию о сделке',
			'update_item'       => 'Обновить информацию',
			'add_new_item'      => 'Добавить тип',
			'new_item_name'     => 'Новый тип сделки',
			'menu_name'         => 'Тип сделки',
		),
		'description'           => 'Тип сделки для недвижимости', // описание таксономии
		'public'                => true,
		'show_in_nav_menus'     => false, // равен аргументу public
		'show_ui'               => true, // равен аргументу public
		'show_tagcloud'         => false, // равен аргументу show_ui
		'hierarchical'          => true,
		'rewrite'               => array('slug'=>'offer', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
		'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
	));
	register_taxonomy('property-type', array('estate'), array(
		'label'                 => 'Вид недвижимости', // определяется параметром $labels->name
		'labels'                => array(
			'name'              => 'Вид недвижимости',
			'singular_name'     => 'Вид недвижимости',
			'search_items'      => 'Найти',
			'all_items'         => 'Показать все',
			'parent_item'       => 'Родит. раздел вопроса',
			'parent_item_colon' => 'Родит. раздел вопроса:',
			'edit_item'         => 'Изменить информацию',
			'update_item'       => 'Обновить информацию',
			'add_new_item'      => 'Добавить вид',
			'new_item_name'     => 'Новый вид недвижимости',
			'menu_name'         => 'Вид недвижимости',
		),
		'description'           => 'Вид недвижимости для объекта', // описание таксономии
		'public'                => true,
		'show_in_nav_menus'     => false, // равен аргументу public
		'show_ui'               => true, // равен аргументу public
		'show_tagcloud'         => false, // равен аргументу show_ui
		'hierarchical'          => true,
		'rewrite'               => array('slug'=>'property-type', 'hierarchical'=>false, 'with_front'=>false, 'feed'=>false ),
		'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
	));
	register_taxonomy('hot_offers', array('estate'), array(
		'label'                 => 'Горячие предложения', // определяется параметром $labels->name
		'labels'                => array(
			'name'              => 'Горячие предложения',
			'singular_name'     => 'Горячее предложение',
			'search_items'      => 'Найти',
			'all_items'         => 'Все записи',
			'parent_item'       => 'Родит. раздел вопроса',
			'parent_item_colon' => 'Родит. раздел вопроса:',
			'edit_item'         => 'Изменить информацию',
			'update_item'       => 'Обновить информацию',
			'add_new_item'      => 'Добавить новое',
			'new_item_name'     => 'Новое'
		),
		'description'           => 'Рубрики для раздела вопросов', // описание таксономии
		'meta_box_cb'       => 'hot_offers_meta',
		'show_admin_column'     => true, // Позволить или нет авто-создание колонки таксономии в таблице ассоциированного типа записи. (с версии 3.5)
	));
	add_role('manager', __('Менеджер' ),array(
		'read' => true, // true allows this capability
		'edit_posts' => true, // Allows user to edit their own posts
		'edit_pages' => true, // Allows user to edit pages
		'edit_others_posts' => true, // Allows user to edit others posts not just their own
		'create_posts' => true, // Allows user to create new posts
		'manage_categories' => true, // Allows user to manage post categories
		'publish_posts' => true, // Allows the user to publish, otherwise posts stays in draft mode
		'edit_themes' => false, // false denies this capability. User can’t edit your theme
		'install_plugins' => false, // User cant add new plugins
		'update_plugin' => false, // User can’t update any plugins
		'update_core' => false // user cant perform core updates
	));
	
	//update
	
}

function remove_some_stuff(){
	//Удаляем лишние ссылки
	global $submenu;
	unset($submenu['edit.php?post_type=estate'][18]);
	//Добавляем настройки
	add_menu_page('CAO', 'Настройки CAO', 'administrator', __FILE__, 'cao_config','data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0ibG9nbyIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiIHdpZHRoPSI0NXB4IiBoZWlnaHQ9IjQ1cHgiIHZpZXdCb3g9IjAgMCA0NSA0NSIgc3R5bGU9ImVuYWJsZS1iYWNrZ3JvdW5kOm5ldyAwIDAgNDUgNDU7IiB4bWw6c3BhY2U9InByZXNlcnZlIj4NCjxzdHlsZSB0eXBlPSJ0ZXh0L2NzcyI+LnN0MHtmaWxsOiNGRkZGRkY7fQ0KPC9zdHlsZT4NCjxnPg0KCTxwYXRoIGNsYXNzPSJzdDAgc3QtYW5pbTAiIGQ9Ik0yMyw3djE2SDdWN0gyMyBNMzAsMEgwdjMwaDMwVjBMMzAsMHoiLz4NCjwvZz4NCjxnPg0KCTxwYXRoIGNsYXNzPSJzdDAgc3QtYW5pbTEiIGQ9Ik0zOCwzMHY4aC04di04SDM4IE00NSwyM0gyM3YyMmgyMlYyM0w0NSwyM3oiLz4NCjwvZz4NCjwvc3ZnPg0K');
	add_action( 'admin_init', 'register_ns' );
}
add_action('admin_menu', 'remove_some_stuff');

function register_ns(){
	$fields = array('cao_city','cao_streetname','cao_streetnum','cao_phones','cao_latlng');
	foreach($fields as $field){
		register_setting('cao_config', strval($field));
	}
}
function cao_config() {
	require get_parent_theme_file_path('/assets/tmp/settings.php');
} 

function hot_offers_meta($post){
	require get_parent_theme_file_path('/assets/tmp/metabox/hot_offer.php');
}
	
add_filter('user_contactmethods', 'my_user_contactmethods');
function my_user_contactmethods($user_contactmethods){
	$user_contactmethods['phone'] = 'Контактный номер телефона';
	return $user_contactmethods;
}

//Регистрируем плагин CAO
function register_metaf(){
	add_meta_box('estate','Недвижимость','estate_extra','estate','normal','high');
	add_meta_box('estate_root','Недвижимость','estate_root_extra','estate_root','normal','high');
}

// html код блока для типа записей page
function estate_extra($post){
	require get_parent_theme_file_path('/assets/tmp/estate.php');
	?>
	<input type="hidden" name="extra_fields_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<?php
}
function estate_root_extra($post){
	require get_parent_theme_file_path('/assets/tmp/estate_root.php');
	?>
	<input type="hidden" name="extra_fields_root_nonce" value="<?php echo wp_create_nonce(__FILE__); ?>" />
	<?php
}
add_action('save_post', 'estate_update', 0);
add_action('save_post', 'estate_root_update', 0);
function estate_update($post_id){
	global $wpdb;
	if ( !isset($_POST['extra_fields_nonce']) || !wp_verify_nonce($_POST['extra_fields_nonce'], __FILE__) ) return false;
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false;
	if ( !current_user_can('edit_post', $post_id) ) return false;
	if( !isset($_POST['extra']) ) return false; 

	$preprice = get_post_meta($post_id,'price',1);

	$_POST['extra'] = array_map('trim', $_POST['extra']);
	foreach($_POST['extra'] as $key=>$value ){
		if( empty($value) ){
			delete_post_meta($post_id, $key);
			continue;
		}
		update_post_meta($post_id, $key, $value);
	}
	//сохраняем файл
	foreach($_FILES as $FILEmeta => $FILE){
		if(!empty($FILE)){
			$FILEDATA = array();
			$FILEDATA['size'] = $FILE['size'];
			$filename = str_replace(array('.','-'),array('',''),strval(microtime()))."".strrchr($FILE['name'], '.');	
			$upload = wp_upload_bits($filename,null,file_get_contents($FILE['tmp_name']));
			if(!$upload['error']){
				$FILEDATA['url'] = $upload['url'];
				update_post_meta($post_id,$FILEmeta,json_encode($FILEDATA));
			}
		}
	}
	//WPDB
	offers_reorder();
	//save tax hot offers
	if($_POST['hot_offer']){
		if($_POST['hot_offer'] == 'true'){
			wp_set_object_terms($post_id,'hot_offers','hot_offers',false);
		}
		update_post_meta($post_id,'hot_offer',$_POST['hot_offer']);	
	}
	return $post_id;
}
function estate_root_update($post_id){
	if ( !isset($_POST['extra_fields_root_nonce']) || !wp_verify_nonce($_POST['extra_fields_root_nonce'], __FILE__) ) return false; // проверка
	if ( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE  ) return false; // если это автосохранение
	if ( !current_user_can('edit_post', $post_id) ) return false; // если юзер не имеет право редактировать запись

	if( !isset($_POST['extra']) ) return false; 

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$_POST['extra'] = array_map('trim', $_POST['extra']);
	foreach($_POST['extra'] as $key=>$value ){
		if( empty($value) ){
			delete_post_meta($post_id, $key); // удаляем поле если значение пустое
			continue;
		}
		update_post_meta($post_id, $key, $value); // add_post_meta() работает автоматически
	}
	//сохраняем файл
	foreach($_FILES as $FILEmeta => $FILE){
		if(!empty($FILE)){
			$FILEDATA = array();
			$FILEDATA['size'] = $FILE['size'];
			$filename = str_replace(array('.','-'),array('',''),strval(microtime()))."".strrchr($FILE['name'], '.');	
			$upload = wp_upload_bits($filename,null,file_get_contents($FILE['tmp_name']));
			if(!$upload['error']){
				$FILEDATA['url'] = $upload['url'];
				update_post_meta($post_id,$FILEmeta,json_encode($FILEDATA));
			}
		}
	}
	//save banner
	return $post_id;
}
post_init();
add_action('admin_init','cao_init');
add_action('add_meta_boxes', 'register_metaf', 1);


function remove_admin_login_header(){remove_action('wp_head', '_admin_bar_bump_cb');}
function remove_website_row_wpse_94963_css(){echo '<style>tr.user-url-wrap,tr.user-description-wrap{ display: none; }</style>';}
//add_action('get_header', 'remove_admin_login_header');
add_action( 'admin_head-user-edit.php', 'remove_website_row_wpse_94963_css' );
add_action( 'admin_head-profile.php',   'remove_website_row_wpse_94963_css' );

//WIDGETS
function cao_widget_events($post,$callback_args){
	require get_parent_theme_file_path('/assets/tmp/widgets/events.php');
}
function cao_widget_bids($post,$callback_args){
	require get_parent_theme_file_path('/assets/tmp/widgets/bids.php');
}
function add_cao_widgets() {
	wp_add_dashboard_widget('cao_widget_events', 'Далее', 'cao_widget_events');
	wp_add_dashboard_widget('cao_widget_bids', 'Заявки', 'cao_widget_bids');
}

add_action('wp_dashboard_setup', 'add_cao_widgets' );
function update_edit_form() {
    echo 'enctype="multipart/form-data"';
}

add_action('post_edit_form_tag', 'update_edit_form');


//TAX
// Поля при добавлении элемента таксономии
add_action("estate_root_add_form_fields", 'add_new_custom_fields');
add_action("estate_root_edit_form_fields", 'edit_new_custom_fields');

// Сохранение при добавлении элемента таксономии
add_action("create_estate_root", 'save_custom_taxonomy_meta');
add_action("edited_estate_root", 'save_custom_taxonomy_meta');

function edit_new_custom_fields($term) {
	require get_parent_theme_file_path('/assets/tmp/estate-tax-edit.php');
}
function add_new_custom_fields($taxonomy_slug){
	require get_parent_theme_file_path('/assets/tmp/estate-tax.php');
}

function save_custom_taxonomy_meta($term_id){
	if ( ! isset($_POST['extra']) ) return;
	if ( ! current_user_can('edit_term', $term_id) ) return;
	if (
		! wp_verify_nonce( $_POST['_wpnonce'], "update-tag_$term_id" ) && // wp_nonce_field( 'update-tag_' . $tag_ID );
		! wp_verify_nonce( $_POST['_wpnonce_add-tag'], "add-tag" ) // wp_nonce_field('add-tag', '_wpnonce_add-tag');
	) return;

	// Все ОК! Теперь, нужно сохранить/удалить данные
	$extra = wp_unslash($_POST['extra']);

	foreach($extra as $key => $val){
		// проверка ключа
		$_key = sanitize_key($key);
		if( $_key !== $key ) wp_die( 'bad key'. esc_html($key) );

		// очистка
		if( $_key === 'tag_posts_shortcode_links' )
			$val = sanitize_textarea_field( strip_tags($val) );
		else
			$val = sanitize_text_field( $val );

		// сохранение
		if( ! $val )
			delete_term_meta( $term_id, $_key );
		else
			update_term_meta( $term_id, $_key, $val );
	}

	return $term_id;
}
function comparedate($f,$t){
	if(!$f){return;}
	$return = '';
	if(!$t){$t = date("d-m-Y");}
	$NDDATE = explode('-',$t);
	if(intval($NDDATE[2]) <= intval($f[0]) && intval($NDDATE[1]) <= intval($f[1])){
		$return = true;
	}
	return $return;
}
function the_icon($name){
	return file_get_contents(get_template_directory_uri().'/assets/images/icon-'.$name.'.svg');
}
function formatnumber($number){
	$number = str_replace(array('(',')',' ','-'),array('','','',''),$number);//8803353535
	$city = ''; // 800
	$rt = ''; // +7 / 7
	if(strlen($number) > 9){
		if(strlen($number) > 10){
			if($number[0] == '+'){
				$rt = substr($number,0,2).' ';
				$city = '('.substr($number,2,3).') ';
				$number = substr($number,5);
			}else{
				$rt = substr($number,0,1).' ';
				$city = '('.substr($number,1,3).') ';
				$number = substr($number,4);
			}
		}else{
			$city = '('.substr($number,0,3).') ';
			$number = substr($number,3);
		}
	}
	return $rt.''.$city.''.substr($number,0,3).'-'.substr($number,3,2).'-'.substr($number,5,2);
}
function my_myme_types($mime_types){
    $mime_types['svg'] = 'image/svg+xml'; //Добавляем расширение svg
    return $mime_types;
}
function offers_reorder(){
	global $wpdb;
	if($wpdb->query("SELECT 1 FROM cao_config LIMIT 1") == false){
		$wpdb->query("CREATE TABLE cao_config (id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY, user INT(6), cfg VARCHAR(50), value VARCHAR(50))");
	}
	$query = $wpdb->get_results("SELECT `id`,`value`,`cfg` FROM `cao_config` WHERE `cfg` IN ('pricelow','pricehigh','arealow','areahigh')",ARRAY_A);
	$old = array();
	foreach($query as $qitem){
		$old[$qitem['cfg']] = array();
		$old[$qitem['cfg']]['id'] = $qitem['id'];
		$old[$qitem['cfg']]['value'] = $qitem['value'];
	}
	$price = array(1000000,0);
	$area = array(1000000,0);
	//
	$posts = get_posts(array('posts_per_page' => -1, 'post_type' => 'estate'));

	foreach($posts as $post){
		$postprice = get_post_meta($post->ID,'price',1);
		$postarea = get_post_meta($post->ID,'area',1);
		if(strlen($postprice)>0){
			if(intval($postprice) < $price[0]){$price[0] = intval($postprice);}
			if(intval($postprice) > $price[1]){$price[1] = intval($postprice);}
		}
		if(strlen($postarea)>0){
			if(intval($postarea) < $area[0]){$area[0] = intval($postarea);}
			if(intval($postarea) > $area[1]){$area[1] = intval($postarea);}
		}
	}
	
	if(!isset($old['pricelow'])){$wpdb->query("INSERT INTO `cao_config` (`user`,`cfg`,`value`) VALUES ('-1','pricelow','".$price[0]."')");}else{if($price[0] != intval($old['pricelow']['value'])){$wpdb->query("UPDATE `cao_config` SET `value`='".$price[0]."' WHERE `id`=".$old['pricelow']['id']);}}
	if(!isset($old['pricehigh'])){$wpdb->query("INSERT INTO `cao_config` (`user`,`cfg`,`value`) VALUES ('-1','pricehigh','".$price[1]."')");}else{if($price[1] != intval($old['pricehigh']['value'])){$wpdb->query("UPDATE `cao_config` SET `value`='".$price[1]."' WHERE `id`=".$old['pricehigh']['id']);}}
	if(!isset($old['arealow'])){$wpdb->query("INSERT INTO `cao_config` (`user`,`cfg`,`value`) VALUES ('-1','arealow','".$area[0]."')");}else{if($area[0] != intval($old['arealow']['value'])){$wpdb->query("UPDATE `cao_config` SET `value`='".$area[0]."' WHERE `id`=".$old['arealow']['id']);}}
	if(!isset($old['areahigh'])){$wpdb->query("INSERT INTO `cao_config` (`user`,`cfg`,`value`) VALUES ('-1','areahigh','".$area[1]."')");}else{if($area[1] != intval($old['areahigh']['value'])){$wpdb->query("UPDATE `cao_config` SET `value`='".$area[1]."' WHERE `id`=".$old['areahigh']['id']);}}
	//
	return true;
	
}
add_filter('upload_mimes', 'my_myme_types', 1, 1);