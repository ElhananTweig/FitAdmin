<?php
/**
 * Twenty Twenty-Five functions and definitions.
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package WordPress
 * @subpackage Twenty_Twenty_Five
 * @since Twenty Twenty-Five 1.0
 */

// Include the authentication handler
require_once get_template_directory() . '/auth-handler.php';

// יוצר מופע גלובלי של מערכת האימות
global $crm_auth;
$crm_auth = new CRM_Auth_Handler();

// הפעלה אוטומטית של תוסף ACF אם הוא קיים אבל לא פעיל
function auto_activate_acf() {
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    $plugin_path = 'advanced-custom-fields/acf.php';
    
    // בדיקה אם התוסף קיים אבל לא פעיל
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path) && !is_plugin_active($plugin_path)) {
        activate_plugin($plugin_path);
        
        // הוספת הודעה לאדמין
        add_action('admin_notices', function() {
            echo '<div class="notice notice-success is-dismissible" style="direction: rtl;">
                <p><strong>✅ תוסף Advanced Custom Fields הופעל אוטומטית!</strong></p>
                <p>כעת מערכת ה-CRM מוכנה לשימוש. תוכל ליצור נתוני דמו.</p>
            </div>';
        });
    }
}
add_action('admin_init', 'auto_activate_acf');

// Adds theme support for post formats.
if ( ! function_exists( 'twentytwentyfive_post_format_setup' ) ) :
	/**
	 * Adds theme support for post formats.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_post_format_setup() {
		add_theme_support( 'post-formats', array( 'aside', 'audio', 'chat', 'gallery', 'image', 'link', 'quote', 'status', 'video' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_post_format_setup' );

// Enqueues editor-style.css in the editors.
if ( ! function_exists( 'twentytwentyfive_editor_style' ) ) :
	/**
	 * Enqueues editor-style.css in the editors.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_editor_style() {
		add_editor_style( get_parent_theme_file_uri( 'assets/css/editor-style.css' ) );
	}
endif;
add_action( 'after_setup_theme', 'twentytwentyfive_editor_style' );

// Enqueues style.css on the front.
if ( ! function_exists( 'twentytwentyfive_enqueue_styles' ) ) :
	/**
	 * Enqueues style.css on the front.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_enqueue_styles() {
		wp_enqueue_style(
			'twentytwentyfive-style',
			get_parent_theme_file_uri( 'style.css' ),
			array(),
			wp_get_theme()->get( 'Version' )
		);
	}
endif;
add_action( 'wp_enqueue_scripts', 'twentytwentyfive_enqueue_styles' );

// Registers custom block styles.
if ( ! function_exists( 'twentytwentyfive_block_styles' ) ) :
	/**
	 * Registers custom block styles.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_block_styles() {
		register_block_style(
			'core/list',
			array(
				'name'         => 'checkmark-list',
				'label'        => __( 'Checkmark', 'twentytwentyfive' ),
				'inline_style' => '
				ul.is-style-checkmark-list {
					list-style-type: "\2713";
				}

				ul.is-style-checkmark-list li {
					padding-inline-start: 1ch;
				}',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_block_styles' );

// Registers pattern categories.
if ( ! function_exists( 'twentytwentyfive_pattern_categories' ) ) :
	/**
	 * Registers pattern categories.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_pattern_categories() {

		register_block_pattern_category(
			'twentytwentyfive_page',
			array(
				'label'       => __( 'Pages', 'twentytwentyfive' ),
				'description' => __( 'A collection of full page layouts.', 'twentytwentyfive' ),
			)
		);

		register_block_pattern_category(
			'twentytwentyfive_post-format',
			array(
				'label'       => __( 'Post formats', 'twentytwentyfive' ),
				'description' => __( 'A collection of post format patterns.', 'twentytwentyfive' ),
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_pattern_categories' );

// Registers block binding sources.
if ( ! function_exists( 'twentytwentyfive_register_block_bindings' ) ) :
	/**
	 * Registers the post format block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return void
	 */
	function twentytwentyfive_register_block_bindings() {
		register_block_bindings_source(
			'twentytwentyfive/format',
			array(
				'label'              => _x( 'Post format name', 'Label for the block binding placeholder in the editor', 'twentytwentyfive' ),
				'get_value_callback' => 'twentytwentyfive_format_binding',
			)
		);
	}
endif;
add_action( 'init', 'twentytwentyfive_register_block_bindings' );

// Registers block binding callback function for the post format name.
if ( ! function_exists( 'twentytwentyfive_format_binding' ) ) :
	/**
	 * Callback function for the post format name block binding source.
	 *
	 * @since Twenty Twenty-Five 1.0
	 *
	 * @return string|void Post format name, or nothing if the format is 'standard'.
	 */
	function twentytwentyfive_format_binding() {
		$post_format_slug = get_post_format();

		if ( $post_format_slug && 'standard' !== $post_format_slug ) {
			return get_post_format_string( $post_format_slug );
		}
	}
