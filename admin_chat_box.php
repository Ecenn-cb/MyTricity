<?php
session_start();
include 'koneksiDB.php';

if (!isset($_SESSION['id_user']) || $_SESSION['role'] !== 'admin') {
    echo "Anda harus login sebagai admin.";
    exit;
}

$users = $conn->query("
    SELECT u.id_user, u.username 
    FROM users u
    JOIN chat_messages cm ON u.id_user = cm.id_user
    GROUP BY u.id_user
    ORDER BY MAX(cm.created_at) DESC
");

$selected_user_id = $_GET['user_id'] ?? null;

// Kirim balasan
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message']) && $selected_user_id) {
    $msg = trim($_POST['message']);
    if ($msg !== '') {
        $stmt = $conn->prepare("INSERT INTO chat_messages (id_user, message, is_from_admin) VALUES (?, ?, 1)");
        $stmt->bind_param("is", $selected_user_id, $msg);
        $stmt->execute();
    }
}

// Ambil pesan jika user dipilih
$messages = [];
if ($selected_user_id) {
    $stmt = $conn->prepare("SELECT message, is_from_admin, created_at FROM chat_messages WHERE id_user = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $selected_user_id);
    $stmt->execute();
    $messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Panel Chat Admin</title>
    <style>
        body {
            background: #111;
            color: #fff;
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            display: flex;
            height: 100vh;
        }

        .sidebar {
            width: 250px;
            background: #1a1a1a;
            padding: 20px;
            overflow-y: auto;
            border-right: 1px solid #333;
        }

        .sidebar h3 {
            margin-top: 0;
            margin-bottom: 10px;
            color: #0ff;
        }

        .sidebar a {
            display: block;
            padding: 10px;
            color: #ccc;
            text-decoration: none;
            border-radius: 6px;
            margin-bottom: 5px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background-color: #0ff3;
            color: #000;
            font-weight: bold;
        }

        .main-content {
            flex: 1;
            padding: 20px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .chat-box {
            background: #222;
            padding: 20px;
            border-radius: 10px;
            max-height: 70vh;
            overflow-y: auto;
        }

        .msg {
            margin: 10px 0;
            padding: 10px;
            border-radius: 8px;
            max-width: 70%;
        }

        .admin-msg {
            background: #0ff2;
            margin-left: auto;
            text-align: right;
        }

        .user-msg {
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

        .footer-links {
            text-align: center;
            margin-top: 20px;
        }

        .footer-links a {
            color: #0ff;
            text-decoration: none;
            font-weight: bold;
            margin: 0 10px;
        }

        .footer-links a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar kiri -->
        <div class="sidebar">
            <h3>Pelanggan</h3>
            <?php while ($u = $users->fetch_assoc()): ?>
                <a href="?user_id=<?= $u['id_user'] ?>" class="<?= $selected_user_id == $u['id_user'] ? 'active' : '' ?>">
                    <?= htmlspecialchars($u['username']) ?>
                </a>
            <?php endwhile; ?>
        </div>

        <!-- Konten kanan -->
        <div class="main-content">
            <?php if ($selected_user_id): ?>
                <div class="chat-box" id="chat-box">
                    <?php foreach ($messages as $msg): ?>
                        <div class="msg <?= $msg['is_from_admin'] ? 'admin-msg' : 'user-msg' ?>">
                            <?= htmlspecialchars($msg['message']) ?>
                            <div class="timestamp"><?= date('d M Y H:i', strtotime($msg['created_at'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <form method="post">
                    <textarea name="message" required placeholder="Tulis balasan..."></textarea>
                    <button type="submit">Kirim Balasan</button>
                </form>
            <?php else: ?>
                <p><i>Pilih pengguna untuk melihat dan membalas pesan.</i></p>
            <?php endif; ?>

            <!-- Footer Links -->
            <div class="footer-links">
                <a href="index.php">← Kembali ke Beranda</a>
            </div>
        </div>
    </div>

    <script>
        const chatBox = document.getElementById('chat-box');
        if (chatBox) {
            chatBox.scrollTop = chatBox.scrollHeight;
        }
    </script>
</body>
</html>
