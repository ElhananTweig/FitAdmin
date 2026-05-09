<?php
/**
 * עמוד ניהול תשלומים
 */

if (!defined('ABSPATH')) {
    exit;
}

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

        $total_amount_paid = $amount_paid;
        $additional_payments = get_field('additional_payments', $client->ID);
        if ($additional_payments && is_array($additional_payments)) {
            foreach ($additional_payments as $payment) {
                if (isset($payment['amount']) && is_numeric($payment['amount'])) {
                    $total_amount_paid += floatval($payment['amount']);
                }
            }
        }

        $payment_method = get_field('payment_method', $client->ID);
        $payment_date = get_field('payment_date', $client->ID);
        $start_date = get_field('start_date', $client->ID);
        $first_name = get_field('first_name', $client->ID);
        $last_name = get_field('last_name', $client->ID);
        $phone = get_field('phone', $client->ID);

        $pending_amount = $payment_amount - $total_amount_paid;

        $payments_data['total_expected'] += $payment_amount;
        $payments_data['total_received'] += $total_amount_paid;
        $payments_data['total_pending'] += $pending_amount;

        $client_data = array(
            'id' => $client->ID,
            'name' => $first_name . ' ' . $last_name,
            'phone' => $phone,
            'payment_amount' => $payment_amount,
            'amount_paid' => $total_amount_paid,
            'pending_amount' => $pending_amount,
            'payment_method' => $payment_method,
            'payment_date' => $payment_date,
            'start_date' => $start_date,
            'status' => $pending_amount <= 0 ? 'paid' : ($total_amount_paid > 0 ? 'partial' : 'unpaid')
        );

        $payments_data['clients_data'][] = $client_data;

        if ($payment_method && $total_amount_paid > 0) {
            if (!isset($payments_data['payment_methods'][$payment_method])) {
                $payments_data['payment_methods'][$payment_method] = 0;
            }
            $payments_data['payment_methods'][$payment_method] += $total_amount_paid;
        }

        if ($payment_date) {
            $month = date('Y-m', strtotime($payment_date));
            if (!isset($payments_data['monthly_breakdown'][$month])) {
                $payments_data['monthly_breakdown'][$month] = 0;
            }
            $payments_data['monthly_breakdown'][$month] += $total_amount_paid;
        }
    }

    return $payments_data;
}

$payments_data = get_payments_data();

// חישוב עסקאות החודש והכנסות צפויות החודש
$current_month = date('Y-m');
$monthly_deals = 0;
$monthly_expected_income = 0;
$monthly_deals_clients   = array(); // פירוט עסקאות החודש
$monthly_expected_clients = array(); // פירוט הכנסות צפויות

