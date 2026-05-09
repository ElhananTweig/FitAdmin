<?php
/*
Template Name: Payments Management
*/

// ווידוא שהקוד לא נגיש ישירות
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

wp_enqueue_style( 'crm-payments-fa', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css', array(), '6.4.0' );

get_header();

// הכללת הלוגיקה וה-UI הקיימים מתוך הקובץ המקורי
include get_template_directory() . '/payments-management.php';

get_footer(); 