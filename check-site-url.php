<?php
// קובץ בדיקה זמני - לבדיקת כתובות האתר במסד הנתונים
require_once('wp-config.php');

// חיבור למסד הנתונים
$connection = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);

if ($connection->connect_error) {
    die("Connection failed: " . $connection->connect_error);
}

echo "<h1>בדיקת כתובות האתר במסד הנתונים</h1>";

// בדיקת כתובות האתר
$sql = "SELECT option_name, option_value FROM wp_options WHERE option_name IN ('home', 'siteurl')";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>כתובות האתר:</h2>";
    while($row = $result->fetch_assoc()) {
        echo "<p><strong>" . $row["option_name"] . ":</strong> " . $row["option_value"] . "</p>";
    }
} else {
    echo "לא נמצאו תוצאות";
}

// בדיקת משתמשים
$sql = "SELECT user_login, user_email, user_registered FROM wp_users LIMIT 5";
$result = $connection->query($sql);

if ($result->num_rows > 0) {
    echo "<h2>משתמשים במערכת:</h2>";
    while($row = $result->fetch_assoc()) {
        echo "<p><strong>שם משתמש:</strong> " . $row["user_login"] . " | <strong>אימייל:</strong> " . $row["user_email"] . " | <strong>תאריך רישום:</strong> " . $row["user_registered"] . "</p>";
    }
} else {
    echo "לא נמצאו משתמשים";
}

$connection->close();
?> 