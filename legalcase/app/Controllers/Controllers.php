<?php

// ═══════════════════════════════════════════
// DASHBOARD CONTROLLER
// ═══════════════════════════════════════════
class DashboardController
{
    public static function index(): void
    {
        $statusCounts  = CaseModel::countByStatus();
        $totalCases    = array_sum($statusCounts);
        $activeCases   = ($statusCounts['in_progress'] ?? 0) + ($statusCounts['hearing_scheduled'] ?? 0) + ($statusCounts['filed'] ?? 0) + ($statusCounts['under_investigation'] ?? 0);
        $closedCases   = $statusCounts['closed'] ?? 0;
        $totalClients  = ClientModel::count();
        $upcomingHearings = HearingModel::upcoming(5);
        $recentCases   = CaseModel::allWithRelations(5, 0);
        $notifications = NotificationModel::getForUser(auth()['id'], 5);

        view('dashboard.index', compact('statusCounts', 'totalCases', 'activeCases', 'closedCases', 'totalClients', 'upcomingHearings', 'recentCases', 'notifications'));
    }
}

// ═══════════════════════════════════════════
// CASE CONTROLLER
// ═══════════════════════════════════════════
class CaseController
{
    public static function index(): void
    {
        $perPage  = 15;
        $page     = max(1, (int)get('page', '1'));
        $filters  = ['status' => get('status'), 'type' => get('type'), 'search' => get('search')];
        if (hasRole('lawyer')) $filters['lawyer_id'] = auth()['id'];
        $total    = CaseModel::countFiltered($filters);
        $pager    = paginate($total, $perPage, $page);
        $cases    = CaseModel::allWithRelations($perPage, $pager['offset'], $filters);
        view('cases.index', compact('cases', 'pager', 'filters'));
    }

    public static function create(): void
    {
        $lawyers = UserModel::getLawyers();
        $clients = ClientModel::all('full_name ASC');
        view('cases.create', compact('lawyers', 'clients'));
    }

    public static function store(): void
    {
        if (!verify_csrf()) {
            redirect('/cases');
        }
        $data = [
            'case_number'  => generateCaseNumber(),
            'title'        => input('title'),
            'case_type'    => input('case_type'),
            'description'  => input('description'),
            'lawyer_id'    => input('lawyer_id'),
            'client_id'    => input('client_id'),
            'court_name'   => input('court_name'),
            'judge_name'   => input('judge_name'),
            'filing_date'  => input('filing_date') ?: date('Y-m-d'),
            'created_by'   => auth()['id'],
        ];
        if (!$data['title'] || !$data['case_type']) {
            flash('error', 'Title and case type are required.', 'error');
            redirect('/cases/create');
        }
        $id = CaseModel::create($data);
        auditLog('case_created', 'cases', $id, "Case {$data['case_number']} created");
        // Notify admins and lawyers
        notifyCaseUpdate(
            $id,
            $data['case_number'],
            'New Case Registered',
            "Case {$data['case_number']}: {$data['title']} has been registered.",
            'case_update'
        );
        // Notify assigned lawyer specifically
        if (!empty($data['lawyer_id'])) {
            notifyUser(
                (int)$data['lawyer_id'],
                'Case Assigned to You',
                "You have been assigned to case {$data['case_number']}: {$data['title']}.",
                'case_update',
                APP_URL . '/cases/' . $id
            );
        }
        flash('success', "Case {$data['case_number']} registered successfully.");
        redirect('/cases/' . $id);
    }

    public static function show(int $id): void
    {
        $case      = CaseModel::findWithRelations($id) ?? (http_response_code(404) ?: view('errors.404') ?: exit());
        $documents = DocumentModel::getByCaseId($id);
        $hearings  = HearingModel::getByCaseId($id);
        $notes     = CaseModel::getNotes($id);
        $lawyers   = UserModel::getLawyers();
        $clients   = ClientModel::all('full_name ASC');
        view('cases.show', compact('case', 'documents', 'hearings', 'notes', 'lawyers', 'clients'));
    }

