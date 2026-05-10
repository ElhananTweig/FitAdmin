<?php
/**
 * עמוד ארכיון קבוצות
 */

get_header(); ?>

<div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px; direction: rtl;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="color: #d7dedc; margin: 0; text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);">👥 קבוצות</h1>
        <a href="<?php echo admin_url('admin.php?page=add-group-form'); ?>"
           style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: bold;">
            ➕ הוסף קבוצה חדשה
        </a>
    </div>

    <?php
    $group_profit_data = array();

    if (have_posts()) : ?>
        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(350px, 1fr)); gap: 20px;">
            <?php while (have_posts()) : the_post();
                $group_id = get_the_ID();
                $group_name = get_field('group_name');
                $group_mentor = get_field('group_mentor');
                $group_description = get_field('group_description');
                $group_start_date = get_field('group_start_date');
                $group_end_date = get_field('group_end_date');
                $group_max_participants = get_field('group_max_participants');

                // פרטי מנטורית
                $mentor_name = '';
                $mentor_phone = '';
                $mentor_fee = 0;
                if ($group_mentor) {
                    $mentor_id = is_object($group_mentor) ? $group_mentor->ID : $group_mentor;
                    $mentor_name = get_field('mentor_first_name', $mentor_id) . ' ' . get_field('mentor_last_name', $mentor_id);
                    $mentor_phone = get_field('mentor_phone', $mentor_id);
                    $mentor_fee = floatval(get_field('payment_percentage', $mentor_id));
                }

                // ספירת משתתפות בקבוצה
                $participants = get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'group_id',
                            'value' => $group_id,
                            'compare' => '='
                        )
                    )
                ));
                $participants_count = count($participants);

                // קביעת סטטוס הקבוצה
                $status = 'active';
                $status_label = 'פעילה';
                $status_color = '#10b981';
                $today = date('Y-m-d');

                if ($group_start_date && $group_start_date > $today) {
                    $status = 'future';
                    $status_label = 'עתידית';
                    $status_color = '#3b82f6';
                } elseif ($group_end_date && $group_end_date < $today) {
                    $status = 'ended';
                    $status_label = 'הסתיימה';
                    $status_color = '#6b7280';
                }

                // בניית נתוני רווח קבוצה
                $participants_data = array();
                foreach ($participants as $p) {
                    $p_first = get_field('first_name', $p->ID);
                    $p_last  = get_field('last_name', $p->ID);
                    $p_amount = floatval(get_field('payment_amount', $p->ID));
                    $participants_data[] = array(
                        'name'   => trim($p_first . ' ' . $p_last),
                        'amount' => $p_amount,
                    );
                }

                $custom_income_raw   = get_post_meta($group_id, 'group_custom_income', true);
                $custom_expenses_raw = get_post_meta($group_id, 'group_custom_expenses', true);
                $custom_income   = json_decode($custom_income_raw ?: '[]', true) ?: array();
                $custom_expenses = json_decode($custom_expenses_raw ?: '[]', true) ?: array();

                $group_profit_data[$group_id] = array(
                    'name'             => $group_name,
                    'mentor_name'      => $mentor_name,
                    'mentor_fee'       => $mentor_fee,
                    'participants'     => $participants_data,
                    'custom_income'    => $custom_income,
                    'custom_expenses'  => $custom_expenses,
                );
            ?>
                <div style="background: rgba(38, 59, 52, 0.70); backdrop-filter: blur(5.9px); -webkit-backdrop-filter: blur(5.9px); border: 1px solid rgba(255, 255, 255, 0.91); border-radius: 16px; padding: 20px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1); transition: all 0.3s; cursor: pointer;"
                     onclick="window.location.href='<?php echo get_post_type_archive_link('clients') . '?group=' . $group_id; ?>';"
                     onmouseenter="this.style.transform='translateY(-5px)'; this.style.boxShadow='0 10px 30px rgba(0,0,0,0.2)';"
                     onmouseleave="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 30px rgba(0, 0, 0, 0.1)';">

                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 15px;">
                        <div>
                            <h3 style="margin: 0; color: #d7dedc; font-size: 1.25rem; font-weight: bold; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">
                                <?php echo $group_name; ?>
                            </h3>
                            <div style="background: <?php echo $status_color; ?>; color: white; padding: 4px 8px; border-radius: 4px; font-size: 0.75rem; margin-top: 5px; display: inline-block;">
                                <?php echo $status_label; ?>
                            </div>
                        </div>

                        <div style="text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: #d7dedc; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">
                                <?php echo $participants_count; ?>
                            </div>
                            <div style="font-size: 0.75rem; color: #d7dedc; opacity: 0.8;">
                                מתוך <?php echo $group_max_participants; ?>
                            </div>
                        </div>
                    </div>

                    <?php if ($mentor_name): ?>
                        <div style="margin-bottom: 10px;">
                            <strong style="color: #d7dedc;">👩‍🏫 מנטורית:</strong>
                            <span style="color: #ffffff; font-weight: 500;"><?php echo $mentor_name; ?></span>
                        </div>
                    <?php endif; ?>

                    <?php if ($group_start_date || $group_end_date): ?>
                        <div style="margin-bottom: 10px; color: #d7dedc; font-size: 0.875rem; opacity: 0.9;">
                            <strong>📅 תאריכים:</strong>
                            <?php if ($group_start_date): ?>
                                <?php echo date('d.m.Y', strtotime($group_start_date)); ?>
                            <?php endif; ?>
                            <?php if ($group_start_date && $group_end_date): ?> - <?php endif; ?>
                            <?php if ($group_end_date): ?>
                                <?php echo date('d.m.Y', strtotime($group_end_date)); ?>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($group_description): ?>
                        <div style="background: rgba(255, 255, 255, 0.15); backdrop-filter: blur(2px); -webkit-backdrop-filter: blur(2px); padding: 10px; border-radius: 8px; margin: 10px 0; font-size: 0.875rem; color: #d7dedc; opacity: 0.9;">
                            <?php echo wp_trim_words($group_description, 15); ?>
                        </div>
                    <?php endif; ?>

                    <div style="display: flex; gap: 10px; margin-top: 15px; flex-wrap: wrap;">
                        <span style="background: #10b981; color: white; padding: 8px 16px; border-radius: 6px; font-size: 0.875rem; font-weight: bold;">
                            👥 צפה במשתתפות (<?php echo $participants_count; ?>)
                        </span>

                        <button type="button"
                                onclick="event.stopPropagation(); openGroupProfitModal(<?php echo $group_id; ?>)"
                                style="background: #8b5cf6; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: bold; cursor: pointer;">
                            💰 רווח קבוצה
                        </button>

                        <?php if (current_user_can('manage_options')): ?>
                            <a href="<?php echo admin_url('admin.php?page=add-group-form&edit=' . $group_id); ?>"
                               style="background: #3b82f6; color: white; padding: 8px 16px; text-decoration: none; border-radius: 6px; font-size: 0.875rem; font-weight: bold;"
                               onclick="event.stopPropagation();">
                                ✏️ ערוך
                            </a>
                            <button type="button" onclick="event.stopPropagation(); deleteGroup(<?php echo $group_id; ?>, '<?php echo esc_js($group_name); ?>', <?php echo $participants_count; ?>);"
                                    style="background: #ef4444; color: white; padding: 8px 16px; border: none; border-radius: 6px; font-size: 0.875rem; font-weight: bold; cursor: pointer;">
                                🗑️ מחק
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div style="text-align: center; padding: 60px; background: rgba(38, 59, 52, 0.70); backdrop-filter: blur(5.9px); -webkit-backdrop-filter: blur(5.9px); border: 1px solid rgba(255, 255, 255, 0.91); border-radius: 16px; box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);">
            <h2 style="color: #d7dedc; margin-bottom: 20px; text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);">אין קבוצות עדיין</h2>
            <p style="color: #d7dedc; opacity: 0.8; margin-bottom: 30px;">התחילי בהוספת הקבוצה הראשונה שלך!</p>
            <?php if (current_user_can('manage_options')): ?>
                <a href="<?php echo admin_url('admin.php?page=add-group-form'); ?>"
                   style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: bold; font-size: 16px;">
                    ➕ הוסף קבוצה חדשה
                </a>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<!-- פופאפ רווח קבוצה -->
