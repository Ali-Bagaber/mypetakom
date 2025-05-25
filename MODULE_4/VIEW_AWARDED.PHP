<?php
include '../../Databased/db_connect.php';

// Hardcoded user_id for testing (e.g., student with ID = 1)
$user_id = 1;

// Get filter parameters first
$selected_year        = $_GET['academic_year']   ?? '';
$selected_semester    = $_GET['semester']        ?? '';
$min_points           = $_GET['min_points']      ?? '';
$selected_event_level = $_GET['event_level']     ?? '';
$search               = trim($_GET['search']     ?? '');

// Base SQL query for filtered merits
$sql = "SELECT 
          e.event_id,
          e.title            AS event_name,
          m.points           AS merit_points,
          m.semester,
          m.academic_year,
          a.status_attd,
          e.start_date,
          e.end_date,
          e.geographic_location,
          e.event_level
        FROM merit m
        JOIN events e 
          ON m.event_id = e.event_id
        JOIN attendance a 
          ON a.event_id = e.event_id 
         AND a.user_id  = m.user_id
       WHERE m.user_id = ? 
         AND a.status_attd = 'present'";

$params     = [$user_id];
$paramTypes = "i";

// Add filters dynamically
if ($selected_year !== '') {
    $sql        .= " AND m.academic_year = ?";
    $params[]    = $selected_year;
    $paramTypes .= "s";
}
if ($selected_semester !== '') {
    $sql        .= " AND m.semester = ?";
    $params[]    = $selected_semester;
    $paramTypes .= "s";
}
if ($min_points !== '') {
    $sql        .= " AND m.points >= ?";
    $params[]    = intval($min_points);
    $paramTypes .= "i";
}
if ($selected_event_level !== '') {
    $sql        .= " AND e.event_level = ?";
    $params[]    = $selected_event_level;
    $paramTypes .= "s";
}
// ─────── NEW: SEARCH BY TITLE ───────
if ($search !== '') {
    $sql        .= " AND e.title LIKE ?";
    $params[]    = "%{$search}%";
    $paramTypes .= "s";
}

$sql .= " ORDER BY e.start_date DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param($paramTypes, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Collect and sum
$total_merits = 0;
$merits_data  = [];
while ($row = $result->fetch_assoc()) {
    $total_merits += $row['merit_points'];
    $merits_data[] = $row;
}

// Current year points
$current_year = date('Y');
$current_sql  = "SELECT SUM(m.points) AS current_semester_points
                 FROM merit m
                 JOIN events e 
                   ON m.event_id = e.event_id
                 JOIN attendance a 
                   ON a.event_id = e.event_id 
                  AND a.user_id  = m.user_id
                WHERE m.user_id      = ?
                  AND a.status_attd  = 'present'
                  AND m.academic_year = ?";
$current_stmt = $conn->prepare($current_sql);
$current_stmt->bind_param("is", $user_id, $current_year);
$current_stmt->execute();
$current_res = $current_stmt->get_result()->fetch_assoc();
$current_semester_points = $current_res['current_semester_points'] ?? 0;

// Load all years & levels for filter dropdowns
$all_sql = "SELECT DISTINCT m.academic_year, e.event_level
            FROM merit m
            JOIN events e 
              ON m.event_id = e.event_id
            JOIN attendance a 
              ON a.event_id = e.event_id 
             AND a.user_id  = m.user_id
           WHERE m.user_id      = ?
             AND a.status_attd  = 'present'
           ORDER BY m.academic_year DESC";
$all_stmt = $conn->prepare($all_sql);
$all_stmt->bind_param("i", $user_id);
$all_stmt->execute();
$all_rs = $all_stmt->get_result();

$all_years = [];
$all_event_levels = [];
while ($r = $all_rs->fetch_assoc()) {
    if (!in_array($r['academic_year'], $all_years)) {
        $all_years[] = $r['academic_year'];
    }
    if ($r['event_level'] && !in_array($r['event_level'], $all_event_levels)) {
        $all_event_levels[] = $r['event_level'];
    }
}
sort($all_event_levels);

// Include your header/sidebar and footer
include '../HADER_SIDER_FOOTER/HST.PHP';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <title>View Awarded Merits – Student Dashboard</title>
  <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="../CSS/MODULE_4_css/VIEW_AWARDED.css">
