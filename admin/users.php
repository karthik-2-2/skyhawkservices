<?php
session_start();

if (!isset($_SESSION['admin_phone'])) {
    header('Location: index.php');
    exit();
}

require_once '../config/db.php';

// Fetch all users with address
$stmt = $conn->prepare("SELECT id, name, email, phone, address FROM \"user\" ORDER BY id ASC");
$stmt->execute();
$users = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Users List</title>
  <style>
    body {
      background-color: #121212;
      color: #eee;
      font-family: Arial, sans-serif;
      margin: 20px;
    }
    h1 {
      color: #39ff14;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
      background-color: #1e1e1e;
    }
    th, td {
      padding: 12px 15px;
      border: 1px solid #333;
      text-align: left;
      vertical-align: top;
    }
    th {
      background-color: #39ff14;
      color: #000;
    }
    tr:hover {
      background-color: #333;
    }
    .view-btn {
      background-color: #39ff14;
      color: #000;
      padding: 6px 12px;
      border: none;
      border-radius: 15px;
      cursor: pointer;
      text-decoration: none;
      font-weight: bold;
      font-size: 12px;
      transition: all 0.3s ease;
    }
    .view-btn:hover {
      background-color: #2ce600;
      transform: scale(1.05);
    }
    a.back {
      display: inline-block;
      margin-bottom: 15px;
      color: #39ff14;
      text-decoration: none;
      font-weight: bold;
    }
    a.back:hover {
      text-decoration: underline;
    }
  </style>
</head>
<body>

  <a href="dashboard.php" class="back">&larr; Back to Dashboard</a>
  <h1>Registered Users</h1>

  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Phone</th>
        <th>Address</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $user) : ?>
        <tr>
          <td><?php echo htmlspecialchars($user['id']); ?></td>
          <td><?php echo htmlspecialchars($user['name']); ?></td>
          <td><?php echo htmlspecialchars($user['email']); ?></td>
          <td><?php echo htmlspecialchars($user['phone']); ?></td>
          <td><?php echo nl2br(htmlspecialchars($user['address'])); ?></td>
          <td>
            <a href="user_details.php?user_id=<?php echo $user['id']; ?>" class="view-btn">View Details</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

</body>
</html>