<div class="gp-overlay" id="gp-overlay">
    <div class="gp-backdrop" onclick="forceCloseGroupProfit()"></div>
    <div class="gp-modal" role="dialog" aria-modal="true" aria-labelledby="gp-title">
        <div class="gp-header">
            <h2 id="gp-title">💰 <span id="gp-title-text"></span></h2>
            <button class="gp-close-btn" onclick="forceCloseGroupProfit()" aria-label="סגור">✕</button>
        </div>

        <div class="gp-body">
            <!-- הכנסות -->
            <div class="gp-section">
                <div class="gp-section-header income">📈 הכנסות</div>
                <div id="gp-income-rows"></div>
                <button class="gp-add-row-btn" onclick="addGPRow('income')">+ הוסף שורה</button>
                <div class="gp-subtotal" id="gp-income-total"></div>
            </div>

            <!-- הוצאות -->
            <div class="gp-section">
                <div class="gp-section-header expense">📉 הוצאות</div>
                <div id="gp-expense-rows"></div>
                <button class="gp-add-row-btn" onclick="addGPRow('expense')">+ הוסף שורה</button>
                <div class="gp-subtotal" id="gp-expense-total"></div>
            </div>

            <!-- סה"כ רווח -->
            <div class="gp-final-row" id="gp-final-profit"></div>
        </div>

        <div class="gp-footer">
            <button class="gp-save-btn" onclick="saveGroupProfitData()">💾 שמור שינויים</button>
            <button class="gp-cancel-btn" onclick="forceCloseGroupProfit()">סגור</button>
        </div>
    </div>