$all_clients_for_monthly = get_posts(array(
    'post_type' => 'clients',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

foreach ($all_clients_for_monthly as $c) {
    $payment_amount_c = (float) get_field('payment_amount', $c->ID);
    $amount_paid_c    = (float) get_field('amount_paid', $c->ID);
    $first_name_c     = get_field('first_name', $c->ID);
    $last_name_c      = get_field('last_name', $c->ID);
    $client_name_c    = trim($first_name_c . ' ' . $last_name_c);

    // עסקאות החודש
    $post_month = date('Y-m', strtotime($c->post_date));
    if ($post_month === $current_month && $payment_amount_c > 0) {
        $monthly_deals += $payment_amount_c;
        $monthly_deals_clients[] = array(
            'name'   => $client_name_c,
            'amount' => $payment_amount_c,
        );
    }

    // הכנסות צפויות החודש
    $payment_date_primary  = get_field('payment_date', $c->ID);
    $installments_primary  = intval(get_field('installments', $c->ID));
    $client_expected       = 0;

    if ($payment_date_primary && $amount_paid_c > 0) {
        if ($installments_primary > 1) {
            $pay_month = date('Y-m', strtotime($payment_date_primary));
            $diff = (intval(date('Y')) - intval(substr($pay_month, 0, 4))) * 12
                  + (intval(date('m')) - intval(substr($pay_month, 5, 2)));
            if ($diff >= 0 && $diff < $installments_primary) {
                $client_expected += $amount_paid_c / $installments_primary;
            }
        } else {
            if (date('Y-m', strtotime($payment_date_primary)) === $current_month) {
                $client_expected += $amount_paid_c;
            }
        }
    }

    $additional_payments_c = get_field('additional_payments', $c->ID);
    if ($additional_payments_c && is_array($additional_payments_c)) {
        foreach ($additional_payments_c as $ap) {
            $ap_amount = isset($ap['amount']) ? floatval($ap['amount']) : 0;
            $ap_date   = isset($ap['date'])   ? $ap['date']             : '';
            $ap_inst   = isset($ap['installments']) ? intval($ap['installments']) : 1;
            if (!$ap_amount || !$ap_date) continue;
            if ($ap_inst > 1) {
                $ap_month = date('Y-m', strtotime($ap_date));
                $diff = (intval(date('Y')) - intval(substr($ap_month, 0, 4))) * 12
                      + (intval(date('m')) - intval(substr($ap_month, 5, 2)));
                if ($diff >= 0 && $diff < $ap_inst) {
                    $client_expected += $ap_amount / $ap_inst;
                }
            } else {
                if (date('Y-m', strtotime($ap_date)) === $current_month) {
                    $client_expected += $ap_amount;
                }
            }
        }
    }

    if ($client_expected > 0) {
        $monthly_expected_income += $client_expected;
        $monthly_expected_clients[] = array(
            'name'   => $client_name_c,
            'amount' => $client_expected,
        );
    }
}

// מיון לפי סכום יורד
usort($monthly_deals_clients,    fn($a, $b) => $b['amount'] <=> $a['amount']);
usort($monthly_expected_clients, fn($a, $b) => $b['amount'] <=> $a['amount']);

$payment_method_labels = array(
    'cash' => 'מזומן',
    'credit' => 'כרטיס אשראי',
    'bank_transfer' => 'העברה בנקאית',
    'paypal' => 'PayPal',
    'bit' => 'Bit'
);
?>

<style>
.crm-payments-page {
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
    direction: rtl;
    text-align: right;
    max-width: 1400px;
    margin: 0 auto;
    padding: 20px;
}

.payments-page-header {
    background: linear-gradient(135deg, #1a3a2a 0%, #2d5a3d 100%);
    border: 1px solid rgba(255, 255, 255, 0.91);
    border-radius: 16px;
    padding: 30px 40px;
    margin-bottom: 40px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.2);
}

.payments-page-header h1 {
    color: #d7dedc;
    font-size: 2rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 14px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.payments-page-header h1 i {
    color: #b8e6b8;
    font-size: 1.8rem;
}

.payments-overview {
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
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
}

.stat-card.pending { border-right-color: #f5d5a0; }
.stat-card.deals   { border-right-color: #99e6d4; }
.stat-card.income  { border-right-color: #b8e6b8; }

.stat-card .stat-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.stat-card.pending .stat-icon i { color: #f5d5a0; }
.stat-card.deals   .stat-icon i { color: #99e6d4; }
.stat-card.income  .stat-icon i { color: #b8e6b8; }

.stat-card .stat-number {
    font-size: 2.5rem;
    font-weight: bold;
    color: #d7dedc;
    margin-bottom: 10px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.stat-card .stat-label {
    color: #d7dedc;
    font-size: 1rem;
    font-weight: 600;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.payments-filters {
    background: rgba(85, 85, 85, 0.70);
    backdrop-filter: blur(5.9px);
    -webkit-backdrop-filter: blur(5.9px);
    border: 1px solid rgba(255, 255, 255, 0.91);
    padding: 25px 30px;
    border-radius: 16px;
    margin-bottom: 30px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.payments-filters h3 {
    color: #d7dedc;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 16px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
}

.filters-row {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
    align-items: center;
}

.filters-row select,
.filters-row input[type="text"] {
    padding: 9px 14px;
    background: rgba(38, 59, 52, 0.60);
    border: 1px solid rgba(255, 255, 255, 0.50);
    border-radius: 8px;
    color: #d7dedc;
    font-size: 0.95rem;
    direction: rtl;
    outline: none;
    transition: border-color 0.2s;
}

.filters-row select:focus,
.filters-row input[type="text"]:focus {
    border-color: rgba(255, 255, 255, 0.80);
}

.filters-row select option {
    background: #2d5a3d;
    color: #d7dedc;
}

.filters-row input[type="text"]::placeholder {
    color: rgba(215, 222, 220, 0.6);
}

.btn-clear-filters {
    padding: 9px 18px;
    background: rgba(107, 114, 128, 0.60);
    border: 1px solid rgba(255, 255, 255, 0.40);
    border-radius: 8px;
    color: #d7dedc;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.2s;
    font-weight: 600;
}

.btn-clear-filters:hover {
    background: rgba(107, 114, 128, 0.85);
    transform: translateY(-1px);
}

.payments-table-container {
    background: rgba(38, 59, 52, 0.70);
    backdrop-filter: blur(5.9px);
    -webkit-backdrop-filter: blur(5.9px);
    border: 1px solid rgba(255, 255, 255, 0.91);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    margin-bottom: 30px;
}

.payments-table-container table {
    width: 100%;
    border-collapse: collapse;
    direction: rtl;
}

.payments-table-container thead th {
    padding: 16px 14px;
    background: rgba(20, 40, 30, 0.70);
    color: #d7dedc;
    font-weight: 700;
    font-size: 0.9rem;
    text-align: right;
    border-bottom: 1px solid rgba(255, 255, 255, 0.20);
    white-space: nowrap;
}

.payments-table-container tbody tr {
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    transition: background 0.2s;
}

.payments-table-container tbody tr:last-child {
    border-bottom: none;
}

.payments-table-container tbody tr:hover {
    background: rgba(255, 255, 255, 0.05);
}

.payments-table-container tbody td {
    padding: 14px 14px;
    color: #d7dedc;
    font-size: 0.95rem;
    vertical-align: middle;
}

.status-badge {
    font-weight: bold;
    font-size: 0.9rem;
}

.btn-edit {
    padding: 6px 14px;
    background: rgba(59, 130, 246, 0.75);
    color: white;
    border: none;
    border-radius: 6px;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-block;
}

.btn-edit:hover {
    background: rgba(59, 130, 246, 1);
    transform: translateY(-1px);
}

.btn-call {
    padding: 6px 14px;
    background: rgba(5, 150, 105, 0.75);
    color: white;
    border-radius: 6px;
    font-size: 0.85rem;
    text-decoration: none;
    display: inline-block;
    transition: all 0.2s;
}

.btn-call:hover {
    background: rgba(5, 150, 105, 1);
    transform: translateY(-1px);
}

.charts-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 25px;
    margin-bottom: 30px;
}

.chart-panel {
    background: rgba(85, 85, 85, 0.70);
    backdrop-filter: blur(5.9px);
    -webkit-backdrop-filter: blur(5.9px);
    border: 1px solid rgba(255, 255, 255, 0.91);
    padding: 25px 30px;
    border-radius: 16px;
    box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
}

.chart-panel h3 {
    color: #d7dedc;
    font-size: 1.1rem;
    font-weight: 600;
    margin: 0 0 20px 0;
    display: flex;
    align-items: center;
    gap: 8px;
    text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    padding-bottom: 14px;
}

.chart-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 11px 0;
    border-bottom: 1px solid rgba(255, 255, 255, 0.10);
    color: #d7dedc;
}

.chart-row:last-child {
    border-bottom: none;
}

.chart-row-label {
    font-weight: 600;
    font-size: 0.95rem;
}

.chart-row-values {
    text-align: left;
    display: flex;
    flex-direction: column;
    gap: 2px;
}

.chart-row-amount {
    font-weight: bold;
    font-size: 0.95rem;
    color: #99e6d4;
}

.chart-row-pct {
    font-size: 0.8rem;
    opacity: 0.75;
}

.chart-row-monthly-amount {
    font-weight: bold;
    font-size: 0.95rem;
    color: #b8e6b8;
}

@media (max-width: 768px) {
    .payments-overview {
        grid-template-columns: 1fr 1fr;
    }
    .charts-grid {
        grid-template-columns: 1fr;
    }
    .filters-row {
        flex-direction: column;
        align-items: stretch;
    }
    .filters-row select,
    .filters-row input[type="text"],
    .btn-clear-filters {
        width: 100%;
    }
    .payments-table-container tbody td,
    .payments-table-container thead th {
        padding: 10px 8px;
        font-size: 0.82rem;
    }
    .payments-page-header h1 {
        font-size: 1.4rem;
    }
}

@media (max-width: 480px) {
    .payments-overview {
        grid-template-columns: 1fr;
    }
}

/* Modal פירוט כרטיסיות */
.breakdown-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0, 0, 0, 0.55);
    z-index: 9000;
    align-items: center;
    justify-content: center;
    padding: 20px;
    backdrop-filter: blur(3px);
}

.breakdown-overlay.open {
    display: flex;
}

.breakdown-modal {
    background: rgba(28, 50, 38, 0.95);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    width: 100%;
    max-width: 520px;
    max-height: 80vh;
    display: flex;
    flex-direction: column;
    animation: modalSlideUp 0.25s ease;
}

@keyframes modalSlideUp {
    from { opacity: 0; transform: translateY(30px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0)   scale(1);    }
}

.breakdown-modal-header {
    padding: 22px 28px 18px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-shrink: 0;
}

.breakdown-modal-header h2 {
    color: #d7dedc;
    font-size: 1.25rem;
    font-weight: 700;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
    text-shadow: 0 1px 2px rgba(0,0,0,0.3);
}

.breakdown-modal-close {
    background: rgba(255,255,255,0.10);
    border: 1px solid rgba(255,255,255,0.25);
    color: #d7dedc;
    width: 32px;
    height: 32px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s;
    flex-shrink: 0;
}

.breakdown-modal-close:hover {
    background: rgba(255,255,255,0.22);
}

.breakdown-modal-total {
    padding: 14px 28px;
    border-bottom: 1px solid rgba(255,255,255,0.10);
    color: #d7dedc;
    font-size: 0.9rem;
    flex-shrink: 0;
}

.breakdown-modal-total span {
    font-weight: 700;
    font-size: 1.1rem;
}

.breakdown-modal-body {
    overflow-y: auto;
    padding: 8px 0;
    flex: 1;
    scrollbar-width: thin;
    scrollbar-color: rgba(255,255,255,0.25) transparent;
}

.breakdown-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 13px 28px;
    border-bottom: 1px solid rgba(255,255,255,0.07);
    transition: background 0.15s;
}

.breakdown-row:last-child { border-bottom: none; }

.breakdown-row:hover { background: rgba(255,255,255,0.05); }

.breakdown-row-name {
    color: #d7dedc;
    font-weight: 600;
    font-size: 0.97rem;
}

.breakdown-row-amount {
    font-weight: 700;
    font-size: 1rem;
}

.breakdown-row-amount.deals  { color: #99e6d4; }
.breakdown-row-amount.income { color: #b8e6b8; }

.breakdown-empty {
    padding: 30px 28px;
    color: rgba(215,222,220,0.6);
    text-align: center;
    font-size: 0.95rem;
}

.stat-card.clickable {
    cursor: pointer;
}

.stat-card.clickable:hover .stat-label::after {
    content: ' ←';
    opacity: 0.7;
}
</style>

<!-- Modal פירוט כרטיסיות -->
<div class="breakdown-overlay" id="breakdown-overlay" onclick="closeBreakdownModal(event)">
    <div class="breakdown-modal" role="dialog" aria-modal="true">
        <div class="breakdown-modal-header">
            <h2 id="breakdown-title"><i class="fa-solid fa-list-ul"></i> <span></span></h2>
            <button class="breakdown-modal-close" onclick="forceCloseBreakdown()" aria-label="סגור">✕</button>
        </div>
        <div class="breakdown-modal-total" id="breakdown-total"></div>
        <div class="breakdown-modal-body" id="breakdown-body"></div>
    </div>
</div>

<div class="crm-payments-page">

    <div class="payments-page-header">
        <h1><i class="fa-solid fa-money-bill-wave"></i> ניהול תשלומים</h1>
    </div>

    <!-- כרטיסיות סטטיסטיקה -->
    <div class="payments-overview">
        <div class="stat-card pending">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-number">₪<?php echo number_format($payments_data['total_pending']); ?></div>
            <div class="stat-label">ממתין לתשלום</div>
        </div>

        <div class="stat-card deals clickable" onclick="openBreakdownModal('deals')" role="button" tabindex="0" aria-label="פירוט עסקאות החודש">
            <div class="stat-icon"><i class="fa-solid fa-handshake"></i></div>
            <div class="stat-number">₪<?php echo number_format($monthly_deals); ?></div>
            <div class="stat-label">עסקאות החודש</div>
        </div>

        <div class="stat-card income clickable" onclick="openBreakdownModal('income')" role="button" tabindex="0" aria-label="פירוט הכנסות צפויות החודש">
            <div class="stat-icon"><i class="fa-solid fa-shekel-sign"></i></div>
            <div class="stat-number">₪<?php echo number_format($monthly_expected_income); ?></div>
            <div class="stat-label">הכנסות צפויות החודש</div>
        </div>
    </div>

    <!-- פילטרים -->
    <div class="payments-filters">
        <h3><i class="fa-solid fa-magnifying-glass"></i> סינון תשלומים</h3>
        <div class="filters-row">
            <select id="status-filter">
                <option value="">כל הסטטוסים</option>
                <option value="paid">שולם במלואו</option>
                <option value="partial">שולם חלקית</option>
                <option value="unpaid">לא שולם</option>
            </select>

            <select id="method-filter">
                <option value="">כל אמצעי התשלום</option>
                <?php foreach ($payment_method_labels as $key => $label): ?>
                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                <?php endforeach; ?>
            </select>

            <input type="text" id="search-filter" placeholder="חיפוש לפי שם...">

            <button class="btn-clear-filters" onclick="clearFilters()">נקה סינון</button>
        </div>
    </div>

    <!-- טבלת תשלומים -->
    <div class="payments-table-container">
        <table id="payments-table">
            <thead>
                <tr>
                    <th>👤 שם מתאמנת</th>
                    <th>📞 טלפון</th>
                    <th>💰 סכום לתשלום</th>
                    <th>✅ שולם</th>
                    <th>⏳ נותר</th>
                    <th>💳 אמצעי</th>
                    <th>📅 תאריך</th>
                    <th>🎯 סטטוס</th>
                    <th>⚙️ פעולות</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($payments_data['clients_data'] as $client): ?>
                    <tr class="payment-row"
                        data-status="<?php echo $client['status']; ?>"
                        data-method="<?php echo $client['payment_method']; ?>"
                        data-name="<?php echo strtolower($client['name']); ?>">

                        <td style="font-weight: bold;">
                            <a href="<?php echo get_edit_post_link($client['id']); ?>" style="color: #a7c7e7; text-decoration: none;">
                                <?php echo esc_html($client['name']); ?>
                            </a>
                        </td>

                        <td>
                            <?php if ($client['phone']): ?>
                                <a href="tel:<?php echo esc_attr($client['phone']); ?>" style="color: #99e6d4; text-decoration: none;">
                                    <?php echo esc_html($client['phone']); ?>
                                </a>
                            <?php endif; ?>
                        </td>

                        <td style="font-weight: bold;">
                            ₪<?php echo number_format($client['payment_amount']); ?>
                        </td>

                        <td style="color: #99e6d4; font-weight: bold;">
                            ₪<?php echo number_format($client['amount_paid']); ?>
                        </td>

                        <td style="color: <?php echo $client['pending_amount'] > 0 ? '#f5b7b1' : '#99e6d4'; ?>; font-weight: bold;">
                            ₪<?php echo number_format($client['pending_amount']); ?>
                        </td>

                        <td>
                            <?php echo $client['payment_method'] ? esc_html($payment_method_labels[$client['payment_method']]) : '-'; ?>
                        </td>

                        <td>
                            <?php echo $client['payment_date'] ? date('d/m/Y', strtotime($client['payment_date'])) : '-'; ?>
                        </td>

                        <td>
                            <?php
                            $status_colors = array(
                                'paid'    => '#99e6d4',
                                'partial' => '#f5d5a0',
                                'unpaid'  => '#f5b7b1'
                            );
                            $status_labels = array(
                                'paid'    => '✅ שולם',
                                'partial' => '⚠️ חלקי',
                                'unpaid'  => '❌ לא שולם'
                            );
                            ?>
                            <span class="status-badge" style="color: <?php echo $status_colors[$client['status']]; ?>;">
                                <?php echo $status_labels[$client['status']]; ?>
                            </span>
                        </td>

                        <td>
                            <div style="display: flex; gap: 8px;">
                                <button type="button"
                                        class="btn-edit"
                                        onclick="openEditClientModal(<?php echo $client['id']; ?>)">
                                    ✏️ ערוך
                                </button>
                                <?php if ($client['phone']): ?>
                                    <a href="tel:<?php echo esc_attr($client['phone']); ?>" class="btn-call">
                                        📞
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- גרפים -->
    <div class="charts-grid">
        <div class="chart-panel">
            <h3><i class="fa-solid fa-credit-card"></i> פירוק לפי אמצעי תשלום</h3>
            <div id="payment-methods-chart"></div>
        </div>

        <div class="chart-panel">
            <h3><i class="fa-solid fa-chart-line"></i> הכנסות חודשיות</h3>
            <div id="monthly-income-chart"></div>
        </div>
    </div>

</div>

<script>
function filterTable() {
    const statusFilter = document.getElementById('status-filter').value;
    const methodFilter = document.getElementById('method-filter').value;
    const searchFilter = document.getElementById('search-filter').value.toLowerCase();
    const rows = document.querySelectorAll('.payment-row');

    rows.forEach(row => {
        const status = row.dataset.status;
        const method = row.dataset.method;
        const name   = row.dataset.name;
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

document.getElementById('status-filter').addEventListener('change', filterTable);
document.getElementById('method-filter').addEventListener('change', filterTable);
document.getElementById('search-filter').addEventListener('input', filterTable);

// נתוני פירוט כרטיסיות
const dealsClients    = <?php echo json_encode($monthly_deals_clients, JSON_UNESCAPED_UNICODE); ?>;
const expectedClients = <?php echo json_encode($monthly_expected_clients, JSON_UNESCAPED_UNICODE); ?>;
const currentMonthHe  = new Date().toLocaleDateString('he-IL', { year: 'numeric', month: 'long' });

function openBreakdownModal(type) {
    const overlay = document.getElementById('breakdown-overlay');
    const title   = document.querySelector('#breakdown-title span');
    const total   = document.getElementById('breakdown-total');
    const body    = document.getElementById('breakdown-body');

    const isDeals  = type === 'deals';
    const clients  = isDeals ? dealsClients : expectedClients;
    const amtClass = isDeals ? 'deals' : 'income';
    const icon     = isDeals ? 'fa-handshake' : 'fa-shekel-sign';

    document.querySelector('#breakdown-title i').className = `fa-solid ${icon}`;
    title.textContent = isDeals ? `עסקאות החודש — ${currentMonthHe}` : `הכנסות צפויות — ${currentMonthHe}`;

    const sum = clients.reduce((acc, c) => acc + c.amount, 0);
    total.innerHTML = isDeals
        ? `<span>${clients.length}</span> עסקאות | סה"כ: <span>₪${Math.round(sum).toLocaleString()}</span>`
        : `<span>${clients.length}</span> מתאמנות | סה"כ צפוי: <span>₪${Math.round(sum).toLocaleString()}</span>`;

    if (clients.length === 0) {
        body.innerHTML = `<div class="breakdown-empty">אין נתונים לחודש זה</div>`;
    } else {
        body.innerHTML = clients.map(c => `
            <div class="breakdown-row">
                <span class="breakdown-row-name">${c.name}</span>
                <span class="breakdown-row-amount ${amtClass}">₪${Math.round(c.amount).toLocaleString()}</span>
            </div>`).join('');
    }

    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function closeBreakdownModal(event) {
    // אם הופעלה מה-overlay עצמו, לסגור רק אם הקליק היה על ה-overlay (לא על תוכן המודל)
    if (event && event.target !== document.getElementById('breakdown-overlay')) return;
    document.getElementById('breakdown-overlay').classList.remove('open');
    document.body.style.overflow = '';
}

function forceCloseBreakdown() {
    document.getElementById('breakdown-overlay').classList.remove('open');
    document.body.style.overflow = '';
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') forceCloseBreakdown();
});

// keyboard accessibility for stat cards
document.querySelectorAll('.stat-card.clickable').forEach(card => {
    card.addEventListener('keydown', e => {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); card.click(); }
    });
});

const paymentMethodsData = <?php echo json_encode($payments_data['payment_methods']); ?>;
const monthlyData        = <?php echo json_encode($payments_data['monthly_breakdown']); ?>;
const methodLabels       = <?php echo json_encode($payment_method_labels); ?>;
const totalReceived      = <?php echo (float) $payments_data['total_received']; ?>;

// גרף אמצעי תשלום
if (Object.keys(paymentMethodsData).length > 0) {
    const container = document.getElementById('payment-methods-chart');
    let html = '';
    Object.keys(paymentMethodsData).forEach(method => {
        const amount = paymentMethodsData[method];
        const pct = totalReceived > 0 ? ((amount / totalReceived) * 100).toFixed(1) : '0.0';
        const label = methodLabels[method] || method;
        html += `<div class="chart-row">
            <span class="chart-row-label">${label}</span>
            <div class="chart-row-values">
                <span class="chart-row-amount">₪${amount.toLocaleString()}</span>
                <span class="chart-row-pct">${pct}%</span>
            </div>
        </div>`;
    });
    container.innerHTML = html;
}

// גרף הכנסות חודשיות
if (Object.keys(monthlyData).length > 0) {
    const container = document.getElementById('monthly-income-chart');
    const sortedMonths = Object.keys(monthlyData).sort();
    let html = '';
    sortedMonths.forEach(month => {
        const amount = monthlyData[month];
        const monthName = new Date(month + '-01').toLocaleDateString('he-IL', { year: 'numeric', month: 'long' });
        html += `<div class="chart-row">
            <span class="chart-row-label">${monthName}</span>
            <span class="chart-row-monthly-amount">₪${amount.toLocaleString()}</span>
        </div>`;
    });
    container.innerHTML = html;
}
</script>