endif;

// Custom Post Types למערכת CRM תזונאית

// יצירת Custom Post Type למתאמנות
function create_clients_post_type() {
    register_post_type('clients',
        array(
            'labels' => array(
                'name' => __('מתאמנות'),
                'singular_name' => __('מתאמנת'),
                'menu_name' => __('מתאמנות'),
                'add_new' => __('הוסף מתאמנת'),
                'add_new_item' => __('הוסף מתאמנת חדשה'),
                'edit_item' => __('ערוך מתאמנת'),
                'new_item' => __('מתאמנת חדשה'),
                'view_item' => __('צפה במתאמנת'),
                'search_items' => __('חפש מתאמנות'),
                'not_found' => __('לא נמצאו מתאמנות'),
                'not_found_in_trash' => __('לא נמצאו מתאמנות בפח')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'clients'),
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-groups',
            'menu_position' => 20,
            'show_in_rest' => true,
            'capabilities' => array(
                'edit_post' => 'edit_clients',
                'read_post' => 'read_clients',
                'delete_post' => 'delete_clients',
                'edit_posts' => 'edit_clients',
                'edit_others_posts' => 'edit_others_clients',
                'publish_posts' => 'publish_clients',
                'read_private_posts' => 'read_private_clients',
            ),
        )
    );
}
add_action('init', 'create_clients_post_type');

