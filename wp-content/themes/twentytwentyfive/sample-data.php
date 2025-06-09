<?php
/**
 * יצירת נתוני דמו למערכת CRM תזונאית
 * הפעל את הקובץ הזה פעם אחת ליצירת מתאמנות לדוגמה
 */

// וודא שהקובץ נטען רק בתוך WordPress
if (!defined('ABSPATH')) {
    exit;
}

function create_sample_clients() {
    // נתוני מתאמנות לדוגמה
    $sample_clients = array(
        array(
            'title' => 'גל גדות',
            'first_name' => 'גל',
            'last_name' => 'גדות',
            'phone' => '054-1234567',
            'email' => 'gal@example.com',
            'age' => 34,
            'start_date' => '2024-01-15',
            'end_date' => '2024-07-15',
            'start_weight' => 75,
            'current_weight' => 68,
            'target_weight' => 65,
            'amount_paid' => 1200,
            'payment_method' => 'credit',
            'invoice_number' => 'INV-001',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'instagram',
            'notes' => 'אלרגית לבוטנים, מתאמנת 3 פעמים בשבוע',
            'is_frozen' => false,
            'payment_amount' => 1200
        ),
        array(
            'title' => 'דניאל פרץ',
            'first_name' => 'דניאל',
            'last_name' => 'פרץ',
            'phone' => '050-7654321',
            'email' => 'daniel@example.com',
            'age' => 42,
            'start_date' => '2024-02-01',
            'end_date' => '2024-08-01',
            'start_weight' => 95,
            'current_weight' => 88,
            'target_weight' => 82,
            'amount_paid' => 800,
            'payment_method' => 'bank_transfer',
            'invoice_number' => 'INV-002',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'referral',
            'notes' => 'דיאטה דלת פחמימות, יעד להורדת לחץ דם',
            'is_frozen' => false,
            'payment_amount' => 1500
        ),
        array(
            'title' => 'נועה קירל',
            'first_name' => 'נועה',
            'last_name' => 'קירל',
            'phone' => '052-9876543',
            'email' => 'noa@example.com',
            'age' => 28,
            'start_date' => '2024-03-15',
            'end_date' => '2024-09-15',
            'start_weight' => 65,
            'current_weight' => 62,
            'target_weight' => 60,
            'amount_paid' => 0,
            'payment_method' => '',
            'invoice_number' => '',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'whatsapp',
            'notes' => 'ספורטאית, מעוניינת באופטימיזציה של תזונה לביצועים',
            'is_frozen' => false,
            'payment_amount' => 1200
        ),
        array(
            'title' => 'אייל ברקוביץ',
            'first_name' => 'אייל',
            'last_name' => 'ברקוביץ',
            'phone' => '053-1122334',
            'email' => 'eyal@example.com',
            'age' => 45,
            'start_date' => '2024-01-20',
            'end_date' => '2024-07-20',
            'start_weight' => 110,
            'current_weight' => 105,
            'target_weight' => 90,
            'amount_paid' => 1500,
            'payment_method' => 'credit',
            'invoice_number' => 'INV-003',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'status',
            'notes' => 'סוכרת סוג 2, צריך השגחה צמודה',
            'is_frozen' => false,
            'payment_amount' => 1500
        ),
        array(
            'title' => 'שירה כהן',
            'first_name' => 'שירה',
            'last_name' => 'כהן',
            'phone' => '050-5566778',
            'email' => 'shira@example.com',
            'age' => 32,
            'start_date' => '2024-02-10',
            'end_date' => '2024-08-10',
            'start_weight' => 68,
            'current_weight' => 64,
            'target_weight' => 60,
            'amount_paid' => 1200,
            'payment_method' => 'credit',
            'invoice_number' => 'INV-004',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'mentor',
            'notes' => 'בהקפאה זמנית עקב נסיעה לחו״ל, תחזור לפעילות במאי',
            'is_frozen' => true,
            'freeze_start' => '2024-04-01',
            'freeze_end' => '2024-05-01',
            'freeze_reason' => 'נסיעה לחו״ל',
            'payment_amount' => 1200
        ),
        array(
            'title' => 'מיכל לוי',
            'first_name' => 'מיכל',
            'last_name' => 'לוי',
            'phone' => '054-7788990',
            'email' => 'michal@example.com',
            'age' => 29,
            'start_date' => '2024-04-01',
            'end_date' => '2024-05-15', // מסיימת בקרוב
            'start_weight' => 70,
            'current_weight' => 66,
            'target_weight' => 63,
            'amount_paid' => 1000,
            'payment_method' => 'bank_transfer',
            'invoice_number' => 'INV-005',
            'main_coach' => 'מרים קרישבסקי',
            'referral_source' => 'instagram',
            'notes' => 'מתקרבת ליעד, מוטיבציה גבוהה',
            'is_frozen' => false,
            'payment_amount' => 1000
        )
    );

    // יצירת המתאמנות
    foreach ($sample_clients as $client_data) {
        // בדיקה אם המתאמנת כבר קיימת
        $existing = get_posts(array(
            'post_type' => 'clients',
            'title' => $client_data['title'],
            'post_status' => 'any',
            'posts_per_page' => 1
        ));

        if (empty($existing)) {
            // יצירת פוסט חדש
            $post_id = wp_insert_post(array(
                'post_title' => $client_data['title'],
                'post_type' => 'clients',
                'post_status' => 'publish',
                'post_content' => $client_data['notes']
            ));

            if ($post_id && !is_wp_error($post_id)) {
                // הוספת שדות מותאמים
                update_field('first_name', $client_data['first_name'], $post_id);
                update_field('last_name', $client_data['last_name'], $post_id);
                update_field('phone', $client_data['phone'], $post_id);
                update_field('email', $client_data['email'], $post_id);
                update_field('age', $client_data['age'], $post_id);
                update_field('start_date', $client_data['start_date'], $post_id);
                update_field('end_date', $client_data['end_date'], $post_id);
                update_field('start_weight', $client_data['start_weight'], $post_id);
                update_field('current_weight', $client_data['current_weight'], $post_id);
                update_field('target_weight', $client_data['target_weight'], $post_id);
                update_field('amount_paid', $client_data['amount_paid'], $post_id);
                update_field('payment_method', $client_data['payment_method'], $post_id);
                update_field('invoice_number', $client_data['invoice_number'], $post_id);
                update_field('main_coach', $client_data['main_coach'], $post_id);
                update_field('referral_source', $client_data['referral_source'], $post_id);
                update_field('notes', $client_data['notes'], $post_id);
                update_field('is_frozen', $client_data['is_frozen'], $post_id);
                update_field('payment_amount', $client_data['payment_amount'], $post_id);

                // שדות הקפאה (אם רלוונטי)
                if ($client_data['is_frozen'] && isset($client_data['freeze_start'])) {
                    update_field('freeze_start', $client_data['freeze_start'], $post_id);
                    update_field('freeze_end', $client_data['freeze_end'], $post_id);
                    update_field('freeze_reason', $client_data['freeze_reason'], $post_id);
                }

                echo "נוצרה מתאמנת: " . $client_data['title'] . "<br>";
            }
        } else {
            echo "המתאמנת " . $client_data['title'] . " כבר קיימת<br>";
        }
    }
}

