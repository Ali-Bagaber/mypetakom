<?php
session_start();
include '../../Databased/db_connect.php';

// *** for quick testing only ***
$_SESSION['user_id']   = 3;
$_SESSION['user_role'] = 'advisor';


// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    
    if (isset($_POST['action'])) {
        switch ($_POST['action']) {
            case 'approve_claim':
                echo json_encode(handleClaimAction($conn, $_POST['application_id'], 'approved', $advisor_user_id));
                break;
            case 'reject_claim':
                echo json_encode(handleClaimAction($conn, $_POST['application_id'], 'rejected', $advisor_user_id));
                break;
            case 'get_claim_details':
                echo json_encode(getClaimDetails($conn, $_POST['application_id']));
                break;
            case 'bulk_approve':
                echo json_encode(handleBulkAction($conn, $_POST['application_ids'], 'approved', $advisor_user_id));
                break;
            case 'bulk_reject':
                echo json_encode(handleBulkAction($conn, $_POST['application_ids'], 'rejected', $advisor_user_id));
                break;
        }
    }
    exit();
}

function handleClaimAction($conn, $application_id, $status, $advisor_user_id) {
    try {
        // Start transaction
        $conn->autocommit(false);
        
        // Verify claim exists and is pending
        $verify_sql = "SELECT ma.*, e.title as event_name, s.student_name, e.event_level
                       FROM meritapplication ma
                       JOIN events e ON ma.event_id = e.event_id
                       JOIN student s ON ma.user_id = s.user_id
                       WHERE ma.application_id = ? AND ma.claim_status = 'pending'";
        
        $verify_stmt = $conn->prepare($verify_sql);
        $verify_stmt->bind_param("i", $application_id);
        $verify_stmt->execute();
        $result = $verify_stmt->get_result();
        
        if ($result->num_rows === 0) {
            throw new Exception("Claim not found or already processed");
        }
        
        $claim_data = $result->fetch_assoc();
        
        // Update claim status
        $update_sql = "UPDATE meritapplication SET claim_status = ? WHERE application_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("si", $status, $application_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update claim status");
        }
        
        // If approved, create merit record
        if ($status === 'approved') {
            // Get merit points based on event level
            $points = calculateMeritPoints($claim_data['event_level']);
            
            // Get current semester and academic year
            $current_semester = getCurrentSemester();
            $current_year = date('Y');
            
            $merit_sql = "INSERT INTO merit (event_id, user_id, points, semester, academic_year) 
                         VALUES (?, ?, ?, ?, ?)";
            $merit_stmt = $conn->prepare($merit_sql);
            $merit_stmt->bind_param("iiisi", 
                $claim_data['event_id'], 
                $claim_data['user_id'], 
                $points, 
                $current_semester, 
                $current_year
            );
            
            if (!$merit_stmt->execute()) {
                throw new Exception("Failed to create merit record");
            }
        }
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => ucfirst($status) . ' successfully!',
            'student_name' => $claim_data['student_name'],
            'event_name' => $claim_data['event_name']
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    } finally {
        $conn->autocommit(true);
    }
}

function handleBulkAction($conn, $application_ids, $status, $advisor_user_id) {
    try {
        $conn->autocommit(false);
        $success_count = 0;
        $total_count = count($application_ids);
        
        foreach ($application_ids as $app_id) {
            $result = handleClaimAction($conn, $app_id, $status, $advisor_user_id);
            if ($result['success']) {
                $success_count++;
            }
        }
        
        $conn->commit();
        
        return [
            'success' => true,
            'message' => "$success_count out of $total_count claims " . $status . " successfully"
        ];
        
    } catch (Exception $e) {
        $conn->rollback();
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    } finally {
        $conn->autocommit(true);
    }
}

function getClaimDetails($conn, $application_id) {
    try {
        $sql = "SELECT ma.*, e.title as event_name, e.description, e.start_date, e.end_date,
                       e.geographic_location, e.event_level, s.student_name, s.student_id_card,
                       s.program, s.faculty, u.email
                FROM meritapplication ma
                JOIN events e ON ma.event_id = e.event_id
                JOIN student s ON ma.user_id = s.user_id
                JOIN users u ON ma.user_id = u.user_id
                WHERE ma.application_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $application_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            return [
                'success' => true,
                'data' => $result->fetch_assoc()
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Claim not found'
            ];
        }
        
    } catch (Exception $e) {
        return [
            'success' => false,
            'message' => $e->getMessage()
        ];
    }
}

