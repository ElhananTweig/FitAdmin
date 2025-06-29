<?php
// עמוד מעקב מתאמנות שסיימו
if (!defined('ABSPATH')) {
    exit;
}

// פונקציה מאופטמת לקבלת מתאמנות שסיימו + מתאמנות פוטנציאליות
function get_finished_clients_with_follow_up($filter = 'all') {
    global $wpdb;
    $today = date('Y-m-d');
    
    // שאילתה ישירה לדטאבייס - מהירה יותר
    $sql = "
        SELECT DISTINCT p.ID 
        FROM {$wpdb->posts} p
        INNER JOIN {$wpdb->postmeta} pm1 ON p.ID = pm1.post_id
        LEFT JOIN {$wpdb->postmeta} pm2 ON p.ID = pm2.post_id AND pm2.meta_key = 'is_contact_lead'
        LEFT JOIN {$wpdb->postmeta} pm3 ON p.ID = pm3.post_id AND pm3.meta_key = 'is_frozen'
        WHERE p.post_type = 'clients' 
        AND p.post_status = 'publish'
        AND (
            (pm1.meta_key = 'end_date' AND pm1.meta_value < %s AND (pm3.meta_value IS NULL OR pm3.meta_value != '1'))
            OR
            (pm2.meta_key = 'is_contact_lead' AND pm2.meta_value = '1')
        )
    ";
    
    // הוספת פילטר לפי הצורך
    if ($filter === 'finished') {
        $sql .= " AND (pm2.meta_value IS NULL OR pm2.meta_value != '1')";
    } elseif ($filter === 'leads') {
        $sql .= " AND pm2.meta_value = '1'";
    }
    
    $sql .= " ORDER BY pm1.meta_value DESC";
    
    $client_ids = $wpdb->get_col($wpdb->prepare($sql, $today));
    
    if (empty($client_ids)) {
        return array();
    }
    
    // קבלת כל הנתונים של ACF בפעם אחת - חיסכון עצום בשאילתות
    $clients_data = array();
    $ids_string = implode(',', array_map('intval', $client_ids));
    
    $meta_sql = "
        SELECT post_id, meta_key, meta_value 
        FROM {$wpdb->postmeta} 
        WHERE post_id IN ($ids_string) 
        AND meta_key IN ('first_name', 'last_name', 'phone', 'end_date', 'last_contact_date', 'next_contact_date', 'follow_up_notes', 'is_contact_lead')
    ";
    
    $all_meta = $wpdb->get_results($meta_sql);
    
    // ארגון הנתונים לפי מתאמנת - מאחד את כל השאילתות
    $organized_clients = array();
    foreach ($all_meta as $meta) {
        $organized_clients[$meta->post_id][$meta->meta_key] = $meta->meta_value;
    }
    
    // המרה לפורמט התואם לקוד הקיים
    $result = array();
    foreach ($client_ids as $client_id) {
        $client = new stdClass();
        $client->ID = $client_id;
        $result[] = $client;
    }
    
    return $result;
}

// פונקציה עזר לקבלת נתוני ACF בצורה מאופטמת
function get_clients_meta_optimized($client_ids) {
    if (empty($client_ids)) {
        return array();
    }
    
    global $wpdb;
    $ids_string = implode(',', array_map('intval', $client_ids));
    
    $meta_sql = "
        SELECT post_id, meta_key, meta_value 
        FROM {$wpdb->postmeta} 
        WHERE post_id IN ($ids_string) 
        AND meta_key IN ('first_name', 'last_name', 'phone', 'end_date', 'last_contact_date', 'next_contact_date', 'follow_up_notes', 'is_contact_lead')
    ";
    
    $all_meta = $wpdb->get_results($meta_sql);
    
    // ארגון הנתונים לפי מתאמנת
    $organized_clients = array();
    foreach ($all_meta as $meta) {
        $organized_clients[$meta->post_id][$meta->meta_key] = $meta->meta_value;
    }
    
    return $organized_clients;
}

// הצגת הודעות הצלחה/שגיאה
$show_success = isset($_GET['updated']) && $_GET['updated'] == '1';
$show_added = isset($_GET['added']) && $_GET['added'] == '1';
$show_error = isset($_GET['error']) && $_GET['error'] != '';
$error_type = isset($_GET['error']) ? intval($_GET['error']) : 0;
$updated_client_id = isset($_GET['client']) ? intval($_GET['client']) : 0;

