<?php
/**
 * סקריפט ליצירת עמודי CRM בכוח
 * הפעל קובץ זה פעם אחת כדי ליצור את כל העמודים הדרושים
 */

// וודא שזה WordPress
if (!defined('ABSPATH')) {
    require_once('../../../wp-config.php');
}

// רשימת העמודים ליצירה
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
    )
);

echo "<h1>יצירת עמודי CRM</h1>";

foreach ($pages_to_create as $page_data) {
    // בדיקה אם העמוד כבר קיים
    $existing_page = get_page_by_path($page_data['slug']);
    
    if ($existing_page) {
        echo "<p>✅ העמוד '{$page_data['title']}' כבר קיים (ID: {$existing_page->ID})</p>";
        
        // עדכן את התבנית
        update_post_meta($existing_page->ID, '_wp_page_template', $page_data['template']);
        echo "<p>🔄 התבנית עודכנה ל-{$page_data['template']}</p>";
    } else {
        // יצירת העמוד
        $page_id = wp_insert_post(array(
            'post_title' => $page_data['title'],
            'post_name' => $page_data['slug'],
            'post_content' => 'עמוד זה מוצג באמצעות תבנית מותאמת.',
            'post_status' => 'publish',
            'post_type' => 'page',
            'page_template' => $page_data['template']
        ));
        
        if ($page_id && !is_wp_error($page_id)) {
            // הגדרת תבנית לעמוד
            update_post_meta($page_id, '_wp_page_template', $page_data['template']);
            echo "<p>✅ העמוד '{$page_data['title']}' נוצר בהצלחה (ID: {$page_id})</p>";
        } else {
            echo "<p>❌ שגיאה ביצירת העמוד '{$page_data['title']}'</p>";
        }
    }
}

// נקה rewrite rules
flush_rewrite_rules();
echo "<p>🔄 Rewrite rules נוקו</p>";

echo "<h2>סיום!</h2>";
echo "<p><a href='" . home_url() . "'>חזרה לאתר</a></p>";
?> 