// יצירת Custom Post Type למנטוריות
function create_mentors_post_type() {
    register_post_type('mentors',
        array(
            'labels' => array(
                'name' => __('מנטוריות'),
                'singular_name' => __('מנטורית'),
                'menu_name' => __('מנטוריות'),
                'add_new' => __('הוסף מנטורית'),
                'add_new_item' => __('הוסף מנטורית חדשה'),
                'edit_item' => __('ערוך מנטורית'),
                'new_item' => __('מנטורית חדשה'),
                'view_item' => __('צפה במנטורית'),
                'search_items' => __('חפש מנטוריות'),
                'not_found' => __('לא נמצאו מנטוריות'),
                'not_found_in_trash' => __('לא נמצאו מנטוריות בפח')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'mentors'),
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-businesswoman',
            'menu_position' => 21,
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'create_mentors_post_type');

// יצירת Custom Post Type לקבוצות
function create_groups_post_type() {
    register_post_type('groups',
        array(
            'labels' => array(
                'name' => __('קבוצות'),
                'singular_name' => __('קבוצה'),
                'menu_name' => __('קבוצות'),
                'add_new' => __('הוסף קבוצה'),
                'add_new_item' => __('הוסף קבוצה חדשה'),
                'edit_item' => __('ערוך קבוצה'),
                'new_item' => __('קבוצה חדשה'),
                'view_item' => __('צפה בקבוצה'),
                'search_items' => __('חפש קבוצות'),
                'not_found' => __('לא נמצאו קבוצות'),
                'not_found_in_trash' => __('לא נמצאו קבוצות בפח')
            ),
            'public' => true,
            'has_archive' => true,
            'rewrite' => array('slug' => 'groups'),
            'supports' => array('title', 'editor', 'custom-fields'),
            'menu_icon' => 'dashicons-groups',
            'menu_position' => 22,
            'show_in_rest' => true,
        )
    );
}
add_action('init', 'create_groups_post_type');

// הוספת טקסונומיות
function create_client_taxonomies() {
    // סטטוס תשלום
    register_taxonomy('payment_status', 'clients', array(
        'labels' => array(
            'name' => 'סטטוס תשלום',
            'singular_name' => 'סטטוס תשלום',
            'menu_name' => 'סטטוס תשלום',
        ),
        'rewrite' => array('slug' => 'payment-status'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
    
    // מקור הגעה
    register_taxonomy('referral_source', 'clients', array(
        'labels' => array(
            'name' => 'מקור הגעה',
            'singular_name' => 'מקור הגעה',
            'menu_name' => 'מקור הגעה',
        ),
        'rewrite' => array('slug' => 'referral-source'),
        'hierarchical' => true,
        'show_in_rest' => true,
    ));
}
add_action('init', 'create_client_taxonomies');

// הוספת שדות מותאמים (ACF)
function add_client_custom_fields() {
    if (function_exists('acf_add_field_group')) {
        acf_add_field_group(array(
            'key' => 'group_clients',
            'title' => 'פרטי מתאמנת',
            'fields' => array(
                array(
                    'key' => 'field_first_name',
                    'label' => 'שם פרטי',
                    'name' => 'first_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_last_name',
                    'label' => 'שם משפחה',
                    'name' => 'last_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_phone',
                    'label' => 'טלפון',
                    'name' => 'phone',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_email',
                    'label' => 'אימייל',
                    'name' => 'email',
                    'type' => 'email',
                ),
                array(
                    'key' => 'field_age',
                    'label' => 'גיל',
                    'name' => 'age',
                    'type' => 'number',
                ),
                array(
                    'key' => 'field_start_date',
                    'label' => 'תאריך התחלה',
                    'name' => 'start_date',
                    'type' => 'date_picker',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_end_date',
                    'label' => 'תאריך סיום',
                    'name' => 'end_date',
                    'type' => 'date_picker',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_start_weight',
                    'label' => 'משקל התחלתי',
                    'name' => 'start_weight',
                    'type' => 'number',
                    'append' => 'ק"ג',
                ),
                array(
                    'key' => 'field_current_weight',
                    'label' => 'משקל נוכחי',
                    'name' => 'current_weight',
                    'type' => 'number',
                    'append' => 'ק"ג',
                ),
                array(
                    'key' => 'field_target_weight',
                    'label' => 'משקל יעד',
                    'name' => 'target_weight',
                    'type' => 'number',
                    'append' => 'ק"ג',
                ),
                array(
                    'key' => 'field_referral_source',
                    'label' => 'דרך הגעה',
                    'name' => 'referral_source',
                    'type' => 'select',
                    'choices' => array(
                        'instagram' => 'אינסטגרם',
                        'status' => 'סטטוס',
                        'whatsapp' => 'וואצאפ',
                        'referral' => 'המלצה',
                        'mentor' => 'מנטורית',
                        'unknown' => 'לא ידוע',
                    ),
                    'required' => 1,
                ),
                array(
                    'key' => 'field_payment_amount',
                    'label' => 'סכום לתשלום',
                    'name' => 'payment_amount',
                    'type' => 'number',
                    'append' => '₪',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_amount_paid',
                    'label' => 'סכום ששולם',
                    'name' => 'amount_paid',
                    'type' => 'number',
                    'append' => '₪',
                    'default_value' => 0,
                ),
                array(
                    'key' => 'field_payment_method',
                    'label' => 'אמצעי תשלום',
                    'name' => 'payment_method',
                    'type' => 'select',
                    'choices' => array(
                        'cash' => 'מזומן',
                        'credit' => 'כרטיס אשראי',
                        'bank_transfer' => 'העברה בנקאית',
                        'paypal' => 'PayPal',
                        'bit' => 'Bit',
                    ),
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_amount_paid',
                                'operator' => '>',
                                'value' => '0',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_payment_date',
                    'label' => 'תאריך תשלום',
                    'name' => 'payment_date',
                    'type' => 'date_picker',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_amount_paid',
                                'operator' => '>',
                                'value' => '0',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_payment_percentage',
                    'label' => 'אחוז עמלה',
                    'name' => 'payment_percentage',
                    'type' => 'number',
                    'append' => '%',
                    'default_value' => 40,
                ),
                array(
                    'key' => 'field_mentor',
                    'label' => 'מנטורית',
                    'name' => 'mentor',
                    'type' => 'post_object',
                    'post_type' => array('mentors'),
                    'allow_null' => 1,
                    'return_format' => 'object',
                ),
                array(
                    'key' => 'field_notes',
                    'label' => 'הערות',
                    'name' => 'notes',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_is_frozen',
                    'label' => 'בהקפאה',
                    'name' => 'is_frozen',
                    'type' => 'true_false',
                ),
                array(
                    'key' => 'field_freeze_start',
                    'label' => 'תאריך תחילת הקפאה',
                    'name' => 'freeze_start',
                    'type' => 'date_picker',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_is_frozen',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_freeze_end',
                    'label' => 'תאריך סיום הקפאה',
                    'name' => 'freeze_end',
                    'type' => 'date_picker',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_is_frozen',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                array(
                    'key' => 'field_freeze_reason',
                    'label' => 'סיבת הקפאה',
                    'name' => 'freeze_reason',
                    'type' => 'text',
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_is_frozen',
                                'operator' => '==',
                                'value' => '1',
                            ),
                        ),
                    ),
                ),
                // שדות מעקב מתאמנות שסיימו
                array(
                    'key' => 'field_follow_up_notes',
                    'label' => 'הערות מעקב',
                    'name' => 'follow_up_notes',
                    'type' => 'textarea',
                    'instructions' => 'הערות על מעקב אחר מתאמנת שסיימה',
                ),
                array(
                    'key' => 'field_last_contact_date',
                    'label' => 'תאריך קשר אחרון',
                    'name' => 'last_contact_date',
                    'type' => 'date_picker',
                    'instructions' => 'מתי נוצר קשר עם המתאמנת בפעם האחרונה',
                ),
                array(
                    'key' => 'field_next_contact_date',
                    'label' => 'תאריך מעקב הבא',
                    'name' => 'next_contact_date',
                    'type' => 'date_picker',
                    'instructions' => 'מתי לבצע מעקב הבא',
                ),
                // שדות ליווי קבוצתי
                array(
                    'key' => 'field_training_type',
                    'label' => 'סוג ליווי',
                    'name' => 'training_type',
                    'type' => 'select',
                    'choices' => array(
                        'personal' => 'אישי',
                        'group' => 'קבוצתי'
                    ),
                    'default_value' => 'personal',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_group_id',
                    'label' => 'קבוצה',
                    'name' => 'group_id',
                    'type' => 'post_object',
                    'post_type' => array('groups'),
                    'allow_null' => 1,
                    'conditional_logic' => array(
                        array(
                            array(
                                'field' => 'field_training_type',
                                'operator' => '==',
                                'value' => 'group',
                            ),
                        ),
                    ),
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'clients',
                    ),
                ),
            ),
        ));
        
        // שדות למנטוריות
        acf_add_field_group(array(
            'key' => 'group_mentors',
            'title' => 'פרטי מנטורית',
            'fields' => array(
                array(
                    'key' => 'field_mentor_first_name',
                    'label' => 'שם פרטי',
                    'name' => 'mentor_first_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_mentor_last_name',
                    'label' => 'שם משפחה',
                    'name' => 'mentor_last_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_mentor_phone',
                    'label' => 'טלפון',
                    'name' => 'mentor_phone',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_mentor_email',
                    'label' => 'אימייל',
                    'name' => 'mentor_email',
                    'type' => 'email',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_payment_percentage',
                    'label' => 'אחוז עמלה',
                    'name' => 'payment_percentage',
                    'type' => 'number',
                    'append' => '%',
                    'default_value' => 40,
                ),
                array(
                    'key' => 'field_mentor_notes',
                    'label' => 'הערות',
                    'name' => 'mentor_notes',
                    'type' => 'textarea',
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'mentors',
                    ),
                ),
            ),
        ));

        // יצירת שדות ACF לקבוצות
        acf_add_local_field_group(array(
            'key' => 'group_groups',
            'title' => 'פרטי קבוצה',
            'fields' => array(
                array(
                    'key' => 'field_group_name',
                    'label' => 'שם הקבוצה',
                    'name' => 'group_name',
                    'type' => 'text',
                    'required' => 1,
                ),
                array(
                    'key' => 'field_group_mentor',
                    'label' => 'מנטורית הקבוצה',
                    'name' => 'group_mentor',
                    'type' => 'post_object',
                    'post_type' => array('mentors'),
                    'allow_null' => 0,
                    'required' => 1,
                ),
                array(
                    'key' => 'field_group_description',
                    'label' => 'תיאור הקבוצה',
                    'name' => 'group_description',
                    'type' => 'textarea',
                ),
                array(
                    'key' => 'field_group_start_date',
                    'label' => 'תאריך התחלת הקבוצה',
                    'name' => 'group_start_date',
                    'type' => 'date_picker',
                ),
                array(
                    'key' => 'field_group_end_date',
                    'label' => 'תאריך סיום הקבוצה',
                    'name' => 'group_end_date',
                    'type' => 'date_picker',
                ),
                array(
                    'key' => 'field_group_max_participants',
                    'label' => 'מספר משתתפות מקסימלי',
                    'name' => 'group_max_participants',
                    'type' => 'number',
                    'default_value' => 10,
                ),
            ),
            'location' => array(
                array(
                    array(
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'groups',
                    ),
                ),
            ),
        ));
    }
}
add_action('acf/init', 'add_client_custom_fields');