// קבלת שם המתאמנת שעודכנה/נוספה
$updated_client_name = '';
if ($updated_client_id && function_exists('get_field')) {
    $first_name = get_field('first_name', $updated_client_id);
    $last_name = get_field('last_name', $updated_client_id);
    $updated_client_name = trim($first_name . ' ' . $last_name);
}

// קבלת פילטר מהפרמטרים
$client_type_filter = isset($_GET['client_type']) ? sanitize_text_field($_GET['client_type']) : 'all';
$finished_clients = get_finished_clients_with_follow_up($client_type_filter);

// קבלת כל הנתונים של ACF בפעם אחת - אופטימיזציה חשובה!
$client_ids = array_map(function($client) { return $client->ID; }, $finished_clients);
$clients_meta_data = get_clients_meta_optimized($client_ids);

// הוספת CSS ו-JS נפרדים לביצועים טובים יותר
wp_enqueue_style('finished-clients-style', get_template_directory_uri() . '/assets/css/finished-clients.css', array(), '1.0');
wp_enqueue_script('finished-clients-script', get_template_directory_uri() . '/assets/js/finished-clients.js', array(), '1.0', true);
?>

<div class="wrap follow-up-container" dir="rtl">
        <!-- כותרת העמוד -->
        <div class="page-header">
            <h1>📝 מעקב מתאמנות שסיימו</h1>
            <p>מעקב אחר מתאמנות שסיימו טיפול להזמנתן בעתיד</p>
        </div>

        <!-- הודעות מערכת -->
        <?php if ($show_success && $updated_client_name): ?>
            <div class="success-message">
                <div class="success-icon">✅</div>
                <div class="success-text">
                    <strong>מתאמנת עודכנה בהצלחה!</strong><br>
                    <span><?php echo esc_html($updated_client_name); ?> עודכנה בהצלחה</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($show_added && $updated_client_name): ?>
            <div class="success-message">
                <div class="success-icon">🎉</div>
                <div class="success-text">
                    <strong>מתאמנת נוספה בהצלחה!</strong><br>
                    <span><?php echo esc_html($updated_client_name); ?> נוספה למעקב</span>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($show_error): ?>
            <div class="error-message">
                <div class="error-icon">❌</div>
                <div class="error-text">
                    <strong>שגיאה!</strong><br>
                    <span>
                        <?php 
                        switch($error_type) {
                            case 1: echo 'שדות חובה חסרים'; break;
                            case 2: echo 'שגיאה בשמירת הנתונים'; break;
                            case 3: echo 'מתאמנת עם אותו טלפון כבר קיימת'; break;
                            default: echo 'אירעה שגיאה לא צפויה'; break;
                        }
                        ?>
                    </span>
                </div>
            </div>
        <?php endif; ?>

        <!-- סטטיסטיקות מהירות -->
        <div class="stats-overview">
            <?php 
            // חישוב סטטיסטיקות
            $all_clients = get_finished_clients_with_follow_up('all');
            $finished_clients_only = get_finished_clients_with_follow_up('finished');
            $contact_leads_only = get_finished_clients_with_follow_up('leads');
            
            $need_contact = 0;
            $today = date('Y-m-d');
            foreach ($finished_clients as $client) {
                $last_contact = get_field('last_contact_date', $client->ID);
                if (!$last_contact || $last_contact < date('Y-m-d', strtotime('-30 days'))) {
                    $need_contact++;
                }
            }
            ?>
            
            <div class="stat-card total">
                <div class="stat-number"><?php echo count($all_clients); ?></div>
                <div class="stat-label">סה"כ למעקב</div>
            </div>
            
            <div class="stat-card" style="border-top-color: #3b82f6;">
                <div class="stat-number"><?php echo count($finished_clients_only); ?></div>
                <div class="stat-label">מתאמנות שסיימו</div>
            </div>
            
            <div class="stat-card" style="border-top-color: #10b981;">
                <div class="stat-number"><?php echo count($contact_leads_only); ?></div>
                <div class="stat-label">מתאמנות פוטנציאליות</div>
            </div>
            
            <div class="stat-card need-contact">
                <div class="stat-number"><?php echo $need_contact; ?></div>
                <div class="stat-label">דורשות מעקב</div>
            </div>
            
            <div class="stat-card contacted">
                <div class="stat-number"><?php echo count($finished_clients) - $need_contact; ?></div>
                <div class="stat-label">נוצר קשר לאחרונה</div>
            </div>
        </div>

        <!-- פילטרים -->
        <div class="filters-section">
            <h3 style="margin-top: 0; margin-bottom: 15px; color: rgba(255, 255, 255, 0.9);">🔍 פילטרים</h3>
            
            <!-- פילטר סוג מתאמנת -->
            <div style="margin-bottom: 20px; padding: 15px; background: #f8fafc; border-radius: 10px; border: 1px solid #e2e8f0;">
                <h4 style="margin: 0 0 10px 0; color: #374151; font-size: 1rem;">סוג מתאמנת:</h4>
                <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                    <a href="?client_type=all" class="filter-btn <?php echo ($client_type_filter === 'all') ? 'active' : ''; ?>" 
                       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s; <?php echo ($client_type_filter === 'all') ? 'background: #6366f1; color: white;' : 'background: white; color: #6366f1; border: 1px solid #6366f1;'; ?>">
                        👥 הכל (<?php echo count($all_clients); ?>)
                    </a>
                    <a href="?client_type=finished" class="filter-btn <?php echo ($client_type_filter === 'finished') ? 'active' : ''; ?>" 
                       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s; <?php echo ($client_type_filter === 'finished') ? 'background: #3b82f6; color: white;' : 'background: white; color: #3b82f6; border: 1px solid #3b82f6;'; ?>">
                        💪 שסיימו (<?php echo count($finished_clients_only); ?>)
                    </a>
                    <a href="?client_type=leads" class="filter-btn <?php echo ($client_type_filter === 'leads') ? 'active' : ''; ?>" 
                       style="padding: 8px 16px; border-radius: 20px; text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: all 0.3s; <?php echo ($client_type_filter === 'leads') ? 'background: #10b981; color: white;' : 'background: white; color: #10b981; border: 1px solid #10b981;'; ?>">
                        📞 פוטנציאליות (<?php echo count($contact_leads_only); ?>)
                    </a>
                </div>
            </div>
            
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

        <!-- כפתורי הוספת מתאמנות -->
        <div style="text-align: center; margin-bottom: 30px; padding: 25px; background: linear-gradient(135deg, #f0fdf4, #ecfdf5); border-radius: 15px; border: 1px solid #bbf7d0;">
            <h3 style="color: #065f46; margin: 0 0 20px 0; font-size: 1.2rem;">➕ הוסף מתאמנת חדשה</h3>
            
            <div style="display: flex; gap: 20px; justify-content: center; flex-wrap: wrap; margin-bottom: 15px;">
                <button type="button" onclick="openAddFinishedClientModal()" 
                        style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 15px 25px; border: none; border-radius: 12px; font-size: 15px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3); transition: all 0.3s;">
                    👥 הוסף מתאמנת שסיימה
                </button>
                
                <button type="button" onclick="openAddContactLeadModal()" 
                        style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 15px 25px; border: none; border-radius: 12px; font-size: 15px; font-weight: bold; cursor: pointer; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3); transition: all 0.3s;">
                    📞 הוסף מתאמנת פוטנציאלית
                </button>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-top: 15px;">
                <div style="background: rgba(59, 130, 246, 0.1); padding: 15px; border-radius: 10px; border: 1px solid rgba(59, 130, 246, 0.2);">
                    <p style="color: #1e40af; font-size: 0.9rem; margin: 0 0 8px 0; font-weight: 600;">
                        👥 מתאמנת שסיימה
                    </p>
                    <p style="color: #6b7280; font-size: 0.8rem; margin: 0;">
                        מתאמנת שהייתה אצלך בטיפול וסיימה - תופיע בכל הסטטיסטיקות
                    </p>
                </div>
                
                <div style="background: rgba(16, 185, 129, 0.1); padding: 15px; border-radius: 10px; border: 1px solid rgba(16, 185, 129, 0.2);">
                    <p style="color: #047857; font-size: 0.9rem; margin: 0 0 8px 0; font-weight: 600;">
                        📞 מתאמנת פוטנציאלית
                    </p>
                    <p style="color: #6b7280; font-size: 0.8rem; margin: 0;">
                        מתאמנת שלא הייתה אצלך - רק למעקב ויצירת קשר עתידי
                    </p>
                </div>
            </div>
        </div>

        <!-- רשימת מתאמנות -->
        <div id="clientsList">
            <?php if ($finished_clients): ?>
                <div class="clients-grid">
                <?php foreach ($finished_clients as $client): 
                    $client_id = $client->ID;
                    // שימוש בנתונים המאופטמים במקום get_field - חיסכון עצום בשאילתות!
                    $client_data = isset($clients_meta_data[$client_id]) ? $clients_meta_data[$client_id] : array();
                    $first_name = isset($client_data['first_name']) ? $client_data['first_name'] : '';
                    $last_name = isset($client_data['last_name']) ? $client_data['last_name'] : '';
                    $phone = isset($client_data['phone']) ? $client_data['phone'] : '';
                    $end_date = isset($client_data['end_date']) ? $client_data['end_date'] : '';
                    $last_contact = isset($client_data['last_contact_date']) ? $client_data['last_contact_date'] : '';
                    $next_contact = isset($client_data['next_contact_date']) ? $client_data['next_contact_date'] : '';
                    $follow_up_notes = isset($client_data['follow_up_notes']) ? $client_data['follow_up_notes'] : '';
                    $is_contact_lead = isset($client_data['is_contact_lead']) && $client_data['is_contact_lead'] == '1';
                    
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
                    <div class="client-follow-card<?php echo $highlight_class; ?>" 
                         data-name="<?php echo $first_name . ' ' . $last_name; ?>" 
                         data-status="<?php echo $contact_status; ?>" 
                         data-end-date="<?php echo date('Y-m', strtotime($end_date)); ?>"
                         data-is-lead="<?php echo $is_contact_lead ? 'true' : 'false'; ?>">
                        <div class="client-header">
                            <div class="client-main-info">
                                <div class="client-name-main"><?php echo $first_name . ' ' . $last_name; ?></div>
                                <?php if ($is_contact_lead): ?>
                                    <span class="badge-lead">📞 פוטנציאלית</span>
                                <?php else: ?>
                                    <span class="badge-finished">💪 סיימה</span>
                                <?php endif; ?>
                                <a href="tel:<?php echo $phone; ?>" class="client-phone-main">
                                    📞 <?php echo $phone; ?>
                                </a>
                                <div class="contact-status <?php echo $contact_status; ?>">
                                    <?php echo $contact_status_text; ?>
                                </div>
                            </div>
                            <div class="client-actions">
                                <button type="button" 
                                        onclick="openEditModal(<?php echo $client_id; ?>, '<?php echo esc_js($first_name); ?>', '<?php echo esc_js($last_name); ?>', '<?php echo esc_js($phone); ?>', '<?php echo esc_js($last_contact); ?>', '<?php echo esc_js($next_contact); ?>', '<?php echo esc_js($follow_up_notes); ?>', <?php echo $is_contact_lead ? 'true' : 'false'; ?>)" 
                                        class="btn-glow edit" title="ערוך מתאמנת">
                                    ✏️
                                </button>
                                <?php 
                                // המרת מספר טלפון ישראלי לפורמט בינלאומי עבור וואצאפ
                                $whatsapp_number = $phone;
                                if (substr($phone, 0, 1) === '0') {
                                    $whatsapp_number = '972' . substr($phone, 1);
                                }
                                ?>
                                <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="btn-glow whatsapp" title="שלח וואצאפ">
                                    💬
                                </a>
                                <button type="button" onclick="deleteClient(<?php echo $client_id; ?>, '<?php echo esc_js($first_name . ' ' . $last_name); ?>')" class="btn-glow delete" title="מחק מתאמנת">
                                    🗑️
                                </button>
                            </div>
                        </div>

                        <div class="follow-up-section">
                            <div class="follow-up-info">
                                <div class="follow-up-item">
                                    <div class="follow-up-label">קשר אחרון</div>
                                    <div class="follow-up-value"><?php echo $last_contact ? date('d/m/Y', strtotime($last_contact)) : 'לא נרשם'; ?></div>
                                </div>
                                
                                <div class="follow-up-item">
                                    <div class="follow-up-label">מעקב הבא</div>
                                    <div class="follow-up-value"><?php echo $next_contact ? date('d/m/Y', strtotime($next_contact)) : 'לא נקבע'; ?></div>
                                </div>
                                
                                <?php if (!$is_contact_lead && $end_date): ?>
                                <div class="follow-up-item">
                                    <div class="follow-up-label">סיום טיפול</div>
                                    <div class="follow-up-value"><?php echo date('d/m/Y', strtotime($end_date)); ?></div>
                                </div>
                                <?php endif; ?>
                                
                                <?php if ($follow_up_notes): ?>
                                <div class="follow-up-item follow-up-notes">
                                    <div class="follow-up-label">הערות</div>
                                    <div class="follow-up-value"><?php echo nl2br(esc_html($follow_up_notes)); ?></div>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-clients-message">
                    <div style="font-size: 4rem; margin-bottom: 20px;">🎉</div>
                    <h3>אין מתאמנות שסיימו עדיין</h3>
                    <p>כשמתאמנות יגיעו לתאריך הסיום שלהן, הן יופיעו כאן</p>
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

