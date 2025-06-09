<?php
/**
 * עמוד הבית הראשי - דשבורד CRM
 */

// בדיקת אימות מערכת
global $crm_auth;
if ($crm_auth && !$crm_auth->is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

// טעינת התבנית החדשה
include(get_template_directory() . '/page-crm-dashboard.php');
?> 