// הוספת תמיכה בעברית
function add_hebrew_support() {
    load_theme_textdomain('crm-tzuna', get_template_directory() . '/languages');
}
add_action('after_setup_theme', 'add_hebrew_support');

// הוספת עמוד דשבורד מותאם
function add_dashboard_menu() {
    add_menu_page(
        'דשבורד CRM',
        'דשבורד CRM',
        'manage_options',
        'crm-dashboard',
        'crm_dashboard_page',
        'dashicons-chart-area',
        2
    );
    
    // הוספת תת-עמוד למתאמנות שסיימו
    add_submenu_page(
        'crm-dashboard',
        'מתאמנות שסיימו',
        'מתאמנות שסיימו',
        'manage_options',
        'finished-clients',
        'finished_clients_page'
    );
    
    // הוספת תת-עמוד לדוחות
    add_submenu_page(
        'crm-dashboard',
        'דוחות ואנליטיקס',
        'דוחות ואנליטיקס',
        'manage_options',
        'crm-reports',
        'crm_reports_page'
    );
    
    // הוספת עמוד ניהול תשלומים
    add_submenu_page(
        'crm-dashboard',
        'ניהול תשלומים',
        'ניהול תשלומים',
        'manage_options',
        'payments-management',
        'payments_management_page'
    );
    
    // הוספת טופס מתאמנת חדשה
    add_submenu_page(
        'crm-dashboard',
        'הוסף מתאמנת',
        'הוסף מתאמנת',
        'manage_options',
        'add-client-form',
        'add_client_form_page'
    );
    
    // הוספת טופס מנטורית חדשה
    add_submenu_page(
        'crm-dashboard',
        'הוסף מנטורית',
        'הוסף מנטורית',
        'manage_options',
        'add-mentor-form',
        'add_mentor_form_page'
    );
    
    // הוספת עמוד קבוצות
    add_submenu_page(
        'crm-dashboard',
        'קבוצות',
        'קבוצות',
        'manage_options',
        'groups-list',
        'groups_list_page'
    );
    
    // הוספת טופס קבוצה חדשה
    add_submenu_page(
        'crm-dashboard',
        'הוסף קבוצה',
        'הוסף קבוצה',
        'manage_options',
        'add-group-form',
        'add_group_form_page'
    );
}
add_action('admin_menu', 'add_dashboard_menu');