// פונקציה למחיקת מתאמנת
function deleteClient(clientId, clientName) {
    // אזהרה לפני מחיקה
    const confirmation = confirm(
        `האם את בטוחה שברצונך למחוק את המתאמנת "${clientName}"?\n\n` +
        `⚠️ זוהי פעולה בלתי הפיכה!\n` +
        `כל הנתונים של המתאמנת יימחקו לצמיתות כולל:\n` +
        `• פרטים אישיים\n` +
        `• היסטוריית משקל\n` +
        `• הערות מעקב\n` +
        `• כל המידע הקשור אליה\n\n` +
        `האם להמשיך?`
    );
    
    if (!confirmation) {
        return; // המשתמש ביטל
    }
    
    // הצגת הודעת טעינה
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
    loadingMessage.innerHTML = '🗑️ מוחקת מתאמנת...';
    document.body.appendChild(loadingMessage);
    
    // שליחת בקשת AJAX למחיקה
    const formData = new FormData();
    formData.append('action', 'delete_client');
    formData.append('client_id', clientId);
    formData.append('nonce', '<?php echo wp_create_nonce("delete_client_nonce"); ?>');
    
    fetch('<?php echo admin_url("admin-ajax.php"); ?>', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        // הסרת הודעת הטעינה
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();
        
        if (data.success) {
            // הצגת הודעת הצלחה
            alert(`✅ המתאמנת "${clientName}" נמחקה בהצלחה!`);
            
            // רענון הדף
            window.location.reload();
        } else {
            alert('❌ שגיאה: ' + (data.data || 'לא ניתן למחוק את המתאמנת'));
        }
    })
    .catch(error => {
        // הסרת הודעת הטעינה
        const loading = document.getElementById('delete-loading');
        if (loading) loading.remove();
        
        console.error('Error:', error);
        alert('❌ אירעה שגיאה במהלך המחיקה. נסה שוב.');
    });
}