</div>

<style>
@media (max-width: 768px) {
    .container {
        padding: 10px !important;
    }

    .container > div:first-child {
        flex-direction: column !important;
        gap: 15px !important;
        text-align: center !important;
    }

    .container > div:nth-child(2) {
        grid-template-columns: 1fr !important;
    }
}

/* ===== פופאפ רווח קבוצה ===== */
.gp-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 10000;
    direction: rtl;
}

.gp-overlay.open {
    display: block;
}

.gp-backdrop {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    backdrop-filter: blur(5px);
    -webkit-backdrop-filter: blur(5px);
}

@keyframes gpSlideUp {
    from { opacity: 0; transform: translateY(-20px) scale(0.97); }
    to   { opacity: 1; transform: translateY(0) scale(1); }
}

.gp-modal {
    position: relative;
    background: rgba(28, 50, 38, 0.95);
    backdrop-filter: blur(15px);
    -webkit-backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.85);
    border-radius: 20px;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.4);
    width: 100%;
    max-width: 560px;
    max-height: 90vh;
    margin: 5vh auto;
    animation: gpSlideUp 0.3s ease-out;
    direction: rtl;
    overflow: hidden;
}

.gp-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 20px 24px 16px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
    position: sticky;
    top: 0;
    z-index: 10;
}

.gp-header h2 {
    margin: 0;
    color: #d7dedc;
    font-size: 1.2rem;
    font-weight: 700;
}

.gp-close-btn {
    background: none;
    border: none;
    color: #d7dedc;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 4px 8px;
    border-radius: 6px;
    transition: background 0.2s;
    line-height: 1;
}

.gp-close-btn:hover {
    background: rgba(255, 255, 255, 0.1);
}

.gp-body {
    overflow-y: auto;
    padding: 20px 24px;
    max-height: 60vh;
}

.gp-section {
    margin-bottom: 20px;
}

.gp-section-header {
    font-size: 1rem;
    font-weight: 700;
    padding: 8px 0;
    margin-bottom: 10px;
    border-bottom: 1px solid rgba(255, 255, 255, 0.15);
}

.gp-section-header.income {
    color: #b8e6b8;
}

.gp-section-header.expense {
    color: #f5b7b1;
}

.gp-row {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 8px 10px;
    border-radius: 8px;
    margin-bottom: 6px;
    background: rgba(255, 255, 255, 0.05);
    gap: 10px;
}

.gp-row-name {
    color: #d7dedc;
    font-size: 0.9rem;
    flex: 1;
}

.gp-row-amount {
    font-size: 0.9rem;
    font-weight: 600;
    white-space: nowrap;
}

.gp-row-amount.income {
    color: #b8e6b8;
}

.gp-row-amount.expense {
    color: #f5b7b1;
}

.gp-custom-row {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 6px;
}

.gp-custom-row input[type="text"] {
    flex: 1;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: #d7dedc;
    padding: 6px 10px;
    font-size: 0.875rem;
    direction: rtl;
}

