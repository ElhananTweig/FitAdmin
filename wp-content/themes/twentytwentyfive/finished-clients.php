<?php
// עמוד מעקב מתאמנות שסיימו
if (!defined('ABSPATH')) {
    exit;
}

// פונקציה לקבלת מתאמנות שסיימו
function get_finished_clients_with_follow_up() {
    $today = date('Y-m-d');
    
    return get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => array(
            array(
                'key' => 'end_date',
                'value' => $today,
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
}

// הצגת הודעת הצלחה אם יש פרמטר updated
$show_success = isset($_GET['updated']) && $_GET['updated'] == '1';
$show_error = isset($_GET['error']) && $_GET['error'] == '1';
$updated_client_id = isset($_GET['client']) ? intval($_GET['client']) : 0;

// קבלת שם המתאמנת שעודכנה
$updated_client_name = '';
if ($updated_client_id && function_exists('get_field')) {
    $first_name = get_field('first_name', $updated_client_id);
    $last_name = get_field('last_name', $updated_client_id);
    $updated_client_name = trim($first_name . ' ' . $last_name);
}

$finished_clients = get_finished_clients_with_follow_up();
?>

<div class="wrap" dir="rtl">
    <style>
        .follow-up-container {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            direction: rtl;
            text-align: right;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .page-header {
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .page-header h1 {
            margin: 0 0 10px 0;
            font-size: 2.5rem;
            font-weight: 700;
        }
        
        .page-header p {
            margin: 0;
            opacity: 0.9;
            font-size: 1.1rem;
        }
        
        .stats-overview {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            text-align: center;
            border-top: 4px solid;
        }
        
        .stat-card.total { border-top-color: #3b82f6; }
        .stat-card.need-contact { border-top-color: #f59e0b; }
        .stat-card.contacted { border-top-color: #10b981; }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .stat-label {
            color: #6b7280;
            font-size: 0.9rem;
        }
        
        .client-follow-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            margin-bottom: 20px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            border-right: 5px solid #6366f1;
            transition: all 0.3s ease;
        }
        
        .client-follow-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.12);
        }
        
        .client-header {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .client-main-info {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        
        .client-name-main {
            font-size: 1.4rem;
            font-weight: 600;
            color: #1f2937;
        }
        
        .client-phone-main {
            color: #3b82f6;
            text-decoration: none;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .client-phone-main:hover {
            text-decoration: underline;
        }
        
        .end-date-badge {
            background: #f3f4f6;
            color: #374151;
            padding: 8px 15px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 500;
            text-align: center;
        }
        
        .follow-up-section {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-top: 15px;
        }
        
        .follow-up-info {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
        }
        
        .follow-up-form {
            background: #fefefe;
            border: 1px solid #e5e7eb;
            padding: 15px;
            border-radius: 10px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        .form-group label {
            display: block;
            font-weight: 600;
            color: #374151;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }
        
        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 14px;
            direction: rtl;
        }
        
        .form-group textarea {
            resize: vertical;
            min-height: 80px;
        }
        
        .update-btn {
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .update-btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        }
        
        .contact-status {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 0.8rem;
            font-weight: 500;
        }
        
        .contact-status.recent {
            background: #d1fae5;
            color: #065f46;
        }
        
        .contact-status.overdue {
            background: #fef3c7;
            color: #92400e;
        }
        
        .contact-status.no-contact {
            background: #fee2e2;
            color: #991b1b;
        }
        
        .filters-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.08);
        }
        
        .filters-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
        }
        
        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        
        .filter-group label {
            font-weight: 500;
            color: #374151;
            font-size: 0.9rem;
        }
        
        .filter-group input,
        .filter-group select {
            padding: 8px 12px;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            direction: rtl;
        }
        
        /* הדגשת כרטיס שזה עתה עודכן */
        .client-follow-card.recently-updated {
            border-right: 5px solid #10b981;
            background: linear-gradient(135deg, #f0fdf4, #ffffff);
            animation: highlightPulse 2s ease-in-out;
        }
        
        @keyframes highlightPulse {
            0%, 100% { box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
            50% { box-shadow: 0 8px 30px rgba(16, 185, 129, 0.3); }
        }
        
        @media (max-width: 768px) {
            .follow-up-section {
                grid-template-columns: 1fr;
            }
            
            .client-header {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .stats-overview {
                grid-template-columns: 1fr;
            }
        }
    </style>

    <div class="follow-up-container">
        <!-- כותרת העמוד -->
        <div class="page-header">
            <h1>📝 מעקב מתאמנות שסיימו</h1>
            <p>מעקב אחר מתאמנות שסיימו טיפול להזמנתן בעתיד</p>
        </div>

        <?php if ($show_success): ?>
            <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                <strong>✅ המעקב עודכן בהצלחה!</strong>
                <?php if ($updated_client_name): ?>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem;">עדכון מעקב עבור <?php echo $updated_client_name; ?></p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_error): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                <strong>❌ שגיאה בעדכון המעקב!</strong>
                <p style="margin: 5px 0 0 0; font-size: 0.9rem;">תוסף Advanced Custom Fields לא זמין או אירעה שגיאה טכנית.</p>
            </div>
        <?php endif; ?>

        <!-- סטטיסטיקות מהירות -->
        <div class="stats-overview">
            <div class="stat-card total">
                <div class="stat-number"><?php echo count($finished_clients); ?></div>
                <div class="stat-label">סה"כ מתאמנות שסיימו</div>
            </div>
            
            <div class="stat-card need-contact">
                <div class="stat-number">
                    <?php 
                    $need_contact = 0;
                    $today = date('Y-m-d');
                    foreach ($finished_clients as $client) {
                        $last_contact = get_field('last_contact_date', $client->ID);
                        if (!$last_contact || $last_contact < date('Y-m-d', strtotime('-30 days'))) {
                            $need_contact++;
                        }
                    }
                    echo $need_contact;
                    ?>
                </div>
                <div class="stat-label">דורשות מעקב</div>
            </div>
            
            <div class="stat-card contacted">
                <div class="stat-number"><?php echo count($finished_clients) - $need_contact; ?></div>
                <div class="stat-label">נוצר קשר לאחרונה</div>
            </div>
        </div>

        <!-- פילטרים -->
        <div class="filters-section">
            <h3 style="margin-top: 0; margin-bottom: 15px; color: #374151;">🔍 פילטרים</h3>
            <div class="filters-grid">
                <div class="filter-group">
                    <label>חיפוש לפי שם</label>
                    <input type="text" id="nameFilter" placeholder="הקלד שם פרטי או משפחה...">
                </div>
                <div class="filter-group">
                    <label>סטטוס מעקב</label>
                    <select id="statusFilter">
                        <option value="">כל הסטטוסים</option>
                        <option value="no-contact">ללא קשר</option>
                        <option value="overdue">מעקב מתארך</option>
                        <option value="recent">נוצר קשר לאחרונה</option>
                    </select>
                </div>
                <div class="filter-group">
                    <label>תאריך סיום טיפול</label>
                    <input type="month" id="endDateFilter">
                </div>
                <div class="filter-group">
                    <label>&nbsp;</label>
                    <button type="button" id="clearFilters" style="background: #f59e0b; color: white; padding: 8px 16px; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">
                        🗑️ נקה פילטרים
                    </button>
                </div>
            </div>
            <div id="filterResults" style="margin-top: 15px; padding: 10px; background: #f3f4f6; border-radius: 6px; font-size: 0.9rem; color: #6b7280; display: none;">
                מציג <span id="visibleCount">0</span> מתוך <span id="totalCount">0</span> מתאמנות
            </div>
        </div>

        <!-- רשימת מתאמנות -->
        <div id="clientsList">
            <?php if ($finished_clients): ?>
                <?php foreach ($finished_clients as $client): 
                    $client_id = $client->ID;
                    $first_name = get_field('first_name', $client_id);
                    $last_name = get_field('last_name', $client_id);
                    $phone = get_field('phone', $client_id);
                    $end_date = get_field('end_date', $client_id);
                    $last_contact = get_field('last_contact_date', $client_id);
                    $next_contact = get_field('next_contact_date', $client_id);
                    $follow_up_notes = get_field('follow_up_notes', $client_id);
                    
                    // חישוב סטטוס מעקב
                    $contact_status = 'no-contact';
                    $contact_status_text = 'ללא קשר';
                    
                    if ($last_contact) {
                        $days_since_contact = (time() - strtotime($last_contact)) / (60 * 60 * 24);
                        if ($days_since_contact <= 14) {
                            $contact_status = 'recent';
                            $contact_status_text = 'נוצר קשר לאחרונה';
                        } elseif ($days_since_contact <= 30) {
                            $contact_status = 'overdue';
                            $contact_status_text = 'מעקב מתארך';
                        } else {
                            $contact_status = 'no-contact';
                            $contact_status_text = 'דורש מעקב';
                        }
                    }
                    
                    // הדגשה אם זה הכרטיס שזה עתה עודכן
                    $highlight_class = ($show_success && $updated_client_id == $client_id) ? ' recently-updated' : '';
                ?>
                    <div class="client-follow-card<?php echo $highlight_class; ?>" data-name="<?php echo $first_name . ' ' . $last_name; ?>" data-status="<?php echo $contact_status; ?>" data-end-date="<?php echo date('Y-m', strtotime($end_date)); ?>">
                        <div class="client-header">
                            <div class="client-main-info">
                                <div class="client-name-main"><?php echo $first_name . ' ' . $last_name; ?></div>
                                <a href="tel:<?php echo $phone; ?>" class="client-phone-main">
                                    📞 <?php echo $phone; ?>
                                </a>
                                <div class="contact-status <?php echo $contact_status; ?>">
                                    <?php echo $contact_status_text; ?>
                                </div>
                            </div>
                            <div class="end-date-badge">
                                סיום טיפול: <?php echo date('d/m/Y', strtotime($end_date)); ?>
                            </div>
                        </div>

                        <div class="follow-up-section">
                            <!-- מידע מעקב נוכחי -->
                            <div class="follow-up-info">
                                <h4 style="margin-top: 0; color: #374151;">📋 מידע מעקב</h4>
                                <p><strong>קשר אחרון:</strong> <?php echo $last_contact ? date('d/m/Y', strtotime($last_contact)) : 'לא נרשם'; ?></p>
                                <p><strong>מעקב הבא:</strong> <?php echo $next_contact ? date('d/m/Y', strtotime($next_contact)) : 'לא נקבע'; ?></p>
                                <?php if ($follow_up_notes): ?>
                                    <p><strong>הערות:</strong><br><?php echo nl2br(esc_html($follow_up_notes)); ?></p>
                                <?php endif; ?>
                            </div>

                            <!-- טופס עדכון -->
                            <div class="follow-up-form">
                                <h4 style="margin-top: 0; color: #374151;">✏️ עדכון מעקב</h4>
                                <form method="post" action="">
                                    <?php wp_nonce_field('update_follow_up', 'follow_up_nonce'); ?>
                                    <input type="hidden" name="client_id" value="<?php echo $client_id; ?>">
                                    
                                    <div class="form-group">
                                        <label>תאריך קשר אחרון</label>
                                        <input type="date" name="last_contact_date" value="<?php echo $last_contact; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>תאריך מעקב הבא</label>
                                        <input type="date" name="next_contact_date" value="<?php echo $next_contact; ?>">
                                    </div>
                                    
                                    <div class="form-group">
                                        <label>הערות מעקב</label>
                                        <textarea name="follow_up_notes" placeholder="הערות על השיחה, תוכניות עתידיות..."><?php echo esc_textarea($follow_up_notes); ?></textarea>
                                    </div>
                                    
                                    <button type="submit" name="update_follow_up" class="update-btn">
                                        💾 עדכן מעקב
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 15px;">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🎉</div>
                    <h3 style="color: #374151; margin-bottom: 10px;">אין מתאמנות שסיימו עדיין</h3>
                    <p style="color: #6b7280;">כשמתאמנות יגיעו לתאריך הסיום שלהן, הן יופיעו כאן</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // פילטרים
    const nameFilter = document.getElementById('nameFilter');
    const statusFilter = document.getElementById('statusFilter');
    const endDateFilter = document.getElementById('endDateFilter');
    const clearFilters = document.getElementById('clearFilters');
    const filterResults = document.getElementById('filterResults');
    const visibleCount = document.getElementById('visibleCount');
    const totalCount = document.getElementById('totalCount');
    
    const allCards = document.querySelectorAll('.client-follow-card');
    totalCount.textContent = allCards.length;
    
    function filterClients() {
        const nameValue = nameFilter.value.toLowerCase();
        const statusValue = statusFilter.value;
        const endDateValue = endDateFilter.value;
        
        let visibleCards = 0;
        
        allCards.forEach(card => {
            const name = card.dataset.name.toLowerCase();
            const status = card.dataset.status;
            const endDate = card.dataset.endDate;
            
            let show = true;
            
            // פילטר שם
            if (nameValue && !name.includes(nameValue)) {
                show = false;
            }
            
            // פילטר סטטוס
            if (statusValue && status !== statusValue) {
                show = false;
            }
            
            // פילטר תאריך
            if (endDateValue && endDate !== endDateValue) {
                show = false;
            }
            
            card.style.display = show ? 'block' : 'none';
            if (show) visibleCards++;
        });
        
        // עדכון ספירת תוצאות
        visibleCount.textContent = visibleCards;
        const hasActiveFilters = nameValue || statusValue || endDateValue;
        filterResults.style.display = hasActiveFilters ? 'block' : 'none';
    }
    
    // פונקציה לניקוי פילטרים
    function clearAllFilters() {
        nameFilter.value = '';
        statusFilter.value = '';
        endDateFilter.value = '';
        filterClients();
    }
    
    nameFilter.addEventListener('input', filterClients);
    statusFilter.addEventListener('change', filterClients);
    endDateFilter.addEventListener('change', filterClients);
    clearFilters.addEventListener('click', clearAllFilters);
    
    // הגדרת תאריך ברירת מחדל למעקב הבא (שבוע מהיום)
    document.querySelectorAll('input[name="next_contact_date"]').forEach(input => {
        if (!input.value) {
            const nextWeek = new Date();
            nextWeek.setDate(nextWeek.getDate() + 7);
            input.value = nextWeek.toISOString().split('T')[0];
        }
    });
    
    // גלילה אוטומטית לכרטיס שעודכן
    const updatedCard = document.querySelector('.client-follow-card.recently-updated');
    if (updatedCard) {
        setTimeout(() => {
            updatedCard.scrollIntoView({
                behavior: 'smooth',
                block: 'center'
            });
        }, 500);
    }
});
</script>

<?php get_footer(); ?> 