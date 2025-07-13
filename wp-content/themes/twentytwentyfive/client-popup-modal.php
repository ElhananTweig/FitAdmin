<?php
/**
 * פופ-אפ להוספת/עריכת מתאמנת
 */

// קבלת רשימת מנטוריות (ללא מנטוריות שנמחקו)
$mentors = get_posts(array(
    'post_type' => 'mentors',
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'is_deleted',
            'value' => false,
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'value' => 'false',
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'value' => '',
            'compare' => '='
        ),
        array(
            'key' => 'is_deleted',
            'compare' => 'NOT EXISTS'
        )
    )
));

// קבלת רשימת קבוצות
$groups = get_posts(array(
    'post_type' => 'groups',
    'posts_per_page' => -1,
    'post_status' => 'publish'
));

$referral_sources = array(
    'instagram' => 'אינסטגרם',
    'status' => 'סטטוס',
    'whatsapp' => 'וואצאפ',
    'referral' => 'המלצה',
    'mentor' => 'מנטורית',
    'unknown' => 'לא ידוע'
);

$payment_methods = array(
    'cash' => 'מזומן',
    'credit' => 'כרטיס אשראי',
    'bank_transfer' => 'העברה בנקאית',
    'paypal' => 'PayPal',
    'bit' => 'Bit'
);
?>