// פונקציה לפתיחת מודל עריכה (אם קיים)
function openEditClientModal(clientId) {
    // בדיקה אם יש מודל עריכה זמין
    if (typeof window.openEditClientModal === 'function') {
        window.openEditClientModal(clientId);
    } else {
        // אם אין מודל זמין, הפניה לעמוד הראשי
        window.location.href = '/clients/?edit=' + clientId;
    }
}

// פונקציה לפתיחת מודל הוספת מתאמנת שסיימה (מלא)
function openAddFinishedClientModal() {
    const modal = document.getElementById('addFinishedClientModal');
    if (modal) {
        modal.style.display = 'block';
        // איפוס הטופס
        document.getElementById('addFinishedClientForm').reset();
        // הגדרת תאריך התחלה וסיום לאתמול
        const yesterday = new Date();
        yesterday.setDate(yesterday.getDate() - 1);
        const yesterdayStr = yesterday.toISOString().split('T')[0];
        document.getElementById('add_start_date').value = yesterdayStr;
        document.getElementById('add_end_date').value = yesterdayStr;
        // הגדרת תאריך הקשר האחרון לתאריך הנוכחי
        document.getElementById('add_last_contact_date').value = new Date().toISOString().split('T')[0];
    }
}

// פונקציה לפתיחת מודל הוספת מתאמנת פוטנציאלית (קצר)
function openAddContactLeadModal() {
    const modal = document.getElementById('addContactLeadModal');
    if (modal) {
        modal.style.display = 'block';
        // איפוס הטופס
        document.getElementById('addContactLeadForm').reset();
        // הגדרת תאריך הקשר האחרון לתאריך הנוכחי
        document.getElementById('lead_last_contact_date').value = new Date().toISOString().split('T')[0];
    }
}