function create_sample_groups() {
    // נתוני קבוצות לדוגמה
    $sample_groups = array(
        array(
            'group_name' => 'קבוצת מתחילות - ינואר 2024',
            'group_mentor' => 1, // ID של המנטורית הראשונה
            'group_description' => 'קבוצה למתחילות שרוצות ללמוד על תזונה נכונה ולרדת במשקל בצורה בריאה',
            'group_start_date' => '2024-01-15',
            'group_end_date' => '2024-04-15',
            'group_max_participants' => 12
        ),
        array(
            'group_name' => 'קבוצת מתקדמות - פברואר 2024',
            'group_mentor' => 2, // ID של המנטורית השנייה
            'group_description' => 'קבוצה למתאמנות מנוסות שרוצות לשפר את התוצאות ולהגיע ליעדים מתקדמים',
            'group_start_date' => '2024-02-01',
            'group_end_date' => '2024-05-01',
            'group_max_participants' => 10
        ),
        array(
            'group_name' => 'קבוצת אמהות - מרץ 2024',
            'group_mentor' => 1,
            'group_description' => 'קבוצה מיוחדת לאמהות שרוצות לחזור לכושר אחרי לידה',
            'group_start_date' => '2024-03-01',
            'group_end_date' => '2024-06-01',
            'group_max_participants' => 8
        )
    );

    foreach ($sample_groups as $group_data) {
        // בדיקה אם הקבוצה כבר קיימת
        $existing = get_posts(array(
            'post_type' => 'groups',
            'meta_query' => array(
                array(
                    'key' => 'group_name',
                    'value' => $group_data['group_name'],
                    'compare' => '='
                )
            ),
            'post_status' => 'any',
            'posts_per_page' => 1
        ));

        if (empty($existing)) {
            // יצירת פוסט חדש
            $post_id = wp_insert_post(array(
                'post_title' => $group_data['group_name'],
                'post_type' => 'groups',
                'post_status' => 'publish',
                'post_content' => $group_data['group_description']
            ));

            if ($post_id && !is_wp_error($post_id)) {
                // הוספת שדות מותאמים
                update_field('group_name', $group_data['group_name'], $post_id);
                update_field('group_mentor', $group_data['group_mentor'], $post_id);
                update_field('group_description', $group_data['group_description'], $post_id);
                update_field('group_start_date', $group_data['group_start_date'], $post_id);
                update_field('group_end_date', $group_data['group_end_date'], $post_id);
                update_field('group_max_participants', $group_data['group_max_participants'], $post_id);

                echo "נוצרה קבוצה: " . $group_data['group_name'] . "<br>";
            }
        } else {
            echo "הקבוצה " . $group_data['group_name'] . " כבר קיימת<br>";
        }
    }
}

