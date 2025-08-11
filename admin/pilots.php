<?php
session_start();
// FIX: Using the correct admin-specific session
if (!isset($_SESSION['admin_phone'])) {
    header("Location: index.php");
    exit();
}
require_once '../config/db.php';

// FIX: This new query correctly calculates total earnings from the pilot_earnings table
// instead of looking for the deleted 'pilotwallet' table.
$stmt = $conn->prepare("
    SELECT
        p.id, p.name, p.email, p.phone, p.pilot_license_number,
        COALESCE(pe.total_earned, 0) AS total_earnings
    FROM
        pilot p
    LEFT JOIN
        (SELECT pilot_phone, SUM(amount) AS total_earned FROM pilot_earnings GROUP BY pilot_phone) AS pe
    ON
        p.phone = pe.pilot_phone
    ORDER BY
        p.id ASC
");
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Pilots List</title>
  <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;600;700&display=swap" rel="stylesheet">
  <style>
    :root { --black: #000; --mint-green: #3EB489; --metallic-silver: #B0B0B0; --dark-gray: #1a1a1a; }
    body { font-family: 'Outfit', sans-serif; background-color: var(--black); color: var(--metallic-silver); margin: 20px; }
    h1 { color: var(--mint-green); }
    table { width: 100%; border-collapse: collapse; margin-top: 20px; background-color: var(--dark-gray); }
    th, td { padding: 12px 15px; border: 1px solid #333; text-align: left; vertical-align: top; }
    th { background-color: var(--mint-green); color: #000; }
    tr:hover { background-color: #2a2a2a; }
    a { color: var(--mint-green); text-decoration: none; }
    a.back { font-weight: bold; margin-bottom: 15px; display: inline-block; }
    .btn-view {
        background-color: #3498db; color: #fff; padding: 5px 10px; border-radius: 5px;
        text-decoration: none; font-size: 0.9rem;
    }
  </style>
</head>
<body>
  <a href="dashboard.php" class="back">&larr; Back to Dashboard</a>
  <h1>Registered Pilots</h1>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Contact</th>
        <th>License No.</th>
        <th>Total Earned</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($result as $pilot) : ?>
        <tr>
          <td><?php echo $pilot['id']; ?></td>
          <td><?php echo htmlspecialchars($pilot['name']); ?></td>
          <td><?php echo htmlspecialchars($pilot['email']); ?><br><?php echo htmlspecialchars($pilot['phone']); ?></td>
          <td><?php echo htmlspecialchars($pilot['pilot_license_number']); ?></td>
          <td>₹<?php echo number_format($pilot['total_earnings'], 2); ?></td>
          <td>
            <a href="view_pilot_work.php?phone=<?php echo htmlspecialchars($pilot['phone']); ?>" class="btn-view">View Work</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</body>
</html>