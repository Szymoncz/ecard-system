<?php
session_start();
require_once 'classes/Database.php';

$db = new Database();
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['content'];
    $submitter_email = isset($_SESSION['user_id']) ? $_SESSION['email'] : $_POST['submitter_email'];
    $sql = "INSERT INTO ideas (content, submitter_email, submit_date) 
            VALUES ('{$db->escape($content)}', '{$db->escape($submitter_email)}', NOW())";
    if ($db->query($sql)) {
        $success = 'Idea submitted successfully!';
    } else {
        $error = 'Failed to submit idea';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Submit Idea</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Submit an Idea</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif ?>
    <form method="POST">
        <?php if (!isset($_SESSION['user_id'])): ?>
            <label>Your Email: <input type="email" name="submitter_email" required></label><br>
        <?php endif ?>
        <label>Idea: <textarea name="content" required></textarea></label><br>
        <button type="submit">Submit Idea</button>
    </form>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>