<?php
include '../../Databased/db_connect.php';

// Hardcoded user_id for testing (e.g., student with ID = 1)
$user_id = 1;

// Query to get awarded merits
$sql = "SELECT e.title AS event_name, 
               m.points AS merit_points, 
               m.semester, 
               m.academic_year,
               a.status_attd,
               e.start_date,
               e.end_date,
               e.geographic_location,
               e.event_level
        FROM merit m
        JOIN events e ON m.event_id = e.event_id
        JOIN attendance a ON a.event_id = e.event_id AND a.user_id = m.user_id
        WHERE m.user_id = ? AND a.status_attd = 'present'
        ORDER BY e.start_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total merits
$total_merits = 0;
$merits_data = [];
while ($row = $result->fetch_assoc()) {
    $total_merits += $row['merit_points'];
    $merits_data[] = $row;
}

// Get current semester statistics
$current_year = date('Y');
$current_semester_sql = "SELECT SUM(m.points) as current_semester_points
                        FROM merit m
                        JOIN events e ON m.event_id = e.event_id
                        JOIN attendance a ON a.event_id = e.event_id AND a.user_id = m.user_id
                        WHERE m.user_id = ? AND a.status_attd = 'present' 
                        AND m.academic_year = ?";

$current_stmt = $conn->prepare($current_semester_sql);
$current_stmt->bind_param("is", $user_id, $current_year);
$current_stmt->execute();
$current_result = $current_stmt->get_result();
$current_semester_data = $current_result->fetch_assoc();
$current_semester_points = $current_semester_data['current_semester_points'] ?? 0;
// Include header and sidebar
include '../HADER_SIDER_FOOTER/HST.PHP';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>View Awarded Merits – Student Dashboard</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../../mypetakom/MODULE_4/module_4.css">
</head>
<body>
  
      <!-- Main Content -->
      <div class="main-content">
        <div class="page-inner">
          <h2><i class="fas fa-medal"></i> View Awarded Merits</h2>
          <p><b>Track your earned merits and achievements</b><br>Review all merits awarded through your event participation</p>
          
          <div class="header-actions">
            <button class="btn-info" onclick="window.print()">
              <i class="fas fa-print"></i> Print Report
            </button>
            <button class="btn-primary" onclick="exportToCSV()">
              <i class="fas fa-download"></i> Export CSV
            </button>
          </div>
        </div>

        <!-- Summary Statistics -->
        <div class="summary-stats">
          <div class="stat-card">
            <div class="stat-number"><?= $total_merits ?></div>
            <div class="stat-label">Total Merits Earned</div>
          </div>
          <div class="stat-card" style="background: linear-gradient(135deg, var(--success), #48c78e);">
            <div class="stat-number"><?= $current_semester_points ?></div>
            <div class="stat-label">Current Semester Points</div>
          </div>
          <div class="stat-card" style="background: linear-gradient(135deg, var(--warning), #ffd93d);">
            <div class="stat-number"><?= count($merits_data) ?></div>
            <div class="stat-label">Events Attended</div>
          </div>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
          <h4>
            <i class="fas fa-filter"></i> Filter Merits
          </h4>
          <form method="GET" action="">
            <div class="filter-row">
              <div class="filter-group">
                <label for="academic_year">Academic Year</label>
                <select name="academic_year" id="academic_year">
                  <option value="">All Years</option>
                  <?php
                  $years = array_unique(array_column($merits_data, 'academic_year'));
                  sort($years);
                  foreach ($years as $year) {
                    $selected = (isset($_GET['academic_year']) && $_GET['academic_year'] == $year) ? 'selected' : '';
                    echo "<option value='$year' $selected>$year</option>";
                  }
                  ?>
                </select>
              </div>
              
              <div class="filter-group">
                <label for="semester">Semester</label>
                <select name="semester" id="semester">
                  <option value="">All Semesters</option>
                  <option value="1" <?= (isset($_GET['semester']) && $_GET['semester'] == '1') ? 'selected' : '' ?>>Semester 1</option>
                  <option value="2" <?= (isset($_GET['semester']) && $_GET['semester'] == '2') ? 'selected' : '' ?>>Semester 2</option>
                </select>
              </div>
              
              <div class="filter-group">
                <label for="min_points">Minimum Points</label>
                <input type="number" name="min_points" id="min_points" placeholder="0" 
                       value="<?= isset($_GET['min_points']) ? $_GET['min_points'] : '' ?>">
              </div>
              
              <div class="filter-group" style="justify-content: flex-end;">
                <label>&nbsp;</label>
                <button type="submit" class="btn-filter">
                  <i class="fas fa-search"></i> Apply Filters
                </button>
              </div>
            </div>
          </form>
        </div>

        <!-- Merits Display -->
        <?php if (count($merits_data) > 0): ?>
          <div class="merits-grid">
            <?php 
            // Apply filters if set
            $filtered_data = $merits_data;
            
            if (isset($_GET['academic_year']) && !empty($_GET['academic_year'])) {
              $filtered_data = array_filter($filtered_data, function($merit) {
                return $merit['academic_year'] == $_GET['academic_year'];
              });
            }
            
            if (isset($_GET['semester']) && !empty($_GET['semester'])) {
              $filtered_data = array_filter($filtered_data, function($merit) {
                return $merit['semester'] == $_GET['semester'];
              });
            }
            
            if (isset($_GET['min_points']) && !empty($_GET['min_points'])) {
              $filtered_data = array_filter($filtered_data, function($merit) {
                return $merit['merit_points'] >= $_GET['min_points'];
              });
            }
            
            foreach ($filtered_data as $merit): ?>
              <div class="merit-card" data-merit='<?= json_encode($merit) ?>'>
                <div class="merit-header">
                  <div>
                    <div class="merit-title"><?= htmlspecialchars($merit['event_name']) ?></div>
                    <div class="merit-details">
                      <?php if (!empty($merit['geographic_location'])): ?>
                        <i class="fas fa-map-marker-alt"></i> <?= htmlspecialchars($merit['geographic_location']) ?><br>
                      <?php endif; ?>
                      <?php if (!empty($merit['event_level'])): ?>
                        <i class="fas fa-level-up-alt"></i> <?= htmlspecialchars($merit['event_level']) ?> Level
                      <?php endif; ?>
                    </div>
                  </div>
                  <div class="merit-points">
                    +<?= $merit['merit_points'] ?> pts
                  </div>
                </div>
                
                <div class="merit-meta">
                  <div class="merit-semester">
                    <?= $merit['academic_year'] ?> - Semester <?= $merit['semester'] ?>
                  </div>
                  <?php if (!empty($merit['start_date'])): ?>
                    <div class="merit-date">
                      <i class="fas fa-calendar-alt"></i> 
                      <?= date('M d, Y', strtotime($merit['start_date'])) ?>
                      <?php if (!empty($merit['end_date']) && $merit['start_date'] != $merit['end_date']): ?>
                        - <?= date('M d, Y', strtotime($merit['end_date'])) ?>
                      <?php endif; ?>
                    </div>
                  <?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
          
          <?php if (count($filtered_data) == 0 && (isset($_GET['academic_year']) || isset($_GET['semester']) || isset($_GET['min_points']))): ?>
            <div class="empty-state">
              <i class="fas fa-search"></i>
              <h3>No merits found</h3>
              <p>Try adjusting your filter criteria to see more results.</p>
            </div>
          <?php endif; ?>
          
        <?php else: ?>
          <div class="empty-state">
            <i class="fas fa-medal"></i>
            <h3>No Awarded Merits Yet</h3>
            <p>You haven't earned any merits through event attendance yet.<br>
               Start participating in events to earn merit points!</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <footer class="footer">
      <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
    </footer>
  </div>

  <!-- Pass PHP data to JavaScript -->
  <script>
    // Make PHP data available to JavaScript
    window.meritData = <?= json_encode($merits_data) ?>;
    window.totalMerits = <?= $total_merits ?>;
    window.currentYearPoints = <?= $current_semester_points ?>;
  </script>
  
  <!-- External JavaScript file -->
  <script src="./view_awarded.js"></script>
</body>
</html>

$current_stmt->close();
$conn->close();