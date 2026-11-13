<?php
// This file is responsible for displaying the list of donors.

require_once '../../controllers/DonorController.php';

$donorController = new DonorController();
$donors = $donorController->getAllDonors();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donor List</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h1>Donor List</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Age</th>
                <th>Blood Type</th>
                <th>Organ</th>
                <th>Availability</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($donors as $donor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($donor['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($donor['age']); ?></td>
                    <td><?php echo htmlspecialchars($donor['blood_type']); ?></td>
                    <td><?php echo htmlspecialchars($donor['organ']); ?></td>
                    <td><?php echo htmlspecialchars($donor['availability']); ?></td>
                    <td><?php echo htmlspecialchars($donor['phone']); ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>