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

// התחלת session לטיפול בהודעות משתמש
if (!session_id() && !headers_sent()) {
    session_start();
}

// כפיית כניסה לכל האתר - חוץ מדפי כניסה ומאבטחת ווארדפרס
function force_login_for_entire_site() {
    // אם המשתמש כבר מחובר, לא צריך לעשות כלום
    if (is_user_logged_in()) {
        return;
    }
    
    // בדיקה אם זה Ajax או REST API
    if (wp_doing_ajax() || (defined('REST_REQUEST') && REST_REQUEST)) {
        return;
    }
    
    // בדיקה אם זה עמוד אדמין
    if (is_admin()) {
        return;
    }
    
    // רשימת דפים שמותרים בלי כניסה (עמודי אימות של ווארדפרס)
    $allowed_actions = array(
        'login',
        'lostpassword',
        'resetpass',
        'rp',
        'register',
        'checkemail'
    );
    
    // בדיקה מיוחדת לדפי אימות של ווארדפרס
    if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
        return;
    }
    
    if (strpos($_SERVER['REQUEST_URI'], 'wp-register.php') !== false) {
        return;
    }
    
    if (strpos($_SERVER['REQUEST_URI'], 'wp-signup.php') !== false) {
        return;
    }
    
    if (strpos($_SERVER['REQUEST_URI'], 'wp-activate.php') !== false) {
        return;
    }
    
    // בדיקה נוספת לפעולות אימות
    if (isset($_GET['action']) && in_array($_GET['action'], $allowed_actions)) {
        return;
    }
    
    // שמירה על ה-URL הנוכחי להפניה חזרה אחרי הכניסה
    $redirect_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    
    // הפניה לדף הכניסה
    wp_redirect(wp_login_url($redirect_url));
    exit;
}

// הוספת הפונקציה להוק template_redirect
add_action('template_redirect', 'force_login_for_entire_site');

// עיצוב עמוד הכניסה בסגנון האתר
function customize_login_page() {
    ?>
    <style type="text/css">
        body.login {
            background: #1A321D;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            height: 100vh;
            overflow: hidden;
            margin: 0;
            padding: 0;
        }

        #login {
            width: 400px;
            margin: 0 auto;
            height: 100vh;
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: center;
            padding-top: 7vh;
            box-sizing: border-box;
        }

        .login h1 a {
            background-image: none !important;
            color: #fff;
            font-size: 28px;
            font-weight: bold;
            text-decoration: none;
            text-indent: 0 !important;
            width: auto !important;
            height: auto !important;
            text-align: center;
            padding: 20px 0;
        }

        .login h1 a:before {
            content: "🌟";
            display: block;
            font-size: 48px;
            margin-bottom: 10px;
        }

        .login form {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            padding: 40px;
            margin-top: 30px;
        }

        .login form .input,
        .login input[type="text"],
        .login input[type="password"] {
            background: #f8f9fa;
            border: 2px solid #e9ecef;
            border-radius: 8px;
            font-size: 16px;
            padding: 12px 15px;
            transition: all 0.3s ease;
            text-align: right;
        }

        .login form .input:focus,
        .login input[type="text"]:focus,
        .login input[type="password"]:focus {
            border-color: rgb(106, 158, 141);
            box-shadow: 0 0 0 3px rgba(106, 158, 141, 0.2);
            outline: none;
        }

        .login label {
            color: #495057;
            font-weight: 500;
            margin-bottom: 8px;
            text-align: right;
            display: block;
        }

        .login .button-primary {
            background: rgb(106, 158, 141);
            border: none;
            border-radius: 8px;
            color: #fff;
            font-size: 16px;
            font-weight: 600;
            padding: 12px 30px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 20px;
        }

        .login .button-primary:hover {
            background: rgba(106, 158, 141, 0.9);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(106, 158, 141, 0.3);
        }

        .login .forgetmenot {
            text-align: right;
            margin: 20px 0;
        }

        .login .forgetmenot label {
            font-size: 14px;
            color: #6c757d;
        }

        .login #nav,
        .login #backtoblog {
            text-align: center;
            margin-top: 20px;
        }

        .login #nav a,
        .login #backtoblog a {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .login #nav a:hover,
        .login #backtoblog a:hover {
            color: #fff;
        }

        .login .message,
        .login .notice {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 8px;
            color: #495057;
            text-align: center;
            padding: 15px;
            margin-bottom: 20px;
        }

        .login .message.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
        }

        .login-title {
            color: #fff;
            text-align: center;
            font-size: 24px;
            margin-bottom: 10px;
            font-weight: 300;
        }

        .login-subtitle {
            color: rgba(255, 255, 255, 0.8);
            text-align: center;
            font-size: 16px;
            margin-bottom: 30px;
        }
    </style>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // הוספת כותרת מותאמת
            var loginHeader = document.querySelector('#login h1 a');
            if (loginHeader) {
                loginHeader.innerHTML = 'CRM תזונה - מרים קרישבסקי';
            }
            
            // הוספת כותרות נוספות
            var form = document.querySelector('#loginform');
            if (form) {
                var titleDiv = document.createElement('div');
                titleDiv.className = 'login-title';
                titleDiv.innerHTML = 'ברוכים הבאים למערכת';
                
                var subtitleDiv = document.createElement('div');
                subtitleDiv.className = 'login-subtitle';
                subtitleDiv.innerHTML = 'אנא הזינו את פרטי הכניסה שלכם';
                
                form.parentNode.insertBefore(titleDiv, form);
                form.parentNode.insertBefore(subtitleDiv, form);
            }
        });
    </script>
    <?php
}
add_action('login_head', 'customize_login_page');