</head>
<body>

  <div class="main-content">
    <div class="page-inner">
      <h2><i class="fas fa-medal"></i> View Awarded Merits</h2>
      <p><b>Track your earned merits and achievements</b><br>
         Review all merits awarded through your event participation</p>
      <div class="header-actions">
        <button class="btn-info" onclick="window.print()">
          <i class="fas fa-print"></i> Print Report
        </button>
        <button class="btn-primary" onclick="exportToCSV()">
          <i class="fas fa-download"></i> Export CSV
        </button>
      </div>
    </div>

    <!-- SUMMARY STATS -->
    <div class="summary-stats">
      <div class="stat-card">
        <div class="stat-number"><?= $total_merits ?></div>
        <div class="stat-label">Total Merits Earned</div>
      </div>
      <div class="stat-card" style="background: green">
        <div class="stat-number"><?= $current_semester_points ?></div>
        <div class="stat-label">Current Year Points</div>
      </div>
      <div class="stat-card" style="background: #ffd93d">
        <div class="stat-number"><?= count($merits_data) ?></div>
        <div class="stat-label">Events Attended</div>
      </div>
    </div>

    <!-- FILTERS -->
    <div class="filter-section">
      <h4><i class="fas fa-filter"></i> Filter Merits</h4>
      <form method="GET" action="">
        <div class="filter-row"> 
          <!-- Academic Year -->
          <div class="filter-group">
            <label for="academic_year">Academic Year</label>
            <select name="academic_year" id="academic_year">
              <option value="">All Years</option>
              <?php foreach ($all_years as $yr): ?>
              <option value="<?= $yr ?>" <?= $selected_year == $yr ? 'selected':'' ?>>
                <?= $yr ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Semester -->
          <div class="filter-group">
            <label for="semester">Semester</label>
            <select name="semester" id="semester">
              <option value="">All Semesters</option>
              <option value="1" <?= $selected_semester=='1'?'selected':'' ?>>Semester 1</option>
              <option value="2" <?= $selected_semester=='2'?'selected':'' ?>>Semester 2</option>
            </select>
          </div>
          <!-- Event Level -->
          <div class="filter-group">
            <label for="event_level">Event Level</label>
            <select name="event_level" id="event_level">
              <option value="">All Levels</option>
              <?php foreach ($all_event_levels as $lvl): ?>
              <option value="<?= htmlspecialchars($lvl) ?>"
                <?= $selected_event_level==$lvl?'selected':'' ?>>
                <?= htmlspecialchars($lvl) ?>
              </option>
              <?php endforeach; ?>
            </select>
          </div>
          <!-- Minimum Points -->
          <div class="filter-group">
            <label for="min_points">Minimum Points</label>
            <input type="number" name="min_points" id="min_points"
                   placeholder="0" value="<?= htmlspecialchars($min_points) ?>">
          </div>
          <!-- Search Box -->
          <div class="filter-group" style="flex:1; min-width:200px;">
            <label for="search">Search Events</label>
            <input type="text" name="search" id="search"
                   placeholder="e.g. Hackathon"
                   value="<?= htmlspecialchars($search) ?>">
          </div>
          <!-- Apply -->
          <div class="filter-group" style="justify-content:flex-end">
            <label>&nbsp;</label>
            <button type="submit" class="btn-filter">
              <i class="fas fa-search"></i> Apply Filters
            </button>
          </div>
        </div>
      </form>
    </div>

    <!-- MERITS GRID -->
    <?php if (count($merits_data) > 0): ?>
      <div class="merits-grid">
        <?php foreach ($merits_data as $merit): ?>
          <div class="merit-card" data-merit='<?= htmlspecialchars(json_encode($merit)) ?>'>
            <div class="merit-header">
              <div class="merit-title"><?= htmlspecialchars($merit['event_name']) ?></div>
              <div class="merit-points">+<?= $merit['merit_points'] ?> pts</div>
            </div>
            <div class="merit-meta">
              <div class="merit-semester">
                <?= htmlspecialchars($merit['academic_year']) ?> – Semester <?= htmlspecialchars($merit['semester']) ?>
              </div>
              <?php if ($merit['start_date']): ?>
                <div class="merit-date">
                  <i class="fas fa-calendar-alt"></i>
                  <?= date('M d, Y', strtotime($merit['start_date'])) ?>
                  <?php if ($merit['end_date'] && $merit['start_date'] != $merit['end_date']): ?>
                    – <?= date('M d, Y', strtotime($merit['end_date'])) ?>
                  <?php endif; ?>
                </div>
              <?php endif; ?>
            </div>
            <div class="merit-actions">
              <a href="merit_details.php?event_id=<?= htmlspecialchars($merit['event_id']) ?>"
                 class="btn-details">
                <i class="fas fa-info-circle"></i> View Details
              </a>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php else: ?>
      <div class="empty-state">
        <i class="fas fa-search"></i>
        <h3>No merits found</h3>
        <p>
          <?php if ($selected_year||$selected_semester||$min_points||$selected_event_level||$search): ?>
            Try adjusting your filter or search terms.
          <?php else: ?>
            You haven't earned any merits yet—participate in events to start earning!
          <?php endif; ?>
        </p>
      </div>
    <?php endif; ?>
  </div>

  <footer class="footer">
    <p>&copy; 2025 MyPetakom System. All rights reserved. | UMP Student Dashboard</p>
  </footer>

  <script>
    // expose data & export
    window.meritData        = <?= json_encode($merits_data) ?>;
    window.totalMerits      = <?= $total_merits ?>;
    window.currentYearPoints= <?= $current_semester_points ?>;

    function exportToCSV() {
      const rows = [
        ['Event Name','Merit Points','Year','Sem','Level','Location','Start','End'],
        ...window.meritData.map(m => [
          m.event_name,
          m.merit_points,
          m.academic_year,
          m.semester,
          m.event_level,
          m.geographic_location,
          m.start_date,
          m.end_date
        ])
      ];
      const csv = rows.map(r => r.map(f => `"${f}"`).join(',')).join('\n');
      const blob = new Blob([csv], {type:'text/csv'});
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `awarded_merits_${new Date().toISOString().slice(0,10)}.csv`;
      document.body.appendChild(a);
      a.click();
      a.remove();
      URL.revokeObjectURL(url);
    }
  </script>

</body>
</html>

<?php
// close statements & connection
$stmt->close();
$current_stmt->close();
$all_stmt->close();
$conn->close();
?>