.gp-custom-row input[type="number"] {
    width: 90px;
    background: rgba(255, 255, 255, 0.08);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 6px;
    color: #d7dedc;
    padding: 6px 8px;
    font-size: 0.875rem;
    text-align: left;
}

.gp-custom-row input:focus {
    outline: none;
    border-color: rgba(255, 255, 255, 0.5);
}

.gp-remove-btn {
    background: none;
    border: none;
    color: #f5b7b1;
    cursor: pointer;
    font-size: 1rem;
    padding: 2px 6px;
    border-radius: 4px;
    transition: background 0.2s;
    flex-shrink: 0;
}

.gp-remove-btn:hover {
    background: rgba(245, 183, 177, 0.15);
}

.gp-add-row-btn {
    background: rgba(255, 255, 255, 0.08);
    border: 1px dashed rgba(255, 255, 255, 0.25);
    color: #d7dedc;
    padding: 6px 14px;
    border-radius: 6px;
    font-size: 0.8rem;
    cursor: pointer;
    margin-top: 4px;
    transition: background 0.2s;
}

.gp-add-row-btn:hover {
    background: rgba(255, 255, 255, 0.13);
}

.gp-subtotal {
    text-align: left;
    color: #d7dedc;
    font-size: 0.9rem;
    font-weight: 600;
    padding: 8px 10px 0;
    opacity: 0.85;
}

.gp-final-row {
    border-top: 2px solid rgba(255, 255, 255, 0.2);
    padding: 14px 10px 4px;
    text-align: center;
    font-size: 1.15rem;
    font-weight: 700;
    color: #d7dedc;
}