// פונקציה לעמוד הדשבורד
function crm_dashboard_page() {
    include(get_template_directory() . '/crm-dashboard.php');
}

// פונקציה לעמוד מתאמנות שסיימו
function finished_clients_page() {
    include(get_template_directory() . '/finished-clients.php');
}

// פונקציה לעמוד דוחות
function crm_reports_page() {
    include(get_template_directory() . '/crm-reports.php');
}

// פונקציה לעמוד ניהול תשלומים
function payments_management_page() {
    include(get_template_directory() . '/payments-management.php');
}

// פונקציה לעמוד קבוצות
function groups_list_page() {
    include(get_template_directory() . '/groups-list.php');
}

// פונקציה לטופס קבוצה חדשה
function add_group_form_page() {
    include(get_template_directory() . '/add-group-form.php');
}

// הוספת סטיילים מותאמים לאדמין
function add_admin_styles() {
    wp_enqueue_style('crm-admin-style', get_template_directory_uri() . '/css/admin-style.css');
}
add_action('admin_enqueue_scripts', 'add_admin_styles');

// הוספת תפריט לניהול מהיר (הוסרו פגישות)
function add_crm_toolbar($wp_admin_bar) {
    $wp_admin_bar->add_menu(array(
        'id'    => 'crm-menu',
        'title' => 'CRM מהיר',
        'href'  => admin_url('admin.php?page=crm-dashboard'),
    ));
    
    $wp_admin_bar->add_menu(array(
        'parent' => 'crm-menu',
        'id'     => 'add-client',
        'title'  => 'הוסף מתאמנת',
        'href'   => admin_url('admin.php?page=add-client-form'),
    ));
    
    $wp_admin_bar->add_menu(array(
        'parent' => 'crm-menu',
        'id'     => 'add-mentor',
        'title'  => 'הוסף מנטורית',
        'href'   => admin_url('admin.php?page=add-mentor-form'),
    ));
}
add_action('admin_bar_menu', 'add_crm_toolbar', 999);

// יצירת עמודים דרושים אוטומטית
function create_crm_pages() {
    // רשימת העמודים שצריך ליצור
    $pages_to_create = array(
        array(
            'slug' => 'login',
            'title' => 'התחברות למערכת CRM',
            'template' => 'page-login.php'
        ),
        array(
            'slug' => 'crm-dashboard',
            'title' => 'דשבורד CRM',
            'template' => 'page-crm-dashboard.php'
        ),
        array(
            'slug' => 'add-client',
            'title' => 'הוספת מתאמנת',
            'template' => 'page-add-client.php'
        ),
        array(
            'slug' => 'add-mentor',
            'title' => 'הוספת מנטור',
            'template' => 'page-add-mentor.php'
        ),
        array(
            'slug' => 'add-group',
            'title' => 'הוספת קבוצה',
            'template' => 'page-add-group.php'
        ),
        array(
            'slug' => 'dashboard',
            'title' => 'דשבורד CRM (ישן)',
            'template' => 'page-dashboard.php'
        ),
        array(
            'slug' => 'finished-clients', 
            'title' => 'מתאמנות שסיימו',
            'template' => 'page-finished-clients.php'
        )
    );
    
    foreach ($pages_to_create as $page_data) {
        // בדיקה אם העמוד כבר קיים
        $existing_page = get_page_by_path($page_data['slug']);
        
        if (!$existing_page) {
            // יצירת העמוד
            $page_id = wp_insert_post(array(
                'post_title' => $page_data['title'],
                'post_name' => $page_data['slug'],
                'post_content' => 'עמוד זה מוצג באמצעות תבנית מותאמת.',
                'post_status' => 'publish',
                'post_type' => 'page',
                'page_template' => $page_data['template']
            ));
            
            // הגדרת תבנית לעמוד
            if ($page_id && !is_wp_error($page_id)) {
                update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            }
        }
    }
}

// הפעלת הפונקציה בטעינת התמה
add_action('after_setup_theme', 'create_crm_pages');

// הפעלה מיידית לעדכון חד-פעמי
add_action('init', 'create_crm_pages_now', 1);
function create_crm_pages_now() {
    // בדוק אם כבר רצנו את העדכון - נכריח ריצה חדשה
    if (get_option('crm_pages_created_v3', false) === false) {
        create_crm_pages();
        update_option('crm_pages_created_v3', true);
        
        // נקה rewrite rules
        flush_rewrite_rules();
    }
}