    public static function edit(int $id): void
    {
        $case    = CaseModel::find($id) ?? (http_response_code(404) ?: view('errors.404') ?: exit());
        $lawyers = UserModel::getLawyers();
        $clients = ClientModel::all('full_name ASC');
        view('cases.edit', compact('case', 'lawyers', 'clients'));
    }

    public static function update(int $id): void
    {
        if (!verify_csrf()) {
            redirect('/cases');
        }
        $data = [
            'title'        => input('title'),
            'case_type'    => input('case_type'),
            'status'       => input('status'),
            'description'  => input('description'),
            'lawyer_id'    => input('lawyer_id'),
            'client_id'    => input('client_id'),
            'court_name'   => input('court_name'),
            'judge_name'   => input('judge_name'),
            'filing_date'  => input('filing_date'),
            'closing_date' => input('closing_date'),
        ];
        $oldCase = CaseModel::find($id);
        CaseModel::updateCase($id, $data);
        auditLog('case_updated', 'cases', $id, "Case #$id updated");
        // Notify on status change
        if ($oldCase && $oldCase['status'] !== $data['status']) {
            $oldLabel = ucwords(str_replace('_', ' ', $oldCase['status']));
            $newLabel = ucwords(str_replace('_', ' ', $data['status']));
            notifyCaseUpdate(
                $id,
                $oldCase['case_number'] ?? '',
                'Case Status Changed',
                "Case {$oldCase['case_number']} status changed from {$oldLabel} to {$newLabel}.",
                'case_update'
            );
            // Also notify assigned lawyer if different from actor
            if (!empty($data['lawyer_id'])) {
                notifyUser(
                    (int)$data['lawyer_id'],
                    'Case Status Changed',
                    "Case {$oldCase['case_number']} status changed to {$newLabel}.",
                    'case_update',
                    APP_URL . '/cases/' . $id
                );
            }
        }
        flash('success', 'Case updated successfully.');
        redirect('/cases/' . $id);
    }

    public static function delete(int $id): void
    {
        if (!verify_csrf() || !hasRole('admin')) {
            redirect('/cases');
        }
        CaseModel::delete($id);
        auditLog('case_deleted', 'cases', $id);
        flash('success', 'Case deleted.');
        redirect('/cases');
    }

    public static function addNote(int $id): void
    {
        if (!verify_csrf()) {
            redirect('/cases/' . $id);
        }
        $note = input('note');
        if ($note) {
            CaseModel::addNote($id, $note, auth()['id']);
            auditLog('note_added', 'cases', $id);
            $noteCase = CaseModel::find($id);
            if ($noteCase) {
                notifyCaseUpdate(
                    $id,
                    $noteCase['case_number'] ?? '',
                    'New Case Note',
                    "A new note was added to case {$noteCase['case_number']}: {$noteCase['title']}.",
                    'case_update'
                );
            }
        }
        redirect('/cases/' . $id);
    }
}

// ═══════════════════════════════════════════
// CLIENT CONTROLLER
// ═══════════════════════════════════════════
class ClientController
{
    public static function index(): void
    {
        $perPage = 15;
        $page    = max(1, (int)get('page', '1'));
        $search  = get('search');
        $total   = ClientModel::count($search ? 'full_name LIKE ? OR phone LIKE ?' : '', $search ? ["%$search%", "%$search%"] : []);
        $pager   = paginate($total, $perPage, $page);
        $clients = ClientModel::allPaginated($perPage, $pager['offset'], $search);
        view('clients.index', compact('clients', 'pager', 'search'));
    }

    public static function create(): void
    {
        view('clients.create');
    }

    public static function store(): void
    {
        if (!verify_csrf()) {
            redirect('/clients');
        }
        $data = [
            'full_name'     => input('full_name'),
            'email'         => input('email'),
            'phone'         => input('phone'),
            'address'       => input('address'),
            'id_number'     => input('id_number'),
            'date_of_birth' => input('date_of_birth'),
            'notes'         => input('notes'),
            'created_by'    => auth()['id'],
        ];
        if (!$data['full_name']) {
            flash('error', 'Full name is required.', 'error');
            redirect('/clients/create');
        }
        $id = ClientModel::create($data);
        auditLog('client_created', 'clients', $id);
        flash('success', 'Client added successfully.');
        redirect('/clients/' . $id);
    }

