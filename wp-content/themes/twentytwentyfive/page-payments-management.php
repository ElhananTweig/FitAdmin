<?php
/*
Template Name: Payments Management
*/

// ווידוא שהקוד לא נגיש ישירות
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

get_header();

// הכללת הלוגיקה וה-UI הקיימים מתוך הקובץ המקורי
include get_template_directory() . '/payments-management.php';

get_footer(); 