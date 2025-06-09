<?php
/**
 * עמוד ניהול תשלומים - מבוסס על הלוגיקה מהאפליקציה הקודמת
 */

if (!defined('ABSPATH')) {
    exit;
}

// פונקציות עזר לחישוב תשלומים
function get_payments_data() {
    $clients = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $payments_data = array(
        'total_expected' => 0,
        'total_received' => 0,
        'total_pending' => 0,
        'clients_data' => array(),
        'payment_methods' => array(),
        'monthly_breakdown' => array()
    );
    
    foreach ($clients as $client) {
        $payment_amount = (float) get_field('payment_amount', $client->ID);
        $amount_paid = (float) get_field('amount_paid', $client->ID);
        $payment_method = get_field('payment_method', $client->ID);
        $payment_date = get_field('payment_date', $client->ID);
        $start_date = get_field('start_date', $client->ID);
        $first_name = get_field('first_name', $client->ID);
        $last_name = get_field('last_name', $client->ID);
        $phone = get_field('phone', $client->ID);
        
        $pending_amount = $payment_amount - $amount_paid;
        
        // חישוב סטטיסטיקות כלליות
        $payments_data['total_expected'] += $payment_amount;
        $payments_data['total_received'] += $amount_paid;
        $payments_data['total_pending'] += $pending_amount;
        
        // נתוני לקוח
        $client_data = array(
            'id' => $client->ID,
            'name' => $first_name . ' ' . $last_name,
            'phone' => $phone,
            'payment_amount' => $payment_amount,
            'amount_paid' => $amount_paid,
            'pending_amount' => $pending_amount,
            'payment_method' => $payment_method,
            'payment_date' => $payment_date,
            'start_date' => $start_date,
            'status' => $pending_amount <= 0 ? 'paid' : ($amount_paid > 0 ? 'partial' : 'unpaid')
        );
        
        $payments_data['clients_data'][] = $client_data;
        
        // ספירת אמצעי תשלום
        if ($payment_method && $amount_paid > 0) {
            if (!isset($payments_data['payment_methods'][$payment_method])) {
                $payments_data['payment_methods'][$payment_method] = 0;
            }
            $payments_data['payment_methods'][$payment_method] += $amount_paid;
        }
        
        // פירוק חודשי
        if ($payment_date) {
            $month = date('Y-m', strtotime($payment_date));
            if (!isset($payments_data['monthly_breakdown'][$month])) {
                $payments_data['monthly_breakdown'][$month] = 0;
            }
            $payments_data['monthly_breakdown'][$month] += $amount_paid;
        }
    }
    
    return $payments_data;
}

$payments_data = get_payments_data();

// תוויות לאמצעי תשלום
$payment_method_labels = array(
    'cash' => 'מזומן',
    'credit' => 'כרטיס אשראי',
    'bank_transfer' => 'העברה בנקאית',
    'paypal' => 'PayPal',
    'bit' => 'Bit'
);
?>

