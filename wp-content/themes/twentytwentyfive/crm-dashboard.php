<?php
// דשבורד CRM מותאם לתזונאית
if (!defined('ABSPATH')) {
    exit;
}

// פונקציות עזר לחישוב סטטיסטיקות
function get_clients_stats() {
    $clients = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $stats = array(
        'total' => count($clients),
        'active' => 0,
        'ending_soon' => 0,
        'frozen' => 0,
        'paid' => 0,
        'unpaid' => 0,
        'partial' => 0,
        'total_income' => 0
    );
    
    $today = date('Y-m-d');
    $one_week_later = date('Y-m-d', strtotime('+7 days')); // שבוע מהיום
    
    foreach ($clients as $client) {
        $end_date = get_field('end_date', $client->ID);
        $is_frozen = get_field('is_frozen', $client->ID);
        $amount_paid = (float) get_field('amount_paid', $client->ID);
        $payment_amount = (float) get_field('payment_amount', $client->ID);
        
        // ספירת פעילים
        if ($end_date >= $today && !$is_frozen) {
            $stats['active']++;
        }
        
        // ספירת מסיימים בקרוב (שבוע אחד)
        if ($end_date <= $one_week_later && $end_date >= $today && !$is_frozen) {
            $stats['ending_soon']++;
        }
        
        // ספירת בהקפאה
        if ($is_frozen) {
            $stats['frozen']++;
        }
        
        // ספירת סטטוס תשלום
        if ($amount_paid == 0) {
            $stats['unpaid']++;
        } elseif ($payment_amount && $amount_paid > 0 && $amount_paid < $payment_amount) {
            $stats['partial']++;
        } else {
            $stats['paid']++;
        }
        
        // חישוב הכנסות
        $stats['total_income'] += $amount_paid;
    }
    
    return $stats;
}

function get_ending_soon_clients() {
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
            )
        ),
        'meta_key' => 'end_date',
        'orderby' => 'meta_value',
        'order' => 'ASC'
    ));
}

function get_frozen_clients() {
    return get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => 5,
        'post_status' => 'publish',
        'meta_query' => array(
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
        )
    ));
}

$stats = get_clients_stats();
$ending_soon = get_ending_soon_clients();
$frozen_clients = get_frozen_clients();
?>

<div class="wrap" dir="rtl">
    <style>
        .crm-dashboard {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            text-align: right;
        }
        .crm-stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .crm-stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            border-right: 4px solid;
        }
        .crm-stat-card.active { border-right-color: #3b82f6; }
        .crm-stat-card.ending { border-right-color: #f59e0b; }
        .crm-stat-card.frozen { border-right-color: #8b5cf6; }
        .crm-stat-card.unpaid { border-right-color: #ef4444; }
        .crm-stat-card.income { border-right-color: #10b981; }
        
        .crm-stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: #1f2937;
            line-height: 1;
        }
        .crm-stat-label {
            color: #6b7280;
            font-size: 0.875rem;
            margin-top: 8px;
        }
        
        .crm-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .crm-section h3 {
            margin-top: 0;
            color: #1f2937;
            border-bottom: 2px solid #e5e7eb;
            padding-bottom: 10px;
        }
        
        .client-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .client-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        
        .client-item:last-child {
            border-bottom: none;
        }
        
        .client-name {
            font-weight: 500;
            color: #1f2937;
        }
        
        .client-phone {
            color: #3b82f6;
            text-decoration: none;
            font-size: 0.875rem;
        }
        
        .client-date {
            color: #6b7280;
            font-size: 0.875rem;
        }
        
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        
        .quick-action-btn {
            display: block;
            padding: 15px 20px;
            background: #3b82f6;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            text-align: center;
            font-weight: 500;
            transition: background 0.2s;
        }
        
        .quick-action-btn:hover {
            background: #2563eb;
            color: white;
        }
        
        .quick-action-btn.secondary {
            background: #6b7280;
        }
        
        .quick-action-btn.secondary:hover {
            background: #4b5563;
        }
    </style>

    <div class="crm-dashboard">
        <h1>📊 דשבורד CRM - מרים קרישבסקי</h1>
        <p>ברוכה הבאה! הנה סקירה כללית של המתאמנות שלך</p>

        <!-- סטטיסטיקות כלליות -->
        <div class="crm-stats-grid">
            <div class="crm-stat-card active">
                <div class="crm-stat-number"><?php echo $stats['active']; ?></div>
                <div class="crm-stat-label">מתאמנות פעילות</div>
            </div>
            
            <div class="crm-stat-card ending">
                <div class="crm-stat-number"><?php echo $stats['ending_soon']; ?></div>
                <div class="crm-stat-label">מסיימות בקרוב</div>
            </div>
            
            <div class="crm-stat-card frozen">
                <div class="crm-stat-number"><?php echo $stats['frozen']; ?></div>
                <div class="crm-stat-label">בהקפאה</div>
            </div>
            
            <div class="crm-stat-card unpaid">
                <div class="crm-stat-number"><?php echo $stats['unpaid']; ?></div>
                <div class="crm-stat-label">ממתינות לתשלום</div>
            </div>
            
            <div class="crm-stat-card income">
                <div class="crm-stat-number">₪<?php echo number_format($stats['total_income']); ?></div>
                <div class="crm-stat-label">הכנסות החודש</div>
            </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
            <!-- מתאמנות שמסיימות בקרוב -->
            <div class="crm-section">
                <h3>⏰ מתאמנות שמסיימות בקרוב</h3>
                <?php if ($ending_soon): ?>
                    <ul class="client-list">
                        <?php foreach ($ending_soon as $client): ?>
                            <li class="client-item">
                                <div>
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
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>אין מתאמנות שמסיימות בקרוב 🎉</p>
                <?php endif; ?>
            </div>

            <!-- מתאמנות בהקפאה -->
            <div class="crm-section">
                <h3>⏸️ מתאמנות בהקפאה</h3>
                <?php if ($frozen_clients): ?>
                    <ul class="client-list">
                        <?php foreach ($frozen_clients as $client): ?>
                            <li class="client-item">
                                <div>
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
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <p>אין מתאמנות בהקפאה כרגע</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- פעולות מהירות -->
        <div class="crm-section">
            <h3>⚡ פעולות מהירות</h3>
            <div class="quick-actions">
                <a href="<?php echo admin_url('admin.php?page=add-client-form'); ?>" class="quick-action-btn">
                    ➕ הוסף מתאמנת חדשה
                </a>
                <a href="<?php echo admin_url('edit.php?post_type=clients'); ?>" class="quick-action-btn secondary">
                    👥 צפה בכל המתאמנות
                </a>
                <a href="<?php echo admin_url('admin.php?page=add-mentor-form'); ?>" class="quick-action-btn secondary">
                    👩‍💼 הוסף מנטורית
                </a>
                <a href="<?php echo admin_url('admin.php?page=payments-management'); ?>" class="quick-action-btn secondary">
                    💰 ניהול תשלומים
                </a>
            </div>
        </div>
    </div>
</div> 