function calculateMeritPoints($event_level) {
    $points_map = [
        'International' => 5,
        'National' => 4,
        'State' => 3,
        'University' => 2,
        'Faculty' => 1
    ];
    
    return isset($points_map[$event_level]) ? $points_map[$event_level] : 1;
}

function getCurrentSemester() {
    $month = date('n');
    if ($month >= 2 && $month <= 6) {
        return 'Semester 2';
    } elseif ($month >= 9 && $month <= 12) {
        return 'Semester 1';
    } else {
        return 'Special Semester';
    }
}

// Get all pending claims with pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Search and filter parameters
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'pending';
$level_filter = isset($_GET['level']) ? $_GET['level'] : '';

// Build where clause
$where_conditions = [];
$params = [];
$types = '';

if (!empty($search)) {
    $where_conditions[] = "(s.student_name LIKE ? OR e.title LIKE ? OR s.student_id_card LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param]);
    $types .= 'sss';
}

if ($status_filter !== 'all') {
    $where_conditions[] = "ma.claim_status = ?";
    $params[] = $status_filter;
    $types .= 's';
}

if (!empty($level_filter)) {
    $where_conditions[] = "e.event_level = ?";
    $params[] = $level_filter;
    $types .= 's';
}

$where_clause = '';
if (!empty($where_conditions)) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_conditions);
}

// Get claims with pagination
try {
    $claims_sql = "SELECT ma.*, e.title as event_name, e.start_date, e.end_date,
                          e.geographic_location, e.event_level, s.student_name, s.student_id_card,
                          s.program, s.faculty
                   FROM meritapplication ma
                   JOIN events e ON ma.event_id = e.event_id
                   JOIN student s ON ma.user_id = s.user_id
                   $where_clause
                   ORDER BY ma.submission_date DESC
                   LIMIT ? OFFSET ?";
    
    $claims_stmt = $conn->prepare($claims_sql);
    $params[] = $per_page;
    $params[] = $offset;
    $types .= 'ii';
    
    if (!empty($params)) {
        $claims_stmt->bind_param($types, ...$params);
    }
    
    $claims_stmt->execute();
    $claims_result = $claims_stmt->get_result();
    $claims = [];
    while ($row = $claims_result->fetch_assoc()) {
        $claims[] = $row;
    }
    
    // Get total count for pagination
    $count_sql = "SELECT COUNT(*) as total
                  FROM meritapplication ma
                  JOIN events e ON ma.event_id = e.event_id
                  JOIN student s ON ma.user_id = s.user_id
                  $where_clause";
    
    $count_stmt = $conn->prepare($count_sql);
    if (!empty($params) && count($params) > 2) {
        // Remove limit and offset parameters for count
        $count_params = array_slice($params, 0, -2);
        $count_types = substr($types, 0, -2);
        $count_stmt->bind_param($count_types, ...$count_params);
    }
    
    $count_stmt->execute();
    $total_claims = $count_stmt->get_result()->fetch_assoc()['total'];
    $total_pages = ceil($total_claims / $per_page);
    
} catch (Exception $e) {
    $claims = [];
    $total_claims = 0;
    $total_pages = 0;
    $error_message = "Error loading claims: " . $e->getMessage();
}

// Get statistics
try {
    $stats_sql = "SELECT 
                    COUNT(CASE WHEN ma.claim_status = 'pending' THEN 1 END) as pending_claims,
                    COUNT(CASE WHEN ma.claim_status = 'approved' THEN 1 END) as approved_claims,
                    COUNT(CASE WHEN ma.claim_status = 'rejected' THEN 1 END) as rejected_claims,
                    COUNT(*) as total_claims
                  FROM meritapplication ma
                  JOIN events e ON ma.event_id = e.event_id";
    
    $stats_result = $conn->query($stats_sql);
    $stats = $stats_result->fetch_assoc();
} catch (Exception $e) {
    $stats = ['pending_claims' => 0, 'approved_claims' => 0, 'rejected_claims' => 0, 'total_claims' => 0];
}

