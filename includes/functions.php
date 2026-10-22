<?php
/**
 * Custom Type Master Pro
 * General Helper & WPM Calculation Functions
 */

/**
 * Clean & sanitize user input for HTML output.
 *
 * @param mixed $data
 * @return string
 */
function e($data): string {
    return htmlspecialchars((string)($data ?? ''), ENT_QUOTES, 'UTF-8');
}

/**
 * Calculate Gross Words Per Minute.
 * Standard formula: (Total characters typed / 5) / (Elapsed time in minutes).
 * Handles zero or near-zero elapsed time safely.
 *
 * @param int|float $totalCharacters
 * @param float $elapsedSeconds
 * @return float
 */
function calculate_gross_wpm($totalCharacters, float $elapsedSeconds): float {
    if ($elapsedSeconds <= 0.5) {
        return 0.0;
    }
    $elapsedMinutes = $elapsedSeconds / 60.0;
    $words = $totalCharacters / 5.0;
    return round($words / $elapsedMinutes, 2);
}

/**
 * Calculate Net Words Per Minute.
 * Standard formula: Gross WPM - (Uncorrected errors / Elapsed time in minutes).
 * Clamped so it cannot be negative.
 *
 * @param float $grossWpm
 * @param int $errors
 * @param float $elapsedSeconds
 * @return float
 */
function calculate_net_wpm(float $grossWpm, int $errors, float $elapsedSeconds): float {
    if ($elapsedSeconds <= 0.5) {
        return 0.0;
    }
    $elapsedMinutes = $elapsedSeconds / 60.0;
    $errorsPerMinute = $errors / $elapsedMinutes;
    $net = $grossWpm - $errorsPerMinute;
    return max(0.0, round($net, 2));
}

/**
 * Calculate Typing Accuracy Percentage.
 * Formula: (Correct Keystrokes / Total Keystrokes) * 100.
 *
 * @param int $correctKeystrokes
 * @param int $totalKeystrokes
 * @return float
 */
function calculate_accuracy(int $correctKeystrokes, int $totalKeystrokes): float {
    if ($totalKeystrokes <= 0) {
        return 0.0;
    }
    $accuracy = ($correctKeystrokes / $totalKeystrokes) * 100.0;
    return min(100.0, max(0.0, round($accuracy, 2)));
}

/**
 * Format seconds into a friendly mm:ss or text string.
 *
 * @param int|float $seconds
 * @return string
 */
function format_duration($seconds): string {
    $seconds = (int)$seconds;
    $m = floor($seconds / 60);
    $s = $seconds % 60;
    if ($m > 0) {
        return sprintf('%dm %02ds', $m, $s);
    }
    return sprintf('%ds', $s);
}

/**
 * Return human-readable time elapsed (e.g. "5 mins ago", "2 hours ago").
 *
 * @param string|int $datetime
 * @return string
 */
function time_ago($datetime): string {
    $time = is_numeric($datetime) ? (int)$datetime : strtotime($datetime);
    $diff = time() - $time;

    if ($diff < 60) {
        return 'Just now';
    } elseif ($diff < 3600) {
        $m = floor($diff / 60);
        return $m . ($m == 1 ? ' min ago' : ' mins ago');
    } elseif ($diff < 86400) {
        $h = floor($diff / 3600);
        return $h . ($h == 1 ? ' hour ago' : ' hours ago');
    } elseif ($diff < 2592000) {
        $d = floor($diff / 86400);
        return $d . ($d == 1 ? ' day ago' : ' days ago');
    } else {
        return date('M j, Y', $time);
    }
}

/**
 * Render a styled Bootstrap badge for difficulty level.
 *
 * @param string $difficulty
 * @return string
 */
function get_difficulty_badge(string $difficulty): string {
    $difficulty = strtolower($difficulty);
    switch ($difficulty) {
        case 'beginner':
            return '<span class="badge badge-diff-beginner"><i class="bi bi-shield-check me-1"></i>Beginner</span>';
        case 'intermediate':
            return '<span class="badge badge-diff-intermediate"><i class="bi bi-lightning-charge me-1"></i>Intermediate</span>';
        case 'advanced':
            return '<span class="badge badge-diff-advanced"><i class="bi bi-fire me-1"></i>Advanced</span>';
        case 'custom':
            return '<span class="badge badge-diff-custom"><i class="bi bi-pencil-square me-1"></i>Custom</span>';
        default:
            return '<span class="badge bg-secondary">' . e(ucfirst($difficulty)) . '</span>';
    }
}

/**
 * Return descriptive rating based on Net WPM.
 *
 * @param float $wpm
 * @return array ['label' => string, 'color' => string, 'icon' => string]
 */
function get_speed_rating(float $wpm): array {
    if ($wpm >= 100) {
        return ['label' => 'Typing God', 'color' => '#f43f5e', 'icon' => 'bi-trophy-fill'];
    } elseif ($wpm >= 80) {
        return ['label' => 'Grandmaster', 'color' => '#a855f7', 'icon' => 'bi-award-fill'];
    } elseif ($wpm >= 60) {
        return ['label' => 'Professional', 'color' => '#3b82f6', 'icon' => 'bi-star-fill'];
    } elseif ($wpm >= 40) {
        return ['label' => 'Intermediate', 'color' => '#10b981', 'icon' => 'bi-check-circle-fill'];
    } elseif ($wpm >= 25) {
        return ['label' => 'Developing', 'color' => '#f59e0b', 'icon' => 'bi-graph-up-arrow'];
    } else {
        return ['label' => 'Beginner', 'color' => '#94a3b8', 'icon' => 'bi-seedling'];
    }
}

/**
 * Set a session flash message.
 *
 * @param string $type success|danger|warning|info
 * @param string $message
 */
function set_flash(string $type, string $message): void {
    $_SESSION['flash'] = [
        'type'    => $type,
        'message' => $message
    ];
}

/**
 * Retrieve and clear the session flash message.
 *
 * @return array|null
 */
function get_flash(): ?array {
    if (!empty($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
