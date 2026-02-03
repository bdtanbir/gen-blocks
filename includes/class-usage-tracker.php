<?php
/**
 * Usage Tracker Class
 *
 * @package GenBlocks
 */

namespace GenBlocks;

defined('ABSPATH') || exit;

/**
 * Tracks API usage and provides analytics
 */
class Usage_Tracker {

    /**
     * Table name
     *
     * @var string
     */
    private $table_name;

    /**
     * Constructor
     */
    public function __construct() {
        global $wpdb;
        $this->table_name = $wpdb->prefix . 'genblocks_usage';
    }

    /**
     * Track a generation request
     *
     * @param int    $user_id    User ID.
     * @param string $prompt     User prompt.
     * @param string $block_type Generated block type.
     * @param int    $tokens     Tokens used.
     * @param float  $cost       Estimated cost.
     * @param string $status     Request status.
     * @return int|false Insert ID or false on failure.
     */
    public function track($user_id, $prompt, $block_type, $tokens, $cost, $status = 'success') {
        global $wpdb;

        $result = $wpdb->insert(
            $this->table_name,
            [
                'user_id'     => $user_id,
                'prompt'      => wp_trim_words($prompt, 100, '...'),
                'block_type'  => $block_type,
                'tokens_used' => $tokens,
                'cost'        => $cost,
                'status'      => $status,
                'created_at'  => current_time('mysql'),
            ],
            ['%d', '%s', '%s', '%d', '%f', '%s', '%s']
        );

        if (false === $result) {
            return false;
        }

        return $wpdb->insert_id;
    }

