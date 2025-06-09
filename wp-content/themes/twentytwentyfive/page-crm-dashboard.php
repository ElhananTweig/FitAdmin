<?php
/**
 * דף דשבורד מערכת CRM
 * Template Name: CRM Dashboard
 */

// בדיקת אימות
global $crm_auth;
if ($crm_auth && !$crm_auth->is_user_logged_in()) {
    wp_redirect(home_url('/login/'));
    exit;
}

$user_info = $crm_auth ? $crm_auth->get_current_user_info() : array('display_name' => 'משתמש');

// קבלת סטטיסטיקות מהירות
$clients_count = wp_count_posts('clients')->publish;
$mentors_count = wp_count_posts('mentors')->publish;
$groups_count = wp_count_posts('groups')->publish;

// מתאמנות פעילות (לא הקפואות)
$active_clients = get_posts(array(
    'post_type' => 'clients',
    'posts_per_page' => -1,
    'meta_query' => array(
        'relation' => 'OR',
        array(
            'key' => 'is_frozen',
            'compare' => 'NOT EXISTS'
        ),
        array(
            'key' => 'is_frozen',
            'value' => '0',
            'compare' => '='
        )
    )
));
$active_clients_count = count($active_clients);

// מתאמנות שהתחילו השבוע
$week_ago = date('Y-m-d', strtotime('-7 days'));
$new_clients_this_week = get_posts(array(
    'post_type' => 'clients',
    'posts_per_page' => -1,
    'meta_query' => array(
        array(
            'key' => 'start_date',
            'value' => $week_ago,
            'compare' => '>='
        )
    )
));
$new_clients_count = count($new_clients_this_week);

get_header(); ?>

