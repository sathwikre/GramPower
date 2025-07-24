<?php include 'db.php'; ?>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['complaint'], $_POST['village'])) {
    $complaint = $_POST['complaint'];
    $village = $_POST['village'];

    $stmt = $conn->prepare("INSERT INTO complaints (message, village) VALUES (?, ?)");
    $stmt->bind_param("ss", $complaint, $village);
    $stmt->execute();
    $stmt->close();
    $success = "✅ Your complaint has been sent!";
}

$notices = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Village Notice Board</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #00b4d8;
    --primary-dark: #0077b6;
    --secondary: #ffc300;
    --accent: #f72585;
    --light: #f1f3f8;
    --dark: #222;
    --gray: #6c757d;
    --light-gray: #e9ecef;
    --success: #90be6d;
    --border-radius: 10px;
    --shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f9fbfc url('https://www.transparenttextures.com/patterns/circuit-board.png');
    color: var(--dark);
    margin: 0;
    padding: 0;
}

.container {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
}

header {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 1.5rem;
    border-radius: var(--border-radius);
    box-shadow: var(--shadow);
    text-align: center;
}

header h1 {
    margin: 0;
    font-size: 2rem;
}

header p {
    margin-top: 0.5rem;
    opacity: 0.9;
}

.notice-board, .complaint-form {
    background: white;
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-top: 2rem;
    box-shadow: var(--shadow);
}

.notice {
    background: #e0f7fa;
    border-left: 5px solid var(--primary);
    border-radius: var(--border-radius);
    padding: 1rem;
    margin-bottom: 1rem;
    box-shadow: var(--shadow);
    transition: all 0.3s ease;
}

.notice:hover {
    transform: translateY(-3px);
    box-shadow: 0 6px 12px rgba(0, 180, 216, 0.2);
}

.notice p {
    margin: 0;
    line-height: 1.5;
}

.notice img {
    max-width: 100%;
    margin-top: 1rem;
    border-radius: 6px;
    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
}

.timestamp {
    font-size: 0.85rem;
    color: var(--gray);
    margin-top: 0.5rem;
}

.empty-state {
    text-align: center;
    color: var(--gray);
    padding: 2rem;
    background: var(--light);
    border-radius: var(--border-radius);
    margin-top: 1rem;
}

.empty-state i {
    font-size: 2rem;
    color: var(--primary);
    margin-bottom: 0.5rem;
}

h2 {
    margin-top: 0;
    margin-bottom: 1rem;
    color: var(--primary-dark);
}

.form-group {
    margin-bottom: 1rem;
}

label {
    font-weight: 600;
    margin-bottom: 0.5rem;
    display: block;
}

textarea, input[type="text"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--light-gray);
    border-radius: 6px;
    font-size: 1rem;
    transition: all 0.3s ease;
}

textarea:focus, input[type="text"]:focus {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,180,216,0.2);
    outline: none;
    background: #fff;
}

textarea {
    min-height: 100px;
}

button {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    color: white;
    border: none;
    padding: 0.75rem 1.5rem;
    border-radius: 6px;
    cursor: pointer;
    font-size: 1rem;
    font-weight: bold;
    transition: all 0.3s ease;
}

button:hover {
    background: linear-gradient(135deg, var(--primary-dark), var(--primary));
    transform: translateY(-1px);
}

.success {
    background: rgba(144, 190, 109, 0.1);
    border-left: 4px solid var(--success);
    padding: 1rem;
    margin-bottom: 1rem;
    border-radius: 6px;
    color: var(--success);
}
</style>
</head>

<body>
<div class="container">

<header>
    <h1><i class="fas fa-bolt"></i> Village Notice Board</h1>
    <p>Stay informed about electricity updates & share your concerns</p>
</header>

<div class="notice-board">
<?php if ($notices->num_rows === 0): ?>
    <div class="empty-state">
        <i class="far fa-bell-slash"></i>
        <p>No notices have been posted yet</p>
    </div>
<?php else: ?>
    <?php while ($row = $notices->fetch_assoc()): ?>
        <div class="notice">
            <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
            <div class="timestamp">
                <i class="far fa-clock"></i>
                Posted on: <?php echo date("F j, Y \\a\\t g:i A", strtotime($row['created_at'])); ?>
            </div>
            <?php if (!empty($row['image_path'])): ?>
                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Notice image">
            <?php endif; ?>
        </div>
    <?php endwhile; ?>
<?php endif; ?>
</div>

<div class="complaint-form">
    <h2><i class="fas fa-edit"></i> Submit a Complaint</h2>

    <?php if (!empty($success)): ?>
        <div class="success">
            <i class="fas fa-check-circle"></i> <?php echo $success; ?>
        </div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label for="complaint">Your Complaint</label>
            <textarea id="complaint" name="complaint" placeholder="Describe your complaint here..." required></textarea>
        </div>

        <div class="form-group">
            <label for="village">Village Name</label>
            <input type="text" id="village" name="village" placeholder="Enter your village name" required>
        </div>

        <button type="submit"><i class="fas fa-paper-plane"></i> Send Complaint</button>
    </form>
</div>

</div>
</body>
</html>
