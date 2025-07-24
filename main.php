<?php include 'db.php'; ?> 
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Handle posting a notice
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['post_notice'])) {
    $message = $_POST['message'];
    $image_path = "";

    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
        $image_path = $target_dir . basename($_FILES["image"]["name"]);
        move_uploaded_file($_FILES["image"]["tmp_name"], $image_path);
    }

    $stmt = $conn->prepare("INSERT INTO messages (message, image_path) VALUES (?, ?)");
    $stmt->bind_param("ss", $message, $image_path);
    $stmt->execute();
    $stmt->close();
}

// Handle deleting a notice
if (isset($_POST['delete_notice'])) {
    $id = intval($_POST['delete_notice']);
    $result = $conn->query("SELECT image_path FROM messages WHERE id = $id");
    if ($row = $result->fetch_assoc()) {
        if (!empty($row['image_path']) && file_exists($row['image_path'])) unlink($row['image_path']);
    }
    $conn->query("DELETE FROM messages WHERE id = $id");
}

// Handle deleting a complaint
if (isset($_POST['delete_complaint'])) {
    $id = intval($_POST['delete_complaint']);
    $conn->query("DELETE FROM complaints WHERE id = $id");
}

// Fetch notices and complaints
$notices = $conn->query("SELECT * FROM messages ORDER BY created_at DESC");
$complaints = $conn->query("SELECT * FROM complaints ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>President Panel - Electricity</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<style>
:root {
    --primary: #00b4d8;         /* electric blue */
    --primary-dark: #0077b6;
    --secondary: #ffc300;      /* bright yellow */
    --danger: #f94144;         
    --danger-dark: #d00000;
    --warning: #f3722c;        
    --success: #90be6d;        
    --light: #f1f3f8;
    --dark: #222;
    --gray: #6c757d;
    --light-gray: #e9ecef;
    --border-radius: 10px;
    --box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
}

body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: linear-gradient(135deg, #fdfbfb, #ebedee);
    background-image: url('https://www.transparenttextures.com/patterns/circuit-board.png');
    color: var(--dark);
}

.container {
    max-width: 1200px;
    margin: 2rem auto;
    padding: 0 1rem;
}

.panel-header {
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    color: white;
    text-shadow: 1px 1px 3px rgba(0,0,0,0.3);
    box-shadow: var(--box-shadow);
    border-radius: var(--border-radius);
    text-align: center;
    padding: 1.5rem;
}

section {
    background: #ffffffee;
    border: 1px solid var(--light-gray);
    border-radius: var(--border-radius);
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: var(--box-shadow);
}

h3 {
    background: linear-gradient(90deg, var(--primary), var(--secondary));
    color: white;
    padding: 0.5rem 1rem;
    border-radius: var(--border-radius);
    font-size: 1.2rem;
    box-shadow: var(--box-shadow);
    text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
    margin-bottom: 1rem;
}

.form-group {
    margin-bottom: 1.5rem;
}

label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
}

textarea, input[type="text"], input[type="file"] {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid var(--light-gray);
    border-radius: var(--border-radius);
    font-family: inherit;
    font-size: 1rem;
}

textarea {
    min-height: 150px;
    resize: vertical;
}

textarea:focus, input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(0,180,216,0.2);
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    padding: 0.75rem 1.5rem;
    border: none;
    border-radius: var(--border-radius);
    font-size: 1rem;
    font-weight: 600;
    cursor: pointer;
    transition: var(--transition);
}

.btn-primary {
    background: linear-gradient(90deg, var(--primary), var(--primary-dark));
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(90deg, var(--primary-dark), var(--primary));
    transform: translateY(-1px);
}

.btn-danger {
    background: linear-gradient(90deg, var(--danger), var(--danger-dark));
    color: white;
}

.btn-danger:hover {
    background: linear-gradient(90deg, var(--danger-dark), var(--danger));
}

.notice, .complaint {
    padding: 1rem;
    border-radius: var(--border-radius);
    margin-bottom: 1rem;
    box-shadow: var(--box-shadow);
    transition: var(--transition);
    position: relative;
}

