<?php
get_header(); ?>

<style>
    .mentors-archive {
        direction: rtl;
        text-align: right;
        max-width: 1200px;
        margin: 0 auto;
        padding: 20px;
    }
    
    .mentors-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 40px 20px;
        text-align: center;
        border-radius: 10px;
        margin-bottom: 30px;
    }
    
    .mentors-header h1 {
        margin: 0 0 10px 0;
        font-size: 2.5rem;
    }
    
    .mentors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 20px;
    }
    
    .mentor-card {
        background: rgba(38, 59, 52, 0.70);
        backdrop-filter: blur(5.9px);
        -webkit-backdrop-filter: blur(5.9px);
        border: 1px solid rgba(255, 255, 255, 0.91);
        border-right: 5px solid #8b5cf6;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s, box-shadow 0.3s;
        position: relative;
    }
    
    .mentor-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
    }
    
    .mentor-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #d7dedc;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.3);
        margin-bottom: 15px;
    }
    
    .mentor-details {
        display: grid;
        gap: 10px;
        margin-bottom: 20px;
    }
    
    .mentor-detail {
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 0.875rem;
        color: #d7dedc;
        opacity: 0.9;
    }
    
    .mentor-detail strong {
        color: #d7dedc;
        min-width: 100px;
        font-weight: 600;
    }
    
    .mentor-detail a {
        color: #ffffff !important;
        text-decoration: none;
        font-weight: 500;
        transition: opacity 0.3s;
    }
    
    .mentor-detail a:hover {
        opacity: 0.8;
        text-decoration: underline;
    }
    
    .mentor-actions {
        display: flex;
        gap: 10px;
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
        background: #8b5cf6;
        color: white;
    }
    
    .action-btn.primary:hover {
        background: #7c3aed;
    }
    
    .action-btn.secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .action-btn.secondary:hover {
        background: #e5e7eb;
    }
    
    .action-btn.whatsapp {
        background: #25d366;
        color: white;
    }
    
    .action-btn.whatsapp:hover {
        background: #128c7e;
        color: white;
    }
    
    .action-btn svg {
        margin-left: 5px;
    }
    
    .no-mentors {
        text-align: center;
        padding: 60px 20px;
        color: #d7dedc;
        background: rgba(38, 59, 52, 0.70);
        backdrop-filter: blur(5.9px);
        -webkit-backdrop-filter: blur(5.9px);
        border: 1px solid rgba(255, 255, 255, 0.91);
        border-radius: 16px;
        box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
    }
    
    .mentor-commission {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 10px;
        margin: 10px 0;
        text-align: center;
        font-weight: 600;
        color: #d7dedc;
    }
    
    /* כפתורים עם אפקט זוהר */
    .mentor-actions {
        display: flex;
        justify-content: center;
        gap: 15px;
        padding: 20px 0;
    }

    .btn-glow {
        width: 45px;
        height: 45px;
        border-radius: 12px;
        border: 2px solid transparent;
        background: rgba(255, 255, 255, 0.05);
        color: white;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        transition: all 0.4s ease;
        position: relative;
        backdrop-filter: blur(10px);
        text-decoration: none;
    }

    .btn-glow:hover {
        border-color: currentColor;
        box-shadow: 0 0 20px currentColor;
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.05);
    }

    /* צבעים ספציפיים לכל כפתור */
    .btn-glow.delete:hover { 
        color: #ff4757; 
        box-shadow: 0 0 20px #ff4757;
    }

    .btn-glow.whatsapp:hover { 
        color: #25d366; 
        box-shadow: 0 0 20px #25d366;
    }

    .btn-glow.edit:hover { 
        color: #3742fa; 
        box-shadow: 0 0 20px #3742fa;
    }

    .btn-glow.view:hover { 
        color: #5352ed; 
        box-shadow: 0 0 20px #5352ed;
    }
</style>

