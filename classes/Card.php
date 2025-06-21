<?php
require_once 'config.php';

class Card {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function getAllCards() {
        $sql = "SELECT id, title, image, tags, send_count, 
                (IF(rating_count > 0, rating_total / rating_count, 0)) as avg_rating 
                FROM cards";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function searchCards($search) {
        $search = $this->db->escape($search);
        $sql = "SELECT id, title, image, tags, send_count, 
                (IF(rating_count > 0, rating_total / rating_count, 0)) as avg_rating 
                FROM cards 
                WHERE title LIKE '%$search%' OR tags LIKE '%$search%'";
        $result = $this->db->query($sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    public function addCard($title, $tags, $image) {
        $title = $this->db->escape($title);
        $tags = $this->db->escape($tags);
        $image = $this->db->escape($image);
        $sql = "INSERT INTO cards (title, image, tags) VALUES ('$title', '$image', '$tags')";
        return $this->db->query($sql);
    }

    public function deleteCard($id) {
        $id = (int)$id;
        $sql = "SELECT image FROM cards WHERE id = $id";
        $result = $this->db->query($sql);
        if ($result->num_rows > 0) {
            $image = $result->fetch_assoc()['image'];
            unlink(UPLOAD_DIR . $image);
            $sql = "DELETE FROM cards WHERE id = $id";
            return $this->db->query($sql);
        }
        return false;
    }

    public function rateCard($card_id, $rating) {
        $card_id = (int)$card_id;
        $rating = (int)$rating;
        if ($rating >= 1 && $rating <= 5) {
            $sql = "UPDATE cards SET rating_total = rating_total + $rating, rating_count = rating_count + 1 WHERE id = $card_id";
            return $this->db->query($sql);
        }
        return false;
    }

    public function incrementSendCount($card_id) {
        $card_id = (int)$card_id;
        $sql = "UPDATE cards SET send_count = send_count + 1 WHERE id = $card_id";
        return $this->db->query($sql);
    }
}
?>