<div class="wrap" style="direction: rtl;">
    <h1>💰 ניהול תשלומים</h1>
    
    <!-- סטטיסטיקות כלליות -->
    <div class="payments-overview" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin: 20px 0;">
        <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 20px; border-radius: 12px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.1rem;">💵 סה"כ התקבל</h3>
            <div style="font-size: 2rem; font-weight: bold;">₪<?php echo number_format($payments_data['total_received']); ?></div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706); color: white; padding: 20px; border-radius: 12px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.1rem;">⏳ ממתין לתשלום</h3>
            <div style="font-size: 2rem; font-weight: bold;">₪<?php echo number_format($payments_data['total_pending']); ?></div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #3b82f6, #2563eb); color: white; padding: 20px; border-radius: 12px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.1rem;">📊 סה"כ צפוי</h3>
            <div style="font-size: 2rem; font-weight: bold;">₪<?php echo number_format($payments_data['total_expected']); ?></div>
        </div>
        
        <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed); color: white; padding: 20px; border-radius: 12px; text-align: center;">
            <h3 style="margin: 0 0 10px 0; font-size: 1.1rem;">📈 אחוז גביה</h3>
            <div style="font-size: 2rem; font-weight: bold;">
                <?php echo $payments_data['total_expected'] > 0 ? round(($payments_data['total_received'] / $payments_data['total_expected']) * 100) : 0; ?>%
            </div>
        </div>
    </div>
    
    <!-- פילטרים -->
    <div class="payments-filters" style="background: white; padding: 20px; border-radius: 12px; margin: 20px 0; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <h3>🔍 סינון תשלומים</h3>
        <div style="display: flex; gap: 15px; flex-wrap: wrap; align-items: center;">
            <select id="status-filter" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                <option value="">כל הסטטוסים</option>
                <option value="paid">שולם במלואו</option>
                <option value="partial">שולם חלקית</option>
                <option value="unpaid">לא שולם</option>
            </select>
            
            <select id="method-filter" style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px;">
                <option value="">כל אמצעי התשלום</option>
                <?php foreach ($payment_method_labels as $key => $label): ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>
            
            <input type="text" id="search-filter" placeholder="חיפוש לפי שם..." style="padding: 8px 12px; border: 1px solid #d1d5db; border-radius: 6px; min-width: 200px;">
            
            <button onclick="clearFilters()" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">
                נקה סינון
            </button>
        </div>
    </div>
    
    <!-- טבלת תשלומים -->
    <div class="payments-table-container" style="background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
        <table class="wp-list-table widefat fixed striped" id="payments-table">
            <thead>
                <tr>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">👤 שם מתאמנת</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">📞 טלפון</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">💰 סכום לתשלום</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">✅ שולם</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">⏳ נותר</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">💳 אמצעי</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">📅 תאריך</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">🎯 סטטוס</th>
                    <th style="padding: 15px; background: #f8fafc; font-weight: bold;">⚙️ פעולות</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments_data['clients_data'] as $client): ?>
                    <tr class="payment-row" 
                        data-status="<?php echo $client['status']; ?>" 
                        data-method="<?php echo $client['payment_method']; ?>" 
                        data-name="<?php echo strtolower($client['name']); ?>">
                        
                        <td style="padding: 15px; font-weight: bold;">
                            <a href="<?php echo get_edit_post_link($client['id']); ?>" style="color: #3b82f6; text-decoration: none;">
                                <?php echo $client['name']; ?>
                            </a>
                        </td>
                        
                        <td style="padding: 15px;">
                            <?php if ($client['phone']): ?>
                                <a href="tel:<?php echo $client['phone']; ?>" style="color: #059669; text-decoration: none;">
                                    <?php echo $client['phone']; ?>
                                </a>
                            <?php endif; ?>
                        </td>
                        
                        <td style="padding: 15px; font-weight: bold; color: #1f2937;">
                            ₪<?php echo number_format($client['payment_amount']); ?>
                        </td>
                        
                        <td style="padding: 15px; color: #059669; font-weight: bold;">
                            ₪<?php echo number_format($client['amount_paid']); ?>
                        </td>
                        
                        <td style="padding: 15px; color: <?php echo $client['pending_amount'] > 0 ? '#dc2626' : '#059669'; ?>; font-weight: bold;">
                            ₪<?php echo number_format($client['pending_amount']); ?>
                        </td>
                        
                        <td style="padding: 15px;">
                            <?php echo $client['payment_method'] ? $payment_method_labels[$client['payment_method']] : '-'; ?>
                        </td>
                        
                        <td style="padding: 15px;">
                            <?php echo $client['payment_date'] ? date('d/m/Y', strtotime($client['payment_date'])) : '-'; ?>
                        </td>
                        
                        <td style="padding: 15px;">
                            <?php
                            $status_colors = array(
                                'paid' => '#059669',
                                'partial' => '#f59e0b',
                                'unpaid' => '#dc2626'
                            );
                            $status_labels = array(
                                'paid' => '✅ שולם',
                                'partial' => '⚠️ חלקי',
                                'unpaid' => '❌ לא שולם'
                            );
                            ?>
                            <span style="color: <?php echo $status_colors[$client['status']]; ?>; font-weight: bold;">
                                <?php echo $status_labels[$client['status']]; ?>
                            </span>
                        </td>
                        
                        <td style="padding: 15px;">
                            <div style="display: flex; gap: 8px;">
                                <a href="<?php echo admin_url('admin.php?page=add-client-form&edit=' . $client['id']); ?>" 
                                   style="padding: 6px 12px; background: #3b82f6; color: white; text-decoration: none; border-radius: 4px; font-size: 0.875rem;">
                                    ✏️ ערוך
                                </a>
                                <?php if ($client['phone']): ?>
                                    <a href="tel:<?php echo $client['phone']; ?>" 
                                       style="padding: 6px 12px; background: #059669; color: white; text-decoration: none; border-radius: 4px; font-size: 0.875rem;">
                                        📞 התקשר
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    
    <!-- גרפים ואנליטיקס -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin: 30px 0;">
        <!-- פירוק לפי אמצעי תשלום -->
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>💳 פירוק לפי אמצעי תשלום</h3>
            <div id="payment-methods-chart" style="height: 300px;"></div>
        </div>
        
        <!-- הכנסות חודשיות -->
        <div style="background: white; padding: 20px; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <h3>📊 הכנסות חודשיות</h3>
            <div id="monthly-income-chart" style="height: 300px;"></div>
        </div>
    </div>
