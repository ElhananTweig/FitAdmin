<?php
/**
 * Template Name: מתאמנות שסיימו
 * עמוד ציבורי להצגת מתאמנות שסיימו עם מעקב
 * 
 * הערה: טיפול בטופס מתבצע כאן לפני get_header() 
 * כדי למנוע שגיאת "headers already sent" עם wp_redirect
 */

// טיפול בעדכון הערות ומעקב - לפני get_header כדי למנוע בעיות headers
if (isset($_POST['action']) && $_POST['action'] === 'update_follow_up' && wp_verify_nonce($_POST['add_contact_lead_nonce'], 'add_contact_lead_action')) {
    // בדיקה שפונקציות ACF זמינות
    if (function_exists('update_field')) {
        $client_id = intval($_POST['client_id']);
        
        // אפשר עדכון של כל השדות, לא רק מעקב
        if (isset($_POST['first_name'])) {
            $first_name = sanitize_text_field($_POST['first_name']);
            $last_name = sanitize_text_field($_POST['last_name']);
            $phone = sanitize_text_field($_POST['phone']);
            
            update_field('first_name', $first_name, $client_id);
            update_field('last_name', $last_name, $client_id);
            update_field('phone', $phone, $client_id);
            
            // עדכון כותרת הפוסט
            wp_update_post(array(
                'ID' => $client_id,
                'post_title' => $first_name . ' ' . $last_name
            ));
        }
        
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

// טיפול בהוספת מתאמנת שסיימה (מלא)
if (isset($_POST['action']) && $_POST['action'] === 'add_finished_client' && wp_verify_nonce($_POST['add_finished_client_nonce'], 'add_finished_client_action')) {
    // בדיקה שפונקציות ACF זמינות
    if (function_exists('update_field')) {
        // קבלת הנתונים מהטופס
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $phone = sanitize_text_field($_POST['phone']);
        $email = sanitize_email($_POST['email']);
        $age = intval($_POST['age']);
        $start_date = sanitize_text_field($_POST['start_date']);
        $end_date = sanitize_text_field($_POST['end_date']);
        $referral_source = sanitize_text_field($_POST['referral_source']);
        $training_type = sanitize_text_field($_POST['training_type']);
        $start_weight = floatval($_POST['start_weight']);
        $current_weight = floatval($_POST['current_weight']);
        $target_weight = floatval($_POST['target_weight']);
        $notes = sanitize_textarea_field($_POST['notes']);
        $last_contact_date = sanitize_text_field($_POST['last_contact_date']);
        $next_contact_date = sanitize_text_field($_POST['next_contact_date']);
        $follow_up_notes = sanitize_textarea_field($_POST['follow_up_notes']);
        
        // בדיקת שדות חובה
        if (empty($first_name) || empty($last_name) || empty($phone)) {
            wp_redirect(home_url('/finished-clients/') . '?error=2'); // שגיאה בשדות חובה
            exit;
        }
        
        // יצירת הפוסט
        $post_data = array(
            'post_title' => $first_name . ' ' . $last_name,
            'post_type' => 'clients',
            'post_status' => 'publish',
            'post_content' => $notes
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            // הוספת שדות מותאמים - פרטים בסיסיים
            update_field('first_name', $first_name, $post_id);
            update_field('last_name', $last_name, $post_id);
            update_field('phone', $phone, $post_id);
            if ($email) update_field('email', $email, $post_id);
            if ($age) update_field('age', $age, $post_id);
            
            // תאריכים
            if ($start_date) update_field('start_date', $start_date, $post_id);
            if ($end_date) update_field('end_date', $end_date, $post_id);
            
            // מקור הגעה וליווי
            update_field('referral_source', $referral_source, $post_id);
            update_field('training_type', $training_type, $post_id);
            
            // משקל
            if ($start_weight) update_field('start_weight', $start_weight, $post_id);
            if ($current_weight) update_field('current_weight', $current_weight, $post_id);
            if ($target_weight) update_field('target_weight', $target_weight, $post_id);
            
            // הערות
            if ($notes) update_field('notes', $notes, $post_id);
            
            // שדות מעקב
            if ($last_contact_date) {
                update_field('last_contact_date', $last_contact_date, $post_id);
            }
            if ($next_contact_date) {
                update_field('next_contact_date', $next_contact_date, $post_id);
            }
            if ($follow_up_notes) {
                update_field('follow_up_notes', $follow_up_notes, $post_id);
            }
            
            // הגדרות בסיסיות נוספות
            update_field('is_frozen', false, $post_id);
            update_field('is_contact_lead', false, $post_id); // מתאמנת רגילה
            
            // redirect עם הודעת הצלחה
            wp_redirect(home_url('/finished-clients/') . '?added=1&client=' . $post_id);
            exit;
        } else {
            // שגיאה ביצירת הפוסט
            wp_redirect(home_url('/finished-clients/') . '?error=3');
            exit;
        }
    } else {
        // אם ACF לא זמין, הפנה עם שגיאה
        wp_redirect(home_url('/finished-clients/') . '?error=1');
        exit;
    }
}

// טיפול בהוספת מתאמנת פוטנציאלית (פשוט)
if (isset($_POST['action']) && $_POST['action'] === 'add_contact_lead' && wp_verify_nonce($_POST['add_contact_lead_nonce'], 'add_contact_lead_action')) {
    // בדיקה שפונקציות ACF זמינות
    if (function_exists('update_field')) {
        // קבלת הנתונים מהטופס
        $first_name = sanitize_text_field($_POST['first_name']);
        $last_name = sanitize_text_field($_POST['last_name']);
        $phone = sanitize_text_field($_POST['phone']);
        $last_contact_date = sanitize_text_field($_POST['last_contact_date']);
        $next_contact_date = sanitize_text_field($_POST['next_contact_date']);
        $follow_up_notes = sanitize_textarea_field($_POST['follow_up_notes']);
        
        // בדיקת שדות חובה
        if (empty($first_name) || empty($last_name) || empty($phone)) {
            wp_redirect(home_url('/finished-clients/') . '?error=2'); // שגיאה בשדות חובה
            exit;
        }
        
        // יצירת הפוסט
        $post_data = array(
            'post_title' => $first_name . ' ' . $last_name,
            'post_type' => 'clients',
            'post_status' => 'publish',
            'post_content' => $follow_up_notes
        );
        
        $post_id = wp_insert_post($post_data);
        
        if ($post_id && !is_wp_error($post_id)) {
            // הוספת שדות מותאמים - פרטים בסיסיים
            update_field('first_name', $first_name, $post_id);
            update_field('last_name', $last_name, $post_id);
            update_field('phone', $phone, $post_id);
            
            // שדות מעקב
            if ($last_contact_date) {
                update_field('last_contact_date', $last_contact_date, $post_id);
            }
            if ($next_contact_date) {
                update_field('next_contact_date', $next_contact_date, $post_id);
            }
            if ($follow_up_notes) {
                update_field('follow_up_notes', $follow_up_notes, $post_id);
            }
            
            // הגדרות בסיסיות נוספות
            update_field('referral_source', 'unknown', $post_id);
            update_field('training_type', 'personal', $post_id);
            update_field('is_frozen', false, $post_id);
            update_field('is_contact_lead', true, $post_id); // מתאמנת פוטנציאלית
            
            // הגדרת תאריכי סיום בעבר (כדי שתופיע ברשימה)
            $yesterday = date('Y-m-d', strtotime('-1 day'));
            update_field('start_date', $yesterday, $post_id);
            update_field('end_date', $yesterday, $post_id);
            
            // redirect עם הודעת הצלחה
            wp_redirect(home_url('/finished-clients/') . '?added=1&client=' . $post_id);
            exit;
        } else {
            // שגיאה ביצירת הפוסט
            wp_redirect(home_url('/finished-clients/') . '?error=3');
            exit;
        }
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