    public static function show(int $id): void
    {
        $client = ClientModel::find($id) ?? (http_response_code(404) ?: view('errors.404') ?: exit());
        $cases  = ClientModel::getCases($id);
        view('clients.show', compact('client', 'cases'));
    }

    public static function edit(int $id): void
    {
        $client = ClientModel::find($id) ?? (http_response_code(404) ?: view('errors.404') ?: exit());
        view('clients.edit', compact('client'));
    }

    public static function update(int $id): void
    {
        if (!verify_csrf()) {
            redirect('/clients');
        }
        $data = ['full_name' => input('full_name'), 'email' => input('email'), 'phone' => input('phone'), 'address' => input('address'), 'id_number' => input('id_number'), 'date_of_birth' => input('date_of_birth'), 'notes' => input('notes')];
        ClientModel::updateClient($id, $data);
        auditLog('client_updated', 'clients', $id);
        flash('success', 'Client updated successfully.');
        redirect('/clients/' . $id);
    }
}

// ═══════════════════════════════════════════
// DOCUMENT CONTROLLER
// ═══════════════════════════════════════════
class DocumentController
{
    public static function upload(): void
    {
        if (!verify_csrf()) {
            redirect('/cases');
        }
        $caseId = (int)input('case_id');
        if (!$caseId || empty($_FILES['document'])) {
            redirect('/cases/' . $caseId);
        }

        $upload = uploadFile($_FILES['document'], $caseId);
        if (!$upload) {
            flash('error', 'File upload failed. Check size and type.', 'error');
            redirect('/cases/' . $caseId);
        }

        DocumentModel::create([
            'case_id'     => $caseId,
            'title'       => input('title') ?: $upload['file_name'],
            'doc_type'    => input('doc_type'),
            'file_name'   => $upload['file_name'],
            'file_path'   => $upload['file_path'],
            'file_size'   => $upload['file_size'],
            'mime_type'   => $upload['mime_type'],
            'uploaded_by' => auth()['id'],
        ]);
        auditLog('document_uploaded', 'documents', $caseId);
        $docCase = CaseModel::find($caseId);
        if ($docCase) {
            notifyCaseUpdate(
                $caseId,
                $docCase['case_number'] ?? '',
                'Document Uploaded',
                "A new document was uploaded to case {$docCase['case_number']}: {$docCase['title']}.",
                'document'
            );
        }
        flash('success', 'Document uploaded successfully.');
        redirect('/cases/' . $caseId);
    }

    public static function download(int $id): void
    {
        $doc  = DocumentModel::find($id);
        if (!$doc) {
            http_response_code(404);
            exit;
        }
        $path = BASE_PATH . '/public/' . $doc['file_path'];
        if (!file_exists($path)) {
            http_response_code(404);
            exit;
        }
        header('Content-Type: ' . $doc['mime_type']);
        header('Content-Disposition: attachment; filename="' . $doc['file_name'] . '"');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        exit;
    }

    public static function delete(int $id): void
    {
        if (!verify_csrf() || !hasRole(['admin', 'lawyer'])) {
            redirect('/cases');
        }
        $doc  = DocumentModel::find($id);
        if ($doc) {
            $path = BASE_PATH . '/public/' . $doc['file_path'];
            if (file_exists($path)) unlink($path);
            DocumentModel::delete($id);
            auditLog('document_deleted', 'documents', $id);
            flash('success', 'Document deleted.');
            redirect('/cases/' . $doc['case_id']);
        }
        redirect('/cases');
    }
}

// ═══════════════════════════════════════════
// HEARING CONTROLLER
// ═══════════════════════════════════════════
class HearingController
{
    public static function index(): void
    {
        $page     = max(1, (int)get('page', '1'));
        $perPage  = 15;
        $total    = HearingModel::count();
        $pager    = paginate($total, $perPage, $page);
        $hearings = HearingModel::allWithCase($perPage, $pager['offset']);
        $cases    = CaseModel::all('title ASC');
        view('schedules.index', compact('hearings', 'pager', 'cases'));
    }

