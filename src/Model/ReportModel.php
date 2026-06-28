<?php

class ReportModel {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getPendingReports() {
        $stmt = $this->db->query("
            SELECT r.id as report_id, r.reason, r.status, r.created_at,
                   u_reporter.firstname as reporter_fn, u_reporter.lastname as reporter_ln,
                   u_reported.id as reported_user_id, u_reported.firstname as reported_fn, u_reported.lastname as reported_ln
            FROM reports r
            JOIN users u_reporter ON r.reporter_id = u_reporter.id
            JOIN users u_reported ON r.reported_id = u_reported.id
            WHERE r.status = 'pending'
            ORDER BY r.created_at DESC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function resolveReport($reportId) {
        $stmt = $this->db->prepare("
            UPDATE reports SET status = 'resolved' WHERE id = :id
        ");

        return $stmt->execute(['id' => $reportId]);
    }
}