$page_title = "Manage Merit Claims";
include '../HADER_SIDER_FOOTER/HST.PHP';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Advisor - Manage Merit Claims</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../CSS/MODULE_4_css/manage_claim_advisor.css">
</head>
<body>
    <div class="main-content">
        <div class="page-inner">
            <h2><i class="fas fa-tasks"></i> Merit Claims Management</h2>
            <p><b>Review and approve student merit claim applications</b><br>Manage pending claims and view approval history</p>
            
            <div id="alert-container"></div>
        </div>

        <!-- Statistics Cards -->
        <div class="summary-stats">
            <div class="stat-card">
                <div class="stat-number"><?= $stats['total_claims'] ?></div>
                <div class="stat-label">Total Claims</div>
            </div>
            <div class="stat-card pending">
                <div class="stat-number"><?= $stats['pending_claims'] ?></div>
                <div class="stat-label">Pending Review</div>
            </div>
            <div class="stat-card approved">
                <div class="stat-number"><?= $stats['approved_claims'] ?></div>
                <div class="stat-label">Approved</div>
            </div>
            <div class="stat-card rejected">
                <div class="stat-number"><?= $stats['rejected_claims'] ?></div>
                <div class="stat-label">Rejected</div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="filters-section">
            <div class="search-bar">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Search by student name, event, or student ID..." value="<?= htmlspecialchars($search) ?>">
            </div>
            
            <div class="filter-controls">
                <select id="statusFilter">
                    <option value="all" <?= $status_filter === 'all' ? 'selected' : '' ?>>All Status</option>
                    <option value="pending" <?= $status_filter === 'pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="approved" <?= $status_filter === 'approved' ? 'selected' : '' ?>>Approved</option>
                    <option value="rejected" <?= $status_filter === 'rejected' ? 'selected' : '' ?>>Rejected</option>
                </select>
                
                <select id="levelFilter">
                    <option value="">All Levels</option>
                    <option value="International" <?= $level_filter === 'International' ? 'selected' : '' ?>>International</option>
                    <option value="National" <?= $level_filter === 'National' ? 'selected' : '' ?>>National</option>
                    <option value="State" <?= $level_filter === 'State' ? 'selected' : '' ?>>State</option>
                    <option value="University" <?= $level_filter === 'University' ? 'selected' : '' ?>>University</option>
                    <option value="Faculty" <?= $level_filter === 'Faculty' ? 'selected' : '' ?>>Faculty</option>
                </select>
                
                <button type="button" id="applyFilters" class="btn-filter">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
            </div>
        </div>

        <!-- Bulk Actions -->
        <div class="bulk-actions" id="bulkActions" style="display: none;">
            <div class="bulk-info">
                <span id="selectedCount">0</span> claims selected
            </div>
            <div class="bulk-buttons">
                <button type="button" id="bulkApprove" class="btn-bulk-approve">
                    <i class="fas fa-check"></i> Bulk Approve
                </button>
                <button type="button" id="bulkReject" class="btn-bulk-reject">
                    <i class="fas fa-times"></i> Bulk Reject
                </button>
                <button type="button" id="clearSelection" class="btn-clear">
                    <i class="fas fa-times-circle"></i> Clear Selection
                </button>
            </div>
        </div>

        <!-- Claims Table -->
        <div class="table-container">
            <?php if (count($claims) > 0): ?>
                <table class="claims-table">
                    <thead>
                        <tr>
                            <th>
                                <input type="checkbox" id="selectAll">
                            </th>
                            <th>Student</th>
                            <th>Event</th>
                            <th>Level</th>
                            <th>Submitted</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($claims as $claim): ?>
                            <tr data-claim-id="<?= $claim['application_id'] ?>">
                                <td>
                                    <input type="checkbox" class="claim-checkbox" value="<?= $claim['application_id'] ?>">
                                </td>
                                <td>
                                    <div class="student-info">
                                        <div class="student-name"><?= htmlspecialchars($claim['student_name']) ?></div>
                                        <div class="student-details">
                                            <?= htmlspecialchars($claim['student_id_card']) ?> | <?= htmlspecialchars($claim['program']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="event-info">
                                        <div class="event-name"><?= htmlspecialchars($claim['event_name']) ?></div>
                                        <div class="event-date">
                                            <?= date('M d, Y', strtotime($claim['start_date'])) ?>
                                            <?php if ($claim['start_date'] != $claim['end_date']): ?>
                                                - <?= date('M d, Y', strtotime($claim['end_date'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="level-badge level-<?= strtolower($claim['event_level']) ?>">
                                        <?= htmlspecialchars($claim['event_level']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($claim['submission_date'])) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $claim['claim_status'] ?>">
                                        <?php
                                        $status_icons = [
                                            'pending' => 'fas fa-clock',
                                            'approved' => 'fas fa-check-circle',
                                            'rejected' => 'fas fa-times-circle'
                                        ];
                                        ?>
                                        <i class="<?= $status_icons[$claim['claim_status']] ?>"></i>
                                        <?= ucfirst($claim['claim_status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn-view" onclick="viewClaimDetails(<?= $claim['application_id'] ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if ($claim['claim_status'] === 'pending'): ?>
                                            <button type="button" class="btn-approve" onclick="approveClaim(<?= $claim['application_id'] ?>)">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn-reject" onclick="rejectClaim(<?= $claim['application_id'] ?>)">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                
                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                    <div class="pagination">
                        <div class="pagination-info">
                            Showing <?= ($offset + 1) ?> to <?= min($offset + $per_page, $total_claims) ?> of <?= $total_claims ?> claims
                        </div>
                        <div class="pagination-buttons">
                            <?php if ($page > 1): ?>
                                <a href="?page=<?= $page - 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&level=<?= urlencode($level_filter) ?>" class="btn-page">
                                    <i class="fas fa-chevron-left"></i> Previous
                                </a>
                            <?php endif; ?>
                            
                            <?php for ($i = max(1, $page - 2); $i <= min($total_pages, $page + 2); $i++): ?>
                                <a href="?page=<?= $i ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&level=<?= urlencode($level_filter) ?>" 
                                   class="btn-page <?= $i === $page ? 'active' : '' ?>">
                                    <?= $i ?>
                                </a>
                            <?php endfor; ?>
                            
                            <?php if ($page < $total_pages): ?>
                                <a href="?page=<?= $page + 1 ?>&search=<?= urlencode($search) ?>&status=<?= urlencode($status_filter) ?>&level=<?= urlencode($level_filter) ?>" class="btn-page">
                                    Next <i class="fas fa-chevron-right"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endif; ?>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-clipboard-list"></i>
                    <h3>No Claims Found</h3>
                    <p>No merit claims match your current filters.<br>Try adjusting your search criteria.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Claim Details Modal -->
    <div id="claimDetailsModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h3><i class="fas fa-info-circle"></i> Claim Details</h3>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body" id="claimDetailsContent">
                <!-- Content will be loaded here -->
            </div>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 MyPetakom System. All rights reserved. | Advisor Dashboard</p>
    </footer>

    <script>
        // Merit Claims Management JavaScript
        let selectedClaims = new Set();

        document.addEventListener('DOMContentLoaded', function() {
            initializeEventHandlers();
            initializeModals();
            updateBulkActionsVisibility();
        });

        function initializeEventHandlers() {
            // Search and filter handlers
            document.getElementById('applyFilters').addEventListener('click', applyFilters);
            document.getElementById('searchInput').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    applyFilters();
                }
            });

            // Bulk action handlers
            document.getElementById('selectAll').addEventListener('change', handleSelectAll);
            document.getElementById('bulkApprove').addEventListener('click', handleBulkApprove);
            document.getElementById('bulkReject').addEventListener('click', handleBulkReject);
            document.getElementById('clearSelection').addEventListener('click', clearSelection);

            // Individual checkbox handlers
            document.querySelectorAll('.claim-checkbox').forEach(checkbox => {
                checkbox.addEventListener('change', handleIndividualCheckbox);
            });
        }

        function initializeModals() {
            const modal = document.getElementById('claimDetailsModal');
            const closeBtn = modal.querySelector('.close');
            
            closeBtn.addEventListener('click', () => closeModal('claimDetailsModal'));
            
            modal.addEventListener('click', function(e) {
                if (e.target === this) {
                    closeModal('claimDetailsModal');
                }
            });
            
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && modal.style.display === 'block') {
                    closeModal('claimDetailsModal');
                }
            });
        }

        function applyFilters() {
            const search = document.getElementById('searchInput').value;
            const status = document.getElementById('statusFilter').value;
            const level = document.getElementById('levelFilter').value;
            
            const params = new URLSearchParams();
            if (search) params.set('search', search);
            if (status !== 'all') params.set('status', status);
            if (level) params.set('level', level);
            params.set('page', '1');
            
            window.location.href = '?' + params.toString();
        }

        function handleSelectAll() {
            const selectAll = document.getElementById('selectAll');
            const checkboxes = document.querySelectorAll('.claim-checkbox');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = selectAll.checked;
                if (selectAll.checked) {
                    selectedClaims.add(checkbox.value);
                } else {
                    selectedClaims.delete(checkbox.value);
                }
            });
            
            updateBulkActionsVisibility();
        }

        function handleIndividualCheckbox() {
            if (this.checked) {
                selectedClaims.add(this.value);
            } else {
                selectedClaims.delete(this.value);
            }
            
            // Update select all checkbox
            const totalCheckboxes = document.querySelectorAll('.claim-checkbox').length;
            const selectAllCheckbox = document.getElementById('selectAll');
            selectAllCheckbox.checked = selectedClaims.size === totalCheckboxes;
            selectAllCheckbox.indeterminate = selectedClaims.size > 0 && selectedClaims.size < totalCheckboxes;
            
            updateBulkActionsVisibility();
        }

        function updateBulkActionsVisibility() {
            const bulkActions = document.getElementById('bulkActions');
            const selectedCount = document.getElementById('selectedCount');
            
            if (selectedClaims.size > 0) {
                bulkActions.style.display = 'flex';
                selectedCount.textContent = selectedClaims.size;
            } else {
                bulkActions.style.display = 'none';
            }
        }

        function clearSelection() {
            selectedClaims.clear();
            document.querySelectorAll('.claim-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            document.getElementById('selectAll').checked = false;
            document.getElementById('selectAll').indeterminate = false;
            updateBulkActionsVisibility();
        }

        function handleBulkApprove() {
            if (selectedClaims.size === 0) return;
            
            const message = `Are you sure you want to approve ${selectedClaims.size} selected claims?`;
            if (confirm(message)) {
                performBulkAction('bulk_approve', Array.from(selectedClaims));
            }
        }

        function handleBulkReject() {
            if (selectedClaims.size === 0) return;
            
            const message = `Are you sure you want to reject ${selectedClaims.size} selected claims?`;
            if (confirm(message)) {
                performBulkAction('bulk_reject', Array.from(selectedClaims));
            }
        }

        function performBulkAction(action, applicationIds) {
            showLoading();
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    ajax: 1,
                    action: action,
                    application_ids: JSON.stringify(applicationIds)
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert('success', data.message);
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('error', 'An error occurred while processing the request');
                console.error('Error:', error);
            });
        }

        function approveClaim(applicationId) {
            if (confirm('Are you sure you want to approve this claim?')) {
                performClaimAction('approve_claim', applicationId);
            }
        }

        function rejectClaim(applicationId) {
            if (confirm('Are you sure you want to reject this claim?')) {
                performClaimAction('reject_claim', applicationId);
            }
        }

        function performClaimAction(action, applicationId) {
            showLoading();
            
            fetch(window.location.href, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    ajax: 1,
                    action: action,
                    application_id: applicationId
                })
            })
            .then(response => response.json())
            .then(data => {
                hideLoading();
                if (data.success) {
                    showAlert('success', data.message);
                    // Remove the row from table or update its status
                    updateClaimRow(applicationId, action.includes('approve') ? 'approved' : 'rejected');
                } else {
                    showAlert('error', data.message);
                }
            })
            .catch(error => {
                hideLoading();
                showAlert('error', 'An error occurred while processing the request');
                console.error('Error:', error);
            });
        }

          function updateClaimRow(applicationId, newStatus) {
        const row = document.querySelector(`tr[data-claim-id="${applicationId}"]`);
        if (!row) return;
        // Update status badge
        const badge = row.querySelector('.status-badge');
        badge.className = `status-badge status-${newStatus}`;
        const icons = {
            approved: 'fas fa-check-circle',
            rejected: 'fas fa-times-circle',
            pending: 'fas fa-clock'
        };
        badge.innerHTML = `<i class="${icons[newStatus]}"></i> ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
        // Remove approve/reject buttons if they exist
        row.querySelectorAll('.btn-approve, .btn-reject').forEach(btn => btn.remove());
    }

    // === View Details via AJAX ===
    function viewClaimDetails(applicationId) {
        showLoading();
        fetch(window.location.href, {
            method: 'POST',
            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
            body: new URLSearchParams({
                ajax: 1,
                action: 'get_claim_details',
                application_id: applicationId
            })
        })
        .then(r => r.json())
        .then(data => {
            hideLoading();
            if (data.success) {
                const c = data.data;
                document.getElementById('claimDetailsContent').innerHTML = `
                    <p><strong>Student:</strong> ${c.student_name} (${c.student_id_card})</p>
                    <p><strong>Email:</strong> ${c.email}</p>
                    <p><strong>Program:</strong> ${c.program}</p>
                    <p><strong>Faculty:</strong> ${c.faculty}</p>
                    <hr>
                    <p><strong>Event:</strong> ${c.event_name}</p>
                    <p><strong>Level:</strong> ${c.event_level}</p>
                    <p><strong>Location:</strong> ${c.geographic_location}</p>
                    <p><strong>Dates:</strong>
                        ${new Date(c.start_date).toLocaleDateString()}
                        ${c.end_date && c.end_date !== c.start_date
                            ? ' – ' + new Date(c.end_date).toLocaleDateString()
                            : ''
                        }
                    </p>
                    <p><strong>Description:</strong> ${c.description}</p>
                    <hr>
                    <p><strong>Submitted:</strong> ${new Date(c.submission_date).toLocaleDateString()}</p>
                    <p><strong>Status:</strong> ${c.claim_status.charAt(0).toUpperCase() + c.claim_status.slice(1)}</p>
                    <p><strong>Document:</strong>
                        <a href="../../uploads/merit_claims/${c.supporting_document}" target="_blank">
                          View File
                        </a>
                    </p>
                `;
                openModal('claimDetailsModal');
            } else {
                showAlert('error', data.message);
            }
        })
        .catch(() => {
            hideLoading();
            showAlert('error', 'Failed to load claim details.');
        });
    }

    // === Modal Helpers ===
    function openModal(id) {
        document.getElementById(id).style.display = 'block';
    }
    function closeModal(id) {
        document.getElementById(id).style.display = 'none';
    }

    // === Global Loading Overlay ===
    function showLoading() {
        let ov = document.getElementById('loadingOverlay');
        if (!ov) {
            ov = document.createElement('div');
            ov.id = 'loadingOverlay';
            ov.innerHTML = '<div class="spinner"></div>';
            document.body.appendChild(ov);
        }
        ov.style.display = 'flex';
    }
    function hideLoading() {
        const ov = document.getElementById('loadingOverlay');
        if (ov) ov.style.display = 'none';
    }

    // === Alert Messages ===
    function showAlert(type, message) {
        const container = document.getElementById('alert-container');
        const div = document.createElement('div');
        div.className = `alert alert-${type}`;
        div.textContent = message;
        container.appendChild(div);
        setTimeout(() => div.remove(), 5000);
    }

    // === Close modal on X or Escape ===
    document.querySelectorAll('.modal .close').forEach(btn => {
        btn.addEventListener('click', e => {
            closeModal(e.target.closest('.modal').id);
        });
    });
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') {
            document.querySelectorAll('.modal').forEach(m => {
                if (m.style.display === 'block') closeModal(m.id);
            });
        }
    });
  </script>
</body>
</html>