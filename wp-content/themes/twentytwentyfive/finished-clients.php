<?php
// עמוד מעקב מתאמנות שסיימו
if (!defined('ABSPATH')) {
    exit;
}

// פונקציה לקבלת מתאמנות שסיימו + מתאמנות פוטנציאליות
function get_finished_clients_with_follow_up($filter = 'all') {
    $today = date('Y-m-d');
    
    $meta_query = array(
        'relation' => 'AND',
        array(
            'relation' => 'OR',
            // מתאמנות שסיימו - שתאריך הסיום עבר
            array(
                'relation' => 'AND',
                array(
                    'key' => 'end_date',
                    'value' => $today,
                    'compare' => '<',
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
            // מתאמנות פוטנציאליות
            array(
                'key' => 'is_contact_lead',
                'value' => true,
                'compare' => '='
            )
        )
    );
    
    // הוספת פילטר לפי סוג
    if ($filter === 'finished') {
        // רק מתאמנות שסיימו (לא פוטנציאליות)
        $meta_query[] = array(
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
        );
    } elseif ($filter === 'leads') {
        // רק מתאמנות פוטנציאליות
        $meta_query[] = array(
            'key' => 'is_contact_lead',
            'value' => true,
            'compare' => '='
        );
    }
    
    return get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish',
        'meta_query' => $meta_query,
        'meta_key' => 'end_date',
        'orderby' => 'meta_value',
        'order' => 'DESC'
    ));
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
        
        /* כפתורי פעולות */
        .client-actions {
            display: flex;
            gap: 8px;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .action-btn {
            padding: 6px 12px;
            border: none;
            border-radius: 6px;
            font-size: 0.8rem;
            font-weight: 500;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            transition: all 0.2s;
        }
        
        .action-btn.primary {
            background: #3b82f6;
            color: white;
        }
        
        .action-btn.primary:hover {
            background: #2563eb;
        }
        
        .action-btn.secondary {
            background: #f3f4f6;
            color: #374151;
        }
        
        .action-btn.secondary:hover {
            background: #e5e7eb;
        }
        
        .action-btn.danger {
            background: #ef4444;
            color: white;
        }
        
        .action-btn.danger:hover {
            background: #dc2626;
        }
        
        .action-btn.whatsapp {
            background: #25d366;
            color: white;
        }
        
        .action-btn.whatsapp:hover {
            background: #128c7e;
            color: white;
        }

        /* כפתורי פילטר */
        .filter-btn {
            transition: all 0.3s ease;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        
        .filter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .filter-btn.active {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        /* סגנונות נוספים לכרטיסי מתאמנת */
        .client-follow-card {
            position: relative;
        }
        
        .client-follow-card::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 4px;
            height: 100%;
            background: #6366f1;
            border-radius: 0 15px 15px 0;
        }
        
        /* עיצוב מיוחד למתאמנות פוטנציאליות */
        .client-follow-card[data-is-lead="true"]::before {
            background: linear-gradient(135deg, #10b981, #059669);
        }
        
        .client-follow-card[data-is-lead="true"] {
            border-right: 5px solid #10b981;
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
            
            .filter-btn {
                font-size: 0.8rem;
                padding: 6px 12px;
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

        <?php if ($show_added): ?>
            <div style="background: #d1fae5; border: 1px solid #10b981; color: #065f46; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                <strong>🎉 המתאמנת נוספה בהצלחה למעקב!</strong>
                <?php if ($updated_client_name): ?>
                    <p style="margin: 5px 0 0 0; font-size: 0.9rem;"><?php echo $updated_client_name; ?> נוספה לרשימת המתאמנות למעקב</p>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($show_error): ?>
            <div style="background: #fee2e2; border: 1px solid #ef4444; color: #991b1b; padding: 15px 20px; border-radius: 10px; margin-bottom: 30px; text-align: center;">
                <strong>❌ 
                    <?php 
                    switch($error_type) {
                        case 2:
                            echo 'שגיאה: נא למלא את כל השדות החובה';
                            break;
                        case 3:
                            echo 'שגיאה ביצירת המתאמנת';
                            break;
                        default:
                            echo 'שגיאה כללית';
                    }
                    ?>
                </strong>
                <p style="margin: 5px 0 0 0; font-size: 0.9rem;">
                    <?php 
                    switch($error_type) {
                        case 2:
                            echo 'שם פרטי, שם משפחה וטלפון הם שדות חובה.';
                            break;
                        case 3:
                            echo 'אירעה שגיאה טכנית ביצירת המתאמנת. נסה שוב.';
                            break;
                        default:
                            echo 'תוסף Advanced Custom Fields לא זמין או אירעה שגיאה טכנית.';
                    }
                    ?>
                </p>
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
            <h3 style="margin-top: 0; margin-bottom: 15px; color: #374151;">🔍 פילטרים</h3>
            
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
                <?php foreach ($finished_clients as $client): 
                    $client_id = $client->ID;
                    $first_name = get_field('first_name', $client_id);
                    $last_name = get_field('last_name', $client_id);
                    $phone = get_field('phone', $client_id);
                    $end_date = get_field('end_date', $client_id);
                    $last_contact = get_field('last_contact_date', $client_id);
                    $next_contact = get_field('next_contact_date', $client_id);
                    $follow_up_notes = get_field('follow_up_notes', $client_id);
                    $is_contact_lead = get_field('is_contact_lead', $client_id);
                    
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
                                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 8px;">
                                    <div class="client-name-main"><?php echo $first_name . ' ' . $last_name; ?></div>
                                    <?php if ($is_contact_lead): ?>
                                        <span style="background: linear-gradient(135deg, #10b981, #059669); color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            📞 פוטנציאלית
                                        </span>
                                    <?php else: ?>
                                        <span style="background: linear-gradient(135deg, #3b82f6, #1d4ed8); color: white; padding: 4px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: 600;">
                                            💪 סיימה
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <a href="tel:<?php echo $phone; ?>" class="client-phone-main">
                                    📞 <?php echo $phone; ?>
                                </a>
                                <div class="contact-status <?php echo $contact_status; ?>">
                                    <?php echo $contact_status_text; ?>
                                </div>
                            </div>
                            <div class="end-date-badge">
                                <?php if ($is_contact_lead): ?>
                                    📞 מתאמנת למעקב
                                <?php else: ?>
                                    סיום טיפול: <?php echo date('d/m/Y', strtotime($end_date)); ?>
                                <?php endif; ?>
                            </div>
                            <div class="client-actions">
                                <button type="button" onclick="openEditClientModal(<?php echo $client_id; ?>)" class="action-btn primary">
                                    ✏️ ערוך
                                </button>
                                <?php 
                                // המרת מספר טלפון ישראלי לפורמט בינלאומי עבור וואצאפ
                                $whatsapp_number = $phone;
                                if (substr($phone, 0, 1) === '0') {
                                    $whatsapp_number = '972' . substr($phone, 1);
                                }
                                ?>
                                <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="action-btn whatsapp">
                                    💬 וואצאפ
                                </a>
                                <button type="button" onclick="deleteClient(<?php echo $client_id; ?>, '<?php echo esc_js($first_name . ' ' . $last_name); ?>')" class="action-btn danger">
                                    🗑️ מחק
                                </button>
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

<style>
@keyframes modalSlideIn {
    from {
        opacity: 0;
        transform: translateY(-50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}
</style>

<?php get_footer(); ?> 