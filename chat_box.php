<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'customer') {
    echo "Anda harus login sebagai pelanggan.";
    exit;
}

$id_user = $_SESSION['id_user'];

// Kirim pesan pelanggan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = trim($_POST['message']);
    if ($message !== '') {
        $stmt = $conn->prepare("INSERT INTO chat_messages (id_user, message, is_from_admin) VALUES (?, ?, 0)");
        $stmt->bind_param("is", $id_user, $message);
        $stmt->execute();
    }
}

// Ambil semua pesan
$stmt = $conn->prepare("SELECT message, is_from_admin, created_at FROM chat_messages WHERE id_user = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Chat dengan Admin</title>
    <style>
        body {
            background: #111;
            color: white;
            font-family: Arial;
            max-width: 600px;
            margin: auto;
            padding: 20px;
        }

        .chat-box {
            background: #222;
            border-radius: 10px;
            padding: 20px;
            max-height: 400px;
            overflow-y: auto;
        }

        .msg {
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }

        .user-msg {
            background: #0ff2;
            margin-left: auto;
            text-align: right;
        }

        .admin-msg {
            background: #f06;
            margin-right: auto;
        }

        .timestamp {
            font-size: 12px;
            color: #aaa;
            margin-top: 5px;
        }

        form {
            margin-top: 15px;
        }

        textarea {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            resize: none;
            font-size: 14px;
        }

        button {
            padding: 10px 20px;
            background: #0ff;
            color: #000;
            border: none;
            border-radius: 8px;
            margin-top: 10px;
            cursor: pointer;
        }

        .back-link {
            margin-top: 20px;
            text-align: center;
        }

        .back-link a {
            color: #0ff;
            text-decoration: none;
            font-weight: bold;
        }

        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Chat dengan Admin</h2>
    <div class="chat-box" id="chat-box">
        <?php foreach ($messages as $msg): ?>
            <div class="msg <?= $msg['is_from_admin'] ? 'admin-msg' : 'user-msg' ?>">
                <?= htmlspecialchars($msg['message']) ?>
                <div class="timestamp"><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <form method="post">
        <textarea name="message" required placeholder="Tulis pesan..."></textarea>
        <button type="submit">Kirim</button>
    </form>

    <div class="back-link">
        <a href="index.php">← Kembali ke Beranda</a>
    </div>

    <script>
        document.getElementById('chat-box').scrollTop = document.getElementById('chat-box').scrollHeight;
    </script>
</body>
</html>