.notice {
    border-left: 5px solid var(--primary);
    background: #e0f7fa;
}

.complaint {
    border-left: 5px solid var(--warning);
    background: #fff7e6;
}

.notice:hover, .complaint:hover {
    transform: scale(1.02);
    box-shadow: 0 0 15px rgba(0,180,216,0.3);
}

.notice img {
    max-width: 100%;
    margin-top: 1rem;
    border-radius: var(--border-radius);
}

.meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 1rem;
    font-size: 0.9rem;
    color: var(--gray);
}

.village {
    background: var(--secondary);
    color: #222;
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    font-size: 0.85rem;
}

.timestamp {
    background: var(--light-gray);
    padding: 0.2rem 0.5rem;
    border-radius: 5px;
    font-size: 0.85rem;
}

.empty-state {
    text-align: center;
    padding: 2rem;
    color: var(--gray);
    background: var(--light);
    border-radius: var(--border-radius);
    border: 2px dashed var(--light-gray);
}

.empty-state i {
    font-size: 2rem;
    margin-bottom: 1rem;
    color: var(--primary);
    text-shadow: 0 0 10px var(--primary);
}
</style>
</head>

<body>
<div class="container">
<header class="panel-header">
    <h2><i class="fas fa-bolt"></i> President Panel - Electricity</h2>
    <p>Manage electricity notices and complaints</p>
</header>

<section>
    <h3><i class="fas fa-edit"></i> Post a New Notice</h3>
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="post_notice" value="1">
        <div class="form-group">
            <label for="message">Notice Content</label>
            <textarea id="message" name="message" placeholder="Write your notice..." required></textarea>
        </div>
        <div class="form-group">
            <label for="image">Upload Image (Optional)</label>
            <input type="file" id="image" name="image" accept="image/*">
        </div>
        <button type="submit" class="btn btn-primary">
            <i class="fas fa-paper-plane"></i> Post Notice
        </button>
    </form>
</section>

<section>
    <h3><i class="fas fa-bullhorn"></i> Notices</h3>
    <?php if ($notices->num_rows === 0): ?>
        <div class="empty-state">
            <i class="far fa-bell-slash"></i>
            <p>No notices have been posted yet</p>
        </div>
    <?php else: ?>
        <?php while ($row = $notices->fetch_assoc()): ?>
            <div class="notice">
                <div class="notice-content">
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                    <?php if (!empty($row['image_path'])): ?>
                        <img src="<?php echo htmlspecialchars($row['image_path']); ?>" alt="Notice image">
                    <?php endif; ?>
                </div>
                <div class="meta">
                    <div class="timestamp">
                        <i class="far fa-clock"></i>
                        <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
                    </div>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this notice?');">
                        <input type="hidden" name="delete_notice" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</section>

<section>
    <h3><i class="fas fa-comment-dots"></i> Villager Complaints</h3>
    <?php if ($complaints->num_rows === 0): ?>
        <div class="empty-state">
            <i class="far fa-smile"></i>
            <p>No complaints have been submitted</p>
        </div>
    <?php else: ?>
        <?php while ($row = $complaints->fetch_assoc()): ?>
            <div class="complaint">
                <div class="complaint-content">
                    <p><?php echo nl2br(htmlspecialchars($row['message'])); ?></p>
                </div>
                <div class="meta">
                    <div>
                        <div class="village">
                            <i class="fas fa-home"></i>
                            <?php echo htmlspecialchars($row['village']); ?>
                        </div>
                        <div class="timestamp">
                            <i class="far fa-clock"></i>
                            <?php echo date("d M Y, h:i A", strtotime($row['created_at'])); ?>
                        </div>
                    </div>
                    <form method="post" onsubmit="return confirm('Are you sure you want to delete this complaint?');">
                        <input type="hidden" name="delete_complaint" value="<?php echo $row['id']; ?>">
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </form>
                </div>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</section>
</div>
</body>
</html>
