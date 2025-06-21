<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Card.php';
require_once 'classes/Email.php';

$db = new Database();
$cardObj = new Card($db);
$emailObj = new Email();
$error = '';
$success = '';

if (!isset($_GET['card_id'])) {
    header('Location: index.php');
    exit;
}

$card_id = (int)$_GET['card_id'];
$card = $cardObj->getAllCards();
$card = array_filter($card, function($c) use ($card_id) { return $c['id'] == $card_id; });
$card = reset($card);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sender_name = $_POST['sender_name'];
    $recipient_email = $_POST['recipient_email'];
    $message = $_POST['message'];
    $rating = isset($_POST['rating']) ? (int)$_POST['rating'] : 0;

    if ($emailObj->sendCard($recipient_email, $sender_name, $message, $card['image'])) {
        $cardObj->incrementSendCount($card_id);
        if ($rating >= 1 && $rating <= 5) {
            $cardObj->rateCard($card_id, $rating);
        }
        $sql = "INSERT INTO logs (card_id, sender_ip, recipient_email, send_date) 
                VALUES ($card_id, '{$db->escape($_SERVER['REMOTE_ADDR'])}', '{$db->escape($recipient_email)}', NOW())";
        $db->query($sql);
        $success = 'Card sent successfully!';
    } else {
        $error = 'Failed to send card';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Card</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Send Card: <?php echo htmlspecialchars($card['title']); ?></h2>
    <img src="assets/images/<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>">
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif ?>
    <form method="POST">
        <label>Sender Name: <input type="text" name="sender_name" required></label><br>
        <label>Recipient Email: <input type="email" name="recipient_email" required></label><br>
        <label>Message: <textarea name="message" required></textarea></label><br>
        <label>Rate Card (1-5): <input type="number" name="rating" min="1" max="5"></label><br>
        <button type="submit">Send Card</button>
    </form>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>