    public static function create(): void
    {
        $cases = CaseModel::all('title ASC');
        view('schedules.create', compact('cases'));
    }

    public static function store(): void
    {
        if (!verify_csrf()) {
            redirect('/hearings');
        }
        $data = [
            'case_id'      => (int)input('case_id'),
            'title'        => input('title'),
            'hearing_date' => input('hearing_date'),
            'hearing_time' => input('hearing_time'),
            'court_room'   => input('court_room'),
            'court_name'   => input('court_name'),
            'judge_name'   => input('judge_name'),
            'notes'        => input('notes'),
            'created_by'   => auth()['id'],
        ];
        if (!$data['case_id'] || !$data['hearing_date']) {
            flash('error', 'Case and hearing date are required.', 'error');
            redirect('/hearings/create');
        }
        $id = HearingModel::create($data);
        // Auto-update case status to 'hearing_scheduled' if still in early stage
        $case = CaseModel::find($data['case_id']);
        if ($case && in_array($case['status'], ['filed', 'under_investigation'])) {
            CaseModel::updateCase($data['case_id'], array_merge($case, ['status' => 'hearing_scheduled']));
        }
        auditLog('hearing_scheduled', 'hearings', $id);
        // Notify all admins and lawyers about the new hearing
        if ($case) {
            $hearingDate = date('d M Y', strtotime($data['hearing_date']));
            notifyCaseUpdate(
                $data['case_id'],
                $case['case_number'] ?? '',
                'Hearing Scheduled',
                "A hearing has been scheduled for case {$case['case_number']} on {$hearingDate}.",
                'hearing'
            );
            if (!empty($case['lawyer_id'])) {
                notifyUser(
                    (int)$case['lawyer_id'],
                    'Court Date Scheduled',
                    "Hearing for case {$case['case_number']} on {$hearingDate}. Please prepare.",
                    'hearing',
                    APP_URL . '/cases/' . $data['case_id']
                );
            }
        }
        flash('success', 'Hearing scheduled successfully. Case status updated to Hearing Scheduled.');
        redirect('/hearings');
    }

    public static function edit(int $id): void
    {
        $hearing = HearingModel::find($id);
        $cases   = CaseModel::all('title ASC');
        view('schedules.edit', compact('hearing', 'cases'));
    }

    public static function update(int $id): void
    {
        if (!verify_csrf()) {
            redirect('/hearings');
        }
        $data = ['title' => input('title'), 'hearing_date' => input('hearing_date'), 'hearing_time' => input('hearing_time'), 'court_room' => input('court_room'), 'court_name' => input('court_name'), 'judge_name' => input('judge_name'), 'status' => input('status'), 'notes' => input('notes')];
        $oldHearing = HearingModel::find($id);
        HearingModel::update($id, $data);
        // Sync case status based on hearing outcome
        $hearing = HearingModel::find($id);
        if ($hearing) {
            $case = CaseModel::find($hearing['case_id']);
            if ($case) {
                $newCaseStatus = null;
                if ($data['status'] === 'completed')  $newCaseStatus = 'in_progress';
                if ($data['status'] === 'postponed')  $newCaseStatus = 'hearing_scheduled';
                if ($data['status'] === 'cancelled')  $newCaseStatus = 'under_investigation';
                if ($newCaseStatus) {
                    CaseModel::updateCase($hearing['case_id'], array_merge($case, ['status' => $newCaseStatus]));
                }
            }
        }
        auditLog('hearing_updated', 'hearings', $id);
        // Notify when hearing status changes
        if ($hearing && $oldHearing && $oldHearing['status'] !== $data['status']) {
            $hearingCase = CaseModel::find($hearing['case_id']);
            if ($hearingCase) {
                $newLabel = ucwords($data['status']);
                notifyCaseUpdate(
                    $hearing['case_id'],
                    $hearingCase['case_number'] ?? '',
                    'Hearing Status Updated',
                    "Hearing for case {$hearingCase['case_number']} has been marked as {$newLabel}.",
                    'hearing'
                );
            }
        }
        flash('success', 'Hearing updated.');
        redirect('/hearings');
    }
}

