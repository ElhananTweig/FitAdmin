<?php
/**
 * מערכת אימות מותאמת לאתר CRM
 * מטפלת בכניסה ויציאה של משתמשים ובהגנה על דפים
 */

if (!defined('ABSPATH')) {
    exit;
}

class CRM_Auth_Handler {
    
    public function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_styles'));
        add_filter('template_redirect', array($this, 'check_auth_redirect'));
        add_action('wp_ajax_crm_login', array($this, 'handle_ajax_login'));
        add_action('wp_ajax_nopriv_crm_login', array($this, 'handle_ajax_login'));
        add_action('wp_ajax_crm_logout', array($this, 'handle_ajax_logout'));
    }
    
    public function init() {
        // טיפול בכניסה דרך POST
        if (isset($_POST['crm_login_action'])) {
            $this->handle_login();
        }
        
        // טיפול ביציאה דרך GET
        if (isset($_GET['crm_logout'])) {
            $this->handle_logout();
        }
    }
    
    /**
     * בדיקה אם המשתמש מחובר למערכת
     */
    public function is_user_logged_in() {
        return is_user_logged_in() && user_can(get_current_user_id(), 'manage_options');
    }
    
    /**
     * בדיקה אם הדף דורש אימות
     */
    public function requires_auth($url = null) {
        if (!$url) {
            $url = $_SERVER['REQUEST_URI'];
        }
        
        // רשימת דפים המוגנים
        $protected_pages = array(
            '/clients/',
            '/mentors/', 
            '/groups/',
            '/crm-dashboard/',
            '/reports/',
            '/payments/',
            '/add-client/',
            '/add-mentor/',
            '/add-group/'
        );
        
        foreach ($protected_pages as $protected_page) {
            if (strpos($url, $protected_page) !== false) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * הפניה אוטומטית לדף כניסה אם נדרש
     */
    public function check_auth_redirect() {
        if ($this->requires_auth() && !$this->is_user_logged_in()) {
            $current_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
            $login_url = home_url('/login/') . '?redirect_to=' . urlencode($current_url);
            wp_redirect($login_url);
            exit;
        }
    }
    
    /**
     * טיפול בכניסה למערכת
     */
    public function handle_login() {
        if (!wp_verify_nonce($_POST['crm_login_nonce'], 'crm_login_action')) {
            wp_die('שגיאת אבטחה');
        }
        
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        $redirect_to = isset($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url('/crm-dashboard/');
        
        $credentials = array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember
        );
        
        $user = wp_signon($credentials, false);
        
        if (is_wp_error($user)) {
            $error_message = $user->get_error_message();
            wp_redirect(home_url('/login/') . '?error=' . urlencode($error_message) . '&redirect_to=' . urlencode($redirect_to));
            exit;
        }
        
        // בדיקה שהמשתמש הוא מנהל
        if (!user_can($user->ID, 'manage_options')) {
            wp_logout();
            wp_redirect(home_url('/login/') . '?error=' . urlencode('אין לך הרשאות מתאימות לגישה למערכת') . '&redirect_to=' . urlencode($redirect_to));
            exit;
        }
        
        wp_redirect($redirect_to);
        exit;
    }
    
    /**
     * טיפול ביציאה מהמערכת
     */
    public function handle_logout() {
        wp_logout();
        wp_redirect(home_url('/login/') . '?logged_out=1');
        exit;
    }
    
    /**
     * טיפול בכניסה באמצעות AJAX
     */
    public function handle_ajax_login() {
        if (!wp_verify_nonce($_POST['nonce'], 'crm_login_action')) {
            wp_send_json_error('שגיאת אבטחה');
        }
        
        $username = sanitize_user($_POST['username']);
        $password = $_POST['password'];
        $remember = isset($_POST['remember']);
        
        $credentials = array(
            'user_login' => $username,
            'user_password' => $password,
            'remember' => $remember
        );
        
        $user = wp_signon($credentials, false);
        
        if (is_wp_error($user)) {
            wp_send_json_error($user->get_error_message());
        }
        
        // בדיקה שהמשתמש הוא מנהל
        if (!user_can($user->ID, 'manage_options')) {
            wp_logout();
            wp_send_json_error('אין לך הרשאות מתאימות לגישה למערכת');
        }
        
        wp_send_json_success(array(
            'redirect' => isset($_POST['redirect_to']) ? $_POST['redirect_to'] : home_url('/crm-dashboard/')
        ));
    }
    
    /**
     * טיפול ביציאה באמצעות AJAX
     */
    public function handle_ajax_logout() {
        wp_logout();
        wp_send_json_success(array(
            'redirect' => home_url('/login/')
        ));
    }
    
    /**
     * הוספת סטיילים לדפי האימות
     */
    public function enqueue_styles() {
        if (is_page('login') || $this->requires_auth()) {
            wp_enqueue_style('crm-auth-style', get_template_directory_uri() . '/css/crm-auth.css', array(), '1.0.0');
            wp_enqueue_script('crm-auth-script', get_template_directory_uri() . '/js/crm-auth.js', array('jquery'), '1.0.0', true);
            
            wp_localize_script('crm-auth-script', 'crm_auth_ajax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('crm_login_action')
            ));
        }
    }
    
    /**
     * קבלת פרטי המשתמש המחובר
     */
    public function get_current_user_info() {
        if (!$this->is_user_logged_in()) {
            return false;
        }
        
        $user = wp_get_current_user();
        return array(
            'ID' => $user->ID,
            'display_name' => $user->display_name,
            'user_email' => $user->user_email,
            'user_login' => $user->user_login
        );
    }
    
    /**
     * יצירת תפריט משתמש
     */
    public function get_user_menu() {
        if (!$this->is_user_logged_in()) {
            return '';
        }
        
        $user_info = $this->get_current_user_info();
        
        ob_start();
        ?>
        <div class="crm-user-menu">
            <div class="user-info">
                <span class="user-name">שלום, <?php echo esc_html($user_info['display_name']); ?></span>
                <div class="user-actions">
                    <a href="<?php echo home_url('/crm-dashboard/'); ?>" class="dashboard-link">דשבורד</a>
                    <a href="<?php echo home_url('/?crm_logout=1'); ?>" class="logout-link">יציאה</a>
                </div>
            </div>
        </div>
        <?php
        return ob_get_clean();
    }
}

// אתחול המחלקה
new CRM_Auth_Handler();

/**
 * פונקציות עזר גלובליות
 */
function crm_is_user_logged_in() {
    $auth_handler = new CRM_Auth_Handler();
    return $auth_handler->is_user_logged_in();
}

function crm_get_current_user_info() {
    $auth_handler = new CRM_Auth_Handler();
    return $auth_handler->get_current_user_info();
}

function crm_get_user_menu() {
    $auth_handler = new CRM_Auth_Handler();
    return $auth_handler->get_user_menu();
} 