function create_sample_mentors() {
    // נתוני מנטוריות לדוגמה
    $sample_mentors = array(
        array(
            'title' => 'רונית כהן',
            'mentor_first_name' => 'רונית',
            'mentor_last_name' => 'כהן',
            'mentor_phone' => '050-1234567',
            'mentor_email' => 'ronit@example.com',
            'payment_percentage' => 40,
            'mentor_notes' => 'מנטורית מנוסה עם התמחות בתזונה ספורטיבית'
        ),
        array(
            'title' => 'נעמה ברק',
            'mentor_first_name' => 'נעמה',
            'mentor_last_name' => 'ברק',
            'mentor_phone' => '052-7654321',
            'mentor_email' => 'neama@example.com',
            'payment_percentage' => 40,
            'mentor_notes' => 'מתמחה בתזונה טיפולית ודיאטות מיוחדות'
        )
    );

    foreach ($sample_mentors as $mentor_data) {
        // בדיקה אם המנטורית כבר קיימת
        $existing = get_posts(array(
            'post_type' => 'mentors',
            'title' => $mentor_data['title'],
            'post_status' => 'any',
            'posts_per_page' => 1
        ));

        if (empty($existing)) {
            // יצירת פוסט חדש
            $post_id = wp_insert_post(array(
                'post_title' => $mentor_data['title'],
                'post_type' => 'mentors',
                'post_status' => 'publish',
                'post_content' => $mentor_data['mentor_notes']
            ));

            if ($post_id && !is_wp_error($post_id)) {
                // הוספת שדות מותאמים
                update_field('mentor_first_name', $mentor_data['mentor_first_name'], $post_id);
                update_field('mentor_last_name', $mentor_data['mentor_last_name'], $post_id);
                update_field('mentor_phone', $mentor_data['mentor_phone'], $post_id);
                update_field('mentor_email', $mentor_data['mentor_email'], $post_id);
                update_field('payment_percentage', $mentor_data['payment_percentage'], $post_id);
                update_field('mentor_notes', $mentor_data['mentor_notes'], $post_id);

                echo "נוצרה מנטורית: " . $mentor_data['title'] . "<br>";
            }
        } else {
            echo "המנטורית " . $mentor_data['title'] . " כבר קיימת<br>";
        }
    }
}

// פונקציה להפעלת יצירת הנתונים
function run_sample_data_creation() {
    echo "<div style='direction: rtl; padding: 20px; font-family: Arial;'>";
    echo "<h2>🚀 יצירת נתוני דמו למערכת CRM</h2>";
    
    if (function_exists('update_field')) {
        create_sample_mentors();
        create_sample_groups();
        create_sample_clients();
        echo "<h3 style='color: green;'>✅ נתוני הדמו נוצרו בהצלחה!</h3>";
        echo "<p>כעת אתה יכול לגשת ל<a href='" . admin_url('admin.php?page=crm-dashboard') . "'>דשבורד CRM</a> ולראות את הנתונים.</p>";
    } else {
        echo "<h3 style='color: red;'>❌ שגיאה: תוסף ACF לא מותקן או לא פעיל</h3>";
        echo "<p>אנא ודא שתוסף Advanced Custom Fields מותקן ופעיל.</p>";
    }
    
    echo "</div>";
}

// הפעלה רק אם נגישים ישירות לקובץ (לא מומלץ בפרודקשיון)
if (isset($_GET['create_sample_data']) && $_GET['create_sample_data'] == 'true') {
    run_sample_data_creation();
}

// הוספת כפתור בדשבורד אדמין
function add_sample_data_admin_notice() {
    if (get_current_screen()->id == 'dashboard') {
        echo '<div class="notice notice-info" style="direction: rtl;">
            <p><strong>💡 רוצה ליצור נתוני דמו למערכת CRM?</strong></p>
            <p><a href="' . add_query_arg('create_sample_data', 'true') . '" class="button button-primary">יצור נתוני דמו</a></p>
        </div>';
    }
}
add_action('admin_notices', 'add_sample_data_admin_notice');
?> 