// פונקציה לסגירת המודל הוספת מתאמנת שסיימה
function closeAddFinishedClientModal() {
    const modal = document.getElementById('addFinishedClientModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// פונקציה לסגירת המודל הוספת מתאמנת פוטנציאלית
function closeAddContactLeadModal() {
    const modal = document.getElementById('addContactLeadModal');
    if (modal) {
        modal.style.display = 'none';
    }
}

// סגירת המודלים בלחיצה על הרקע
window.onclick = function(event) {
    const finishedModal = document.getElementById('addFinishedClientModal');
    const leadModal = document.getElementById('addContactLeadModal');
    
    if (event.target === finishedModal) {
        closeAddFinishedClientModal();
    }
    if (event.target === leadModal) {
        closeAddContactLeadModal();
    }
}
</script>

<!-- מודל הוספת מתאמנת שסיימה -->
<div id="addFinishedClientModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); direction: rtl; overflow-y: auto;">
    <div style="background-color: white; margin: 3% auto; padding: 0; border-radius: 15px; width: 95%; max-width: 800px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: modalSlideIn 0.3s ease-out;">
        <!-- כותרת המודל -->
        <div style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 25px 30px; border-radius: 15px 15px 0 0; text-align: center;">
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">
                👥 הוסף מתאמנת שסיימה
            </h2>
            <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 0.95rem;">
                מתאמנת שהייתה אצלך בטיפול וסיימה - תופיע בכל הסטטיסטיקות
            </p>
        </div>
        
        <!-- תוכן המודל -->
        <div style="padding: 30px; max-height: 80vh; overflow-y: auto;">
            <form id="addFinishedClientForm" method="post">
                <?php wp_nonce_field('add_finished_client_action', 'add_finished_client_nonce'); ?>
                <input type="hidden" name="action" value="add_finished_client">
                <input type="hidden" name="is_finished_client" value="1">
                
                <!-- פרטים אישיים -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #3b82f6; padding-bottom: 8px;">
                        👤 פרטים אישיים
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                שם פרטי <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="first_name" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                שם משפחה <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="last_name" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                טלפון <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="tel" name="phone" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                   placeholder="050-1234567">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                אימייל
                            </label>
                            <input type="email" name="email" 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                                   placeholder="example@email.com">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                גיל
                            </label>
                            <input type="number" name="age" min="1" max="120" 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>
                
                <!-- תאריכי טיפול -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #10b981; padding-bottom: 8px;">
                        📅 תאריכי טיפול
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך התחלה
                            </label>
                            <input type="date" name="start_date" id="add_start_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך סיום
                            </label>
                            <input type="date" name="end_date" id="add_end_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>
                
                <!-- מקור הגעה וליווי -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #f59e0b; padding-bottom: 8px;">
                        🎯 מקור הגעה וליווי
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                מקור הגעה
                            </label>
                            <select name="referral_source" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                <option value="instagram">אינסטגרם</option>
                                <option value="status">סטטוס</option>
                                <option value="whatsapp">וואצאפ</option>
                                <option value="referral">המלצה</option>
                                <option value="mentor">מנטורית</option>
                                <option value="unknown">לא ידוע</option>
                            </select>
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                סוג ליווי
                            </label>
                            <select name="training_type" style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                                <option value="personal">אישי</option>
                                <option value="group">קבוצתי</option>
                            </select>
                        </div>
                    </div>
                </div>
                
                <!-- משקל -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #8b5cf6; padding-bottom: 8px;">
                        ⚖️ משקל
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                משקל התחלה
                            </label>
                            <input type="number" name="start_weight" step="0.1" min="0" 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                משקל סיום
                            </label>
                            <input type="number" name="current_weight" step="0.1" min="0" 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                משקל יעד
                            </label>
                            <input type="number" name="target_weight" step="0.1" min="0" 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                </div>
                
                <!-- הערות -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #6b7280; padding-bottom: 8px;">
                        📝 הערות
                    </h3>
                    
                    <div>
                        <textarea name="notes" rows="3" 
                                  style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="הערות כלליות על המתאמנת..."></textarea>
                    </div>
                </div>
                
                <!-- מעקב ויצירת קשר -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #ef4444; padding-bottom: 8px;">
                        📞 מעקב ויצירת קשר
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך קשר אחרון
                            </label>
                            <input type="date" name="last_contact_date" id="add_last_contact_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך מעקב הבא
                            </label>
                            <input type="date" name="next_contact_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                            הערות מעקב
                        </label>
                        <textarea name="follow_up_notes" rows="4" 
                                  style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="הוסף הערות על המתאמנת, היסטוריית קשר, הערות חשובות..."></textarea>
                    </div>
                </div>
                
                <!-- כפתורי פעולה -->
                <div style="display: flex; gap: 12px; justify-content: flex-start; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <button type="submit" 
                            style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 12px 25px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        ✅ הוסף מתאמנת שסיימה
                    </button>
                    
                    <button type="button" onclick="closeAddFinishedClientModal()" 
                            style="background: #6b7280; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer;">
                        ❌ ביטול
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- מודל הוספת מתאמנת פוטנציאלית -->
<div id="addContactLeadModal" style="display: none; position: fixed; z-index: 10000; left: 0; top: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); direction: rtl;">
    <div style="background-color: white; margin: 5% auto; padding: 0; border-radius: 15px; width: 90%; max-width: 600px; box-shadow: 0 20px 60px rgba(0,0,0,0.3); animation: modalSlideIn 0.3s ease-out;">
        <!-- כותרת המודל -->
        <div style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 25px 30px; border-radius: 15px 15px 0 0; text-align: center;">
            <h2 style="margin: 0; font-size: 1.5rem; font-weight: 700;">
                📞 הוסף מתאמנת פוטנציאלית
            </h2>
            <p style="margin: 8px 0 0 0; opacity: 0.9; font-size: 0.95rem;">
                מתאמנת שלא הייתה אצלך - רק למעקב ויצירת קשר עתידי
            </p>
        </div>
        
        <!-- תוכן המודל -->
        <div style="padding: 30px;">
            <form id="addContactLeadForm" method="post">
                <?php wp_nonce_field('add_contact_lead_action', 'add_contact_lead_nonce'); ?>
                <input type="hidden" name="action" value="add_contact_lead">
                <input type="hidden" name="is_contact_lead" value="1">
                
                <!-- פרטים בסיסיים -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #10b981; padding-bottom: 8px;">
                        👤 פרטים בסיסיים
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                שם פרטי <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="first_name" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                שם משפחה <span style="color: #dc2626;">*</span>
                            </label>
                            <input type="text" name="last_name" required 
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                            טלפון <span style="color: #dc2626;">*</span>
                        </label>
                        <input type="tel" name="phone" required 
                               style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;"
                               placeholder="050-1234567">
                    </div>
                </div>
                
                <!-- מעקב ויצירת קשר -->
                <div style="margin-bottom: 25px;">
                    <h3 style="color: #374151; margin-bottom: 15px; font-size: 1.1rem; border-bottom: 2px solid #ef4444; padding-bottom: 8px;">
                        📞 מעקב ויצירת קשר
                    </h3>
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 15px;">
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך קשר אחרון
                            </label>
                            <input type="date" name="last_contact_date" id="lead_last_contact_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                        
                        <div>
                            <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                                תאריך מעקב הבא
                            </label>
                            <input type="date" name="next_contact_date"
                                   style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px;">
                        </div>
                    </div>
                    
                    <div>
                        <label style="display: block; font-weight: 600; margin-bottom: 5px; color: #374151;">
                            הערות מעקב
                        </label>
                        <textarea name="follow_up_notes" rows="4" 
                                  style="width: 100%; padding: 12px; border: 1px solid #d1d5db; border-radius: 8px; font-size: 14px; resize: vertical;"
                                  placeholder="הוסף הערות על המתאמנת, היסטוריית קשר, הערות חשובות..."></textarea>
                    </div>
                </div>
                
                <!-- כפתורי פעולה -->
                <div style="display: flex; gap: 12px; justify-content: flex-start; border-top: 1px solid #e5e7eb; padding-top: 20px;">
                    <button type="submit" 
                            style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 12px 25px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer; transition: all 0.3s;">
                        ✅ הוסף מתאמנת פוטנציאלית
                    </button>
                    
                    <button type="button" onclick="closeAddContactLeadModal()" 
                            style="background: #6b7280; color: white; padding: 12px 25px; border: none; border-radius: 8px; font-size: 15px; font-weight: 600; cursor: pointer;">
                        ❌ ביטול
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>



<?php get_footer(); ?> 