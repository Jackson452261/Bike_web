<?php

// 允許錯誤顯示（測試用）
error_reporting(E_ALL);
ini_set('display_errors', 1);

// 允許 GET 測試（防止 405 錯誤）
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    echo json_encode(["status" => 200, "message" => "GET request works!"]);
    exit();
}

// 確保是 POST 請求
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    http_response_code(405);
    echo json_encode(["error" => "Method Not Allowed"]);
    exit();
}

// 安全過濾輸入，防止 XSS
$name     = htmlspecialchars(trim($_POST['name'] ?? ''));
$email    = filter_var(trim($_POST['email'] ?? ''), FILTER_SANITIZE_EMAIL);
$comments = htmlspecialchars(trim($_POST['message'] ?? ''));

// 簡化 Email 驗證
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(["error" => "Invalid email address"]);
    exit();
}

// 必填欄位檢查
if (empty($name) || empty($email) || empty($comments)) {
    echo json_encode(["error" => "All fields are required"]);
    exit();
}

// 設定固定的收件人 Email（重要！）
$recipient = "contact@yourdomain.com"; // 請更改為你的 Email

// 設定 Email 內容
$subject = "New Contact Form Submission from $name";
$message = "You have received a new message from $name.\n\n";
$message .= "Email: $email\n\n";
$message .= "Message:\n$comments\n\n";

$headers = "From: noreply@yourdomain.com\r\n";
$headers .= "Reply-To: $email\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

// 嘗試發送郵件
if (mail($recipient, $subject, $message, $headers)) {
    echo json_encode(["status" => 200, "success" => true]);
} else {
    echo json_encode(["error" => "Failed to send email. Check mail server settings."]);
}
?>