// ═══════════════════════════════════════════
// USER CONTROLLER (admin only)
// ═══════════════════════════════════════════
class UserController
{
    public static function index(): void
    {
        AuthMiddleware::requireRole('admin');
        $users = UserModel::all();
        view('users.index', compact('users'));
    }

    public static function create(): void
    {
        AuthMiddleware::requireRole('admin');
        view('users.create');
    }

    public static function store(): void
    {
        AuthMiddleware::requireRole('admin');
        if (!verify_csrf()) {
            redirect('/users');
        }
        $data = ['name' => input('name'), 'email' => input('email'), 'password' => $_POST['password'] ?? '', 'role' => input('role'), 'phone' => input('phone')];
        if (!$data['name'] || !$data['email'] || !$data['password']) {
            flash('error', 'All fields required.', 'error');
            redirect('/users/create');
        }
        if (UserModel::findByEmail($data['email'])) {
            flash('error', 'Email already exists.', 'error');
            redirect('/users/create');
        }
        UserModel::create($data);
        auditLog('user_created', 'users', 0, "User {$data['email']} created");
        flash('success', 'User created successfully.');
        redirect('/users');
    }

    public static function edit(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        $user = UserModel::find($id);
        view('users.edit', compact('user'));
    }

    public static function update(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        if (!verify_csrf()) {
            redirect('/users');
        }
        $data = ['name' => input('name'), 'email' => input('email'), 'role' => input('role'), 'phone' => input('phone')];
        UserModel::updateUser($id, $data);
        if (!empty($_POST['password'])) UserModel::updatePassword($id, $_POST['password']);
        auditLog('user_updated', 'users', $id);
        flash('success', 'User updated.');
        redirect('/users');
    }

    public static function toggle(int $id): void
    {
        AuthMiddleware::requireRole('admin');
        if (!verify_csrf()) {
            redirect('/users');
        }
        UserModel::toggleActive($id);
        flash('success', 'User status toggled.');
        redirect('/users');
    }
}

// ═══════════════════════════════════════════
// REPORT CONTROLLER
// ═══════════════════════════════════════════
class ReportController
{
    public static function index(): void
    {
        $statusCounts = CaseModel::countByStatus();
        $totalCases   = array_sum($statusCounts);
        $totalClients = ClientModel::count();
        $upcoming7    = HearingModel::count("hearing_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY) AND status='scheduled'");
        $upcoming30   = HearingModel::count("hearing_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND status='scheduled'");
        view('reports.index', compact('statusCounts', 'totalCases', 'totalClients', 'upcoming7', 'upcoming30'));
    }

    public static function cases(): void
    {
        $cases = CaseModel::allWithRelations(100, 0);
        view('reports.cases', compact('cases'));
    }

    public static function hearings(): void
    {
        $hearings = HearingModel::allWithCase(100, 0);
        view('reports.hearings', compact('hearings'));
    }
}

// ═══════════════════════════════════════════
// NOTIFICATION CONTROLLER
// ═══════════════════════════════════════════
class NotificationController
{
    public static function markRead(): void
    {
        NotificationModel::markAllRead(auth()['id']);
        redirect($_SERVER['HTTP_REFERER'] ?? '/dashboard');
    }
}

// ═══════════════════════════════════════════
// PROFILE CONTROLLER
// ═══════════════════════════════════════════
class ProfileController
{
    public static function index(): void
    {
        $user = UserModel::find(auth()['id']);
        view('auth.profile', compact('user'));
    }

    public static function update(): void
    {
        if (!verify_csrf()) {
            redirect('/profile');
        }
        $data = ['name' => input('name'), 'email' => input('email'), 'role' => auth()['role'], 'phone' => input('phone')];
        UserModel::updateUser(auth()['id'], $data);
        if (!empty($_POST['password']) && strlen($_POST['password']) >= 8) {
            UserModel::updatePassword(auth()['id'], $_POST['password']);
        }
        $_SESSION['user']['name'] = $data['name'];
        flash('success', 'Profile updated successfully.');
        redirect('/profile');
    }
}