// דאיגה ל-rewrite rules
function crm_flush_rewrite_rules() {
    create_clients_post_type();
    create_mentors_post_type();
    flush_rewrite_rules();
}
add_action('after_switch_theme', 'crm_flush_rewrite_rules');

// וודא שהקישורים עובדים - הפעלה מיידית
function crm_ensure_rewrite_rules() {
    // וודא שהקישורים קיימים
    $rules = get_option('rewrite_rules');
    
    if (empty($rules) || !isset($rules['clients/?$']) || !isset($rules['mentors/?$'])) {
        flush_rewrite_rules();
    }
}
add_action('init', 'crm_ensure_rewrite_rules', 99);

// הוספת capabilities למשתמשים
function add_crm_capabilities() {
    $admin_role = get_role('administrator');
    if ($admin_role) {
        $admin_role->add_cap('edit_clients');
        $admin_role->add_cap('read_clients');
        $admin_role->add_cap('delete_clients');
        $admin_role->add_cap('edit_others_clients');
        $admin_role->add_cap('publish_clients');
        $admin_role->add_cap('read_private_clients');
    }
}
add_action('admin_init', 'add_crm_capabilities');

// טעינת קובץ נתוני הדמו
require_once get_template_directory() . '/sample-data.php';

// מערכת האימות נטענת למעלה

// פונקציה לטופס הוספת מתאמנת
function add_client_form_page() {
    include(get_template_directory() . '/add-client-form.php');
}

// פונקציה לטופס הוספת מנטורית
function add_mentor_form_page() {
    include(get_template_directory() . '/add-mentor-form.php');
}