<div class="crm-dashboard-container">
    <!-- כותרת עליונה -->
    <div class="dashboard-header">
        <div class="container">
            <div class="header-content">
                <div class="welcome-section">
                    <h1>🏢 דשבורד CRM</h1>
                    <p>שלום <?php echo esc_html($user_info['display_name']); ?>, ברוך הבא למערכת הניהול</p>
                </div>
                <div class="quick-actions">
                    <a href="<?php echo home_url('/add-client/'); ?>" class="quick-btn add-client">
                        <span class="icon">👤</span>
                        <span>הוסף מתאמנת</span>
                    </a>
                    <a href="<?php echo home_url('/add-mentor/'); ?>" class="quick-btn add-mentor">
                        <span class="icon">👩‍🏫</span>
                        <span>הוסף מנטורית</span>
                    </a>
                    <a href="<?php echo home_url('/add-group/'); ?>" class="quick-btn add-group">
                        <span class="icon">👥</span>
                        <span>הוסף קבוצה</span>
                    </a>
                    <a href="<?php echo wp_logout_url(home_url('/')); ?>" class="quick-btn logout">
                        <span class="icon">🚪</span>
                        <span>יציאה</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="container dashboard-content">
        <!-- כרטיסי סטטיסטיקות -->
        <div class="stats-grid">
            <div class="stat-card total-clients">
                <div class="stat-icon">👥</div>
                <div class="stat-info">
                    <h3><?php echo $clients_count; ?></h3>
                    <p>סה״כ מתאמנות</p>
                </div>
                <div class="stat-trend">
                    <span class="trend-up">+<?php echo $new_clients_count; ?> השבוע</span>
                </div>
            </div>

            <div class="stat-card active-clients">
                <div class="stat-icon">✅</div>
                <div class="stat-info">
                    <h3><?php echo $active_clients_count; ?></h3>
                    <p>מתאמנות פעילות</p>
                </div>
                <div class="stat-trend">
                    <span class="trend-percentage"><?php echo $clients_count > 0 ? round(($active_clients_count / $clients_count) * 100) : 0; ?>%</span>
                </div>
            </div>

            <div class="stat-card total-mentors">
                <div class="stat-icon">👩‍🏫</div>
                <div class="stat-info">
                    <h3><?php echo $mentors_count; ?></h3>
                    <p>מנטוריות</p>
                </div>
                <div class="stat-trend">
                    <span class="trend-stable">יציב</span>
                </div>
            </div>

            <div class="stat-card total-groups">
                <div class="stat-icon">🏆</div>
                <div class="stat-info">
                    <h3><?php echo $groups_count; ?></h3>
                    <p>קבוצות פעילות</p>
                </div>
                <div class="stat-trend">
                    <span class="trend-stable">פעיל</span>
                </div>
            </div>
        </div>

        <!-- תפריט ניווט ראשי -->
        <div class="main-navigation">
            <h2>🎯 מערכות ניהול</h2>
            <div class="nav-grid">
                <a href="<?php echo home_url('/clients/'); ?>" class="nav-card clients">
                    <div class="nav-icon">👥</div>
                    <div class="nav-content">
                        <h3>ניהול מתאמנות</h3>
                        <p>צפייה, עריכה ומעקב אחר כל המתאמנות במערכת</p>
                        <div class="nav-stats">
                            <span><?php echo $clients_count; ?> מתאמנות</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>

                <a href="<?php echo home_url('/mentors/'); ?>" class="nav-card mentors">
                    <div class="nav-icon">👩‍🏫</div>
                    <div class="nav-content">
                        <h3>ניהול מנטוריות</h3>
                        <p>ניהול הצוות המקצועי ומעקב ביצועים</p>
                        <div class="nav-stats">
                            <span><?php echo $mentors_count; ?> מנטוריות</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>

                <a href="<?php echo home_url('/groups/'); ?>" class="nav-card groups">
                    <div class="nav-icon">🏆</div>
                    <div class="nav-content">
                        <h3>ניהול קבוצות</h3>
                        <p>יצירה וניהול של קבוצות ליווי קבוצתי</p>
                        <div class="nav-stats">
                            <span><?php echo $groups_count; ?> קבוצות</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>

                <a href="<?php echo home_url('/reports/'); ?>" class="nav-card reports">
                    <div class="nav-icon">📊</div>
                    <div class="nav-content">
                        <h3>דוחות ואנליטיקס</h3>
                        <p>ניתוח נתונים, דוחות הכנסות ותובנות עסקיות</p>
                        <div class="nav-stats">
                            <span>דוחות מתקדמים</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>

                <a href="<?php echo home_url('/payments/'); ?>" class="nav-card payments">
                    <div class="nav-icon">💳</div>
                    <div class="nav-content">
                        <h3>ניהול תשלומים</h3>
                        <p>מעקב תשלומים, חובות ותזרים מזומנים</p>
                        <div class="nav-stats">
                            <span>ניהול כספי</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>

                <a href="<?php echo home_url('/finished-clients/'); ?>" class="nav-card finished">
                    <div class="nav-icon">🎓</div>
                    <div class="nav-content">
                        <h3>מתאמנות שסיימו</h3>
                        <p>מעקב אחר בוגרות המערכת ומעקב המשך</p>
                        <div class="nav-stats">
                            <span>מעקב בוגרות</span>
                        </div>
                    </div>
                    <div class="nav-arrow">←</div>
                </a>
            </div>
        </div>

        <!-- מתאמנות אחרונות -->
        <div class="recent-section">
            <h2>🕐 פעילות אחרונה</h2>
            <div class="recent-grid">
                <div class="recent-card">
                    <h3>מתאמנות חדשות השבוע</h3>
                    <div class="recent-list">
                        <?php if ($new_clients_this_week): ?>
                            <?php foreach (array_slice($new_clients_this_week, 0, 5) as $client): ?>
                                <?php 
                                $first_name = get_field('first_name', $client->ID);
                                $last_name = get_field('last_name', $client->ID);
                                $start_date = get_field('start_date', $client->ID);
                                ?>
                                <div class="recent-item">
                                    <div class="item-info">
                                        <strong><?php echo $first_name . ' ' . $last_name; ?></strong>
                                        <span>התחילה: <?php echo date('d/m/Y', strtotime($start_date)); ?></span>
                                    </div>
                                    <a href="<?php echo get_edit_post_link($client->ID); ?>" class="item-action">👁️</a>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <p class="no-items">אין מתאמנות חדשות השבוע</p>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="recent-card">
                    <h3>פעולות מהירות</h3>
                    <div class="quick-links">
                        <a href="<?php echo home_url('/add-client/'); ?>" class="quick-link">
                            <span class="link-icon">➕</span>
                            <span>הוסף מתאמנת חדשה</span>
                        </a>
                        <a href="<?php echo home_url('/add-mentor/'); ?>" class="quick-link">
                            <span class="link-icon">➕</span>
                            <span>הוסף מנטורית חדשה</span>
                        </a>
                        <a href="<?php echo home_url('/add-group/'); ?>" class="quick-link">
                            <span class="link-icon">➕</span>
                            <span>צור קבוצה חדשה</span>
                        </a>
                        <a href="<?php echo home_url('/clients/?search='); ?>" class="quick-link">
                            <span class="link-icon">🔍</span>
                            <span>חפש מתאמנת</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.crm-dashboard-container {
    min-height: 100vh;
    background: #f8fafc;
    direction: rtl;
}

