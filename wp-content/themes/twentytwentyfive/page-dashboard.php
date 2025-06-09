<?php
/**
 * Template Name: דשבורד CRM
 * עמוד דשבורד CRM מותאם לתצוגה ציבורית
 */

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
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 60px 40px;
        text-align: center;
        border-radius: 20px;
        margin-bottom: 40px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.2);
    }
    
    .dashboard-hero h1 {
        font-size: 3rem;
        margin: 0 0 20px 0;
        font-weight: 700;
    }
    
    .dashboard-hero p {
        font-size: 1.2rem;
        opacity: 0.9;
        max-width: 600px;
        margin: 0 auto;
    }
    
    .stats-overview {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 25px;
        margin-bottom: 40px;
    }
    
    .stat-card {
        background: white;
        padding: 30px;
        border-radius: 15px;
        text-align: center;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border-right: 5px solid;
        transition: transform 0.3s, box-shadow 0.3s;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .stat-card.active { border-right-color: #3b82f6; }
    .stat-card.ending { border-right-color: #f59e0b; }
    .stat-card.frozen { border-right-color: #8b5cf6; }
    .stat-card.unpaid { border-right-color: #ef4444; }
    .stat-card.income { border-right-color: #10b981; }
    
    .stat-icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    
    .stat-number {
        font-size: 3rem;
        font-weight: bold;
        color: #1f2937;
        margin-bottom: 10px;
    }
    
    .stat-label {
        color: #6b7280;
        font-size: 1rem;
        font-weight: 500;
    }
    
    .dashboard-sections {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .dashboard-section {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .section-title {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
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
        color: #1f2937;
    }
    
    .client-phone {
        color: #3b82f6;
        font-size: 0.875rem;
        text-decoration: none;
    }
    
    .client-date {
        color: #6b7280;
        font-size: 0.875rem;
    }
    
    .referral-chart {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        margin-bottom: 40px;
    }
    
    .chart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .chart-item:last-child {
        border-bottom: none;
    }
    
    .chart-bar {
        background: #3b82f6;
        height: 8px;
        border-radius: 4px;
        margin: 8px 0;
        transition: width 0.5s ease;
    }
    
    .quick-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-top: 30px;
    }
    
    .action-button {
        display: block;
        padding: 20px;
        background: #3b82f6;
        color: white;
        text-decoration: none;
        border-radius: 12px;
        text-align: center;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
    }
    
    .action-button:hover {
        background: #2563eb;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.4);
        color: white;
    }
    
    .action-button.secondary {
        background: #f3f4f6;
        color: #374151;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .action-button.secondary:hover {
        background: #e5e7eb;
        color: #374151;
    }
    
    @media (max-width: 768px) {
        .dashboard-sections {
            grid-template-columns: 1fr;
        }
        .stats-overview {
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        }
        .dashboard-hero h1 {
            font-size: 2rem;
        }
        .chart-responsive-container {
            grid-template-columns: 1fr !important;
            gap: 20px !important;
        }
        .chart-canvas-container {
            display: flex;
            justify-content: center;
        }
        .chart-canvas-container canvas {
            max-width: 250px;
            height: auto;
        }
    }
    
    /* סטיילים לגרף עוגה רספונסיבי */
    .chart-responsive-container {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        align-items: center;
    }
    
    .chart-canvas-container {
        position: relative;
        height: 300px;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .chart-legend {
        display: flex;
        flex-direction: column;
        gap: 15px;
    }
    
    .legend-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .legend-item:last-child {
        border-bottom: none;
    }
    
    .legend-info {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .legend-color {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        flex-shrink: 0;
    }
    
    .legend-stats {
        text-align: left;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .legend-count {
        font-weight: 600;
        font-size: 0.95rem;
    }
    
    .legend-percentage {
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    /* סגנונות לבאנרים בשני עמודות */
    .two-column-banners {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 30px;
        margin-bottom: 40px;
    }
    
    .banner-half {
        background: white;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
    }
    
    .banner-half .chart-responsive-container {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .banner-half .chart-canvas-container {
        height: 200px;
    }
    
    /* סגנונות לבאנר מתאמנות שסיימו */
    .finished-clients-banner {
        border-right: 5px solid #6366f1;
    }
    
    .finished-summary {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        margin-bottom: 25px;
    }
    
    .summary-stat {
        text-align: center;
        padding: 20px;
        background: #f8fafc;
        border-radius: 12px;
        transition: transform 0.3s;
    }
    
    .summary-stat:hover {
        transform: translateY(-2px);
    }
    
    .summary-stat.alert {
        background: #fef3c7;
        border: 1px solid #f59e0b;
    }
    
    .summary-stat .stat-icon {
        font-size: 2rem;
        margin-bottom: 10px;
    }
    
    .summary-stat .stat-number {
        font-size: 2rem;
        font-weight: 700;
        color: #1f2937;
        margin-bottom: 5px;
    }
    
    .summary-stat .stat-label {
        color: #6b7280;
        font-size: 0.9rem;
        font-weight: 500;
    }
    
    .recent-finished h4 {
        color: #374151;
        margin-bottom: 15px;
        font-size: 1.1rem;
    }
    
    .finished-client-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 10px 0;
        border-bottom: 1px solid #f3f4f6;
    }
    
    .finished-client-item:last-child {
        border-bottom: none;
    }
    
    .finished-client-item .client-name {
        font-weight: 600;
        color: #1f2937;
    }
    
    .finished-client-item .finish-date {
        color: #6b7280;
        font-size: 0.85rem;
    }
    
    .banner-action {
        margin-top: 25px;
        text-align: center;
    }
    
    .action-button.primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
        padding: 15px 25px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        display: inline-block;
        transition: all 0.3s;
        box-shadow: 0 4px 15px rgba(99, 102, 241, 0.3);
    }
    
    .action-button.primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
        color: white;
    }
    
    @media (max-width: 768px) {
        .two-column-banners {
            grid-template-columns: 1fr;
        }
        
        .finished-summary {
            grid-template-columns: 1fr;
        }
        
        .banner-half .chart-responsive-container {
            grid-template-columns: 1fr !important;
        }
    }
</style>

<div class="crm-main-dashboard">
    <div class="dashboard-hero">
        <h1>🌟 מערכת CRM תזונה</h1>
        <p>ברוכה הבאה! הנה סקירה כללית של המתאמנות שלך</p>
    </div>

    <?php
    // פונקציות עזר לחישוב סטטיסטיקות
    function get_public_clients_stats() {
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
            'total_income' => 0,
            'referral_sources' => array()
        );
        
        $today = date('Y-m-d');
        $two_weeks_later = date('Y-m-d', strtotime('+14 days'));
        
        foreach ($clients as $client) {
            if (!function_exists('get_field')) continue;
            
            $end_date = get_field('end_date', $client->ID);
            $is_frozen = get_field('is_frozen', $client->ID);
            $amount_paid = (float) get_field('amount_paid', $client->ID);
            $payment_amount = (float) get_field('payment_amount', $client->ID);
            $referral_source = get_field('referral_source', $client->ID);
            
            // ספירת פעילים
            if ($end_date >= $today && !$is_frozen) {
                $stats['active']++;
            }
            
            // ספירת מסיימים בקרוב
            if ($end_date <= $two_weeks_later && $end_date >= $today && !$is_frozen) {
                $stats['ending_soon']++;
            }
            
            // ספירת בהקפאה
            if ($is_frozen) {
                $stats['frozen']++;
            }
            
            // ספירת סטטוס תשלום
            if ($amount_paid == 0) {
                $stats['unpaid']++;
            } else {
                $stats['paid']++;
            }
            
            // חישוב הכנסות
            $stats['total_income'] += $amount_paid;
            
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
        $two_weeks_later = date('Y-m-d', strtotime('+14 days'));
        
        return get_posts(array(
            'post_type' => 'clients',
            'posts_per_page' => 5,
            'post_status' => 'publish',
            'meta_query' => array(
                array(
                    'key' => 'end_date',
                    'value' => array($today, $two_weeks_later),
                    'compare' => 'BETWEEN',
                    'type' => 'DATE'
                ),
                array(
                    'key' => 'is_frozen',
                    'value' => false,
                    'compare' => '='
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
                array(
                    'key' => 'is_frozen',
                    'value' => true,
                    'compare' => '='
                )
            )
        ));
    }

    $stats = get_public_clients_stats();
    $ending_soon = get_public_ending_soon_clients();
    $frozen_clients = get_public_frozen_clients();
    ?>

    <!-- סטטיסטיקות כלליות -->
    <div class="stats-overview">
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=active'; ?>" class="stat-card active" style="text-decoration: none; color: inherit;">
            <div class="stat-icon">👥</div>
            <div class="stat-number"><?php echo $stats['active']; ?></div>
            <div class="stat-label">מתאמנות פעילות</div>
        </a>
        
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=ending'; ?>" class="stat-card ending" style="text-decoration: none; color: inherit;">
            <div class="stat-icon">⏰</div>
            <div class="stat-number"><?php echo $stats['ending_soon']; ?></div>
            <div class="stat-label">מסיימות בקרוב</div>
        </a>
        
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=frozen'; ?>" class="stat-card frozen" style="text-decoration: none; color: inherit;">
            <div class="stat-icon">⏸️</div>
            <div class="stat-number"><?php echo $stats['frozen']; ?></div>
            <div class="stat-label">בהקפאה</div>
        </a>
        
        <a href="<?php echo (get_post_type_archive_link('clients') ?: home_url('/clients/')) . '?filter=unpaid'; ?>" class="stat-card unpaid" style="text-decoration: none; color: inherit;">
            <div class="stat-icon">💳</div>
            <div class="stat-number"><?php echo $stats['unpaid']; ?></div>
            <div class="stat-label">ממתינות לתשלום</div>
        </a>
        
        <?php if (current_user_can('manage_options')): ?>
            <a href="<?php echo admin_url('admin.php?page=payments-management'); ?>" class="stat-card income" style="text-decoration: none; color: inherit;">
                <div class="stat-icon">💰</div>
                <div class="stat-number">₪<?php echo number_format($stats['total_income']); ?></div>
                <div class="stat-label">הכנסות החודש</div>
            </a>
        <?php else: ?>
            <div class="stat-card income">
                <div class="stat-icon">💰</div>
                <div class="stat-number">₪<?php echo number_format($stats['total_income']); ?></div>
                <div class="stat-label">הכנסות החודש</div>
            </div>
        <?php endif; ?>
    </div>

    <!-- מתאמנות שמסיימות בקרוב ובהקפאה -->
    <div class="dashboard-sections">
        <!-- מתאמנות שמסיימות בקרוב -->
        <div class="dashboard-section">
            <h3 class="section-title">
                ⏰ מתאמנות שמסיימות בקרוב
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
                    ⏸️ מתאמנות בהקפאה
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

    <!-- בקטה חדשה: מקורות הגעה ומתאמנות שסיימו -->
    <div class="two-column-banners">
        <!-- סטטיסטיקות מקורות הגעה עם גרף עוגה רספונסיבי -->
        <div class="banner-half referral-chart">
            <h3 class="section-title">
                📈 מקורות הגעה של מתאמנות
            </h3>
            
            <div class="chart-responsive-container">
                <!-- גרף עוגה -->
                <div class="chart-canvas-container">
                    <canvas id="pieChart" width="200" height="200"></canvas>
                </div>
                
                <!-- רשימת מקורות -->
                <div class="chart-legend">
                    <?php 
                    $source_labels = array(
                        'instagram' => 'אינסטגרם',
                        'status' => 'סטטוס',
                        'whatsapp' => 'וואצאפ',
                        'referral' => 'המלצה',
                        'mentor' => 'מנטורית'
                    );
                    
                    $colors = array(
                        'instagram' => '#E1306C',
                        'status' => '#25D366',
                        'whatsapp' => '#25D366',
                        'referral' => '#3b82f6',
                        'mentor' => '#8b5cf6'
                    );
                    
                    $total_referrals = array_sum($stats['referral_sources']);
                    
                    foreach ($stats['referral_sources'] as $source => $count): 
                        $percentage = $total_referrals > 0 ? round(($count / $total_referrals) * 100) : 0;
                        $label = isset($source_labels[$source]) ? $source_labels[$source] : $source;
                        $color = isset($colors[$source]) ? $colors[$source] : '#6b7280';
                    ?>
                        <div class="legend-item">
                            <div class="legend-info">
                                <div class="legend-color" style="background: <?php echo $color; ?>;"></div>
                                <strong><?php echo $label; ?></strong>
                            </div>
                            <div class="legend-stats">
                                <div class="legend-count"><?php echo $count; ?> מתאמנות</div>
                                <div class="legend-percentage"><?php echo $percentage; ?>%</div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>

        <!-- באנר מתאמנות שסיימו -->
        <div class="banner-half finished-clients-banner">
            <h3 class="section-title">
                📝 מתאמנות שסיימו
            </h3>
            
            <div class="finished-stats">
                <?php 
                $finished_clients_count = count(get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'end_date',
                            'value' => date('Y-m-d'),
                            'compare' => '<',
                            'type' => 'DATE'
                        ),
                        array(
                            'key' => 'is_frozen',
                            'value' => false,
                            'compare' => '='
                        )
                    )
                )));
                
                $need_follow_up = 0;
                $recent_finished = get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => 3,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'end_date',
                            'value' => date('Y-m-d'),
                            'compare' => '<',
                            'type' => 'DATE'
                        ),
                        array(
                            'key' => 'is_frozen',
                            'value' => false,
                            'compare' => '='
                        )
                    ),
                    'meta_key' => 'end_date',
                    'orderby' => 'meta_value',
                    'order' => 'DESC'
                ));
                
                // ספירת מתאמנות שדורשות מעקב
                foreach (get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => -1,
                    'post_status' => 'publish',
                    'meta_query' => array(
                        array(
                            'key' => 'end_date',
                            'value' => date('Y-m-d'),
                            'compare' => '<',
                            'type' => 'DATE'
                        )
                    )
                )) as $finished_client) {
                    $last_contact = get_field('last_contact_date', $finished_client->ID);
                    if (!$last_contact || $last_contact < date('Y-m-d', strtotime('-30 days'))) {
                        $need_follow_up++;
                    }
                }
                ?>
                
                <div class="finished-summary">
                    <div class="summary-stat">
                        <div class="stat-icon">📊</div>
                        <div class="stat-number"><?php echo $finished_clients_count; ?></div>
                        <div class="stat-label">סה"כ סיימו</div>
                    </div>
                    
                    <div class="summary-stat alert">
                        <div class="stat-icon">📞</div>
                        <div class="stat-number"><?php echo $need_follow_up; ?></div>
                        <div class="stat-label">דורשות מעקב</div>
                    </div>
                </div>
                
                <div class="recent-finished">
                    <h4>📋 סיימו לאחרונה:</h4>
                    <?php if ($recent_finished): ?>
                        <?php foreach ($recent_finished as $client): ?>
                            <div class="finished-client-item">
                                <div class="client-name">
                                    <?php echo get_field('first_name', $client->ID) . ' ' . get_field('last_name', $client->ID); ?>
                                </div>
                                <div class="finish-date">
                                    סיום: <?php echo date('d/m/Y', strtotime(get_field('end_date', $client->ID))); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>אין מתאמנות שסיימו לאחרונה</p>
                    <?php endif; ?>
                </div>
                
                <div class="banner-action">
                    <a href="<?php echo home_url('/finished-clients/'); ?>" class="action-button primary">
                        👥 צפה בכל המתאמנות שסיימו
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- פעולות מהירות -->
    <div class="dashboard-section">
        <h3 class="section-title">
            🔗 קישורים מהירים
        </h3>
        <div class="quick-actions">
            <a href="<?php echo get_post_type_archive_link('clients') ?: home_url('/clients/'); ?>" class="action-button">
                👥 צפה בכל המתאמנות
            </a>
            <a href="<?php echo home_url('/finished-clients/'); ?>" class="action-button secondary">
                📝 מתאמנות שסיימו
            </a>
            <a href="<?php echo get_post_type_archive_link('mentors') ?: home_url('/mentors/'); ?>" class="action-button secondary">
                👩‍💼 מנטוריות
            </a>
            <?php if (current_user_can('manage_options')): ?>
                <a href="<?php echo admin_url('admin.php?page=payments-management'); ?>" class="action-button">
                    💰 ניהול תשלומים
                </a>
                <a href="<?php echo admin_url('admin.php?page=crm-dashboard'); ?>" class="action-button">
                    ⚙️ ניהול מערכת
                </a>
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
    const radius = Math.min(centerX, centerY) - 15; // קטן יותר לגודל החדש
    
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
    ctx.font = 'bold 12px Arial';
    ctx.textAlign = 'center';
    ctx.fillText('סה"כ', centerX, centerY - 6);
    ctx.font = 'bold 16px Arial';
    ctx.fillText(total, centerX, centerY + 8);
});
</script>

<?php get_footer(); ?> 