.gp-footer {
    padding: 14px 24px 18px;
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.gp-save-btn {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.9rem;
    font-weight: 600;
    cursor: pointer;
    transition: opacity 0.2s;
}

.gp-save-btn:hover {
    opacity: 0.9;
}

.gp-cancel-btn {
    background: rgba(255, 255, 255, 0.08);
    color: #d7dedc;
    border: 1px solid rgba(255, 255, 255, 0.2);
    padding: 10px 20px;
    border-radius: 8px;
    font-size: 0.9rem;
    cursor: pointer;
    transition: background 0.2s;
}

.gp-cancel-btn:hover {
    background: rgba(255, 255, 255, 0.13);
}

.gp-save-msg {
    font-size: 0.8rem;
    padding: 4px 10px;
    border-radius: 4px;
    align-self: center;
}

.gp-save-msg.success { color: #b8e6b8; }
.gp-save-msg.error   { color: #f5b7b1; }
</style>

<script>
// העברת ה-overlay ישירות ל-body כדי לצאת מ-stacking context של backdrop-filter
document.addEventListener('DOMContentLoaded', function() {
    document.body.appendChild(document.getElementById('gp-overlay'));
});

// נתוני רווח קבוצה
const allGroupProfitData = <?php echo json_encode($group_profit_data, JSON_UNESCAPED_UNICODE); ?>;
const gpAjaxUrl  = '<?php echo admin_url("admin-ajax.php"); ?>';
const gpNonce    = '<?php echo wp_create_nonce("delete_group_nonce"); ?>';

let currentGroupId   = null;
let gpIncomeRows     = []; // {label, amount, isCustom, customIndex}
let gpExpenseRows    = [];

function openGroupProfitModal(groupId) {
    currentGroupId = groupId;
    const data = allGroupProfitData[groupId];
    if (!data) return;

    document.getElementById('gp-title-text').textContent = data.name;

    // בניית שורות הכנסות
    gpIncomeRows = [];
    data.participants.forEach(p => {
        gpIncomeRows.push({ label: p.name, amount: p.amount, isCustom: false });
    });
    (data.custom_income || []).forEach((row, i) => {
        gpIncomeRows.push({ label: row.label || '', amount: parseFloat(row.amount) || 0, isCustom: true, customIndex: i });
    });

    // בניית שורות הוצאות
    gpExpenseRows = [];
    if (data.mentor_name && data.mentor_fee > 0) {
        gpExpenseRows.push({ label: 'מנטורית — ' + data.mentor_name, amount: data.mentor_fee, isCustom: false });
    }
    (data.custom_expenses || []).forEach((row, i) => {
        gpExpenseRows.push({ label: row.label || '', amount: parseFloat(row.amount) || 0, isCustom: true, customIndex: i });
    });

    renderGPRows();

    const overlay = document.getElementById('gp-overlay');
    overlay.classList.add('open');
    document.body.style.overflow = 'hidden';
}

function renderGPRows() {
    renderSection('income', gpIncomeRows, document.getElementById('gp-income-rows'));
    renderSection('expense', gpExpenseRows, document.getElementById('gp-expense-rows'));
    updateGPTotals();
}

function renderSection(type, rows, container) {
    container.innerHTML = '';
    let customCount = 0;

    rows.forEach((row, idx) => {
        if (!row.isCustom) {
            const div = document.createElement('div');
            div.className = 'gp-row';
            div.innerHTML = `
                <span class="gp-row-name">${escHtml(row.label)}</span>
                <span class="gp-row-amount ${type}">₪${Math.round(row.amount).toLocaleString()}</span>`;
            container.appendChild(div);
        } else {
            const ci = customCount++;
            const div = document.createElement('div');
            div.className = 'gp-custom-row';
            div.dataset.idx = idx;
            div.innerHTML = `
                <input type="text" placeholder="פירוט" value="${escAttr(row.label)}"
                       oninput="updateGPRowLabel('${type}', ${idx}, this.value)">
                <input type="number" placeholder="0" value="${row.amount || ''}" min="0"
                       oninput="updateGPRowAmount('${type}', ${idx}, this.value)">
                <button class="gp-remove-btn" onclick="removeGPRow('${type}', ${idx})" title="הסר">✕</button>`;
            container.appendChild(div);
        }
    });
}

function updateGPRowLabel(type, idx, val) {
    const rows = type === 'income' ? gpIncomeRows : gpExpenseRows;
    rows[idx].label = val;
    updateGPTotals();
}

function updateGPRowAmount(type, idx, val) {
    const rows = type === 'income' ? gpIncomeRows : gpExpenseRows;
    rows[idx].amount = parseFloat(val) || 0;
    updateGPTotals();
}

function removeGPRow(type, idx) {
    if (type === 'income') {
        gpIncomeRows.splice(idx, 1);
    } else {
        gpExpenseRows.splice(idx, 1);
    }
    renderGPRows();
}

function addGPRow(type) {
    const newRow = { label: '', amount: 0, isCustom: true };
    if (type === 'income') {
        gpIncomeRows.push(newRow);
    } else {
        gpExpenseRows.push(newRow);
    }
    renderGPRows();

    // פוקוס על שדה הטקסט החדש
    const containers = { income: 'gp-income-rows', expense: 'gp-expense-rows' };
    const container = document.getElementById(containers[type]);
    const inputs = container.querySelectorAll('.gp-custom-row input[type="text"]');
    if (inputs.length) inputs[inputs.length - 1].focus();
}

function updateGPTotals() {
    const incomeTotal  = gpIncomeRows.reduce((s, r) => s + (parseFloat(r.amount) || 0), 0);
    const expenseTotal = gpExpenseRows.reduce((s, r) => s + (parseFloat(r.amount) || 0), 0);
    const profit = incomeTotal - expenseTotal;

    document.getElementById('gp-income-total').textContent  = `סה"כ הכנסות: ₪${Math.round(incomeTotal).toLocaleString()}`;
    document.getElementById('gp-expense-total').textContent = `סה"כ הוצאות: ₪${Math.round(expenseTotal).toLocaleString()}`;

    const color  = profit >= 0 ? '#b8e6b8' : '#f5b7b1';
    const sign   = profit >= 0 ? '' : '-';
    document.getElementById('gp-final-profit').innerHTML =
        `<span style="color:${color}">סה"כ רווח: ${sign}₪${Math.round(Math.abs(profit)).toLocaleString()}</span>`;
}

async function saveGroupProfitData() {
    const customIncome   = gpIncomeRows.filter(r => r.isCustom).map(r => ({ label: r.label, amount: r.amount }));
    const customExpenses = gpExpenseRows.filter(r => r.isCustom).map(r => ({ label: r.label, amount: r.amount }));

    const saveBtn = document.querySelector('.gp-save-btn');
    saveBtn.disabled = true;
    saveBtn.textContent = '⏳ שומר...';

    // הסרת הודעה קודמת
    const oldMsg = document.querySelector('.gp-save-msg');
    if (oldMsg) oldMsg.remove();

    try {
        const formData = new FormData();
        formData.append('action', 'save_group_profit_rows');
        formData.append('nonce', gpNonce);
        formData.append('group_id', currentGroupId);
        formData.append('custom_income', JSON.stringify(customIncome));
        formData.append('custom_expenses', JSON.stringify(customExpenses));

        const res  = await fetch(gpAjaxUrl, { method: 'POST', body: formData });
        const data = await res.json();

        const msg = document.createElement('span');
        msg.className = 'gp-save-msg ' + (data.success ? 'success' : 'error');
        msg.textContent = data.success ? '✅ נשמר!' : ('❌ ' + (data.data || 'שגיאה'));
        document.querySelector('.gp-footer').insertBefore(msg, saveBtn);

        // עדכון הנתונים המקומיים
        if (data.success) {
            allGroupProfitData[currentGroupId].custom_income    = customIncome;
            allGroupProfitData[currentGroupId].custom_expenses  = customExpenses;
            setTimeout(() => msg.remove(), 3000);
        }
    } catch (e) {
        console.error(e);
    } finally {
        saveBtn.disabled = false;
        saveBtn.textContent = '💾 שמור שינויים';
    }
}

function forceCloseGroupProfit() {
    _doCloseGP();
}

function _doCloseGP() {
    document.getElementById('gp-overlay').classList.remove('open');
    document.body.style.overflow = '';
    currentGroupId = null;
}

document.addEventListener('keydown', e => {
    if (e.key === 'Escape') forceCloseGroupProfit();
});

// פונקציות עזר
function escHtml(s) {
    return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
}
function escAttr(s) { return escHtml(s); }

// פונקציה למחיקת קבוצה
function deleteGroup(groupId, groupName, participantsCount) {
    // בניית הודעת אזהרה מפורטת
    let warningMessage = `האם את בטוחה שברצונך למחוק את הקבוצה "${groupName}"?\n\n⚠️ זוהי פעולה בלתי הפיכה!\n\n`;

    if (participantsCount > 0) {
        warningMessage += `🚨 שימי לב: בקבוצה זו יש ${participantsCount} משתתפות!\n`;
        warningMessage += `מחיקת הקבוצה תגרום לכך שהמשתתפות יועברו לליווי אישי.\n\n`;
    }

    warningMessage += `מה יימחק:\n`;
    warningMessage += `• פרטי הקבוצה\n`;
    warningMessage += `• תיאור הקבוצה\n`;
    warningMessage += `• קישור למנטורית\n`;
    warningMessage += `• כל המידע הקשור לקבוצה\n\n`;

    if (participantsCount > 0) {
        warningMessage += `המשתתפות יישארו במערכת אבל יועברו לליווי אישי.\n\n`;
    }

    warningMessage += `האם להמשיך?`;

    const confirmation = confirm(warningMessage);

    if (!confirmation) {
        return;
    }

    const loadingMessage = document.createElement('div');
    loadingMessage.id = 'delete-loading';
    loadingMessage.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 20px 30px;
        border-radius: 10px;
        z-index: 9999;
        text-align: center;
        font-size: 16px;
    `;
    loadingMessage.innerHTML = '🗑️ מוחקת קבוצה...';
    document.body.appendChild(loadingMessage);

    const formData = new FormData();
    formData.append('action', 'delete_group');
    formData.append('group_id', groupId);
    formData.append('nonce', '<?php echo wp_create_nonce("delete_group_nonce"); ?>');

    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();

        if (data.success) {
            let successMessage = `✅ הקבוצה "${groupName}" נמחקה בהצלחה!`;
            if (data.data.participants_updated > 0) {
                successMessage += `\n\n${data.data.participants_updated} משתתפות הועברו לליווי אישי.`;
            }
            alert(successMessage);
            window.location.reload();
        } else {
            alert('❌ שגיאה: ' + (data.data || 'לא ניתן למחוק את הקבוצה'));
        }
    })
    .catch(error => {
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();
        console.error('Error:', error);
        alert('❌ אירעה שגיאה במהלך המחיקה. נסה שוב.');
    });
}
</script>

<?php get_footer(); ?>
