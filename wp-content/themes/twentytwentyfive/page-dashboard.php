<?php
/**
 * Template Name: דשבורד CRM
 * עמוד דשבורד CRM מותאם לתצוגה ציבורית
 */

// טעינת Font Awesome לאייקונים מודרניים
wp_enqueue_style( 'crm-dashboard-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );

get_header(); ?>

<style>
    .crm-main-dashboard {
        font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        direction: rtl;
        text-align: right;
        max-width: 1400px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .dashboard-hero {
        margin-bottom: 40px;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        border: 1px solid rgba(255, 255, 255, 0.50);
    }
    
    .dashboard-hero img {
        width: 100%;
        display: block;
        height: auto;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: rgba(38, 59, 52, 0.70);
        backdrop-filter: blur(5.9px);
        -webkit-backdrop-filter: blur(5.9px);
        border: 1px solid rgba(255, 255, 255, 0.91);
        border-right: 5px solid;
        padding: 30px;
        border-radius: 16px;
        text-align: center;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .stat-card.active { border-right-color: #a7c7e7; }
    .stat-card.ending { border-right-color: #f5d5a0; }
    .stat-card.deals  { border-right-color: #99e6d4; }
    .stat-card.unpaid { border-right-color: #f5b7b1; }
    .stat-card.income { border-right-color: #b8e6b8; }

   .stat-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    
    /* אייקונים מודרניים צבעוניים */
    .stat-icon i {
        color: #d7dedc; /* ברירת מחדל בהירה */
    }
    .stat-card.active   .stat-icon i { color: #a7c7e7; }
    .stat-card.ending   .stat-icon i { color: #f5d5a0; }
    .stat-card.deals    .stat-icon i { color: #99e6d4; }
    .stat-card.unpaid   .stat-icon i { color: #f5b7b1; }
    .stat-card.income   .stat-icon i { color: #b8e6b8; }
    
    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        color: #d7dedc;
        margin-bottom: 10px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .stat-label {
        color: #d7dedc;
        font-size: 1rem;
        font-weight: 600;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .dashboard-sections {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .dashboard-section {
        background: rgba(85, 85, 85, 0.70);
        backdrop-filter: blur(5.9px);
        -webkit-backdrop-filter: blur(5.9px);
        border: 1px solid rgba(255, 255, 255, 0.91);
        padding: 30px;
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    
    .section-title {
        font-size: 1.2rem;
        font-weight: 600;
        color: #d7dedc;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .client-preview {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .client-preview:last-child {
        border-bottom: none;
    }
    
    .client-info {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .client-name {
        font-weight: 600;
        color: #d7dedc;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    }
    
    .client-phone {
        color: #d7dedc;
        font-size: 0.875rem;
        text-decoration: none;
        opacity: 0.9;
    }
    
    .client-date {
        color: #d7dedc;
        font-size: 0.875rem;
        opacity: 0.8;
    }
    
    /* סטיילים לגרף עוגה רספונסיבי */
    .chart-responsive-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        align-items: center;
    }
    
    .chart-canvas-container {
        position: relative;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 5px;
    }
    
    .legend-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 4px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .legend-item:last-child {
        border-bottom: none;
    }
    
    .legend-info {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .legend-color {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .legend-stats {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 1px;
    }
    
    .legend-count {
        font-weight: 600;
        font-size: 0.75rem;
        color: #d7dedc;
    }
    
    .legend-percentage {
        font-size: 0.65rem;
        color: #d7dedc;
        opacity: 0.8;
    }
    
    /* סגנונות לבאנרים בשני עמודות */
    .two-column-banners {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .banner-half {
        background: rgba(85, 85, 85, 0.70);
        backdrop-filter: blur(5.9px);
        -webkit-backdrop-filter: blur(5.9px);
        border: 1px solid rgba(255, 255, 255, 0.91);
        padding: 20px;
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        height: fit-content;
    }
    
    .banner-half .chart-responsive-container {
        grid-template-columns: 1fr 1fr;
        gap: 15px;
        align-items: center;
    }
    
    .banner-half .chart-canvas-container {
        height: 150px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    /* סגנונות לבאנר פעולות מהירות אנכי */
    .vertical-quick-actions {
        display: flex;
        flex-direction: column;
        gap: 8px;
        padding: 6px 0;
    }
    
    .vertical-action-button {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 16px;
        border: none;
        border-radius: 10px;
        color: white;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
        box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        text-decoration: none;
    }
    
    .vertical-action-button:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        color: white;
    }
    
    .vertical-action-button .action-icon {
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .vertical-action-button .action-text {
        flex: 1;
        text-align: right;
    }
    
    @media (max-width: 768px) {
        .dashboard-sections {
            grid-template-columns: 1fr;
        }
        .stats-overview {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        .two-column-banners {
            grid-template-columns: 1fr;
        }
        
        .banner-half .chart-responsive-container {
            grid-template-columns: 1fr !important;
            gap: 8px !important;
        }
        
        .banner-half .chart-canvas-container {
            height: 110px;
        }
        
        .vertical-action-button {
            font-size: 12px;
            padding: 8px 12px;
            gap: 8px;
        }
        
        .vertical-action-button .action-icon {
            font-size: 13px;
        }
        
        .vertical-quick-actions {
            gap: 5px;
        }
        
        .banner-half .section-title {
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .legend-count {
            font-size: 0.7rem !important;
        }
        
        .legend-percentage {
            font-size: 0.6rem !important;
        }
    }
</style>

<div class="crm-main-dashboard">
    <div class="dashboard-hero">
        <img src="<?php echo get_template_directory_uri(); ?>/assets/images/miriam-upper-banner.jpg" alt="מערכת CRM תזונה">
    </div>

    <?php
    // פונקציות עזר לחישוב סטטיסטיקות
    function get_public_clients_stats() {
        $clients = get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
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
            )
        ));
        
        $stats = array(
            'total' => count($clients),
            'active' => 0,
            'ending_soon' => 0,
            'frozen' => 0,
            'paid' => 0,
            'unpaid' => 0,
            'total_income' => 0,
            'monthly_deals' => 0,
            'monthly_expected_income' => 0,
            'referral_sources' => array()
        );

        $today = date('Y-m-d');
        $current_month = date('Y-m');
        $one_week_later = date('Y-m-d', strtotime('+7 days')); // שבוע מהיום
        
        foreach ($clients as $client) {
            if (!function_exists('get_field')) continue;
            
            $end_date = get_field('end_date', $client->ID);
            $is_frozen = get_field('is_frozen', $client->ID);
            $amount_paid = (float) get_field('amount_paid', $client->ID);
            $payment_amount = (float) get_field('payment_amount', $client->ID);
            
            // חישוב סך כל התשלומים כולל תשלומים נוספים
            $total_amount_paid = $amount_paid;
            $additional_payments = get_field('additional_payments', $client->ID);
            if ($additional_payments && is_array($additional_payments)) {
                foreach ($additional_payments as $payment) {
                    if (isset($payment['amount']) && is_numeric($payment['amount'])) {
                        $total_amount_paid += floatval($payment['amount']);
                    }
                }
            }
            $referral_source = get_field('referral_source', $client->ID);
            
            // ספירת פעילים
            if ($end_date >= $today && !$is_frozen) {
                $stats['active']++;
            }
            
            // ספירת מסיימים בקרוב
            if ($end_date <= $one_week_later && $end_date >= $today && !$is_frozen) {
                $stats['ending_soon']++;
            }
            
            // ספירת בהקפאה
            if ($is_frozen) {
                $stats['frozen']++;
            }
            
            // ספירת סטטוס תשלום
            if ($total_amount_paid == 0) {
                $stats['unpaid']++;
            } elseif ($payment_amount && $total_amount_paid > 0 && $total_amount_paid < $payment_amount) {
                // אם יש סכום לתשלום והתשלום חלקי
                if (!isset($stats['partial'])) {
                    $stats['partial'] = 0;
                }
                $stats['partial']++;
            } else {
                $stats['paid']++;
            }
            
            // חישוב הכנסות
            $stats['total_income'] += $total_amount_paid;

            // עסקאות החודש - לפי תאריך יצירת הרשומה
            $post_month = date('Y-m', strtotime($client->post_date));
            if ($post_month === $current_month) {
                $stats['monthly_deals'] += $payment_amount;
            }

            // הכנסות צפויות החודש - תשלומים שמגיעים החודש (כולל תשלומים בתשלומים)
            $payment_date_primary = get_field('payment_date', $client->ID);
            $installments_primary = intval(get_field('installments', $client->ID));
            if ($payment_date_primary && $amount_paid > 0) {
                if ($installments_primary > 1) {
                    $pay_month = date('Y-m', strtotime($payment_date_primary));
                    $diff = (intval(date('Y')) - intval(substr($pay_month, 0, 4))) * 12
                          + (intval(date('m')) - intval(substr($pay_month, 5, 2)));
                    if ($diff >= 0 && $diff < $installments_primary) {
                        $stats['monthly_expected_income'] += $amount_paid / $installments_primary;
                    }
                } else {
                    if (date('Y-m', strtotime($payment_date_primary)) === $current_month) {
                        $stats['monthly_expected_income'] += $amount_paid;
                    }
                }
            }
            if ($additional_payments && is_array($additional_payments)) {
                foreach ($additional_payments as $ap) {
                    $ap_amount = isset($ap['amount']) ? floatval($ap['amount']) : 0;
                    $ap_date   = isset($ap['date'])   ? $ap['date']             : '';
                    $ap_inst   = isset($ap['installments']) ? intval($ap['installments']) : 1;
                    if (!$ap_amount || !$ap_date) continue;
                    if ($ap_inst > 1) {
                        $ap_month = date('Y-m', strtotime($ap_date));
                        $diff = (intval(date('Y')) - intval(substr($ap_month, 0, 4))) * 12
                              + (intval(date('m')) - intval(substr($ap_month, 5, 2)));
                        if ($diff >= 0 && $diff < $ap_inst) {
                            $stats['monthly_expected_income'] += $ap_amount / $ap_inst;
                        }
                    } else {
                        if (date('Y-m', strtotime($ap_date)) === $current_month) {
                            $stats['monthly_expected_income'] += $ap_amount;
                        }
                    }
                }
            }

            // ספירת מקורות הגעה
            if ($referral_source) {
                if (!isset($stats['referral_sources'][$referral_source])) {
                    $stats['referral_sources'][$referral_source] = 0;
                }
                $stats['referral_sources'][$referral_source]++;
            }
        }
        
        return $stats;
    }

    function get_public_ending_soon_clients() {
        $today = date('Y-m-d');
        $one_week_later = date('Y-m-d', strtotime('+7 days')); // שבוע מהיום
        
        return get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'end_date',
                    'value' => array($today, $one_week_later),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                ),
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'is_frozen',
                        'value' => false,
                        'compare' => '='
                    ),
                    array(
                        'key' => 'is_frozen',
                        'value' => 'false',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'is_frozen',
                        'value' => '',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'is_frozen',
                        'compare' => 'NOT EXISTS'
                    )
                ),
                // הוצאת מתאמנות פוטנציאליות
                array(
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
                )
            ),
            'meta_key' => 'end_date',
            'orderby' => 'meta_value',
            'order' => 'ASC'
        ));
    }

    function get_public_frozen_clients() {
        return get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'relation' => 'OR',
                    array(
                        'key' => 'is_frozen',
                        'value' => true,
                        'compare' => '='
                    ),
                    array(
                        'key' => 'is_frozen',
                        'value' => 'true',
                        'compare' => '='
                    ),
                    array(
                        'key' => 'is_frozen',
                        'value' => '1',
                        'compare' => '='
                    )
                ),
                // הוצאת מתאמנות פוטנציאליות
                array(
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
                )
            )
        ));
    }

    // פונקציה לקבלת נתוני "מה חדש השבוע"
    function get_weekly_updates() {
        // חישוב תחילת השבוע (יום ראשון)
        $current_day = date('w'); // 0=ראשון, 1=שני, וכו'
        if ($current_day == 0) {
            // אם היום הוא ראשון, השבוע התחיל היום
            $week_start = date('Y-m-d');
        } else {
            // אחרת, חזור לראשון הקודם
            $week_start = date('Y-m-d', strtotime('-' . $current_day . ' days'));
        }
        $today = date('Y-m-d');
        
        // מתאמנות חדשות השבוע - גם לפי תאריך יצירה וגם לפי תאריך התחלה (ללא פוטנציאליות)
        $new_clients_by_creation = get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'after' => $week_start,
                    'before' => $today . ' 23:59:59',
                    'inclusive' => true
                )
            ),
            'meta_query' => array(
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
            )
        ));
        
        $new_clients_by_start = get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'start_date',
                    'value' => array($week_start, $today),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                ),
                // הוצאת מתאמנות פוטנציאליות
                array(
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
                )
            )
        ));
        
        // מיזוג והסרת כפילויות
        $new_clients_ids = array();
        $new_clients = array();
        
        foreach (array_merge($new_clients_by_creation, $new_clients_by_start) as $client) {
            if (!in_array($client->ID, $new_clients_ids)) {
                $new_clients_ids[] = $client->ID;
                $new_clients[] = $client;
            }
        }
        
        // קבוצות חדשות השבוע - גם לפי יצירה וגם לפי תאריך התחלה
        $new_groups_by_creation = get_posts(array(
            'post_type' => 'groups',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'after' => $week_start,
                    'before' => $today . ' 23:59:59',
                    'inclusive' => true
                )
            )
        ));
        
        $new_groups_by_start = get_posts(array(
            'post_type' => 'groups',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'group_start_date',
                    'value' => array($week_start, $today),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                )
            )
        ));
        
        // מיזוג קבוצות
        $new_groups_ids = array();
        $new_groups = array();
        
        foreach (array_merge($new_groups_by_creation, $new_groups_by_start) as $group) {
            if (!in_array($group->ID, $new_groups_ids)) {
                $new_groups_ids[] = $group->ID;
                $new_groups[] = $group;
            }
        }
        
        // מנטוריות חדשות השבוע (ללא מנטוריות שנמחקו)
        $new_mentors = get_posts(array(
            'post_type' => 'mentors',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'date_query' => array(
                array(
                    'after' => $week_start,
                    'before' => $today . ' 23:59:59',
                    'inclusive' => true
                )
            ),
            'meta_query' => array(
                'relation' => 'OR',
                array(
                    'key' => 'is_deleted',
                    'value' => false,
                    'compare' => '='
                ),
                array(
                    'key' => 'is_deleted',
                    'value' => 'false',
                    'compare' => '='
                ),
                array(
                    'key' => 'is_deleted',
                    'value' => '',
                    'compare' => '='
                ),
                array(
                    'key' => 'is_deleted',
                    'compare' => 'NOT EXISTS'
                )
            )
        ));
        
        // תשלומים שהתעדכנו השבוע - חיפוש בשתי דרכים
        $payments_by_payment_date = get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'payment_date',
                    'value' => array($week_start, $today),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                ),
                // הוצאת מתאמנות פוטנציאליות
                array(
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
                )
            )
        ));
        
        // תשלומים שהתעדכנו השבוע - גישה משופרת (ללא פוטנציאליות)
        // נחפש את כל המתאמנות עם תשלומים ונבדוק אם הן התעדכנו השבוע
        $all_clients_with_payments = get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => -1,
            'post_status' => 'publish',
            'meta_query' => array(
                'relation' => 'AND',
                array(
                    'key' => 'amount_paid',
                    'value' => 0,
                    'compare' => '>',
                    'type' => 'NUMERIC'
                ),
                // הוצאת מתאמנות פוטנציאליות
                array(
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
                )
            )
        ));
        
        $payments_by_modification = array();
        foreach ($all_clients_with_payments as $client) {
            // בדיקה אם הפוסט התעדכן השבוע
            $post_modified = strtotime($client->post_modified);
            $week_start_timestamp = strtotime($week_start);
            
            if ($post_modified >= $week_start_timestamp) {
                $payments_by_modification[] = $client;
            }
        }
        
        // מיזוג תשלומים והסרת כפילויות
        $updated_payments_ids = array();
        $updated_payments = array();
        
        foreach (array_merge($payments_by_payment_date, $payments_by_modification) as $payment) {
            if (!in_array($payment->ID, $updated_payments_ids)) {
                $updated_payments_ids[] = $payment->ID;
                $updated_payments[] = $payment;
            }
        }
        
        return array(
            'new_clients' => $new_clients,
            'new_groups' => $new_groups,
            'new_mentors' => $new_mentors,
            'updated_payments' => $updated_payments
        );
    }

    $stats = get_public_clients_stats();
    $ending_soon = get_public_ending_soon_clients();
    $frozen_clients = get_public_frozen_clients();
    $weekly_data = get_weekly_updates();
    ?>

    <!-- סטטיסטיקות כלליות -->
    <div class="stats-overview">
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=active'; ?>" class="stat-card active" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
       <div class="stat-number"><?php echo $stats['active']; ?></div>
            <div class="stat-label">מתאמנות פעילות</div>
        </a>
        
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=ending'; ?>" class="stat-card ending" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fa-regular fa-hourglass-half"></i></div>
            <div class="stat-number"><?php echo $stats['ending_soon']; ?></div>
            <div class="stat-label">מסיימות בקרוב</div>
        </a>
        
        <div class="stat-card deals">
            <div class="stat-icon"><i class="fa-solid fa-handshake"></i></div>
            <div class="stat-number">₪<?php echo number_format($stats['monthly_deals']); ?></div>
            <div class="stat-label">עסקאות החודש</div>
        </div>

        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=unpaid'; ?>" class="stat-card unpaid" style="text-decoration: none; color: inherit;">
            <div class="stat-icon"><i class="fa-solid fa-credit-card"></i></div>
            <div class="stat-number"><?php echo $stats['unpaid'] + (isset($stats['partial']) ? $stats['partial'] : 0); ?></div>
            <div class="stat-label">ממתינות לתשלום</div>
        </a>
        
        <?php if (current_user_can('manage_options')): ?>
            <a href="<?php echo home_url('/payments-management'); ?>" class="stat-card income" style="text-decoration: none; color: inherit;">
                <div class="stat-icon"><i class="fa-solid fa-shekel-sign"></i></div>
                <div class="stat-number">₪<?php echo number_format($stats['monthly_expected_income']); ?></div>
                <div class="stat-label">הכנסות צפויות החודש</div>
            </a>
        <?php else: ?>
            <div class="stat-card income">
                <div class="stat-icon"><i class="fa-solid fa-shekel-sign"></i></div>
                <div class="stat-number">₪<?php echo number_format($stats['monthly_expected_income']); ?></div>
                <div class="stat-label">הכנסות צפויות החודש</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- מתאמנות שמסיימות בקרוב ובהקפאה -->
    <div class="dashboard-sections">
        <!-- מתאמנות שמסיימות בקרוב -->
        <div class="dashboard-section">
            <h3 class="section-title">
                <i class="fa-regular fa-hourglass-half" style="color:#f5d5a0;"></i>
                מתאמנות שמסיימות בקרוב
            </h3>
            <?php if ($ending_soon && function_exists('get_field')): ?>
                <?php foreach ($ending_soon as $client): ?>
                    <div class="client-preview">
                        <div class="client-info">
                            <div class="client-name">
                                <?php echo get_field('first_name', $client->ID) . ' ' . get_field('last_name', $client->ID); ?>
                            </div>
                            <a href="tel:<?php echo get_field('phone', $client->ID); ?>" class="client-phone">
                                <?php echo get_field('phone', $client->ID); ?>
                            </a>
                        </div>
                        <div class="client-date">
                            מסתיים: <?php echo get_field('end_date', $client->ID); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>אין מתאמנות שמסיימות בקרוב 🎉</p>
            <?php endif; ?>
        </div>

        <!-- מתאמנות בהקפאה - עכשיו עם קישור -->
        <div class="dashboard-section">
            <h3 class="section-title">
                <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=frozen'; ?>" style="color: inherit; text-decoration: none; display: flex; align-items: center; gap: 10px;">
                    <i class="fa-solid fa-pause-circle" style="color:#d4c5f9;"></i>
                    מתאמנות בהקפאה
                    <span style="font-size: 0.8rem; color: #6b7280;">👆 לחצי לצפייה</span>
                </a>
            </h3>
            <?php if ($frozen_clients && function_exists('get_field')): ?>
                <?php foreach ($frozen_clients as $client): ?>
                    <div class="client-preview">
                        <div class="client-info">
                            <div class="client-name">
                                <?php echo get_field('first_name', $client->ID) . ' ' . get_field('last_name', $client->ID); ?>
                            </div>
                            <a href="tel:<?php echo get_field('phone', $client->ID); ?>" class="client-phone">
                                <?php echo get_field('phone', $client->ID); ?>
                            </a>
                        </div>
                        <div class="client-date">
                            עד: <?php echo get_field('freeze_end', $client->ID); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>אין מתאמנות בהקפאה כרגע</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- מקורות הגעה ופעולות מהירות -->
    <div class="two-column-banners">
        <!-- סטטיסטיקות מקורות הגעה עם גרף עוגה רספונסיבי -->
        <div class="banner-half referral-chart">
            <h3 class="section-title">
                📈 מקורות הגעה של מתאמנות
            </h3>
            
            <div class="chart-responsive-container">
                <!-- גרף עוגה -->
                <div class="chart-canvas-container">
                    <canvas id="pieChart" width="150" height="150"></canvas>
                </div>
                
                <!-- רשימת מקורות -->
                <div class="chart-legend">
                    <?php 
                    $source_labels = array(
                        'instagram' => 'אינסטגרם',
                        'status' => 'סטטוס',
                        'whatsapp' => 'וואצאפ',
                        'referral' => 'המלצה',
                        'mentor' => 'מנטורית',
                        'unknown' => 'לא ידוע'
                    );
                    
                    $colors = array(
                        'instagram' => '#EF4444',
                        'status' => '#10B981',
                        'whatsapp' => '#3B82F6',
                        'referral' => '#8B5CF6',
                        'mentor' => '#F59E0B',
                        'unknown' => '#6B7280'
                    );
                    
                    $total_referrals = array_sum($stats['referral_sources']);
                    
                    foreach ($stats['referral_sources'] as $source => $count): 
                        $percentage = $total_referrals > 0 ? round(($count / $total_referrals) * 100) : 0;
                        $label = isset($source_labels[$source]) ? $source_labels[$source] : 'לא ידוע';
                        $color = isset($colors[$source]) ? $colors[$source] : '#95A5A6';
                    ?>
                        <div class="legend-item">
                            <div class="legend-info">
                                <div class="legend-color" style="background: <?php echo $color; ?>;"></div>
                                <strong><?php echo $label; ?></strong>
                            </div>
                            <div class="legend-stats">
                                <div class="legend-count"><?php echo $count; ?> מתאמנת<?php echo $count > 1 ? 'ות' : ''; ?></div>
                                <div class="legend-percentage"><?php echo $percentage; ?>%</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- פעולות מהירות -->
        <div class="banner-half quick-actions-banner">
            <h3 class="section-title">
                🚀 פעולות מהירות
            </h3>
            
            <div class="vertical-quick-actions">
                <button type="button" onclick="openAddClientModal()" class="vertical-action-button" style="background: linear-gradient(135deg, #059669, #047857);">
                    <span class="action-icon">👥</span>
                    <span class="action-text">הוסף מתאמנת חדשה</span>
                </button>
                
                <button type="button" onclick="openAddMentorModal()" class="vertical-action-button" style="background: linear-gradient(135deg, #0d9488, #0f766e);">
                    <span class="action-icon">📝</span>
                    <span class="action-text">הוסף מנטורית חדשה</span>
                </button>
                
                <a href="<?php echo admin_url('admin.php?page=add-group-form'); ?>" class="vertical-action-button" style="background: linear-gradient(135deg, #0891b2, #0e7490); text-decoration: none; color: white;">
                    <span class="action-icon">✨</span>
                    <span class="action-text">צור קבוצה חדשה</span>
                </a>
                
                <a href="<?php echo admin_url('admin.php?page=crm-reports'); ?>" class="vertical-action-button" style="background: linear-gradient(135deg, #2563eb, #1d4ed8); text-decoration: none; color: white;">
                    <span class="action-icon">📊</span>
                    <span class="action-text">דוחות ואנליטיקס</span>
                </a>
                
                <a href="<?php echo home_url('/payments-management'); ?>" class="vertical-action-button" style="background: linear-gradient(135deg, #4f46e5, #4338ca); text-decoration: none; color: white;">
                    <span class="action-icon">💰</span>
                    <span class="action-text">ניהול תשלומים</span>
                </a>
            </div>
        </div>
    </div>

    <!-- מה חדש השבוע -->
    <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #d7dedc; padding: 40px; border-radius: 20px; margin-top: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.2);">
        <h2 style="color: #d7dedc; text-align: center; margin-bottom: 30px; font-size: 2rem;">☀️ מה חדש השבוע</h2>
        
        <!-- נתונים כלליים -->
        <?php 
        // חישוב תחילת השבוע לתצוגה
        $current_day = date('w');
        if ($current_day == 0) {
            $week_start_display = date('d/m');
        } else {
            $week_start_display = date('d/m', strtotime('-' . $current_day . ' days'));
        }
        ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
            <div style="text-align: center; background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;"><?php echo count($weekly_data['new_clients']); ?></div>
                <div style="font-size: 1rem; opacity: 0.9;">מתאמנות חדשות</div>
                <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 5px;">
                    (מ-<?php echo $week_start_display; ?> עד <?php echo date('d/m'); ?>)
                </div>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;"><?php echo count($weekly_data['new_mentors']); ?></div>
                <div style="font-size: 1rem; opacity: 0.9;">מנטוריות חדשות</div>
                <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 5px;">
                    (מ-<?php echo $week_start_display; ?> עד <?php echo date('d/m'); ?>)
                </div>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;"><?php echo count($weekly_data['new_groups']); ?></div>
                <div style="font-size: 1rem; opacity: 0.9;">קבוצות חדשות</div>
                <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 5px;">
                    (מ-<?php echo $week_start_display; ?> עד <?php echo date('d/m'); ?>)
                </div>
            </div>
            <div style="text-align: center; background: rgba(255,255,255,0.15); padding: 20px; border-radius: 12px;">
                <div style="font-size: 2.5rem; font-weight: bold; margin-bottom: 10px;"><?php echo count($weekly_data['updated_payments']); ?></div>
                <div style="font-size: 1rem; opacity: 0.9;">תשלומים עודכנו</div>
                <div style="font-size: 0.7rem; opacity: 0.7; margin-top: 5px;">
                    (מ-<?php echo $week_start_display; ?> עד <?php echo date('d/m'); ?>)
                </div>
            </div>
        </div>

        <!-- פירוט הפעילויות -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(350px, 1fr)); gap: 25px;">
            <!-- מתאמנות שהתחילו השבוע -->
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 15px;">
                <h4 style="margin: 0 0 15px 0; color: #d7dedc; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    👥 מתאמנות שהתחילו השבוע
                </h4>
                <?php if ($weekly_data['new_clients']): ?>
                    <?php foreach ($weekly_data['new_clients'] as $client): ?>
                        <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                            • <?php echo get_field('first_name', $client->ID) . ' ' . get_field('last_name', $client->ID); ?>
                            <br><span style="font-size: 0.8rem; opacity: 0.8;">
                                נוצר: <?php echo date('d/m/Y', strtotime($client->post_date)); ?>
                                <?php if (get_field('start_date', $client->ID)): ?>
                                    | התחלה: <?php echo date('d/m/Y', strtotime(get_field('start_date', $client->ID))); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="font-size: 0.9rem; opacity: 0.7; font-style: italic;">אין מתאמנות חדשות השבוע</div>
                <?php endif; ?>
            </div>

            <!-- קבוצות חדשות שנפתחו השבוע -->
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 15px;">
                <h4 style="margin: 0 0 15px 0; color: #d7dedc; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    🌟 קבוצות חדשות שנפתחו השבוע
                </h4>
                <?php if ($weekly_data['new_groups']): ?>
                    <?php foreach ($weekly_data['new_groups'] as $group): ?>
                        <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                            • <?php echo get_field('group_name', $group->ID) ?: $group->post_title; ?>
                            <br><span style="font-size: 0.8rem; opacity: 0.8;">
                                נוצר: <?php echo date('d/m/Y', strtotime($group->post_date)); ?>
                                <?php if (get_field('group_start_date', $group->ID)): ?>
                                    | התחלה: <?php echo date('d/m/Y', strtotime(get_field('group_start_date', $group->ID))); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="font-size: 0.9rem; opacity: 0.7; font-style: italic;">אין קבוצות חדשות השבוע</div>
                <?php endif; ?>
            </div>

            <!-- תשלומים שהתעדכנו השבוע -->
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 15px;">
                <h4 style="margin: 0 0 15px 0; color: #d7dedc; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    💰 תשלומים שהתעדכנו השבוע
                </h4>
                <?php if ($weekly_data['updated_payments']): ?>
                    <?php foreach ($weekly_data['updated_payments'] as $client): ?>
                        <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                            • <?php echo get_field('first_name', $client->ID) . ' ' . get_field('last_name', $client->ID); ?> - 
                            <?php 
                            $amount_paid = (float) get_field('amount_paid', $client->ID);
                            $additional_payments = get_field('additional_payments', $client->ID);
                            $total_amount_paid = $amount_paid;
                            if ($additional_payments && is_array($additional_payments)) {
                                foreach ($additional_payments as $payment) {
                                    if (isset($payment['amount']) && is_numeric($payment['amount'])) {
                                        $total_amount_paid += floatval($payment['amount']);
                                    }
                                }
                            }
                            ?>
                            ₪<?php echo number_format($total_amount_paid); ?>
                            <br><span style="font-size: 0.8rem; opacity: 0.8;">
                                עודכן: <?php echo date('d/m/Y', strtotime($client->post_modified)); ?>
                                <?php if (get_field('payment_date', $client->ID)): ?>
                                    | תשלום: <?php echo date('d/m/Y', strtotime(get_field('payment_date', $client->ID))); ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div style="font-size: 0.9rem; opacity: 0.7; font-style: italic;">אין תשלומים שהתעדכנו השבוע</div>
                <?php endif; ?>
            </div>

            <!-- מנטוריות חדשות -->
            <?php if ($weekly_data['new_mentors']): ?>
            <div style="background: rgba(255,255,255,0.1); padding: 20px; border-radius: 15px;">
                <h4 style="margin: 0 0 15px 0; color: #d7dedc; font-size: 1.1rem; display: flex; align-items: center; gap: 8px;">
                    👩‍💼 מנטוריות חדשות השבוע
                </h4>
                <?php foreach ($weekly_data['new_mentors'] as $mentor): ?>
                    <div style="font-size: 0.9rem; opacity: 0.9; margin-bottom: 8px; padding: 8px; background: rgba(255,255,255,0.1); border-radius: 8px;">
                        • <?php echo get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID); ?> 
                        (<?php echo date('d/m', strtotime($mentor->post_date)); ?>)
                    </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // יצירת גרף עוגה למקורות הגעה
    const canvas = document.getElementById('pieChart');
    if (!canvas) return; // בדיקה שהקנבס קיים
    
    const ctx = canvas.getContext('2d');
    
    // נתוני הגרף
    const data = [
        <?php 
        $data_array = array();
        foreach ($stats['referral_sources'] as $source => $count): 
            $label = isset($source_labels[$source]) ? $source_labels[$source] : $source;
            $color = isset($colors[$source]) ? $colors[$source] : '#6b7280';
            $data_array[] = "{ label: '$label', value: $count, color: '$color' }";
        endforeach;
        echo implode(',', $data_array);
        ?>
    ];
    
    // חישוב זוויות
    const total = data.reduce((sum, item) => sum + item.value, 0);
    if (total === 0) return; // אם אין נתונים, אל תצייר
    
    let currentAngle = -Math.PI / 2; // התחלה מלמעלה
    
    const centerX = canvas.width / 2;
    const centerY = canvas.height / 2;
    const radius = Math.min(centerX, centerY) - 10; // קטן יותר לגודל החדש
    
    // ציור הגרף
    data.forEach(item => {
        if (item.value > 0) {
            const sliceAngle = (item.value / total) * 2 * Math.PI;
            
            // ציור הפלח
            ctx.beginPath();
            ctx.moveTo(centerX, centerY);
            ctx.arc(centerX, centerY, radius, currentAngle, currentAngle + sliceAngle);
            ctx.closePath();
            ctx.fillStyle = item.color;
            ctx.fill();
            
            // ציור גבול
            ctx.strokeStyle = '#ffffff';
            ctx.lineWidth = 1.5;
            ctx.stroke();
            
            currentAngle += sliceAngle;
        }
    });
    
    // ציור עיגול פנימי (דונאט)
    ctx.beginPath();
    ctx.arc(centerX, centerY, radius * 0.45, 0, 2 * Math.PI);
    ctx.fillStyle = '#ffffff';
    ctx.fill();
    
    // הוספת טקסט במרכז - קטן יותר
    ctx.fillStyle = '#1f2937';
    ctx.font = 'bold 10px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('סה"כ', centerX, centerY - 4);
    ctx.font = 'bold 14px Arial';
    ctx.fillText(total, centerX, centerY + 6);
});
</script>

<?php get_footer(); ?> 