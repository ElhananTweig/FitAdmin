<?php
get_header(); ?>

<style>
    .clients-archive {
        direction: rtl;
        text-align: right;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .clients-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 40px 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .clients-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
    }
    
    .clients-filters {
        background: white;
        padding: 20px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        margin-bottom: 20px;
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
    }
    
    .filter-group {
        display: flex;
        flex-direction: column;
    }
    
    .filter-group label {
        font-weight: 500;
        margin-bottom: 5px;
        color: #374151;
    }
    
    .filter-group select,
    .filter-group input {
        padding: 8px 12px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 14px;
        direction: rtl;
    }
    
    .clients-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .client-card {
        background: white;
        border-radius: 12px;
        padding: 20px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        border-right: 4px solid #3b82f6;
        position: relative;
    }
    
    .client-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .client-card.ending-soon {
        border-right-color: #f59e0b;
    }
    
    .client-card.frozen {
        border-right-color: #8b5cf6;
        opacity: 0.8;
    }
    
    .client-card.unpaid {
        border-right-color: #ef4444;
    }
    
    .client-card.partial {
        border-right-color: #f59e0b;
    }
    
    .client-name {
        font-size: 1.25rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 10px;
    }
    
    .client-details {
        display: grid;
        gap: 8px;
        margin-bottom: 15px;
    }
    
    .client-detail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        color: #6b7280;
    }
    
    .client-detail strong {
        color: #374151;
        min-width: 80px;
    }
    
    .client-status {
        position: absolute;
        top: 10px;
        left: 10px;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    
    .status-active {
        background: #d1fae5;
        color: #065f46;
    }
    
    .status-ending {
        background: #fef3c7;
        color: #92400e;
    }
    
    .status-frozen {
        background: #e9d5ff;
        color: #6b21a8;
    }
    
    .status-ended {
        background: #fee2e2;
        color: #991b1b;
    }
    
    .client-actions {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }
    
    .action-btn {
        padding: 8px 12px;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        transition: background 0.2s;
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
    
    .weight-progress {
        background: #f3f4f6;
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px;
    }
    
    .weight-bar {
        background: #e5e7eb;
        height: 4px;
        border-radius: 2px;
        overflow: hidden;
        margin: 5px 0;
    }
    
    .weight-fill {
        background: #10b981;
        height: 100%;
        transition: width 0.3s;
    }
    
    .no-clients {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    
    .stats-bar {
        display: flex;
        justify-content: space-around;
        background: white;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .stat-item {
        text-align: center;
    }
    
    .stat-number {
        font-size: 1.5rem;
        font-weight: 600;
        color: #1f2937;
    }
    
    .stat-label {
        font-size: 0.875rem;
        color: #6b7280;
        margin-top: 5px;
    }
</style>

<div class="clients-archive">
    <div class="clients-header">
        <h1>👥 מתאמנות</h1>
        <p>ניהול ומעקב אחר כל המתאמנות שלך במקום אחד</p>
    </div>

    <?php
    // שאילתה מותאמת לסטטיסטיקות
    $all_clients = get_posts(array(
        'post_type' => 'clients',
        'posts_per_page' => -1,
        'post_status' => 'publish'
    ));
    
    $today = date('Y-m-d');
            $one_week_later = date('Y-m-d', strtotime('+7 days')); // שבוע מהיום
    
    $active_count = 0;
    $ending_soon_count = 0;
    $frozen_count = 0;
    $ended_count = 0;
    
    foreach ($all_clients as $client) {
        $end_date = get_field('end_date', $client->ID);
        $is_frozen = get_field('is_frozen', $client->ID);
        
        if ($is_frozen) {
            $frozen_count++;
        } elseif ($end_date < $today) {
            $ended_count++;
        } elseif ($end_date <= $one_week_later) {
            // מתאמנות שמסיימות בקרוב נחשבות גם כפעילות וגם כמסיימות בקרוב
            $ending_soon_count++;
            $active_count++; // הוספה לפעילות גם כן
        } else {
            $active_count++;
        }
    }
    ?>

    <div class="stats-bar">
        <div class="stat-item">
            <div class="stat-number"><?php echo $active_count; ?></div>
            <div class="stat-label">פעילות</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $ending_soon_count; ?></div>
            <div class="stat-label">מסיימות בקרוב</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $frozen_count; ?></div>
            <div class="stat-label">בהקפאה</div>
        </div>
        <div class="stat-item">
            <div class="stat-number"><?php echo $ended_count; ?></div>
            <div class="stat-label">סיימו</div>
        </div>
    </div>

    <!-- פילטרים -->
    <div class="clients-filters">
        <div class="filter-group">
            <label>חיפוש שם:</label>
            <input type="text" id="client-search" placeholder="הקלד שם...">
        </div>
        <div class="filter-group">
            <label>סטטוס:</label>
            <select id="status-filter">
                <option value="">כל הסטטוסים</option>
                <option value="active">פעיל</option>
                <option value="ending">מסיים בקרוב</option>
                <option value="frozen">בהקפאה</option>
                <option value="ended">סיימה</option>
                <option value="unpaid">לא שילמה</option>
                <option value="partial">שילמה חלקית</option>
            </select>
        </div>
        <div class="filter-group">
            <label>מקור הגעה:</label>
            <select id="source-filter">
                <option value="">כל המקורות</option>
                <option value="instagram">אינסטגרם</option>
                <option value="status">סטטוס</option>
                <option value="whatsapp">וואצאפ</option>
                <option value="referral">המלצה</option>
                <option value="mentor">מנטורית</option>
                <option value="unknown">לא ידוע</option>
            </select>
        </div>
        <div class="filter-group">
            <label>ליווי:</label>
            <select id="training-filter">
                <option value="">כל סוגי הליווי</option>
                <option value="personal">אישי</option>
                <?php
                $groups = get_posts(array(
                    'post_type' => 'groups',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                ));
                foreach ($groups as $group):
                    $group_name = get_field('group_name', $group->ID);
                ?>
                    <option value="group-<?php echo $group->ID; ?>"><?php echo $group_name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label>מנטורית:</label>
            <select id="mentor-filter">
                <option value="">כל המנטוריות</option>
                <?php
                $mentors = get_posts(array(
                    'post_type' => 'mentors',
                    'posts_per_page' => -1,
                    'post_status' => 'publish'
                ));
                foreach ($mentors as $mentor):
                    $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                ?>
                    <option value="<?php echo $mentor->ID; ?>"><?php echo $mentor_name; ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="filter-group">
            <label></label>
            <button id="clear-filters" style="padding: 8px 16px; background: #6b7280; color: white; border: none; border-radius: 6px; cursor: pointer;">נקה פילטרים</button>
        </div>
    </div>

    <?php if (have_posts()) : ?>
        <div class="clients-grid" id="clients-container">
            <?php while (have_posts()) : the_post(); 
                $client_id = get_the_ID();
                $first_name = get_field('first_name');
                $last_name = get_field('last_name');
                $phone = get_field('phone');
                $age = get_field('age');
                $start_date = get_field('start_date');
                $end_date = get_field('end_date');
                $start_weight = get_field('start_weight');
                $current_weight = get_field('current_weight');
                $target_weight = get_field('target_weight');
                $is_frozen = get_field('is_frozen');
                $referral_source = get_field('referral_source');
                $amount_paid = get_field('amount_paid');
                $payment_amount = get_field('payment_amount');
                $mentor = get_field('mentor');
                $training_type = get_field('training_type');
                $group_id = get_field('group_id');
                
                // פרטי מנטורית וקבוצה
                $mentor_name = '';
                $mentor_id = '';
                $group_name = '';
                $training_display = '';
                
                if ($training_type === 'group' && $group_id) {
                    // אם זה ליווי קבוצתי
                    $group_id_num = is_object($group_id) ? $group_id->ID : $group_id;
                    $group_name = get_field('group_name', $group_id_num);
                    $group_mentor = get_field('group_mentor', $group_id_num);
                    $training_display = 'קבוצתי - ' . $group_name;
                    
                    if ($group_mentor) {
                        $mentor_id = is_object($group_mentor) ? $group_mentor->ID : $group_mentor;
                        $mentor_name = get_field('mentor_first_name', $mentor_id) . ' ' . get_field('mentor_last_name', $mentor_id);
                    }
                } elseif ($mentor) {
                    // אם זה ליווי אישי
                    $training_display = 'אישי';
                    
                    if (is_object($mentor)) {
                        $mentor_id = $mentor->ID;
                        $mentor_name = get_field('mentor_first_name', $mentor_id) . ' ' . get_field('mentor_last_name', $mentor_id);
                    } else {
                        $mentor_id = $mentor;
                        $mentor_name = get_field('mentor_first_name', $mentor_id) . ' ' . get_field('mentor_last_name', $mentor_id);
                    }
                } else {
                    $training_display = 'אישי';
                }
                
                // חישוב סטטוס
                $status = 'active';
                $status_text = 'פעיל';
                $card_class = '';
                
                if ($is_frozen) {
                    $status = 'frozen';
                    $status_text = 'בהקפאה';
                    $card_class = 'frozen';
                } elseif ($end_date < $today) {
                    $status = 'ended';
                    $status_text = 'סיימה';
                    $card_class = 'ended';
                } elseif ($end_date <= $one_week_later) {
                    // מתאמנות שמסיימות בקרוב הן גם פעילות וגם מסיימות בקרוב
                    $status = 'active ending';
                    $status_text = 'מסיים בקרוב';
                    $card_class = 'ending-soon';
                }
                
                // טיפול בסטטוס תשלום
                if ($amount_paid == 0) {
                    $card_class .= ' unpaid';
                    // הוספת סטטוס unpaid לכל הסטטוסים הקיימים
                    $status_parts = array_unique(array_merge(explode(' ', $status), array('unpaid')));
                    $status = implode(' ', $status_parts);
                } elseif ($payment_amount && $amount_paid > 0 && $amount_paid < $payment_amount) {
                    $card_class .= ' partial';
                    // הוספת סטטוס partial לכל הסטטוסים הקיימים
                    $status_parts = array_unique(array_merge(explode(' ', $status), array('partial')));
                    $status = implode(' ', $status_parts);
                }
                
                // חישוב התקדמות משקל
                $weight_progress = 0;
                if ($start_weight && $target_weight && $current_weight) {
                    $total_loss_needed = $start_weight - $target_weight;
                    $current_loss = $start_weight - $current_weight;
                    $weight_progress = ($current_loss / $total_loss_needed) * 100;
                    $weight_progress = max(0, min(100, $weight_progress));
                }
                
                $source_labels = array(
                    'instagram' => 'אינסטגרם',
                    'status' => 'סטטוס',
                    'whatsapp' => 'וואצאפ',
                    'referral' => 'המלצה',
                    'mentor' => 'מנטורית',
                    'unknown' => 'לא ידוע'
                );
            ?>
                <div class="client-card <?php echo $card_class; ?>" 
                     data-name="<?php echo strtolower($first_name . ' ' . $last_name); ?>"
                     data-status="<?php echo $status; ?>"
                     data-source="<?php echo $referral_source; ?>"
                     data-mentor="<?php echo $mentor_id; ?>"
                     data-training="<?php echo $training_type; ?>"
                     data-group="<?php echo $group_id ? (is_object($group_id) ? $group_id->ID : $group_id) : ''; ?>">
                    
                    <div class="client-status status-<?php echo str_replace(' ', '-', $status); ?>">
                        <?php echo $status_text; ?>
                        <?php if ($amount_paid == 0): ?>
                            + לא שילמה
                        <?php elseif ($payment_amount && $amount_paid > 0 && $amount_paid < $payment_amount): ?>
                            + שילמה חלקית
                        <?php endif; ?>
                    </div>
                    
                    <div class="client-name">
                        <?php echo $first_name . ' ' . $last_name; ?>
                    </div>
                    
                    <div class="client-details">
                        <?php if ($phone): ?>
                            <div class="client-detail">
                                <strong>📞 טלפון:</strong>
                                <a href="tel:<?php echo $phone; ?>" style="color: #3b82f6;"><?php echo $phone; ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($age): ?>
                            <div class="client-detail">
                                <strong>🎂 גיל:</strong> <?php echo $age; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="client-detail">
                            <strong>📅 תאריכים:</strong> <?php echo $start_date; ?> - <?php echo $end_date; ?>
                        </div>
                        
                        <?php if ($referral_source): ?>
                            <div class="client-detail">
                                <strong>📍 מקור:</strong> <?php echo isset($source_labels[$referral_source]) ? $source_labels[$referral_source] : $referral_source; ?>
                            </div>
                        <?php endif; ?>
                        
                        <div class="client-detail">
                            <strong>🎯 ליווי:</strong> <?php echo $training_display; ?>
                        </div>
                        
                        <?php if ($mentor_name): ?>
                            <div class="client-detail">
                                <strong>👩‍💼 מנטורית:</strong> <?php echo $mentor_name; ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($amount_paid !== null && $payment_amount): ?>
                            <div class="client-detail">
                                <strong>💰 תשלום:</strong> ₪<?php echo number_format($amount_paid); ?> / ₪<?php echo number_format($payment_amount); ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <?php if ($start_weight && $current_weight && $target_weight): ?>
                        <div class="weight-progress">
                            <div style="display: flex; justify-content: space-between; font-size: 0.875rem;">
                                <span>התחלה: <?php echo $start_weight; ?>ק"ג</span>
                                <span>נוכחי: <?php echo $current_weight; ?>ק"ג</span>
                                <span>יעד: <?php echo $target_weight; ?>ק"ג</span>
                            </div>
                            <div class="weight-bar">
                                <div class="weight-fill" style="width: <?php echo $weight_progress; ?>%;"></div>
                            </div>
                            <div style="text-align: center; font-size: 0.75rem; color: #6b7280;">
                                התקדמות: <?php echo round($weight_progress); ?>%
                            </div>
                        </div>
                    <?php endif; ?>
                    
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
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="no-clients">
            <h3>אין מתאמנות עדיין</h3>
            <p>התחילי בהוספת המתאמנת הראשונה שלך!</p>
            <a href="<?php echo admin_url('post-new.php?post_type=clients'); ?>" class="action-btn primary">
                ➕ הוסף מתאמנת חדשה
            </a>
        </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('client-search');
    const statusFilter = document.getElementById('status-filter');
    const sourceFilter = document.getElementById('source-filter');
    const trainingFilter = document.getElementById('training-filter');
    const mentorFilter = document.getElementById('mentor-filter');
    const clearButton = document.getElementById('clear-filters');
    const clientsContainer = document.getElementById('clients-container');
    const clientCards = document.querySelectorAll('.client-card');

    // קריאת פילטרים מה-URL
    const urlParams = new URLSearchParams(window.location.search);
    const filterParam = urlParams.get('filter');
    const mentorParam = urlParams.get('mentor');
    const groupParam = urlParams.get('group');
    
    if (filterParam) {
        statusFilter.value = filterParam;
    }
    
    if (mentorParam) {
        mentorFilter.value = mentorParam;
    }
    
    if (groupParam) {
        trainingFilter.value = 'group-' + groupParam;
    }
    
    if (filterParam || mentorParam || groupParam) {
        filterClients();
    }

    function filterClients() {
        const searchTerm = searchInput.value.toLowerCase();
        const statusValue = statusFilter.value;
        const sourceValue = sourceFilter.value;
        const trainingValue = trainingFilter.value;
        const mentorValue = mentorFilter.value;

        let visibleCount = 0;

        clientCards.forEach(card => {
            const name = card.dataset.name;
            const status = card.dataset.status;
            const source = card.dataset.source;
            const training = card.dataset.training;
            const group = card.dataset.group;
            const mentor = card.dataset.mentor;

            const matchesSearch = !searchTerm || name.includes(searchTerm);
            const matchesStatus = !statusValue || status.includes(statusValue);
            const matchesSource = !sourceValue || source === sourceValue;
            const matchesMentor = !mentorValue || mentor === mentorValue;
            
            let matchesTraining = true;
            if (trainingValue) {
                if (trainingValue === 'personal') {
                    matchesTraining = training === 'personal' || !training;
                } else if (trainingValue.startsWith('group-')) {
                    const groupId = trainingValue.replace('group-', '');
                    matchesTraining = training === 'group' && group === groupId;
                }
            }

            if (matchesSearch && matchesStatus && matchesSource && matchesTraining && matchesMentor) {
                card.style.display = 'block';
                visibleCount++;
            } else {
                card.style.display = 'none';
            }
        });

        // הצגת הודעה אם אין תוצאות
        const existingMessage = document.querySelector('.no-results-message');
        if (existingMessage) {
            existingMessage.remove();
        }

        if (visibleCount === 0) {
            const noResultsMessage = document.createElement('div');
            noResultsMessage.className = 'no-results-message';
            noResultsMessage.style.cssText = 'text-align: center; padding: 40px; color: #6b7280; grid-column: 1 / -1;';
            noResultsMessage.innerHTML = '<h3>לא נמצאו מתאמנות</h3><p>נסה לשנות את הפילטרים</p>';
            clientsContainer.appendChild(noResultsMessage);
        }
    }

    function clearFilters() {
        searchInput.value = '';
        statusFilter.value = '';
        sourceFilter.value = '';
        trainingFilter.value = '';
        mentorFilter.value = '';
        filterClients();
        
        // הסרת פילטר מה-URL
        const url = new URL(window.location);
        url.searchParams.delete('filter');
        url.searchParams.delete('mentor');
        url.searchParams.delete('group');
        window.history.replaceState({}, '', url);
    }

    searchInput.addEventListener('input', filterClients);
    statusFilter.addEventListener('change', filterClients);
    sourceFilter.addEventListener('change', filterClients);
    trainingFilter.addEventListener('change', filterClients);
    mentorFilter.addEventListener('change', filterClients);
    clearButton.addEventListener('click', clearFilters);
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
</script>

<?php get_footer(); ?> 