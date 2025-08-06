<?php
// עמוד דוחות ואנליטיקס מתקדם
if (!defined('ABSPATH')) {
    exit;
}

// פונקציות עזר לדוחות
function get_detailed_reports() {
    $clients = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $reports = array(
        'monthly_income' => array(),
        'referral_breakdown' => array(),
        'weight_loss_stats' => array(),
        'client_retention' => array()
    );
    
    $today = date('Y-m-d');
    
    foreach ($clients as $client) {
        if (!function_exists('get_field')) continue;
        
        $start_date = get_field('start_date', $client->ID);
        $end_date = get_field('end_date', $client->ID);
        $amount_paid = (float) get_field('amount_paid', $client->ID);
        
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
        $start_weight = get_field('start_weight', $client->ID);
        $current_weight = get_field('current_weight', $client->ID);
        
        // הכנסות חודשיות
        if ($start_date) {
            $month = date('Y-m', strtotime($start_date));
            if (!isset($reports['monthly_income'][$month])) {
                $reports['monthly_income'][$month] = 0;
            }
            $reports['monthly_income'][$month] += $total_amount_paid;
        }
        
        // מקורות הגעה
        if ($referral_source) {
            if (!isset($reports['referral_breakdown'][$referral_source])) {
                $reports['referral_breakdown'][$referral_source] = 0;
            }
            $reports['referral_breakdown'][$referral_source]++;
        }
        
        // סטטיסטיקות ירידה במשקל
        if ($start_weight && $current_weight) {
            $weight_lost = $start_weight - $current_weight;
            $reports['weight_loss_stats'][] = $weight_lost;
        }
    }
    
    return $reports;
}

$reports = get_detailed_reports();
?>

<div class="wrap" dir="rtl">
    <style>
        .reports-dashboard {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
        }
        
        .reports-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .report-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        
        .report-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .chart-container {
            height: 300px;
            margin: 20px 0;
        }
        
        .stats-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .stats-table th,
        .stats-table td {
            padding: 10px;
            text-align: right;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .stats-table th {
            background: #f9fafb;
            font-weight: 600;
        }
    </style>

    <div class="reports-dashboard">
        <h1>📊 דוחות ואנליטיקס מתקדמים</h1>
        <p>ניתוח מעמיק של נתוני המתאמנות והביצועים העסקיים</p>

        <div class="reports-grid">
            <!-- הכנסות חודשיות -->
            <div class="report-card">
                <h3 class="report-title">💰 הכנסות חודשיות</h3>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>חודש</th>
                            <th>הכנסות</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reports['monthly_income'] as $month => $income): ?>
                            <tr>
                                <td><?php echo date('m/Y', strtotime($month . '-01')); ?></td>
                                <td>₪<?php echo number_format($income); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- מקורות הגעה -->
            <div class="report-card">
                <h3 class="report-title">📈 פילוח מקורות הגעה</h3>
                <?php 
                $source_labels = array(
                    'instagram' => 'אינסטגרם',
                    'status' => 'סטטוס',
                    'whatsapp' => 'וואצאפ',
                    'referral' => 'המלצה',
                    'mentor' => 'מנטורית'
                );
                ?>
                <table class="stats-table">
                    <thead>
                        <tr>
                            <th>מקור</th>
                            <th>כמות</th>
                            <th>אחוז</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $total_referrals = array_sum($reports['referral_breakdown']);
                        foreach ($reports['referral_breakdown'] as $source => $count): 
                            $percentage = $total_referrals > 0 ? round(($count / $total_referrals) * 100) : 0;
                            $label = isset($source_labels[$source]) ? $source_labels[$source] : $source;
                        ?>
                            <tr>
                                <td><?php echo $label; ?></td>
                                <td><?php echo $count; ?></td>
                                <td><?php echo $percentage; ?>%</td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- סטטיסטיקות ירידה במשקל -->
            <div class="report-card">
                <h3 class="report-title">⚖️ סטטיסטיקות ירידה במשקל</h3>
                <?php 
                $weight_stats = $reports['weight_loss_stats'];
                if (!empty($weight_stats)) {
                    $avg_weight_loss = array_sum($weight_stats) / count($weight_stats);
                    $max_weight_loss = max($weight_stats);
                    $min_weight_loss = min($weight_stats);
                ?>
                    <div style="display: grid; gap: 15px;">
                        <div style="display: flex; justify-content: space-between;">
                            <span>ממוצע ירידה:</span>
                            <strong><?php echo round($avg_weight_loss, 1); ?> ק"ג</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>מקסימום ירידה:</span>
                            <strong><?php echo round($max_weight_loss, 1); ?> ק"ג</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>מינימום ירידה:</span>
                            <strong><?php echo round($min_weight_loss, 1); ?> ק"ג</strong>
                        </div>
                        <div style="display: flex; justify-content: space-between;">
                            <span>סה"כ מתאמנות:</span>
                            <strong><?php echo count($weight_stats); ?></strong>
                        </div>
                    </div>
                <?php } else { ?>
                    <p>אין נתונים זמינים עדיין</p>
                <?php } ?>
            </div>

            <!-- דוח תקופתי -->
            <div class="report-card">
                <h3 class="report-title">📅 סיכום תקופתי</h3>
                <div style="display: grid; gap: 15px;">
                    <div style="display: flex; justify-content: space-between;">
                        <span>מתאמנות פעילות היום:</span>
                        <strong><?php echo count(get_posts(['post_type' => 'clients', 'posts_per_page' => -1])); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>הכנסות החודש:</span>
                        <strong>₪<?php echo number_format(array_sum($reports['monthly_income'])); ?></strong>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <span>ממוצע לקוח:</span>
                        <strong>₪<?php 
                        $total_clients = count(get_posts(['post_type' => 'clients', 'posts_per_page' => -1]));
                        echo $total_clients > 0 ? number_format(array_sum($reports['monthly_income']) / $total_clients) : '0';
                        ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div> 