// טיפול בשליחת טופס מתאמנת חדשה
function handle_add_client_form() {
    if (!isset($_POST['client_nonce']) || !wp_verify_nonce($_POST['client_nonce'], 'add_client_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    // איסוף נתונים מהטופס
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $age = intval($_POST['age']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $referral_source = sanitize_text_field($_POST['referral_source']);
    $payment_amount = floatval($_POST['payment_amount']);
    $amount_paid = floatval($_POST['amount_paid']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $payment_date = sanitize_text_field($_POST['payment_date']);
    $start_weight = floatval($_POST['start_weight']);
    $current_weight = floatval($_POST['current_weight']);
    $target_weight = floatval($_POST['target_weight']);
    $mentor_id = intval($_POST['mentor_id']);
    $notes = sanitize_textarea_field($_POST['notes']);
    $training_type = sanitize_text_field($_POST['training_type']);
    $group_id = intval($_POST['group_id']);
    
    // יצירת הפוסט
    $post_id = wp_insert_post(array(
        'post_title' => $first_name . ' ' . $last_name,
        'post_type' => 'clients',
        'post_status' => 'publish',
        'post_content' => $notes
    ));
    
    if ($post_id && !is_wp_error($post_id)) {
        // הוספת שדות מותאמים
        update_field('first_name', $first_name, $post_id);
        update_field('last_name', $last_name, $post_id);
        update_field('phone', $phone, $post_id);
        update_field('email', $email, $post_id);
        update_field('age', $age, $post_id);
        update_field('start_date', $start_date, $post_id);
        update_field('end_date', $end_date, $post_id);
        update_field('referral_source', $referral_source, $post_id);
        update_field('payment_amount', $payment_amount, $post_id);
        update_field('amount_paid', $amount_paid, $post_id);
        if ($payment_method) update_field('payment_method', $payment_method, $post_id);
        if ($payment_date) update_field('payment_date', $payment_date, $post_id);
        update_field('start_weight', $start_weight, $post_id);
        if ($current_weight) update_field('current_weight', $current_weight, $post_id);
        update_field('target_weight', $target_weight, $post_id);
        update_field('training_type', $training_type, $post_id);
        if ($training_type === 'personal' && $mentor_id) {
            update_field('mentor', $mentor_id, $post_id);
        } elseif ($training_type === 'group' && $group_id) {
            update_field('group_id', $group_id, $post_id);
        }
        update_field('notes', $notes, $post_id);
        
        // הפניה לעמוד המתאמנת החדשה
        wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit&message=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-client-form&error=1'));
        exit;
    }
}
add_action('admin_post_add_client', 'handle_add_client_form');

// טיפול בשליחת טופס מנטורית חדשה
function handle_add_mentor_form() {
    if (!isset($_POST['mentor_nonce']) || !wp_verify_nonce($_POST['mentor_nonce'], 'add_mentor_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    // איסוף נתונים מהטופס
    $first_name = sanitize_text_field($_POST['mentor_first_name']);
    $last_name = sanitize_text_field($_POST['mentor_last_name']);
    $phone = sanitize_text_field($_POST['mentor_phone']);
    $email = sanitize_email($_POST['mentor_email']);
    $payment_percentage = floatval($_POST['payment_percentage']);
    $notes = sanitize_textarea_field($_POST['mentor_notes']);
    
    // יצירת הפוסט
    $post_id = wp_insert_post(array(
        'post_title' => $first_name . ' ' . $last_name,
        'post_type' => 'mentors',
        'post_status' => 'publish',
        'post_content' => $notes
    ));
    
    if ($post_id && !is_wp_error($post_id)) {
        // הוספת שדות מותאמים
        update_field('mentor_first_name', $first_name, $post_id);
        update_field('mentor_last_name', $last_name, $post_id);
        update_field('mentor_phone', $phone, $post_id);
        update_field('mentor_email', $email, $post_id);
        update_field('payment_percentage', $payment_percentage, $post_id);
        update_field('mentor_notes', $notes, $post_id);
        
        // הפניה לעמוד המנטורית החדשה
        wp_redirect(admin_url('post.php?post=' . $post_id . '&action=edit&message=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-mentor-form&error=1'));
        exit;
    }
}
add_action('admin_post_add_mentor', 'handle_add_mentor_form');

// טיפול בעריכת מתאמנת קיימת
function handle_edit_client_form() {
    if (!isset($_POST['client_nonce']) || !wp_verify_nonce($_POST['client_nonce'], 'edit_client_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    $client_id = intval($_POST['client_id']);
    if (!$client_id || get_post_type($client_id) !== 'clients') {
        wp_die('מתאמנת לא נמצאה');
    }
    
    // איסוף נתונים מהטופס
    $first_name = sanitize_text_field($_POST['first_name']);
    $last_name = sanitize_text_field($_POST['last_name']);
    $phone = sanitize_text_field($_POST['phone']);
    $email = sanitize_email($_POST['email']);
    $age = intval($_POST['age']);
    $start_date = sanitize_text_field($_POST['start_date']);
    $end_date = sanitize_text_field($_POST['end_date']);
    $referral_source = sanitize_text_field($_POST['referral_source']);
    $payment_amount = floatval($_POST['payment_amount']);
    $amount_paid = floatval($_POST['amount_paid']);
    $payment_method = sanitize_text_field($_POST['payment_method']);
    $payment_date = sanitize_text_field($_POST['payment_date']);
    $start_weight = floatval($_POST['start_weight']);
    $current_weight = floatval($_POST['current_weight']);
    $target_weight = floatval($_POST['target_weight']);
    $mentor_id = intval($_POST['mentor_id']);
    $notes = sanitize_textarea_field($_POST['notes']);
    $training_type = sanitize_text_field($_POST['training_type']);
    $group_id = intval($_POST['group_id']);
    
    // עדכון הפוסט
    $updated = wp_update_post(array(
        'ID' => $client_id,
        'post_title' => $first_name . ' ' . $last_name,
        'post_content' => $notes
    ));
    
    if ($updated && !is_wp_error($updated)) {
        // עדכון שדות מותאמים
        update_field('first_name', $first_name, $client_id);
        update_field('last_name', $last_name, $client_id);
        update_field('phone', $phone, $client_id);
        update_field('email', $email, $client_id);
        update_field('age', $age, $client_id);
        update_field('start_date', $start_date, $client_id);
        update_field('end_date', $end_date, $client_id);
        update_field('referral_source', $referral_source, $client_id);
        update_field('payment_amount', $payment_amount, $client_id);
        update_field('amount_paid', $amount_paid, $client_id);
        if ($payment_method) update_field('payment_method', $payment_method, $client_id);
        if ($payment_date) update_field('payment_date', $payment_date, $client_id);
        update_field('start_weight', $start_weight, $client_id);
        if ($current_weight) update_field('current_weight', $current_weight, $client_id);
        update_field('target_weight', $target_weight, $client_id);
        update_field('training_type', $training_type, $client_id);
        if ($training_type === 'personal' && $mentor_id) {
            update_field('mentor', $mentor_id, $client_id);
            update_field('group_id', '', $client_id); // נקה קבוצה אם עובר לאישי
        } elseif ($training_type === 'group' && $group_id) {
            update_field('group_id', $group_id, $client_id);
            update_field('mentor', '', $client_id); // נקה מנטורית אם עובר לקבוצתי
        }
        update_field('notes', $notes, $client_id);
        
        // הפניה חזרה לטופס עם הודעת הצלחה
        wp_redirect(admin_url('admin.php?page=add-client-form&edit=' . $client_id . '&updated=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-client-form&edit=' . $client_id . '&error=1'));
        exit;
    }
}
add_action('admin_post_edit_client', 'handle_edit_client_form');

// טיפול בעריכת מנטורית קיימת
function handle_edit_mentor_form() {
    if (!isset($_POST['mentor_nonce']) || !wp_verify_nonce($_POST['mentor_nonce'], 'edit_mentor_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    $mentor_id = intval($_POST['mentor_id']);
    if (!$mentor_id || get_post_type($mentor_id) !== 'mentors') {
        wp_die('מנטורית לא נמצאה');
    }
    
    // איסוף נתונים מהטופס
    $first_name = sanitize_text_field($_POST['mentor_first_name']);
    $last_name = sanitize_text_field($_POST['mentor_last_name']);
    $phone = sanitize_text_field($_POST['mentor_phone']);
    $email = sanitize_email($_POST['mentor_email']);
    $payment_percentage = floatval($_POST['payment_percentage']);
    $notes = sanitize_textarea_field($_POST['mentor_notes']);
    
    // עדכון הפוסט
    $updated = wp_update_post(array(
        'ID' => $mentor_id,
        'post_title' => $first_name . ' ' . $last_name,
        'post_content' => $notes
    ));
    
    if ($updated && !is_wp_error($updated)) {
        // עדכון שדות מותאמים
        update_field('mentor_first_name', $first_name, $mentor_id);
        update_field('mentor_last_name', $last_name, $mentor_id);
        update_field('mentor_phone', $phone, $mentor_id);
        update_field('mentor_email', $email, $mentor_id);
        update_field('payment_percentage', $payment_percentage, $mentor_id);
        update_field('mentor_notes', $notes, $mentor_id);
        
        // הפניה חזרה לטופס עם הודעת הצלחה
        wp_redirect(admin_url('admin.php?page=add-mentor-form&edit=' . $mentor_id . '&updated=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-mentor-form&edit=' . $mentor_id . '&error=1'));
        exit;
    }
}
add_action('admin_post_edit_mentor', 'handle_edit_mentor_form');

// טיפול בשליחת טופס קבוצה חדשה
function handle_add_group_form() {
    if (!isset($_POST['group_nonce']) || !wp_verify_nonce($_POST['group_nonce'], 'add_group_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    // איסוף נתונים מהטופס
    $group_name = sanitize_text_field($_POST['group_name']);
    $group_mentor = intval($_POST['group_mentor']);
    $group_description = sanitize_textarea_field($_POST['group_description']);
    $group_start_date = sanitize_text_field($_POST['group_start_date']);
    $group_end_date = sanitize_text_field($_POST['group_end_date']);
    $group_max_participants = intval($_POST['group_max_participants']);
    
    // יצירת הפוסט
    $post_id = wp_insert_post(array(
        'post_title' => $group_name,
        'post_type' => 'groups',
        'post_status' => 'publish',
        'post_content' => $group_description
    ));
    
    if ($post_id && !is_wp_error($post_id)) {
        // הוספת שדות מותאמים
        update_field('group_name', $group_name, $post_id);
        update_field('group_mentor', $group_mentor, $post_id);
        update_field('group_description', $group_description, $post_id);
        update_field('group_start_date', $group_start_date, $post_id);
        update_field('group_end_date', $group_end_date, $post_id);
        update_field('group_max_participants', $group_max_participants, $post_id);
        
        // הפניה לעמוד הקבוצות
        wp_redirect(admin_url('admin.php?page=groups-list&success=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-group-form&error=1'));
        exit;
    }
}
add_action('admin_post_add_group', 'handle_add_group_form');

// טיפול בעריכת קבוצה קיימת
function handle_edit_group_form() {
    if (!isset($_POST['group_nonce']) || !wp_verify_nonce($_POST['group_nonce'], 'edit_group_action')) {
        wp_die('שגיאת אבטחה');
    }
    
    if (!current_user_can('manage_options')) {
        wp_die('אין לך הרשאות מתאימות');
    }
    
    $group_id = intval($_POST['group_id']);
    if (!$group_id || get_post_type($group_id) !== 'groups') {
        wp_die('קבוצה לא נמצאה');
    }
    
    // איסוף נתונים מהטופס
    $group_name = sanitize_text_field($_POST['group_name']);
    $group_mentor = intval($_POST['group_mentor']);
    $group_description = sanitize_textarea_field($_POST['group_description']);
    $group_start_date = sanitize_text_field($_POST['group_start_date']);
    $group_end_date = sanitize_text_field($_POST['group_end_date']);
    $group_max_participants = intval($_POST['group_max_participants']);
    
    // עדכון הפוסט
    $updated = wp_update_post(array(
        'ID' => $group_id,
        'post_title' => $group_name,
        'post_content' => $group_description
    ));
    
    if ($updated && !is_wp_error($updated)) {
        // עדכון שדות מותאמים
        update_field('group_name', $group_name, $group_id);
        update_field('group_mentor', $group_mentor, $group_id);
        update_field('group_description', $group_description, $group_id);
        update_field('group_start_date', $group_start_date, $group_id);
        update_field('group_end_date', $group_end_date, $group_id);
        update_field('group_max_participants', $group_max_participants, $group_id);
        
        // הפניה חזרה לטופס עם הודעת הצלחה
        wp_redirect(admin_url('admin.php?page=add-group-form&edit=' . $group_id . '&updated=1'));
        exit;
    } else {
        wp_redirect(admin_url('admin.php?page=add-group-form&edit=' . $group_id . '&error=1'));
        exit;
    }
}
add_action('admin_post_edit_group', 'handle_edit_group_form');
