<?php
/**
 * Custom Type Master Pro
 * Typing Lessons Curriculum Data & Helper Functions
 * Inspired by Typing.com student lessons: Beginner, Intermediate, and Advanced stages.
 */

require_once __DIR__ . '/../config/app.php';
require_once __DIR__ . '/../config/database.php';

/**
 * Ensures the user_lesson_progress table exists in the database.
 * Auto-creates it on first run so no manual SQL migration is required on live servers.
 */
function ensure_lesson_table_exists(?PDO $pdo = null): void {
    if (!$pdo) {
        $pdo = getDBConnection();
    }
    if (!$pdo) {
        return;
    }

    try {
        $sql = "
            CREATE TABLE IF NOT EXISTS `user_lesson_progress` (
                `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `user_id` INT UNSIGNED NOT NULL,
                `lesson_id` VARCHAR(50) NOT NULL,
                `stage` ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
                `stars` TINYINT UNSIGNED NOT NULL DEFAULT 0,
                `highest_wpm` DECIMAL(6,2) NOT NULL DEFAULT 0.00,
                `highest_accuracy` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
                `status` ENUM('completed', 'in_progress', 'unlocked') NOT NULL DEFAULT 'completed',
                `completed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                UNIQUE KEY `uniq_user_lesson` (`user_id`, `lesson_id`),
                INDEX `idx_user_stage` (`user_id`, `stage`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
        ";
        $pdo->exec($sql);
    } catch (Exception $e) {
        error_log('[Lesson Table Check] ' . $e->getMessage());
    }
}

/**
 * Returns the full Typing.com-style student lessons curriculum.
 *
 * @return array
 */
function get_all_lessons(): array {
    static $lessons = null;
    if ($lessons !== null) {
        return $lessons;
    }

    $lessons = [
        // =====================================================================
        // STAGE 1: BEGINNER (Home Row & Reach Fundamentals)
        // =====================================================================
        [
            'id' => 'beginner-1',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Home Row Fundamentals',
            'number' => 1,
            'title' => 'Home Row Anchors: J, F, and Space',
            'description' => 'Master your anchor keys where your index fingers rest on the tactile bumps.',
            'keys' => ['J', 'F', 'Space'],
            'finger_hints' => [
                'j' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                'f' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                ' ' => ['finger' => 'Thumb', 'hand' => 'Either', 'zone' => 'zone-thumb'],
            ],
            'exercises' => [
                'jj ff jf fj',
                'j f j f jj ff',
                'fjf jfj f j f j',
                'jjj fff jf fj jj ff',
                'j f j f jj ff jf fj'
            ]
        ],
        [
            'id' => 'beginner-2',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Home Row Fundamentals',
            'number' => 2,
            'title' => 'Home Row Expansion: K and L',
            'description' => 'Engage your right middle and ring fingers on the primary home row keys.',
            'keys' => ['K', 'L', 'J', 'F'],
            'finger_hints' => [
                'k' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
                'l' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                'j' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                'f' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                ' ' => ['finger' => 'Thumb', 'hand' => 'Either', 'zone' => 'zone-thumb'],
            ],
            'exercises' => [
                'kk ll kl lk',
                'jk lj fk kl',
                'k l k l kk ll',
                'fl jk lf kj ll kk',
                'fjk lkj fkl jkl klf'
            ]
        ],
        [
            'id' => 'beginner-3',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Home Row Fundamentals',
            'number' => 3,
            'title' => 'Home Row Left Hand: D and S',
            'description' => 'Coordinate your left middle and ring fingers across the home row foundation.',
            'keys' => ['D', 'S', 'K', 'L'],
            'finger_hints' => [
                'd' => ['finger' => 'Left Middle', 'hand' => 'Left', 'zone' => 'zone-l-middle'],
                's' => ['finger' => 'Left Ring', 'hand' => 'Left', 'zone' => 'zone-l-ring'],
                'k' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
                'l' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                ' ' => ['finger' => 'Thumb', 'hand' => 'Either', 'zone' => 'zone-thumb'],
            ],
            'exercises' => [
                'dd ss ds sd',
                'sd dk sl ks',
                'd s d s dd ss',
                'sk dl ks ld ss dd',
                'sld kds dls skl dsk'
            ]
        ],
        [
            'id' => 'beginner-4',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Home Row Mastery',
            'number' => 4,
            'title' => 'Complete Home Row: A and Semicolon ;',
            'description' => 'Bring in your pinky fingers on A and Semicolon to complete the entire 8-key home row.',
            'keys' => ['A', ';', 'S', 'D', 'J', 'K', 'L'],
            'finger_hints' => [
                'a' => ['finger' => 'Left Pinky', 'hand' => 'Left', 'zone' => 'zone-l-pinky'],
                ';' => ['finger' => 'Right Pinky', 'hand' => 'Right', 'zone' => 'zone-r-pinky'],
                's' => ['finger' => 'Left Ring', 'hand' => 'Left', 'zone' => 'zone-l-ring'],
                'l' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                ' ' => ['finger' => 'Thumb', 'hand' => 'Either', 'zone' => 'zone-thumb'],
            ],
            'exercises' => [
                'aa ;; a; ;a',
                'as df jkl; a;',
                'a s d f j k l ;',
                'all ask fad lad salsa',
                'a lad asks a fall dad;'
            ]
        ],
        [
            'id' => 'beginner-5',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Top Row Reach',
            'number' => 5,
            'title' => 'Reaching Up: U and R Keys',
            'description' => 'Reach upward with your index fingers to tap U (right index) and R (left index).',
            'keys' => ['U', 'R', 'F', 'J'],
            'finger_hints' => [
                'u' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                'r' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                'f' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                'j' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
            ],
            'exercises' => [
                'uu rr ur ru',
                'fur jug rug jar',
                'u r u r uu rr',
                'rug jar fur jug run',
                'a fur jar full jug;'
            ]
        ],
        [
            'id' => 'beginner-6',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Top Row Reach',
            'number' => 6,
            'title' => 'Vowels & Accuracy: E and I Keys',
            'description' => 'Reach with middle fingers to master high-frequency English vowels E and I.',
            'keys' => ['E', 'I', 'D', 'K'],
            'finger_hints' => [
                'e' => ['finger' => 'Left Middle', 'hand' => 'Left', 'zone' => 'zone-l-middle'],
                'i' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
                'd' => ['finger' => 'Left Middle', 'hand' => 'Left', 'zone' => 'zone-l-middle'],
                'k' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
            ],
            'exercises' => [
                'ee ii ei ie',
                'die kid lie red',
                'see fee led rail',
                'like ride file side',
                'she is a real leader'
            ]
        ],
        [
            'id' => 'beginner-7',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Top Row Reach',
            'number' => 7,
            'title' => 'Top Row Extension: T, O, and P',
            'description' => 'Extend across top row keys using your index, ring, and pinky fingers.',
            'keys' => ['T', 'O', 'P'],
            'finger_hints' => [
                't' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                'o' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                'p' => ['finger' => 'Right Pinky', 'hand' => 'Right', 'zone' => 'zone-r-pinky'],
            ],
            'exercises' => [
                'tt oo pp top',
                'pot tip pop tot',
                'stop plot post trip',
                'top post report plot',
                'keep a polite top post'
            ]
        ],
        [
            'id' => 'beginner-8',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Bottom Row Reach',
            'number' => 8,
            'title' => 'Bottom Row: C, V, N, and M',
            'description' => 'Reach downward smoothly to the bottom row without lifting your palms.',
            'keys' => ['C', 'V', 'N', 'M'],
            'finger_hints' => [
                'c' => ['finger' => 'Left Middle', 'hand' => 'Left', 'zone' => 'zone-l-middle'],
                'v' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                'n' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                'm' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
            ],
            'exercises' => [
                'cc vv nn mm',
                'van can man vim',
                'move come nine name',
                'clean music modern view',
                'can you make a nice move'
            ]
        ],
        [
            'id' => 'beginner-9',
            'stage' => 'beginner',
            'stage_title' => 'Beginner',
            'stage_badge' => 'Stage 1 Assessment',
            'number' => 9,
            'title' => 'Beginner Milestone Assessment',
            'description' => 'Put your home row, top row, and bottom row skills together in a timed proficiency trial.',
            'keys' => ['Full Alphabet', 'Space'],
            'finger_hints' => [],
            'exercises' => [
                'the red fox jumps over',
                'simple words create great speed',
                'keep fingers close to home row',
                'practice makes accuracy very strong',
                'touch typing without looking is freedom'
            ]
        ],

        // =====================================================================
        // STAGE 2: INTERMEDIATE (Words, Capitalization & Fluid Rhythm)
        // =====================================================================
        [
            'id' => 'intermediate-1',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Language Flow',
            'number' => 10,
            'title' => 'High-Frequency English Vocabulary',
            'description' => 'Type the most common English words in rapid, continuous cadence.',
            'keys' => ['the', 'and', 'for', 'you', 'with'],
            'finger_hints' => [],
            'exercises' => [
                'the and for you that',
                'with have from this they',
                'one had word but what',
                'all were when there each',
                'the time for you to learn is now'
            ]
        ],
        [
            'id' => 'intermediate-2',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Shift Key Coordination',
            'number' => 11,
            'title' => 'Shift Key & Capital Letters',
            'description' => 'Coordinate opposite pinkies for left and right Shift keys smoothly.',
            'keys' => ['Shift', 'Capitals'],
            'finger_hints' => [
                'shift' => ['finger' => 'Opposite Pinky', 'hand' => 'Alternate', 'zone' => 'zone-l-pinky']
            ],
            'exercises' => [
                'Java Python Linux React',
                'London Paris Tokyo New York',
                'Monday Tuesday Friday Sunday',
                'America Europe Asia Australia',
                'Great Minds Think With Clarity'
            ]
        ],
        [
            'id' => 'intermediate-3',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Punctuation Precision',
            'number' => 12,
            'title' => 'Punctuation: Periods, Commas & Quotes',
            'description' => 'Integrate commas, periods, quotation marks, and apostrophes into sentences.',
            'keys' => ['.', ',', "'", '"', '?'],
            'finger_hints' => [
                '.' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                ',' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
                "'" => ['finger' => 'Right Pinky', 'hand' => 'Right', 'zone' => 'zone-r-pinky']
            ],
            'exercises' => [
                'Yes, I can. No, you cannot.',
                'Wait, look at this. It works.',
                'She said, "Keep typing every day."',
                "It's a matter of daily practice, isn't it?",
                'Speed comes with time, but accuracy comes first.'
            ]
        ],
        [
            'id' => 'intermediate-4',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Rhythm & Speed Drills',
            'number' => 13,
            'title' => 'Fast Alternating Hand Cadence',
            'description' => 'Build high-speed muscle rhythm by alternating left and right hand key patterns.',
            'keys' => ['Alternating Hand Patterns'],
            'finger_hints' => [],
            'exercises' => [
                'form land fish lake disk pane',
                'make them work hard every minute',
                'bright light shines upon the road',
                'focus on steady rhythm not frantic speed',
                'smooth is fast and steady wins the race'
            ]
        ],
        [
            'id' => 'intermediate-5',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Sentence Building',
            'number' => 14,
            'title' => 'Natural Paragraph Cadence',
            'description' => 'Develop stamina and consistency across realistic, multi-sentence paragraphs.',
            'keys' => ['Flow & Stamina'],
            'finger_hints' => [],
            'exercises' => [
                'Every day you type, your fingers grow faster and more confident.',
                'The secret of touch typing is trusting muscle memory over your eyes.',
                'Good posture, relaxed shoulders, and steady breathing boost your words per minute.',
                'Consistent practice for just ten minutes daily beats an hour once a week.',
                'You are transforming from a casual keyboard user into a professional typist.'
            ]
        ],
        [
            'id' => 'intermediate-6',
            'stage' => 'intermediate',
            'stage_title' => 'Intermediate',
            'stage_badge' => 'Stage 2 Benchmark',
            'number' => 15,
            'title' => 'Intermediate Speed Benchmark',
            'description' => 'Prove your intermediate fluency with speed, capitalization, and punctuation.',
            'keys' => ['All Intermediate Skills'],
            'finger_hints' => [],
            'exercises' => [
                'Technology empowers curious minds to build creative solutions.',
                'When you touch type accurately, your thoughts flow directly onto the screen.',
                'Never stop asking questions, learning new skills, and refining your craft.',
                'High speed is an effortless byproduct of disciplined precision and focus.',
                'Congratulations on reaching the threshold of advanced keyboard mastery!'
            ]
        ],

        // =====================================================================
        // STAGE 3: ADVANCED (Numbers, Symbols & Technical Syntax)
        // =====================================================================
        [
            'id' => 'advanced-1',
            'stage' => 'advanced',
            'stage_title' => 'Advanced',
            'stage_badge' => 'Top Row Numbers',
            'number' => 16,
            'title' => 'Number Row Reaches: 1, 2, 3, 4, 5',
            'description' => 'Reach up from the home row to the number keys with your left hand fingers.',
            'keys' => ['1', '2', '3', '4', '5'],
            'finger_hints' => [
                '1' => ['finger' => 'Left Pinky', 'hand' => 'Left', 'zone' => 'zone-l-pinky'],
                '2' => ['finger' => 'Left Ring', 'hand' => 'Left', 'zone' => 'zone-l-ring'],
                '3' => ['finger' => 'Left Middle', 'hand' => 'Left', 'zone' => 'zone-l-middle'],
                '4' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
                '5' => ['finger' => 'Left Index', 'hand' => 'Left', 'zone' => 'zone-l-index'],
            ],
            'exercises' => [
                '11 22 33 44 55',
                '12 34 51 25 43',
                'room 101, floor 2, building 35',
                'page 45, chapter 12, section 3',
                'call 123-4512 before 5 pm today'
            ]
        ],
        [
            'id' => 'advanced-2',
            'stage' => 'advanced',
            'stage_title' => 'Advanced',
            'stage_badge' => 'Full Number Row',
            'number' => 17,
            'title' => 'Number Row Mastery: 6, 7, 8, 9, 0',
            'description' => 'Master the right-hand number row reach for dates, phone numbers, and codes.',
            'keys' => ['6', '7', '8', '9', '0'],
            'finger_hints' => [
                '6' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                '7' => ['finger' => 'Right Index', 'hand' => 'Right', 'zone' => 'zone-r-index'],
                '8' => ['finger' => 'Right Middle', 'hand' => 'Right', 'zone' => 'zone-r-middle'],
                '9' => ['finger' => 'Right Ring', 'hand' => 'Right', 'zone' => 'zone-r-ring'],
                '0' => ['finger' => 'Right Pinky', 'hand' => 'Right', 'zone' => 'zone-r-pinky'],
            ],
            'exercises' => [
                '66 77 88 99 00',
                '67 89 90 76 80',
                '2026 was the year of 100% digital speed',
                'flight 789 lands at 06:45 on gate 20',
                'zip codes: 90210, 10001, 75001, 80302'
            ]
        ],
        [
            'id' => 'advanced-3',
            'stage' => 'advanced',
            'stage_title' => 'Advanced',
            'stage_badge' => 'Shifted Symbols',
            'number' => 18,
            'title' => 'Special Characters: !, @, #, $, %, &',
            'description' => 'Master shifted number row symbols with precision pinky and reach coordination.',
            'keys' => ['!', '@', '#', '$', '%', '&', '*'],
            'finger_hints' => [],
            'exercises' => [
                '! @ # $ % & *',
                'Save $50 on 100% genuine items!',
                'Email: user@example.com #dev #typing',
                'Total: $250.00 & 15% discount applies!',
                'Success = 99% sweat & 1% inspiration!'
            ]
        ],
        [
            'id' => 'advanced-4',
            'stage' => 'advanced',
            'stage_title' => 'Advanced',
            'stage_badge' => 'Developer & Code Syntax',
            'number' => 19,
            'title' => 'Coding Syntax: Brackets, Operators & Math',
            'description' => 'Touch type code syntax without hesitation: brackets, operators, and slashes.',
            'keys' => ['{ }', '[ ]', '( )', '< >', '=', '+', '/', '\\'],
            'finger_hints' => [],
            'exercises' => [
                'const x = [1, 2, 3];',
                'if (a > b && c <= d) { return true; }',
                'function calculate(x, y) { return x + y; }',
                '<div class="app" id="main">Type Master</div>',
                'const result = items.map(i => i.score * 2);'
            ]
        ],
        [
            'id' => 'advanced-5',
            'stage' => 'advanced',
            'stage_title' => 'Advanced',
            'stage_badge' => 'Pro Certification',
            'number' => 20,
            'title' => 'Touch-Typing Master Certification',
            'description' => 'The ultimate endurance test: complex prose, numbers, code, and symbols.',
            'keys' => ['Full Keyboard Mastery'],
            'finger_hints' => [],
            'exercises' => [
                'In 2026, software engineers touch-type at 80+ WPM with 98% accuracy.',
                'The master typist feels the keyboard as a direct extension of the mind.',
                'Precision (99.5%) + Speed (75 WPM) = True Touch-Typing Superpower!',
                'Every bracket { }, symbol $, and number 1-9 is at your fingertips.',
                'You have achieved elite touch-typing mastery in TypeMaster PRO!'
            ]
        ]
    ];

    return $lessons;
}

/**
 * Retrieves a single lesson by ID.
 *
 * @param string $id
 * @return array|null
 */
function get_lesson_by_id(string $id): ?array {
    $all = get_all_lessons();
    foreach ($all as $l) {
        if ($l['id'] === $id) {
            return $l;
        }
    }
    return null;
}

/**
 * Returns user progress for all lessons from database or default empty map.
 *
 * @param int $userId
 * @return array Map of lesson_id => progress data
 */
function get_user_all_lesson_progress(int $userId): array {
    $pdo = getDBConnection();
    if (!$pdo || $userId <= 0) {
        return [];
    }

    ensure_lesson_table_exists($pdo);

    try {
        $stmt = $pdo->prepare("
            SELECT lesson_id, stage, stars, highest_wpm, highest_accuracy, status, completed_at 
            FROM user_lesson_progress 
            WHERE user_id = :uid
        ");
        $stmt->execute([':uid' => $userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $progressMap = [];
        foreach ($rows as $r) {
            $progressMap[$r['lesson_id']] = $r;
        }
        return $progressMap;
    } catch (Exception $e) {
        error_log('[Get Lesson Progress] ' . $e->getMessage());
        return [];
    }
}

/**
 * Calculates overall student curriculum statistics.
 *
 * @param int $userId
 * @return array
 */
function get_user_curriculum_stats(int $userId): array {
    $allLessons = get_all_lessons();
    $totalLessons = count($allLessons);
    $userProgress = get_user_all_lesson_progress($userId);

    $completedCount = 0;
    $totalStars = 0;
    $maxPossibleStars = $totalLessons * 3;
    $totalWpm = 0.0;
    $totalAccuracy = 0.0;

    $stageStats = [
        'beginner'     => ['total' => 0, 'completed' => 0, 'stars' => 0],
        'intermediate' => ['total' => 0, 'completed' => 0, 'stars' => 0],
        'advanced'     => ['total' => 0, 'completed' => 0, 'stars' => 0],
    ];

    foreach ($allLessons as $lesson) {
        $stage = $lesson['stage'];
        $stageStats[$stage]['total']++;

        $lid = $lesson['id'];
        if (!empty($userProgress[$lid])) {
            $p = $userProgress[$lid];
            $stars = (int)$p['stars'];
            $totalStars += $stars;
            $stageStats[$stage]['stars'] += $stars;

            if ($stars > 0 || $p['status'] === 'completed') {
                $completedCount++;
                $stageStats[$stage]['completed']++;
                $totalWpm += (float)$p['highest_wpm'];
                $totalAccuracy += (float)$p['highest_accuracy'];
            }
        }
    }

    $avgWpm = $completedCount > 0 ? round($totalWpm / $completedCount, 1) : 0.0;
    $avgAccuracy = $completedCount > 0 ? round($totalAccuracy / $completedCount, 1) : 0.0;
    $completionPct = $totalLessons > 0 ? round(($completedCount / $totalLessons) * 100) : 0;

    return [
        'total_lessons'      => $totalLessons,
        'completed_lessons'  => $completedCount,
        'total_stars'        => $totalStars,
        'max_stars'          => $maxPossibleStars,
        'completion_pct'     => $completionPct,
        'avg_wpm'            => $avgWpm,
        'avg_accuracy'       => $avgAccuracy,
        'stage_stats'        => $stageStats,
        'progress_map'       => $userProgress
    ];
}