    /**
     * Get usage count for today
     *
     * @param int $user_id User ID.
     * @return int
     */
    public function get_today_count($user_id) {
        global $wpdb;

        $count = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name}
                WHERE user_id = %d
                AND DATE(created_at) = CURDATE()
                AND status = 'success'",
                $user_id
            )
        );

        return (int) $count;
    }

    /**
     * Get usage statistics
     *
     * @param string $period Time period (day, week, month, year, all).
     * @return array
     */
    public function get_stats($period = 'month') {
        global $wpdb;

        $date_condition = $this->get_date_condition($period);

        // Total generations
        $total_generations = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE status = 'success' {$date_condition}"
        );

        // Total tokens
        $total_tokens = (int) $wpdb->get_var(
            "SELECT COALESCE(SUM(tokens_used), 0) FROM {$this->table_name}
            WHERE status = 'success' {$date_condition}"
        );

        // Total cost
        $total_cost = (float) $wpdb->get_var(
            "SELECT COALESCE(SUM(cost), 0) FROM {$this->table_name}
            WHERE status = 'success' {$date_condition}"
        );

        // This month count
        $this_month = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}
            WHERE status = 'success'
            AND MONTH(created_at) = MONTH(CURDATE())
            AND YEAR(created_at) = YEAR(CURDATE())"
        );

        // Success rate
        $total_attempts = (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name} {$date_condition}"
        );
        $success_rate = $total_attempts > 0
            ? round(($total_generations / $total_attempts) * 100, 1)
            : 100;

        // Average tokens per request
        $avg_tokens = $total_generations > 0
            ? round($total_tokens / $total_generations)
            : 0;

        return [
            'total_generations' => $total_generations,
            'total_tokens'      => $total_tokens,
            'total_cost'        => round($total_cost, 4),
            'this_month'        => $this_month,
            'success_rate'      => $success_rate,
            'avg_tokens'        => $avg_tokens,
        ];
    }

    /**
     * Get chart data for visualization
     *
     * @param string $period Time period.
     * @return array
     */
    public function get_chart_data($period = 'month') {
        global $wpdb;

        $date_format = $this->get_date_format($period);
        $date_condition = $this->get_date_condition($period);
        $group_by = $this->get_group_by($period);

        $results = $wpdb->get_results(
            "SELECT
                {$date_format} as label,
                COUNT(*) as value
            FROM {$this->table_name}
            WHERE status = 'success' {$date_condition}
            GROUP BY {$group_by}
            ORDER BY created_at ASC",
            ARRAY_A
        );

        $labels = [];
        $values = [];

        foreach ($results as $row) {
            $labels[] = $row['label'];
            $values[] = (int) $row['value'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get block type statistics
     *
     * @param string $period Time period.
     * @return array
     */
    public function get_block_type_stats($period = 'month') {
        global $wpdb;

        $date_condition = $this->get_date_condition($period);

        $results = $wpdb->get_results(
            "SELECT
                block_type,
                COUNT(*) as count
            FROM {$this->table_name}
            WHERE status = 'success'
            AND block_type != '' {$date_condition}
            GROUP BY block_type
            ORDER BY count DESC
            LIMIT 10",
            ARRAY_A
        );

        $labels = [];
        $values = [];

        foreach ($results as $row) {
            // Clean up block type for display
            $label = str_replace(['core/', 'genblocks/'], '', $row['block_type']);
            $label = ucwords(str_replace('-', ' ', $label));
            $labels[] = $label;
            $values[] = (int) $row['count'];
        }

        return [
            'labels' => $labels,
            'values' => $values,
        ];
    }

    /**
     * Get recent activity
     *
     * @param int $limit Number of records.
     * @return array
     */
    public function get_recent($limit = 10) {
        global $wpdb;

        $results = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                    u.id,
                    u.user_id,
                    u.prompt,
                    u.block_type,
                    u.tokens_used,
                    u.cost,
                    u.status,
                    u.created_at,
                    usr.display_name as user_name
                FROM {$this->table_name} u
                LEFT JOIN {$wpdb->users} usr ON u.user_id = usr.ID
                ORDER BY u.created_at DESC
                LIMIT %d",
                $limit
            ),
            ARRAY_A
        );

        return $results;
    }

    /**
     * Get user's generation history
     *
     * @param int $user_id  User ID.
     * @param int $page     Page number.
     * @param int $per_page Items per page.
     * @return array
     */
    public function get_user_history($user_id, $page = 1, $per_page = 20) {
        global $wpdb;

        $offset = ($page - 1) * $per_page;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT * FROM {$this->table_name}
                WHERE user_id = %d
                ORDER BY created_at DESC
                LIMIT %d OFFSET %d",
                $user_id,
                $per_page,
                $offset
            ),
            ARRAY_A
        );
    }

    /**
     * Get user history count
     *
     * @param int $user_id User ID.
     * @return int
     */
    public function get_user_history_count($user_id) {
        global $wpdb;

        return (int) $wpdb->get_var(
            $wpdb->prepare(
                "SELECT COUNT(*) FROM {$this->table_name} WHERE user_id = %d",
                $user_id
            )
        );
    }

    /**
     * Get all history (admin)
     *
     * @param int $page     Page number.
     * @param int $per_page Items per page.
     * @return array
     */
    public function get_all_history($page = 1, $per_page = 20) {
        global $wpdb;

        $offset = ($page - 1) * $per_page;

        return $wpdb->get_results(
            $wpdb->prepare(
                "SELECT
                    u.*,
                    usr.display_name as user_name
                FROM {$this->table_name} u
                LEFT JOIN {$wpdb->users} usr ON u.user_id = usr.ID
                ORDER BY u.created_at DESC
                LIMIT %d OFFSET %d",
                $per_page,
                $offset
            ),
            ARRAY_A
        );
    }

    /**
     * Get total record count
     *
     * @return int
     */
    public function get_total_count() {
        global $wpdb;

        return (int) $wpdb->get_var(
            "SELECT COUNT(*) FROM {$this->table_name}"
        );
    }

    /**
     * Delete old records
     *
     * @param int $days Days to keep.
     * @return int Number of deleted rows.
     */
    public function cleanup($days = 90) {
        global $wpdb;

        return $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$this->table_name}
                WHERE created_at < DATE_SUB(NOW(), INTERVAL %d DAY)",
                $days
            )
        );
    }

    /**
     * Get date condition for SQL
     *
     * @param string $period Time period.
     * @return string
     */
    private function get_date_condition($period) {
        switch ($period) {
            case 'day':
                return "AND DATE(created_at) = CURDATE()";
            case 'week':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            case 'month':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            case 'year':
                return "AND created_at >= DATE_SUB(NOW(), INTERVAL 1 YEAR)";
            case 'all':
            default:
                return "";
        }
    }

    /**
     * Get date format for SQL
     *
     * @param string $period Time period.
     * @return string
     */
    private function get_date_format($period) {
        switch ($period) {
            case 'day':
                return "DATE_FORMAT(created_at, '%H:00')";
            case 'week':
                return "DATE_FORMAT(created_at, '%a')";
            case 'month':
                return "DATE_FORMAT(created_at, '%b %d')";
            case 'year':
                return "DATE_FORMAT(created_at, '%b %Y')";
            case 'all':
            default:
                return "DATE_FORMAT(created_at, '%Y-%m')";
        }
    }

    /**
     * Get GROUP BY clause
     *
     * @param string $period Time period.
     * @return string
     */
    private function get_group_by($period) {
        switch ($period) {
            case 'day':
                return "HOUR(created_at)";
            case 'week':
                return "DATE(created_at)";
            case 'month':
                return "DATE(created_at)";
            case 'year':
                return "MONTH(created_at), YEAR(created_at)";
            case 'all':
            default:
                return "YEAR(created_at), MONTH(created_at)";
        }
    }
}
