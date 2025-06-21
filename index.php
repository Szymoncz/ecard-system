<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/Card.php';

$db = new Database();
$cardObj = new Card($db);
$cards = $cardObj->getAllCards();

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
if ($search) {
    $cards = $cardObj->searchCards($search);
}

if (isset($_GET['card_id'])) {
    setcookie('last_card_id', $_GET['card_id'], time() + (86400 * 30), "/");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>E-Card System</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>E-Card Sending System</h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="submit_idea.php">Submit Idea</a>
            <?php if (isset($_SESSION['user_id'])): ?>
                <?php if ($_SESSION['role'] == 'admin' || $_SESSION['role'] == 'moderator'): ?>
                    <a href="admin.php">Admin Panel</a>
                <?php endif ?>
                <a href="logout.php">Logout</a>
            <?php else: ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php endif ?>
        </nav>
    </header>
    <main>
        <h2>Available Cards</h2>
        <form method="GET" action="index.php">
            <input type="text" name="search" placeholder="Search by tag or title" value="<?php echo htmlspecialchars($search); ?>">
            <button type="submit">Search</button>
        </form>
        <div class="card-list">
            <?php foreach ($cards as $card): ?>
                <div class="card">
                    <h3><?php echo htmlspecialchars($card['title']); ?></h3>
                    <img src="assets/images/<?php echo htmlspecialchars($card['image']); ?>" alt="<?php echo htmlspecialchars($card['title']); ?>">
                    <p>Tags: <?php echo htmlspecialchars($card['tags']); ?></p>
                    <p>Times Sent: <?php echo $card['send_count']; ?> | Rating: <?php echo number_format($card['avg_rating'], 1); ?></p>
                    <a href="send_card.php?card_id=<?php echo $card['id']; ?>">Send Card</a>
                </div>
            <?php endforeach ?>
        </div>
        <?php if (isset($_COOKIE['last_card_id'])): ?>
            <p>Last viewed card ID: <?php echo htmlspecialchars($_COOKIE['last_card_id']); ?></p>
        <?php endif ?>
    </main>
</body>
</html>