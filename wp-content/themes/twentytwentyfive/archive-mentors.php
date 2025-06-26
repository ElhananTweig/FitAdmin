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
        background: white;
        border-radius: 12px;
        padding: 25px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s, box-shadow 0.2s;
        border-right: 4px solid #8b5cf6;
        position: relative;
    }
    
    .mentor-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }
    
    .mentor-name {
        font-size: 1.3rem;
        font-weight: 600;
        color: #1f2937;
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
        color: #6b7280;
    }
    
    .mentor-detail strong {
        color: #374151;
        min-width: 100px;
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
    
    .no-mentors {
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    
    .mentor-commission {
        background: #f0f9ff;
        border: 2px solid #bfdbfe;
        border-radius: 8px;
        padding: 10px;
        margin: 10px 0;
        text-align: center;
        font-weight: 600;
        color: #1e40af;
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
                        <button type="button" onclick="openEditMentorModal(<?php echo $mentor_id; ?>)" class="action-btn primary">
                            ✏️ ערוך
                        </button>
                        <a href="<?php echo get_post_type_archive_link('clients') . '?mentor=' . $mentor_id; ?>" class="action-btn secondary">
                            👥 המתאמנות שלה
                        </a>
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

<?php get_footer(); ?> 