<!-- פופ-אפ להוספת/עריכת מתאמנת -->
<div id="client-modal" class="client-modal" style="display: none;">
    <div class="modal-backdrop" id="modal-backdrop"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h2 id="modal-title">👥 הוספת מתאמנת חדשה</h2>
            <button type="button" class="modal-close" id="modal-close">×</button>
        </div>
        
        <div class="modal-body">
            <form id="client-form" method="post">
                <input type="hidden" id="form-action" name="action" value="add_client">
                <input type="hidden" id="client-id" name="client_id" value="">
                <input type="hidden" id="form-nonce" name="client_nonce" value="<?php echo wp_create_nonce('client_action'); ?>">
                
                <!-- פרטים אישיים -->
                <div class="form-section">
                    <h3 class="section-title">👤 פרטים אישיים</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="first_name">שם פרטי <span class="required">*</span></label>
                            <input type="text" id="first_name" name="first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">שם משפחה <span class="required">*</span></label>
                            <input type="text" id="last_name" name="last_name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">טלפון <span class="required">*</span></label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">אימייל</label>
                            <input type="email" id="email" name="email">
                        </div>
                        
                        <div class="form-group">
                            <label for="age">גיל</label>
                            <input type="number" id="age" name="age" min="1" max="120">
                        </div>
                    </div>
                </div>
                
                <!-- תאריכים -->
                <div class="form-section">
                    <h3 class="section-title">📅 תאריכי טיפול</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_date">תאריך התחלה <span class="required">*</span></label>
                            <input type="date" id="start_date" name="start_date" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="end_date">תאריך סיום <span class="required">*</span></label>
                            <input type="date" id="end_date" name="end_date" required>
                        </div>
                    </div>
                </div>
                
                <!-- מקור הגעה וליווי -->
                <div class="form-section">
                    <h3 class="section-title">🎯 מקור הגעה וליווי</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="referral_source">מקור הגעה <span class="required">*</span></label>
                            <select id="referral_source" name="referral_source" required>
                                <option value="">בחר מקור הגעה...</option>
                                <?php foreach ($referral_sources as $key => $label): ?>
                                    <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="training_type">סוג ליווי <span class="required">*</span></label>
                            <select id="training_type" name="training_type" required>
                                <option value="personal">👤 אישי</option>
                                <option value="group">👥 קבוצתי</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- מנטורית (ליווי אישי) -->
                    <div id="personal-mentor" class="form-group">
                        <label for="mentor_id">מנטורית</label>
                        <select id="mentor_id" name="mentor_id">
                            <option value="">ללא מנטורית</option>
                            <?php foreach ($mentors as $mentor): 
                                $mentor_name = get_field('mentor_first_name', $mentor->ID) . ' ' . get_field('mentor_last_name', $mentor->ID);
                            ?>
                                <option value="<?php echo $mentor->ID; ?>"><?php echo $mentor_name; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- קבוצה (ליווי קבוצתי) -->
                    <div id="group-selection" class="form-group" style="display: none;">
                        <label for="group_id">בחירת קבוצה <span class="required">*</span></label>
                        <select id="group_id" name="group_id">
                            <option value="">בחר קבוצה...</option>
                            <?php foreach ($groups as $group): 
                                $group_name = get_field('group_name', $group->ID);
                                $group_mentor = get_field('group_mentor', $group->ID);
                                $mentor_name = '';
                                if ($group_mentor) {
                                    $mentor_name = get_field('mentor_first_name', $group_mentor->ID) . ' ' . get_field('mentor_last_name', $group_mentor->ID);
                                }
                            ?>
                                <option value="<?php echo $group->ID; ?>">
                                    <?php echo $group_name; ?><?php echo $mentor_name ? ' (מנטורית: ' . $mentor_name . ')' : ''; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                
                <!-- תשלום -->
                <div class="form-section">
                    <h3 class="section-title">💰 פרטי תשלום</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="payment_amount">סכום לתשלום (₪) <span class="required">*</span></label>
                            <input type="number" id="payment_amount" name="payment_amount" min="0" step="0.01" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="amount_paid">סכום ששולם (₪)</label>
                            <input type="number" id="amount_paid" name="amount_paid" min="0" step="0.01">
                        </div>
                    </div>
                    
                    <div id="payment-details" style="display: none;">
                        <div class="form-row">
                            <div class="form-group">
                                <label for="payment_method">אמצעי תשלום</label>
                                <select id="payment_method" name="payment_method">
                                    <option value="">בחר אמצעי תשלום...</option>
                                    <?php foreach ($payment_methods as $key => $label): ?>
                                        <option value="<?php echo $key; ?>"><?php echo $label; ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <label for="payment_date">תאריך תשלום</label>
                                <input type="date" id="payment_date" name="payment_date">
                            </div>
                        </div>
                        
                        <!-- שדה מספר תשלומים - מוצג רק עבור אשראי -->
                        <div id="installments-section" class="form-row" style="display: none;">
                            <div class="form-group">
                                <label for="installments">מספר תשלומים <span class="required">*</span></label>
                                <input type="number" id="installments" name="installments" min="1" max="99" placeholder="הזן מספר תשלומים">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- משקל -->
                <div class="form-section">
                    <h3 class="section-title">⚖️ נתוני משקל</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="start_weight">משקל התחלתי (ק"ג)</label>
                            <input type="number" id="start_weight" name="start_weight" min="0" step="0.1">
                        </div>
                        
                        <div class="form-group">
                            <label for="current_weight">משקל נוכחי (ק"ג)</label>
                            <input type="number" id="current_weight" name="current_weight" min="0" step="0.1">
                        </div>
                        
                        <div class="form-group">
                            <label for="target_weight">משקל יעד (ק"ג)</label>
                            <input type="number" id="target_weight" name="target_weight" min="0" step="0.1">
                        </div>
                    </div>
                </div>
                
                <!-- הערות -->
                <div class="form-section">
                    <h3 class="section-title">📝 הערות</h3>
                    
                    <div class="form-group full-width">
                        <label for="notes">הערות נוספות</label>
                        <textarea id="notes" name="notes" rows="4" placeholder="אלרגיות, העדפות, מטרות מיוחדות..."></textarea>
                    </div>
                </div>
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" id="cancel-btn">❌ ביטול</button>
            <button type="submit" form="client-form" class="btn btn-primary" id="save-btn">✅ שמור מתאמנת</button>
        </div>
    </div>
</div>

<!-- פופאפ מנטורית -->
<div id="mentor-modal" class="client-modal" style="display: none;">
    <div class="modal-backdrop" id="mentor-modal-backdrop"></div>
    <div class="modal-container mentor-modal">
        <div class="modal-header">
            <h2 id="mentorModalTitle">👩‍💼 הוספת מנטורית חדשה</h2>
            <button type="button" class="modal-close" onclick="closeMentorModal()">&times;</button>
        </div>
        
        <div class="modal-body">
            <form id="mentorForm" method="post">
                <input type="hidden" id="mentorFormType" name="action" value="add_mentor">
                <input type="hidden" id="mentorId" name="mentor_id" value="">
                <input type="hidden" id="mentorFormNonce" name="mentor_nonce" value="<?php echo wp_create_nonce('client_action'); ?>">
                
                <!-- פרטים אישיים -->
                <div class="form-section">
                    <h3 class="section-title">👤 פרטים אישיים</h3>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mentorFirstName">שם פרטי <span class="required">*</span></label>
                            <input type="text" id="mentorFirstName" name="mentor_first_name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="mentorLastName">שם משפחה <span class="required">*</span></label>
                            <input type="text" id="mentorLastName" name="mentor_last_name" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="mentorPhone">טלפון <span class="required">*</span></label>
                            <input type="tel" id="mentorPhone" name="mentor_phone" required placeholder="050-1234567">
                        </div>
                        
                        <div class="form-group">
                            <label for="mentorEmail">אימייל</label>
                            <input type="email" id="mentorEmail" name="mentor_email" placeholder="mentor@example.com">
                        </div>
                    </div>
                </div>
                
                <!-- פרטי עמלה -->
                <div class="form-section">
                    <h3 class="section-title">💰 פרטי עמלה</h3>
                    <div class="form-group">
                        <label for="paymentPercentage">אחוז עמלה (%)</label>
                        <input type="number" id="paymentPercentage" name="payment_percentage" min="0" max="100" step="0.1" value="40" placeholder="40">
                        <small style="color: #6b7280; font-size: 0.875rem; display: block; margin-top: 5px;">ברירת מחדל: 40%. ניתן לשנות בהתאם לצורך.</small>
                    </div>
                </div>
                
                <!-- הערות -->
                <div class="form-section">
                    <h3 class="section-title">📝 הערות ומידע נוסף</h3>
                    <div class="form-group">
                        <label for="mentorNotes">הערות ומידע נוסף</label>
                        <textarea id="mentorNotes" name="mentor_notes" rows="3" placeholder="התמחויות, ניסיון, הערות מיוחדות..."></textarea>
                    </div>
                </div>
                
            </form>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeMentorModal()">❌ ביטול</button>
            <button type="submit" form="mentorForm" class="btn btn-primary" id="mentor-save-btn">✅ שמור מנטורית</button>
        </div>
        
        <div id="mentorModalMessage" class="modal-message" style="display: none;"></div>
    </div>
</div> 