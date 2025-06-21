<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Card.php';

if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'moderator'])) {
    header('Location: index.php');
    exit;
}

$db = new Database();
$cardObj = new Card($db);
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderator')) {
    $title = $_POST['title'];
    $tags = $_POST['tags'];
    $image = $_FILES['image']['name'];
    $target = UPLOAD_DIR . basename($image);
    if (move_uploaded_file($_FILES['image']['tmp_name'], $target)) {
        if ($cardObj->addCard($title, $tags, $image)) {
            $success = 'Card added successfully!';
        } else {
            $error = 'Failed to add card';
        }
    } else {
        $error = 'Failed to upload image';
    }
}

if (isset($_GET['delete_card']) && $_SESSION['role'] == 'admin') {
    if ($cardObj->deleteCard($_GET['delete_card'])) {
        $success = 'Card deleted successfully!';
    } else {
        $error = 'Failed to delete card';
    }
}

if (isset($_GET['review_idea']) && $_SESSION['role'] == 'admin') {
    $sql = "UPDATE ideas SET reviewed = 1 WHERE id = " . (int)$_GET['review_idea'];
    if ($db->query($sql)) {
        $success = 'Idea marked as reviewed!';
    } else {
        $error = 'Failed to review idea';
    }
}

$cards = $cardObj->getAllCards();
$logs = $db->query("SELECT logs.*, cards.title FROM logs JOIN cards ON logs.card_id = cards.id")->fetch_all(MYSQLI_ASSOC);
$ideas = $db->query("SELECT * FROM ideas")->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <h2>Admin Panel</h2>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif ?>
    <?php if ($success): ?>
        <p class="success"><?php echo htmlspecialchars($success); ?></p>
    <?php endif ?>
    <h3>Add New Card</h3>
    <form method="POST" enctype="multipart/form-data">
        <label>Title: <input type="text" name="title" required></label><br>
        <label>Tags: <input type="text" name="tags" required></label><br>
        <label>Image: <input type="file" name="image" accept="image/*" required></label><br>
        <button type="submit">Add Card</button>
    </form>
    <h3>Manage Cards</h3>
    <table>
        <tr><th>Title</th><th>Image</th><th>Tags</th><th>Actions</th></tr>
        <?php foreach ($cards as $card): ?>
            <tr>
                <td><?php echo htmlspecialchars($card['title']); ?></td>
                <td><img src="assets/images/<?php echo htmlspecialchars($card['image']); ?>" width="50"></td>
                <td><?php echo htmlspecialchars($card['tags']); ?></td>
                <td>
                    <?php if ($_SESSION['role'] == 'admin'): ?>
                        <a href="admin.php?delete_card=<?php echo $card['id']; ?>">Delete</a>
                    <?php endif ?>
                </td>
            </tr>
        <?php endforeach ?>
    </table>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <h3>Logs</h3>
        <table>
            <tr><th>Card</th><th>Sender IP</th><th>Recipient Email</th><th>Date</th></tr>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?php echo htmlspecialchars($log['title']); ?></td>
                    <td><?php echo htmlspecialchars($log['sender_ip']); ?></td>
                    <td><?php echo htmlspecialchars($log['recipient_email']); ?></td>
                    <td><?php echo $log['send_date']; ?></td>
                </tr>
            <?php endforeach ?>
        </table>
        <h3>Submitted Ideas</h3>
        <table>
            <tr><th>Content</th><th>Submitter</th><th>Date</th><th>Status</th><th>Actions</th></tr>
            <?php foreach ($ideas as $idea): ?>
                <tr>
                    <td><?php echo htmlspecialchars($idea['content']); ?></td>
                    <td><?php echo htmlspecialchars($idea['submitter_email'] ?: 'Guest'); ?></td>
                    <td><?php echo $idea['submit_date']; ?></td>
                    <td><?php echo $idea['reviewed'] ? 'Reviewed' : 'Pending'; ?></td>
                    <td>
                        <?php if (!$idea['reviewed']): ?>
                            <a href="admin.php?review_idea=<?php echo $idea['id']; ?>">Mark as Reviewed</a>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endforeach ?>
        </table>
    <?php endif ?>
    <p><a href="index.php">Back to Home</a></p>
</body>
</html>