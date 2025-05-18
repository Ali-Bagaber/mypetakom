 <?php

include 'Databased/db_connect.php';




$sql = "SELECT e.title AS event_name, 
               m.points AS merit_points, 
               m.semester, 
               m.academic_year,
               a.status_attd
        FROM merit m
        JOIN event e ON m.event_id = e.event_id
        JOIN attendance a ON a.event_id = e.event_id AND a.user_id = m.user_id
        WHERE m.user_id = ? AND a.status_attd = 'present'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head><title>Merits for Attended Events</title></head>
<body>
    <h2>Merits Earned Through Attendance</h2>
    <table border="1">
        <tr>
            <th>Event</th>
            <th>Points</th>
            <th>Semester</th>
            <th>Year</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['event_name'] ?></td>
            <td><?= $row['merit_points'] ?></td>
            <td><?= $row['semester'] ?></td>
            <td><?= $row['academic_year'] ?></td>
        </tr>
        <?php endwhile; ?>

        <?php if ($result->num_rows === 0): ?>
        <tr><td colspan="4">No merits found for attended events.</td></tr>
        <?php endif; ?>
    </table>
</body>
</html>