<div class="mentors-archive">
    <div class="mentors-header">
        <h1>👩‍💼 מנטוריות</h1>
        <p>צוות המנטוריות המקצועי שלנו</p>
    </div>

    <?php if (have_posts()) : ?>
        <div class="mentors-grid">
            <?php while (have_posts()) : the_post(); 
                $mentor_id = get_the_ID();
                $first_name = get_field('mentor_first_name');
                $last_name = get_field('mentor_last_name');
                $phone = get_field('mentor_phone');
                $email = get_field('mentor_email');
                $payment_percentage = get_field('payment_percentage');
                $notes = get_field('mentor_notes');
                
                // ספירת מתאמנות למנטורית
                $clients_count = get_posts(array(
                    'post_type' => 'clients',
                    'posts_per_page' => -1,
                    'meta_query' => array(
                        array(
                            'key' => 'mentor',
                            'value' => $mentor_id,
                            'compare' => '='
                        )
                    )
                ));
                $clients_count = count($clients_count);
            ?>
                <div class="mentor-card">
                    <div class="mentor-name">
                        <?php echo $first_name . ' ' . $last_name; ?>
                    </div>
                    
                    <div class="mentor-details">
                        <?php if ($phone): ?>
                            <div class="mentor-detail">
                                <strong>📞 טלפון:</strong>
                                <a href="tel:<?php echo $phone; ?>" style="color: #8b5cf6;"><?php echo $phone; ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($email): ?>
                            <div class="mentor-detail">
                                <strong>📧 אימייל:</strong>
                                <a href="mailto:<?php echo $email; ?>" style="color: #8b5cf6;"><?php echo $email; ?></a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="mentor-detail">
                            <strong>👥 מתאמנות:</strong>
                            <a href="<?php echo get_post_type_archive_link('clients') . '?mentor=' . $mentor_id; ?>" style="color: #8b5cf6;">
                                <?php echo $clients_count; ?> מתאמנות פעילות
                            </a>
                        </div>
                    </div>
                    
                    <?php if ($payment_percentage): ?>
                        <div class="mentor-commission">
                            💰 עמלה: <?php echo $payment_percentage; ?>%
                        </div>
                    <?php endif; ?>
                    
                    <?php if ($notes): ?>
                        <div style="background: #f9fafb; padding: 10px; border-radius: 6px; margin: 10px 0; font-size: 0.875rem; color: #6b7280;">
                            <strong>הערות:</strong> <?php echo wp_trim_words($notes, 20); ?>
                        </div>
                    <?php endif; ?>
                    
                    <div class="mentor-actions">
                        <button type="button" onclick="openEditMentorModal(<?php echo $mentor_id; ?>)" class="btn-glow edit" title="ערוך מנטורית">✏️</button>
                        <a href="<?php echo get_post_type_archive_link('clients') . '?mentor=' . $mentor_id; ?>" class="btn-glow view" title="צפה במתאמנות של המנטורית">👁️</a>
                        <?php if ($phone): ?>
                            <?php 
                            // המרת מספר טלפון ישראלי לפורמט בינלאומי עבור וואצאפ
                            $whatsapp_number = $phone;
                            if (substr($phone, 0, 1) === '0') {
                                $whatsapp_number = '972' . substr($phone, 1);
                            }
                            ?>
                            <a href="https://wa.me/<?php echo $whatsapp_number; ?>" target="_blank" class="btn-glow whatsapp" title="שלח וואצאפ">💬</a>
                        <?php endif; ?>
                        <button type="button" onclick="deleteMentor(<?php echo $mentor_id; ?>, '<?php echo esc_js($first_name . ' ' . $last_name); ?>')" class="btn-glow delete" title="מחק מנטורית">🗑️</button>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else : ?>
        <div class="no-mentors">
            <h3>אין מנטוריות עדיין</h3>
            <p>התחילי בהוספת המנטורית הראשונה שלך!</p>
            <button type="button" onclick="openAddMentorModal()" class="action-btn primary">
                ➕ הוסף מנטורית חדשה
            </button>
        </div>
    <?php endif; ?>
</div>

<script>
// פונקציה למחיקת מנטורית
function deleteMentor(mentorId, mentorName) {
    if (!confirm(`האם אתה בטוח שברצונך למחוק את המנטורית "${mentorName}"?\n\nפעולה זו בלתי הפיכה!`)) {
        return;
    }
    
    // הצגת הודעת טעינה
    const loading = document.createElement('div');
    loading.id = 'delete-loading';
    loading.style.cssText = `
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(0,0,0,0.8);
        color: white;
        padding: 20px;
        border-radius: 10px;
        z-index: 9999;
        font-size: 16px;
    `;
    loading.textContent = '🗑️ מוחק מנטורית...';
    document.body.appendChild(loading);
    
    // שליחת בקשת AJAX למחיקה
    const formData = new FormData();
    formData.append('action', 'delete_mentor');
    formData.append('mentor_id', mentorId);
    formData.append('nonce', '<?php echo wp_create_nonce("delete_mentor_nonce"); ?>');
    
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
            alert(`✅ המנטורית "${mentorName}" נמחקה בהצלחה!`);
            
            // רענון הדף
            window.location.reload();
        } else {
            alert('❌ שגיאה: ' + (data.data || 'לא ניתן למחוק את המנטורית'));
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