<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo( 'charset' ); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
    
    <style>
        /* רקע אתר מותאם לפוטר */
        body {
            background: linear-gradient(135deg, #1f2937 0%, #374151 25%, #111827 50%, #1f2937 75%, #374151 100%);
            background-size: 400% 400%;
            animation: gradientShift 20s ease infinite;
            background-attachment: fixed;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }
        
        /* אנימציה עדינה לרקע */
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        /* דקורציה בוטנית עדינה ברקע */
        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 200 200"><g opacity="0.03" fill="white"><path d="M50 50C50 30 70 10 90 10s40 20 40 40-20 40-40 40-40-20-40-40z"/><path d="M150 150C150 130 170 110 190 110s40 20 40 40-20 40-40 40-40-20-40-40z"/><circle cx="30" cy="170" r="15"/><circle cx="170" cy="30" r="12"/><path d="M100 180c-20-10-30-30-20-50s30-30 50-20 30 30 20 50-30 30-50 20z"/></g></svg>') repeat;
            pointer-events: none;
            z-index: -1;
        }
        
        /* עיצוב מותאם לכותרת */
        .crm-header {
            background: linear-gradient(135deg, rgba(31, 41, 55, 0.95) 0%, rgba(55, 65, 81, 0.95) 100%);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(16, 185, 129, 0.2);
            color: white;
            padding: 20px 0;
            box-shadow: 0 8px 32px rgba(0,0,0,0.3);
            position: relative;
            z-index: 100;
        }
        
        .crm-header-content {
            max-width: 1800px;
            margin: 0 auto;
            padding: 0 30px;
            display: grid;
            grid-template-columns: 1fr auto 1fr;
            align-items: center;
            direction: rtl;
            gap: 20px;
        }
        
        .crm-logo {
            font-size: 2rem;
            font-weight: 700;
            text-decoration: none;
            color: white;
            text-shadow: 0 2px 4px rgba(0,0,0,0.3);
            justify-self: start;
            transition: all 0.3s ease;
        }
        
        .crm-logo:hover {
            transform: translateY(-1px);
            text-shadow: 0 4px 8px rgba(16, 185, 129, 0.3);
        }
        
        .crm-nav {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-content: center;
            flex-wrap: wrap;
            justify-self: center;
        }
        
        .crm-nav a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            font-size: 1.1rem;
            padding: 14px 24px;
            border-radius: 30px;
            transition: all 0.3s ease;
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(16, 185, 129, 0.3);
            white-space: nowrap;
            backdrop-filter: blur(10px);
            min-height: 50px;
            display: flex;
            align-items: center;
            position: relative;
            overflow: hidden;
        }
        
        .crm-nav a::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(16, 185, 129, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .crm-nav a:hover {
            background: rgba(16, 185, 129, 0.15);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(16, 185, 129, 0.2);
            border-color: rgba(16, 185, 129, 0.5);
        }
        
        .crm-nav a:hover::before {
            left: 100%;
        }
        
        .user-actions {
            display: flex;
            gap: 12px;
            align-items: center;
            justify-self: end;
        }
        
        .user-actions a {
            color: white;
            text-decoration: none;
            font-weight: 600;
            padding: 12px 20px;
            border-radius: 25px;
            transition: all 0.3s ease;
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.4);
            font-size: 1rem;
            min-height: 45px;
            display: flex;
            align-items: center;
            backdrop-filter: blur(10px);
        }
        
        .user-actions a:hover {
            background: rgba(16, 185, 129, 0.25);
            transform: translateY(-1px);
            box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
            border-color: rgba(16, 185, 129, 0.6);
        }
        
        /* התאמה לתוכן הראשי */
        .site-main {
            position: relative;
            z-index: 1;
        }
        
        /* הבטחה שכל הקונטיינרים יהיו שקופים למחצה */
        .wp-site-blocks {
            background-color: rgba(255, 255, 255, 0.02) !important;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            margin: 20px;
            padding: 20px;
        }
        
        /* רספונסיבי */
        @media (max-width: 1400px) {
            .crm-nav {
                gap: 10px;
            }
            
            .crm-nav a {
                padding: 12px 20px;
                font-size: 1rem;
            }
        }
        
        @media (max-width: 1200px) {
            .crm-nav {
                gap: 8px;
            }
            
            .crm-nav a {
                padding: 10px 16px;
                font-size: 0.95rem;
            }
            
            .user-actions a {
                padding: 10px 16px;
                font-size: 0.9rem;
            }
        }
        
        @media (max-width: 992px) {
            .crm-header-content {
                padding: 0 20px;
                flex-direction: column;
                gap: 15px;
            }
            
            .crm-logo {
                order: 1;
            }
            
            .crm-nav {
                order: 2;
                gap: 8px;
                justify-content: center;
            }
            
            .user-actions {
                order: 3;
                justify-content: center;
            }
        }
        
        @media (max-width: 768px) {
            body {
                background-attachment: scroll;
            }
            
            .crm-header {
                padding: 15px 0;
            }
            
            .crm-logo {
                font-size: 1.7rem;
            }
            
            .crm-nav {
                gap: 6px;
                flex-wrap: wrap;
            }
            
            .crm-nav a {
                font-size: 0.85rem;
                padding: 8px 12px;
                min-height: 40px;
            }
            
            .user-actions a {
                font-size: 0.85rem;
                padding: 8px 12px;
                min-height: 40px;
            }
        }
    </style>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>

<header class="crm-header">
    <div class="crm-header-content">
        <a href="<?php echo home_url(); ?>" class="crm-logo">
            🌟 CRM תזונה - מרים קרישבסקי
        </a>
        
        <nav class="crm-nav">
            <a href="<?php echo home_url(); ?>">🏠 מסך הבית</a>
            <a href="<?php echo get_post_type_archive_link('clients') ?: home_url('/clients/'); ?>">👥 מתאמנות</a>
            <a href="<?php echo get_post_type_archive_link('mentors') ?: home_url('/mentors/'); ?>">👩‍💼 מנטוריות</a>
            <a href="<?php echo get_post_type_archive_link('groups') ?: home_url('/groups/'); ?>">🎯 קבוצות</a>
            <a href="<?php echo home_url('/finished-clients/'); ?>">📝 מתאמנות שסיימו</a>
        </nav>
        
        <div class="user-actions">
            <?php if (is_user_logged_in()): ?>
                <a href="<?php echo wp_logout_url(home_url()); ?>">🚪 יציאה</a>
            <?php else: ?>
                <a href="<?php echo wp_login_url(); ?>">🔑 כניסה</a>
            <?php endif; ?>
        </div>
    </div>
</header>

<main id="main" class="site-main">