</div>

<script>
// פונקציות סינון
function filterTable() {
    const statusFilter = document.getElementById('status-filter').value;
    const methodFilter = document.getElementById('method-filter').value;
    const searchFilter = document.getElementById('search-filter').value.toLowerCase();
    const rows = document.querySelectorAll('.payment-row');
    
    rows.forEach(row => {
        const status = row.dataset.status;
        const method = row.dataset.method;
        const name = row.dataset.name;
        
        let show = true;
        
        if (statusFilter && status !== statusFilter) show = false;
        if (methodFilter && method !== methodFilter) show = false;
        if (searchFilter && !name.includes(searchFilter)) show = false;
        
        row.style.display = show ? '' : 'none';
    });
}

function clearFilters() {
    document.getElementById('status-filter').value = '';
    document.getElementById('method-filter').value = '';
    document.getElementById('search-filter').value = '';
    filterTable();
}

// הוספת event listeners
document.getElementById('status-filter').addEventListener('change', filterTable);
document.getElementById('method-filter').addEventListener('change', filterTable);
document.getElementById('search-filter').addEventListener('input', filterTable);

// נתונים לגרפים
const paymentMethodsData = <?php echo json_encode($payments_data['payment_methods']); ?>;
const monthlyData = <?php echo json_encode($payments_data['monthly_breakdown']); ?>;
const methodLabels = <?php echo json_encode($payment_method_labels); ?>;

// יצירת גרף אמצעי תשלום
if (Object.keys(paymentMethodsData).length > 0) {
    const methodsChart = document.getElementById('payment-methods-chart');
    let methodsHtml = '';
    
    Object.keys(paymentMethodsData).forEach(method => {
        const amount = paymentMethodsData[method];
        const percentage = (amount / <?php echo $payments_data['total_received']; ?>) * 100;
        const label = methodLabels[method] || method;
        
        methodsHtml += `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="font-weight: bold;">${label}</span>
                <div style="text-align: left;">
                    <div style="font-weight: bold; color: #059669;">₪${amount.toLocaleString()}</div>
                    <div style="font-size: 0.875rem; color: #6b7280;">${percentage.toFixed(1)}%</div>
                </div>
            </div>
        `;
    });
    
    methodsChart.innerHTML = methodsHtml;
}

// יצירת גרף הכנסות חודשיות
if (Object.keys(monthlyData).length > 0) {
    const monthlyChart = document.getElementById('monthly-income-chart');
    let monthlyHtml = '';
    
    // מיון לפי חודש
    const sortedMonths = Object.keys(monthlyData).sort();
    
    sortedMonths.forEach(month => {
        const amount = monthlyData[month];
        const monthName = new Date(month + '-01').toLocaleDateString('he-IL', { year: 'numeric', month: 'long' });
        
        monthlyHtml += `
            <div style="display: flex; justify-content: space-between; align-items: center; padding: 10px 0; border-bottom: 1px solid #e5e7eb;">
                <span style="font-weight: bold;">${monthName}</span>
                <div style="font-weight: bold; color: #3b82f6;">₪${amount.toLocaleString()}</div>
            </div>
        `;
    });
    
    monthlyChart.innerHTML = monthlyHtml;
}
</script>

<style>
.payments-table-container {
    overflow-x: auto;
}

@media (max-width: 768px) {
    .payments-overview {
        grid-template-columns: 1fr 1fr !important;
    }
    
    .payments-filters > div {
        flex-direction: column !important;
        align-items: stretch !important;
    }
    
    .payments-filters select,
    .payments-filters input {
        width: 100% !important;
        min-width: auto !important;
    }
    
    #payments-table th,
    #payments-table td {
        padding: 8px !important;
        font-size: 0.875rem;
    }
}
</style> 