.dashboard-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 40px 0;
    margin-bottom: 40px;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 20px;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.welcome-section h1 {
    font-size: 2.5rem;
    margin: 0 0 10px 0;
    font-weight: 700;
}

.welcome-section p {
    font-size: 1.2rem;
    opacity: 0.9;
    margin: 0;
}

.quick-actions {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.quick-btn {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 12px 20px;
    border-radius: 8px;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    font-weight: 500;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.quick-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: translateY(-2px);
    color: white;
}

.quick-btn.logout {
    background: rgba(220, 38, 38, 0.2);
    border-color: rgba(220, 38, 38, 0.3);
}

.quick-btn.logout:hover {
    background: rgba(220, 38, 38, 0.3);
}

.dashboard-content {
    padding-bottom: 60px;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 25px;
    margin-bottom: 50px;
}

.stat-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 4px;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
}

.stat-icon {
    font-size: 3rem;
    opacity: 0.8;
}

.stat-info h3 {
    font-size: 2.5rem;
    margin: 0;
    font-weight: 700;
    color: #1f2937;
}

.stat-info p {
    margin: 5px 0 0 0;
    color: #6b7280;
    font-weight: 500;
}

.stat-trend {
    margin-right: auto;
}

.trend-up {
    color: #10b981;
    font-weight: 600;
    font-size: 0.9rem;
}

.trend-percentage {
    color: #3b82f6;
    font-weight: 600;
    font-size: 0.9rem;
}

.trend-stable {
    color: #6b7280;
    font-weight: 600;
    font-size: 0.9rem;
}

.main-navigation {
    margin-bottom: 50px;
}

.main-navigation h2 {
    font-size: 2rem;
    color: #1f2937;
    margin-bottom: 30px;
    text-align: center;
}

.nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
    gap: 25px;
}

.nav-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.nav-card::before {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    width: 0;
    height: 100%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

.nav-card:hover::before {
    width: 4px;
}

.nav-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
    color: inherit;
}

.nav-icon {
    font-size: 3rem;
    opacity: 0.8;
    z-index: 1;
}

.nav-content {
    flex: 1;
    z-index: 1;
}

.nav-content h3 {
    font-size: 1.5rem;
    margin: 0 0 8px 0;
    color: #1f2937;
    font-weight: 600;
}

.nav-content p {
    color: #6b7280;
    margin: 0 0 12px 0;
    line-height: 1.5;
}

.nav-stats {
    font-size: 0.9rem;
    color: #8b5cf6;
    font-weight: 600;
}

.nav-arrow {
    font-size: 1.5rem;
    color: #d1d5db;
    z-index: 1;
}

.recent-section h2 {
    font-size: 2rem;
    color: #1f2937;
    margin-bottom: 30px;
    text-align: center;
}

.recent-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 25px;
}

.recent-card {
    background: white;
    padding: 30px;
    border-radius: 15px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
}

.recent-card h3 {
    font-size: 1.3rem;
    color: #1f2937;
    margin: 0 0 20px 0;
    font-weight: 600;
}

.recent-list {
    display: flex;
    flex-direction: column;
    gap: 15px;
}

.recent-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 12px 0;
    border-bottom: 1px solid #f3f4f6;
}

.recent-item:last-child {
    border-bottom: none;
}

.item-info strong {
    display: block;
    color: #1f2937;
    font-weight: 600;
}

.item-info span {
    color: #6b7280;
    font-size: 0.9rem;
}

.item-action {
    color: #8b5cf6;
    font-size: 1.2rem;
    text-decoration: none;
}

.no-items {
    color: #9ca3af;
    text-align: center;
    padding: 20px;
}

.quick-links {
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.quick-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 16px;
    background: #f8fafc;
    border-radius: 8px;
    text-decoration: none;
    color: #374151;
    transition: all 0.3s ease;
    font-weight: 500;
}

.quick-link:hover {
    background: #e5e7eb;
    color: #1f2937;
    transform: translateX(-3px);
}

.link-icon {
    font-size: 1.2rem;
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        text-align: center;
    }
    
    .welcome-section h1 {
        font-size: 2rem;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .nav-grid {
        grid-template-columns: 1fr;
    }
    
    .recent-grid {
        grid-template-columns: 1fr;
    }
    
    .quick-actions {
        justify-content: center;
    }
}
</style>

<?php get_footer(); ?> 