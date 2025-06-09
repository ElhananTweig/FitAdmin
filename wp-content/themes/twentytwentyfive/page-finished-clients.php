<?php
/**
 * Template Name: מתאמנות שסיימו
 * עמוד ציבורי להצגת מתאמנות שסיימו עם מעקב
 * 
 * הערה: טיפול בטופס מתבצע כאן לפני get_header() 
 * כדי למנוע שגיאת "headers already sent" עם wp_redirect
 */

// טיפול בעדכון הערות ומעקב - לפני get_header כדי למנוע בעיות headers
if (isset($_POST['update_follow_up']) && wp_verify_nonce($_POST['follow_up_nonce'], 'update_follow_up')) {
    // בדיקה שפונקציות ACF זמינות
    if (function_exists('update_field')) {
        $client_id = intval($_POST['client_id']);
        $follow_up_notes = sanitize_textarea_field($_POST['follow_up_notes']);
        $last_contact_date = sanitize_text_field($_POST['last_contact_date']);
        $next_contact_date = sanitize_text_field($_POST['next_contact_date']);
        
        // עדכון השדות
        update_field('follow_up_notes', $follow_up_notes, $client_id);
        update_field('last_contact_date', $last_contact_date, $client_id);
        update_field('next_contact_date', $next_contact_date, $client_id);
        
        // redirect למניעת שליחה חוזרת
        $current_url = home_url('/finished-clients/');
        wp_redirect($current_url . '?updated=1&client=' . $client_id);
        exit;
    } else {
        // אם ACF לא זמין, הפנה עם שגיאה
        wp_redirect(home_url('/finished-clients/') . '?error=1');
        exit;
    }
}

get_header(); 

// טעינת התוכן מהקובץ החדש
include(get_template_directory() . '/finished-clients.php');

get_footer(); ?> 