// שינוי הלוגו בעמוד הכניסה להפנות לבית
function change_login_logo_url() {
    return home_url();
}
add_filter('login_headerurl', 'change_login_logo_url');

// שינוי הטקסט של הלוגו בעמוד הכניסה
function change_login_logo_text() {
    return 'CRM תזונה - מרים קרישבסקי';
}
add_filter('login_headertext', 'change_login_logo_text');

// הוספת הודעת ברוכים הבאים אחרי כניסה מוצלחת
function custom_login_redirect($redirect_to, $request, $user) {
    // אם יש בקשה להפניה לדף מסוים, נכבד את זה
    if (!empty($request)) {
        return $request;
    }
    
    // אחרת נפנה לעמוד הבית
    return home_url();
}
add_filter('login_redirect', 'custom_login_redirect', 10, 3);

// הוספת הפונקציה להוק template_redirect
add_action('template_redirect', 'force_login_for_entire_site');

// הפעלה אוטומטית של תוסף ACF אם הוא קיים אבל לא פעיל
function auto_activate_acf() {
    if (!function_exists('is_plugin_active')) {
        include_once(ABSPATH . 'wp-admin/includes/plugin.php');
    }
    
    $plugin_path = 'advanced-custom-fields/acf.php';
    
    // בדיקה אם התוסף קיים אבל לא פעיל
    if (file_exists(WP_PLUGIN_DIR . '/' . $plugin_path) && !is_plugin_active($plugin_path)) {
        // הפעלה בטוחה של התוסף
        $result = activate_plugin($plugin_path, '', false, true);
        
        // בדיקה אם ההפעלה הצליחה
        if (!is_wp_error($result)) {
            // הוספת הודעה לאדמין
            add_action('admin_notices', function() {
                echo '<div class="notice notice-success is-dismissible" style="direction: rtl;">
                    <p><strong>✅ תוסף Advanced Custom Fields הופעל אוטומטית!</strong></p>
                    <p>כעת מערכת ה-CRM מוכנה לשימוש. תוכל ליצור נתוני דמו.</p>
                </div>';
            });
        }
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

// התאמת כמות הפוסטים בדפי archive של clients
function modify_clients_query($query) {
    if (!is_admin() && $query->is_main_query()) {
        if (is_post_type_archive('clients')) {
            $query->set('posts_per_page', -1); // הצגת כל המתאמנות
            
            // הוספת meta_query לסינון מתאמנות פוטנציאליות
            $existing_meta_query = $query->get('meta_query');
            if (!is_array($existing_meta_query)) {
                $existing_meta_query = array();
            }
            
            // הוספת תנאי לא לכלול מתאמנות פוטנציאליות
            $existing_meta_query[] = array(
                'relation' => 'OR',
                array(
                    'key' => 'is_contact_lead',
                    'value' => false,
                    'compare' => '='
                ),
                array(
                    'key' => 'is_contact_lead',
                    'value' => 'false',
                    'compare' => '='
                ),
                array(
                    'key' => 'is_contact_lead',
                    'value' => '',
                    'compare' => '='
                ),
                array(
                    'key' => 'is_contact_lead',
                    'compare' => 'NOT EXISTS'
                )
            );
            
            $query->set('meta_query', $existing_meta_query);
        }
    }
}
add_action('pre_get_posts', 'modify_clients_query');

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
    if (function_exists('acf_add_local_field_group')) {
        acf_add_local_field_group(array(
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
                // שדה לזיהוי מתאמנת פוטנציאלית למעקב
                array(
                    'key' => 'field_is_contact_lead',
                    'label' => 'מתאמנת פוטנציאלית',
                    'name' => 'is_contact_lead',
                    'type' => 'true_false',
                    'instructions' => 'האם זו מתאמנת פוטנציאלית למעקב (לא הייתה אצלך בטיפול)?',
                    'default_value' => 0,
                    'ui' => 1,
                    'ui_on_text' => 'מתאמנת פוטנציאלית',
                    'ui_off_text' => 'מתאמנת רגילה',
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
        acf_add_local_field_group(array(
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
            'slug' => 'dashboard',
            'title' => 'דשבורד CRM',
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

// הוספת סקריפטים ועיצובים לפופ-אפ מתאמנות
function enqueue_client_modal_assets() {
    // טעינה בכל הפורנט (פרט לעמודי הבלוגים)
    if (!is_admin()) {
        wp_enqueue_style('client-modal-css', 
            get_template_directory_uri() . '/assets/css/client-modal.css',
            array(), 
            '1.0.0'
        );
        
        wp_enqueue_script('client-modal-js', 
            get_template_directory_uri() . '/assets/js/client-modal.js',
            array(), 
            '1.0.0', 
            true
        );
        
        // הוספת משתנה ajaxurl לג'אווהסקריפט
        wp_localize_script('client-modal-js', 'ajax_object', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('client_action')
        ));
    }
}
add_action('wp_enqueue_scripts', 'enqueue_client_modal_assets');

// הוספת הפופ-אפ לכל עמוד
function include_client_modal() {
    if (!is_admin()) {
        include get_template_directory() . '/client-popup-modal.php';
    }
}
add_action('wp_footer', 'include_client_modal');

// טיפול ב-AJAX לקבלת נתוני מתאמנת
function get_client_data_ajax() {
    check_ajax_referer('client_action', 'nonce');
    
    $client_id = intval($_POST['client_id']);
    
    if (!$client_id) {
        wp_send_json_error(array('message' => 'מזהה מתאמנת לא חוקי'));
    }
    
    $client_post = get_post($client_id);
    if (!$client_post || $client_post->post_type !== 'clients') {
        wp_send_json_error(array('message' => 'מתאמנת לא נמצאה'));
    }
    
    $client_data = array(
        'first_name' => get_field('first_name', $client_id),
        'last_name' => get_field('last_name', $client_id),
        'phone' => get_field('phone', $client_id),
        'email' => get_field('email', $client_id),
        'age' => get_field('age', $client_id),
        'start_date' => get_field('start_date', $client_id),
        'end_date' => get_field('end_date', $client_id),
        'referral_source' => get_field('referral_source', $client_id),
        'payment_amount' => get_field('payment_amount', $client_id),
        'amount_paid' => get_field('amount_paid', $client_id),
        'payment_method' => get_field('payment_method', $client_id),
        'payment_date' => get_field('payment_date', $client_id),
        'start_weight' => get_field('start_weight', $client_id),
        'current_weight' => get_field('current_weight', $client_id),
        'target_weight' => get_field('target_weight', $client_id),
        'training_type' => get_field('training_type', $client_id),
        'notes' => get_field('notes', $client_id)
    );
    
    // טיפול במנטורית
    $mentor = get_field('mentor', $client_id);
    if (is_object($mentor)) {
        $client_data['mentor_id'] = $mentor->ID;
    } else {
        $client_data['mentor_id'] = $mentor;
    }
    
    // טיפול בקבוצה
    $group = get_field('group_id', $client_id);
    if (is_object($group)) {
        $client_data['group_id'] = $group->ID;
    } else {
        $client_data['group_id'] = $group;
    }
    
    wp_send_json_success($client_data);
}
add_action('wp_ajax_get_client_data_ajax', 'get_client_data_ajax');
add_action('wp_ajax_nopriv_get_client_data_ajax', 'get_client_data_ajax');

// טיפול ב-AJAX להוספת מתאמנת
function add_client_ajax() {
    check_ajax_referer('client_action', 'client_nonce');
    
    // קבלת הנתונים מהטופס
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
    $training_type = sanitize_text_field($_POST['training_type']);
    $mentor_id = intval($_POST['mentor_id']);
    $group_id = intval($_POST['group_id']);
    $notes = sanitize_textarea_field($_POST['notes']);
    
    // בדיקת שדות חובה
    if (empty($first_name) || empty($last_name) || empty($phone) || empty($start_date) || empty($end_date) || empty($referral_source)) {
        wp_send_json_error(array('message' => 'אנא מלא את כל השדות החובה'));
    }
    
    // יצירת הפוסט
    $post_data = array(
        'post_title' => $first_name . ' ' . $last_name,
        'post_type' => 'clients',
        'post_status' => 'publish'
    );
    
    $post_id = wp_insert_post($post_data);
    
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
        
        wp_send_json_success(array('message' => 'המתאמנת נוספה בהצלחה!', 'post_id' => $post_id));
    } else {
        wp_send_json_error(array('message' => 'שגיאה ביצירת המתאמנת'));
    }
}
add_action('wp_ajax_add_client_ajax', 'add_client_ajax');
add_action('wp_ajax_nopriv_add_client_ajax', 'add_client_ajax');

// טיפול ב-AJAX לעריכת מתאמנת
function edit_client_ajax() {
    check_ajax_referer('client_action', 'client_nonce');
    
    $client_id = intval($_POST['client_id']);
    
    if (!$client_id) {
        wp_send_json_error(array('message' => 'מזהה מתאמנת לא חוקי'));
    }
    
    // קבלת הנתונים מהטופס
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
    $training_type = sanitize_text_field($_POST['training_type']);
    $mentor_id = intval($_POST['mentor_id']);
    $group_id = intval($_POST['group_id']);
    $notes = sanitize_textarea_field($_POST['notes']);
    
    // בדיקת שדות חובה
    if (empty($first_name) || empty($last_name) || empty($phone) || empty($start_date) || empty($end_date) || empty($referral_source)) {
        wp_send_json_error(array('message' => 'אנא מלא את כל השדות החובה'));
    }
    
    // עדכון הפוסט
    $post_data = array(
        'ID' => $client_id,
        'post_title' => $first_name . ' ' . $last_name
    );
    
    $updated = wp_update_post($post_data);
    
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
        
        // ניקוי שדות מנטורית/קבוצה
        update_field('mentor', null, $client_id);
        update_field('group_id', null, $client_id);
        
        if ($training_type === 'personal' && $mentor_id) {
            update_field('mentor', $mentor_id, $client_id);
        } elseif ($training_type === 'group' && $group_id) {
            update_field('group_id', $group_id, $client_id);
        }
        update_field('notes', $notes, $client_id);
        
        wp_send_json_success(array('message' => 'המתאמנת עודכנה בהצלחה!'));
    } else {
        wp_send_json_error(array('message' => 'שגיאה בעדכון המתאמנת'));
    }
}
add_action('wp_ajax_edit_client_ajax', 'edit_client_ajax');
add_action('wp_ajax_nopriv_edit_client_ajax', 'edit_client_ajax');

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
    if (!isset($_POST['add_client_nonce']) || !wp_verify_nonce($_POST['add_client_nonce'], 'add_client_action')) {
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
    if (!isset($_POST['add_mentor_nonce']) || !wp_verify_nonce($_POST['add_mentor_nonce'], 'add_mentor_action')) {
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
    if (!isset($_POST['edit_client_nonce']) || !wp_verify_nonce($_POST['edit_client_nonce'], 'edit_client_action')) {
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
    if (!isset($_POST['edit_mentor_nonce']) || !wp_verify_nonce($_POST['edit_mentor_nonce'], 'edit_mentor_action')) {
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
    if (!isset($_POST['add_group_nonce']) || !wp_verify_nonce($_POST['add_group_nonce'], 'add_group_action')) {
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
    if (!isset($_POST['edit_group_nonce']) || !wp_verify_nonce($_POST['edit_group_nonce'], 'edit_group_action')) {
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



// תרגום טקסטים בעמוד הכניסה לעברית
function translate_login_page_texts() {
    ?>
    <script type="text/javascript">
        document.addEventListener('DOMContentLoaded', function() {
            // תרגום שדות הכניסה
            var usernameLabel = document.querySelector('label[for="user_login"]');
            if (usernameLabel) {
                usernameLabel.innerHTML = 'שם משתמש או אימייל';
            }
            
            var passwordLabel = document.querySelector('label[for="user_pass"]');
            if (passwordLabel) {
                passwordLabel.innerHTML = 'סיסמה';
            }

            var usernameInput = document.querySelector('#user_login');
            if (usernameInput) {
                usernameInput.setAttribute('placeholder', 'הזן שם משתמש או אימייל');
            }

            var passwordInput = document.querySelector('#user_pass');
            if (passwordInput) {
                passwordInput.setAttribute('placeholder', 'הזן סיסמה');
            }
            
            // תרגום כפתור הכניסה
            var submitButton = document.querySelector('#wp-submit');
            if (submitButton) {
                submitButton.value = '🔑 כניסה למערכת';
            }
            
            // תרגום "זכור אותי"
            var rememberLabel = document.querySelector('.forgetmenot label');
            if (rememberLabel) {
                rememberLabel.innerHTML = rememberLabel.innerHTML.replace('Remember Me', 'זכור אותי');
            }
            
            // תרגום קישורים
            var lostPasswordLink = document.querySelector('#nav a');
            if (lostPasswordLink && lostPasswordLink.href.includes('wp-login.php?action=lostpassword')) {
                lostPasswordLink.innerHTML = '🔐 שכחת סיסמה?';
            }
            
            var backToBlogLink = document.querySelector('#backtoblog a');
            if (backToBlogLink) {
                backToBlogLink.innerHTML = '← חזרה לאתר';
            }
        });
    </script>
    <?php
}
add_action('login_footer', 'translate_login_page_texts');

// הוספת סגנונות נוספים לעמוד הכניסה
function additional_login_styles() {
    ?>
    <style type="text/css">
        /* אנימציות נוספות */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login form {
            animation: fadeInUp 0.6s ease-out;
        }

        .login h1 a {
            animation: fadeInUp 0.4s ease-out;
        }

        /* שיפור עיצוב השדות */
        .login form .input::placeholder,
        .login input[type="text"]::placeholder,
        .login input[type="password"]::placeholder {
            color: #6c757d;
            font-style: italic;
        }

        /* עיצוב משופר לכפתור */
        .login .button-primary:active {
            transform: translateY(0);
        }

        /* עיצוב לשגיאות */
        .login .login-error-list,
        .login .error {
            background: rgba(220, 53, 69, 0.1);
            border: 1px solid rgba(220, 53, 69, 0.3);
            color: #dc3545;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* עיצוב לא טוב כלום */
        .login .success {
            background: rgba(40, 167, 69, 0.1);
            border: 1px solid rgba(40, 167, 69, 0.3);
            color: #28a745;
        }

        /* שיפור למובייל */
        @media (max-width: 480px) {
            #login {
                width: 90%;
                margin: 0 auto;
                height: 100vh;
                display: flex;
                flex-direction: column;
                justify-content: flex-start;
                align-items: center;
                padding-top: 8vh;
                box-sizing: border-box;
            }
            
            .login form {
                padding: 20px;
            }
            
            .login h1 a {
                font-size: 20px;
            }
            
            .login-title {
                font-size: 18px;
            }
            
            .login-subtitle {
                font-size: 14px;
            }
        }
    </style>
    <?php
}
add_action('login_head', 'additional_login_styles', 20);

// הפניה לעמוד הכניסה אחרי יציאה
function custom_logout_redirect() {
    wp_redirect(wp_login_url());
    exit;
}
add_action('wp_logout', 'custom_logout_redirect');

// הודעה מותאמת אחרי יציאה מוצלחת
function custom_logout_message($message) {
    if (isset($_GET['loggedout']) && $_GET['loggedout'] == 'true') {
        $message = '<div class="message">יצאת בהצלחה מהמערכת. תודה על השימוש! 👋</div>';
    }
    return $message;
}
add_filter('login_message', 'custom_logout_message');

// הגבלת גישה לאזור האדמין (אופציונלי - אם רוצים להגביל)
function restrict_admin_access() {
    // אם המשתמש לא מחובר, לא צריך לבדוק
    if (!is_user_logged_in()) {
        return;
    }
    
    // אם זה לא אזור אדמין, לא צריך לבדוק
    if (!is_admin()) {
        return;
    }
    
    // אם זה Ajax, מותר
    if (wp_doing_ajax()) {
        return;
    }
    
    // אפשר למשתמשים עם יכולות מינימליות לגשת לפרופיל שלהם
    if (current_user_can('read')) {
        // אבל רק לדפים מסוימים
        $allowed_admin_pages = array(
            'profile.php',
            'user-edit.php',
            'admin-ajax.php'
        );
        
        $current_page = basename($_SERVER['PHP_SELF']);
        
        if (!in_array($current_page, $allowed_admin_pages) && !current_user_can('manage_options')) {
            // הפניה לעמוד הראשי
            wp_redirect(home_url());
            exit;
        }
    }
}
// מוגיב כרגע - אפשר להפעיל אם רוצים הגבלה נוספת
// add_action('admin_init', 'restrict_admin_access');



// ===== פונקציות AJAX למנטוריות =====

// טיפול ב-AJAX לקבלת נתוני מנטורית
function get_mentor_data_ajax() {
    check_ajax_referer('client_action', 'nonce');
    
    $mentor_id = intval($_POST['mentor_id']);
    
    if (!$mentor_id) {
        wp_send_json_error(array('message' => 'מזהה מנטורית לא חוקי'));
    }
    
    $mentor_post = get_post($mentor_id);
    if (!$mentor_post || $mentor_post->post_type !== 'mentors') {
        wp_send_json_error(array('message' => 'מנטורית לא נמצאה'));
    }
    
    $mentor_data = array(
        'mentor_first_name' => get_field('mentor_first_name', $mentor_id),
        'mentor_last_name' => get_field('mentor_last_name', $mentor_id),
        'mentor_phone' => get_field('mentor_phone', $mentor_id),
        'mentor_email' => get_field('mentor_email', $mentor_id),
        'payment_percentage' => get_field('payment_percentage', $mentor_id),
        'mentor_notes' => get_field('mentor_notes', $mentor_id)
    );
    
    wp_send_json_success($mentor_data);
}
add_action('wp_ajax_get_mentor_data_ajax', 'get_mentor_data_ajax');
add_action('wp_ajax_nopriv_get_mentor_data_ajax', 'get_mentor_data_ajax');

// טיפול ב-AJAX להוספת מנטורית
function add_mentor_ajax() {
    check_ajax_referer('client_action', 'mentor_nonce');
    
    // קבלת הנתונים מהטופס
    $first_name = sanitize_text_field($_POST['mentor_first_name']);
    $last_name = sanitize_text_field($_POST['mentor_last_name']);
    $phone = sanitize_text_field($_POST['mentor_phone']);
    $email = sanitize_email($_POST['mentor_email']);
    $payment_percentage = floatval($_POST['payment_percentage']);
    $notes = sanitize_textarea_field($_POST['mentor_notes']);
    
    // בדיקת שדות חובה
    if (empty($first_name) || empty($last_name) || empty($phone)) {
        wp_send_json_error(array('message' => 'אנא מלא את כל השדות החובה'));
    }
    
    // יצירת הפוסט
    $post_data = array(
        'post_title' => $first_name . ' ' . $last_name,
        'post_type' => 'mentors',
        'post_status' => 'publish'
    );
    
    $post_id = wp_insert_post($post_data);
    
    if ($post_id && !is_wp_error($post_id)) {
        // הוספת שדות מותאמים
        update_field('mentor_first_name', $first_name, $post_id);
        update_field('mentor_last_name', $last_name, $post_id);
        update_field('mentor_phone', $phone, $post_id);
        if ($email) update_field('mentor_email', $email, $post_id);
        update_field('payment_percentage', $payment_percentage ?: 40, $post_id);
        if ($notes) update_field('mentor_notes', $notes, $post_id);
        
        wp_send_json_success(array('message' => 'המנטורית נוספה בהצלחה!', 'post_id' => $post_id));
    } else {
        wp_send_json_error(array('message' => 'שגיאה ביצירת המנטורית'));
    }
}
add_action('wp_ajax_add_mentor_ajax', 'add_mentor_ajax');
add_action('wp_ajax_nopriv_add_mentor_ajax', 'add_mentor_ajax');

// טיפול ב-AJAX לעריכת מנטורית
function edit_mentor_ajax() {
    check_ajax_referer('client_action', 'mentor_nonce');
    
    $mentor_id = intval($_POST['mentor_id']);
    
    if (!$mentor_id) {
        wp_send_json_error(array('message' => 'מזהה מנטורית לא חוקי'));
    }
    
    // קבלת הנתונים מהטופס
    $first_name = sanitize_text_field($_POST['mentor_first_name']);
    $last_name = sanitize_text_field($_POST['mentor_last_name']);
    $phone = sanitize_text_field($_POST['mentor_phone']);
    $email = sanitize_email($_POST['mentor_email']);
    $payment_percentage = floatval($_POST['payment_percentage']);
    $notes = sanitize_textarea_field($_POST['mentor_notes']);
    
    // בדיקת שדות חובה
    if (empty($first_name) || empty($last_name) || empty($phone)) {
        wp_send_json_error(array('message' => 'אנא מלא את כל השדות החובה'));
    }
    
    // עדכון הפוסט
    $post_data = array(
        'ID' => $mentor_id,
        'post_title' => $first_name . ' ' . $last_name
    );
    
    $result = wp_update_post($post_data);
    
    if ($result && !is_wp_error($result)) {
        // עדכון שדות מותאמים
        update_field('mentor_first_name', $first_name, $mentor_id);
        update_field('mentor_last_name', $last_name, $mentor_id);
        update_field('mentor_phone', $phone, $mentor_id);
        update_field('mentor_email', $email, $mentor_id);
        update_field('payment_percentage', $payment_percentage ?: 40, $mentor_id);
        update_field('mentor_notes', $notes, $mentor_id);
        
        wp_send_json_success(array('message' => 'המנטורית עודכנה בהצלחה!'));
    } else {
        wp_send_json_error(array('message' => 'שגיאה בעדכון המנטורית'));
    }
}
add_action('wp_ajax_edit_mentor_ajax', 'edit_mentor_ajax');
add_action('wp_ajax_nopriv_edit_mentor_ajax', 'edit_mentor_ajax');

// AJAX handler למחיקת מתאמנת
function delete_client_ajax() {
    // בדיקת nonce לאבטחה
    if (!wp_verify_nonce($_POST['nonce'], 'delete_client_nonce')) {
        wp_die('אין הרשאה לבצע פעולה זו', 'שגיאת אבטחה', array('response' => 403));
    }
    
    // בדיקה שהמשתמש מחובר
    if (!is_user_logged_in()) {
        wp_send_json_error('יש להתחבר כדי לבצע פעולה זו');
        return;
    }
    
    // קבלת מזהה המתאמנת
    $client_id = intval($_POST['client_id']);
    
    if (!$client_id) {
        wp_send_json_error('מזהה מתאמנת לא תקין');
        return;
    }
    
    // בדיקה שהפוסט קיים ומסוג clients
    $post = get_post($client_id);
    if (!$post || $post->post_type !== 'clients') {
        wp_send_json_error('המתאמנת לא נמצאה');
        return;
    }
    
    // שמירת שם המתאמנת לפני המחיקה
    $first_name = get_field('first_name', $client_id);
    $last_name = get_field('last_name', $client_id);
    $client_name = trim($first_name . ' ' . $last_name);
    
    // ביצוע המחיקה
    $deleted = wp_delete_post($client_id, true); // true = מחיקה לצמיתות (לא לפח)
    
    if ($deleted) {
        // מחיקה מוצלחת
        wp_send_json_success(array(
            'message' => "המתאמנת {$client_name} נמחקה בהצלחה",
            'client_id' => $client_id,
            'client_name' => $client_name
        ));
    } else {
        // כשל במחיקה
        wp_send_json_error('אירעה שגיאה במהלך המחיקה');
    }
}

add_action('wp_ajax_delete_client', 'delete_client_ajax');
add_action('wp_ajax_nopriv_delete_client', 'delete_client_ajax');

// AJAX handler למחיקת קבוצה
function delete_group_ajax() {
    // בדיקת nonce לאבטחה
    if (!wp_verify_nonce($_POST['nonce'], 'delete_group_nonce')) {
        wp_die('אין הרשאה לבצע פעולה זו', 'שגיאת אבטחה', array('response' => 403));
    }
    
    // בדיקה שהמשתמש מחובר ויש לו הרשאות
    if (!is_user_logged_in() || !current_user_can('manage_options')) {
        wp_send_json_error('יש להתחבר עם הרשאות מנהל כדי לבצע פעולה זו');
        return;
    }
    
    // קבלת מזהה הקבוצה
    $group_id = intval($_POST['group_id']);
    
    if (!$group_id) {
        wp_send_json_error('מזהה קבוצה לא תקין');
        return;
    }
    
    // בדיקה שהפוסט קיים ומסוג groups
    $post = get_post($group_id);
    if (!$post || $post->post_type !== 'groups') {
        wp_send_json_error('הקבוצה לא נמצאה');
        return;
    }
    
    // שמירת שם הקבוצה לפני המחיקה
    $group_name = get_field('group_name', $group_id);
    if (!$group_name) {
        $group_name = $post->post_title;
    }
    
    // חיפוש משתתפות בקבוצה וההעברה שלהן לליווי אישי
    $participants = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'meta_query' => array(
            array(
                'key' => 'group_id',
                'value' => $group_id,
                'compare' => '='
            )
        )
    ));
    
    $participants_updated = 0;
    foreach ($participants as $participant) {
        // העברת המשתתפת לליווי אישי
        update_field('training_type', 'personal', $participant->ID);
        update_field('group_id', '', $participant->ID); // מחיקת הקישור לקבוצה
        $participants_updated++;
    }
    
    // ביצוע המחיקה
    $deleted = wp_delete_post($group_id, true); // true = מחיקה לצמיתות (לא לפח)
    
    if ($deleted) {
        // מחיקה מוצלחת
        wp_send_json_success(array(
            'message' => "הקבוצה {$group_name} נמחקה בהצלחה",
            'group_id' => $group_id,
            'group_name' => $group_name,
            'participants_updated' => $participants_updated
        ));
    } else {
        // כשל במחיקה
        wp_send_json_error('אירעה שגיאה במהלך המחיקה');
    }
}

add_action('wp_ajax_delete_group', 'delete_group_ajax');
add_action('wp_ajax_nopriv_delete_group', 'delete_group_ajax');

/**
 * ACF JSON Sync - הפעלת סנכרון שדות ACF באמצעות קבצי JSON
 * זה יאפשר לשמור שדות ACF בקבצי JSON במקום רק במסד הנתונים
 * ויסנכרן אותם בין הסביבה המקומית לפרודקשן
 */

// הגדרת תיקיית save point לשמירת קבצי JSON
add_filter('acf/settings/save_json', 'my_acf_json_save_point');
function my_acf_json_save_point( $path ) {
    // החזרת הנתיב לתיקיית acf-json בתמה
    $path = get_stylesheet_directory() . '/acf-json';
    return $path;
}

// הגדרת תיקיית load point לטעינת קבצי JSON
add_filter('acf/settings/load_json', 'my_acf_json_load_point');
function my_acf_json_load_point( $paths ) {
    // הסרת הנתיב הברירת מחדל
    unset($paths[0]);
    
    // הוספת הנתיב לתיקיית acf-json בתמה
    $paths[] = get_stylesheet_directory() . '/acf-json';
    
    return $paths;
}

// פונקציה לוודא שתיקיית acf-json קיימת
function ensure_acf_json_directory() {
    $json_dir = get_stylesheet_directory() . '/acf-json';
    if (!file_exists($json_dir)) {
        wp_mkdir_p($json_dir);
    }
}

// הפעלת הפונקציה בעת טעינת התמה
add_action('after_setup_theme', 'ensure_acf_json_directory');

/**
 * הוספת הודעת מנהל עבור Sync של שדות ACF
 */
add_action('admin_notices', 'acf_json_sync_notice');
function acf_json_sync_notice() {
    if (!current_user_can('manage_options')) {
        return;
    }
    
    // בדיקה אם יש שדות שצריכים sync
    $local_json_files = glob(get_stylesheet_directory() . '/acf-json/*.json');
    if ($local_json_files && is_admin()) {
        echo '<div class="notice notice-info is-dismissible">';
        echo '<p><strong>ACF JSON Sync:</strong> שדות ACF יישמרו כעת כקבצי JSON ויסונכרנו בין הסביבות. ';
        echo 'כשתעדכן שדה, לך ל-<a href="' . admin_url('edit.php?post_type=acf-field-group') . '">Custom Fields > Field Groups</a> ובדוק אם יש שדות שצריכים Sync.</p>';
        echo '</div>';
    }
}




