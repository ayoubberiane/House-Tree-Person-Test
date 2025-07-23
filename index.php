<?php
/**
 * House-Tree-Person Test Advanced Analysis Server
 * Handles psychological analysis and data processing
 */

class HTPAnalyzer {
    
    private $phases = [
        1 => [
            'name' => 'House',
            'icon' => '🏠',
            'psychological_focus' => 'Home, family dynamics, security, social attitudes',
            'analysis_factors' => [
                'windows' => 'Openness to social interaction',
                'doors' => 'Accessibility and welcoming nature',
                'size' => 'Self-worth and confidence',
                'details' => 'Attention to home environment',
                'surroundings' => 'Social context awareness'
            ]
        ],
        2 => [
            'name' => 'Tree', 
            'icon' => '🌳',
            'psychological_focus' => 'Life energy, personal growth, stability, inner strength',
            'analysis_factors' => [
                'roots' => 'Connection to past and family',
                'trunk' => 'Inner strength and stability',
                'branches' => 'Aspirations and future goals',
                'leaves' => 'Current life energy',
                'overall_structure' => 'Life philosophy and approach'
            ]
        ],
        3 => [
            'name' => 'Person',
            'icon' => '👤',
            'psychological_focus' => 'Self-image, body awareness, interpersonal relationships',
            'analysis_factors' => [
                'facial_features' => 'Self-perception and social presentation',
                'body_posture' => 'Confidence and emotional state',
                'clothing' => 'Social awareness and self-expression',
                'activity_level' => 'Energy and engagement with world',
                'proportions' => 'Body image and self-acceptance'
            ]
        ]
    ];

    /**
     * Analyze drawing data from client
     */
    public function analyzeDrawingData($inputData) {
        $data = json_decode($inputData, true);
        
        if (!$data || !isset($data['phases'])) {
            return $this->errorResponse('Invalid data format');
        }
        
        $analysis = [
            'individual_phases' => [],
            'overall_analysis' => [],
            'psychological_insights' => [],
            'recommendations' => []
        ];
        
        // Analyze each phase
        foreach ($data['phases'] as $phaseData) {
            $phaseAnalysis = $this->analyzePhase($phaseData);
            $analysis['individual_phases'][] = $phaseAnalysis;
        }
        
        // Generate overall analysis
        $analysis['overall_analysis'] = $this->generateOverallAnalysis($data['phases']);
        $analysis['psychological_insights'] = $this->generatePsychologicalInsights($data['phases']);
        $analysis['recommendations'] = $this->generateRecommendations($data['phases']);
        
        return json_encode($analysis, JSON_PRETTY_PRINT);
    }
    
    /**
     * Analyze individual phase data
     */
    private function analyzePhase($phaseData) {
        $phaseNum = $phaseData['phase'];
        $phase = $this->phases[$phaseNum];
        
        $analysis = [
            'phase' => $phaseNum,
            'name' => $phase['name'],
            'icon' => $phase['icon'],
            'drawing_metrics' => $this->calculateDrawingMetrics($phaseData),
            'behavioral_indicators' => $this->analyzeBehavioralPatterns($phaseData),
            'psychological_interpretation' => $this->interpretPsychologically($phaseData, $phase)
        ];
        
        return $analysis;
    }
    
    /**
     * Calculate drawing metrics
     */
    private function calculateDrawingMetrics($phaseData) {
        $timeInMinutes = round($phaseData['timeSpent'] / 60000, 2);
        $strokeDensity = $phaseData['strokeCount'] / ($timeInMinutes > 0 ? $timeInMinutes : 1);
        
        return [
            'time_spent_minutes' => $timeInMinutes,
            'total_strokes' => $phaseData['strokeCount'],
            'colors_used' => count($phaseData['colorsUsed']),
            'color_palette' => $phaseData['colorsUsed'],
            'coverage_percentage' => $phaseData['coverage'],
            'stroke_density_per_minute' => round($strokeDensity, 2),
            'drawing_intensity' => $this->calculateIntensity($phaseData)
        ];
    }
    
    /**
     * Calculate drawing intensity score
     */
    private function calculateIntensity($phaseData) {
        $timeWeight = min($phaseData['timeSpent'] / 300000, 1); // Max 5 minutes
        $strokeWeight = min($phaseData['strokeCount'] / 200, 1); // Max 200 strokes
        $colorWeight = min(count($phaseData['colorsUsed']) / 5, 1); // Max 5 colors
        
        $intensity = ($timeWeight + $strokeWeight + $colorWeight) / 3;
        
        if ($intensity < 0.3) return 'Low';
        if ($intensity < 0.7) return 'Moderate';
        return 'High';
    }
    
    /**
     * Analyze behavioral patterns
     */
    private function analyzeBehavioralPatterns($phaseData) {
        $patterns = [];
        
        // Time analysis
        $timeMinutes = $phaseData['timeSpent'] / 60000;
        if ($timeMinutes < 1) {
            $patterns[] = "Impulsive approach - Quick decision making";
        } elseif ($timeMinutes > 5) {
            $patterns[] = "Perfectionist tendency - High attention to detail";
        }
        
        // Stroke analysis
        if ($phaseData['strokeCount'] < 20) {
            $patterns[] = "Minimalist style - Focus on essential elements";
        } elseif ($phaseData['strokeCount'] > 100) {
            $patterns[] = "Detail-oriented - Complex thinking patterns";
        }
        
        // Color analysis
        $colorCount = count($phaseData['colorsUsed']);
        if ($colorCount === 1) {
            $patterns[] = "Conservative approach - Structured thinking";
        } elseif ($colorCount > 3) {
            $patterns[] = "Creative expression - Emotional richness";
        }
        
        return $patterns;
    }
    
    /**
     * Psychological interpretation
     */
    private function interpretPsychologically($phaseData, $phase) {
        $interpretation = [
            'primary_focus' => $phase['psychological_focus'],
            'emotional_indicators' => [],
            'personality_traits' => [],
            'potential_concerns' => []
        ];
        
        // Analyze based on phase-specific factors
        switch ($phase['name']) {
            case 'House':
                $interpretation = $this->interpretHouse($phaseData, $interpretation);
                break;
            case 'Tree':
                $interpretation = $this->interpretTree($phaseData, $interpretation);
                break;
            case 'Person':
                $interpretation = $this->interpretPerson($phaseData, $interpretation);
                break;
        }
        
        return $interpretation;
    }
    
    /**
     * House-specific interpretation
     */
    private function interpretHouse($phaseData, $interpretation) {
        $timeSpent = $phaseData['timeSpent'] / 60000;
        $strokes = $phaseData['strokeCount'];
        
        if ($timeSpent > 3) {
            $interpretation['emotional_indicators'][] = "Strong attachment to home and family";
            $interpretation['personality_traits'][] = "Values security and stability";
        }
        
        if ($strokes > 80) {
            $interpretation['personality_traits'][] = "Detail-oriented in domestic matters";
            $interpretation['emotional_indicators'][] = "High investment in home environment";
        }
        
        if (count($phaseData['colorsUsed']) > 2) {
            $interpretation['personality_traits'][] = "Warm and welcoming nature";
        }
        
        return $interpretation;
    }
    
    /**
     * Tree-specific interpretation
     */
    private function interpretTree($phaseData, $interpretation) {
        $timeSpent = $phaseData['timeSpent'] / 60000;
        $strokes = $phaseData['strokeCount'];
        
        if ($timeSpent > 4) {
            $interpretation['emotional_indicators'][] = "Deep contemplation of personal growth";
            $interpretation['personality_traits'][] = "Introspective and self-aware";
        }
        
        if ($strokes < 30) {
            $interpretation['personality_traits'][] = "Minimalist life philosophy";
        } elseif ($strokes > 100) {
            $interpretation['personality_traits'][] = "Complex inner world";
            $interpretation['emotional_indicators'][] = "Rich emotional landscape";
        }
        
        return $interpretation;
    }
    
    /**
     * Person-specific interpretation
     */
    private function interpretPerson($phaseData, $interpretation) {
        $timeSpent = $phaseData['timeSpent'] / 60000;
        $strokes = $phaseData['strokeCount'];
        
        if ($timeSpent < 2) {
            $interpretation['potential_concerns'][] = "May avoid self-reflection";
        } elseif ($timeSpent > 5) {
            $interpretation['personality_traits'][] = "High self-awareness";
            $interpretation['emotional_indicators'][] = "Complex self-image";
        }
        
        if ($strokes > 120) {
            $interpretation['personality_traits'][] = "Highly detailed self-perception";
        }
        
        return $interpretation;
    }
    
    /**
     * Generate overall analysis
     */
    private function generateOverallAnalysis($phases) {
        $totalTime = array_sum(array_column($phases, 'timeSpent'));
        $totalStrokes = array_sum(array_column($phases, 'strokeCount'));
        $allColors = [];
        
        foreach ($phases as $phase) {
            $allColors = array_merge($allColors, $phase['colorsUsed']);
        }
        $uniqueColors = count(array_unique($allColors));
        
        // Determine primary focus
        $phaseTimes = array_column($phases, 'timeSpent');
        $maxTimeIndex = array_search(max($phaseTimes), $phaseTimes);
        $focusAreas = ['Home & Security', 'Personal Growth', 'Self-Image'];
        
        return [
            'completion_time_minutes' => round($totalTime / 60000, 2),
            'total_strokes' => $totalStrokes,
            'unique_colors_used' => $uniqueColors,
            'primary_psychological_focus' => $focusAreas[$maxTimeIndex],
            'overall_approach' => $this->determineOverallApproach($totalStrokes, $uniqueColors),
            'personality_summary' => $this->generatePersonalitySummary($phases)
        ];
    }
    
    /**
     * Determine overall approach
     */
    private function determineOverallApproach($totalStrokes, $colorCount) {
        $avgStrokes = $totalStrokes / 3;
        
        if ($avgStrokes < 30 && $colorCount <= 2) {
            return "Minimalist and Direct";
        } elseif ($avgStrokes > 80 && $colorCount > 3) {
            return "Expressive and Detailed";
        } elseif ($avgStrokes > 60) {
            return "Thorough and Methodical";
        } else {
            return "Balanced and Practical";
        }
    }
    
    /**
     * Generate personality summary
     */
    private function generatePersonalitySummary($phases) {
        $traits = [];
        
        // Analyze consistency across phases
        $strokeVariation = $this->calculateVariation(array_column($phases, 'strokeCount'));
        $timeVariation = $this->calculateVariation(array_column($phases, 'timeSpent'));
        
        if ($strokeVariation < 0.3) {
            $traits[] = "Consistent approach across different life areas";
        } else {
            $traits[] = "Varied engagement depending on topic significance";
        }
        
        if ($timeVariation < 0.3) {
            $traits[] = "Steady pacing and deliberate thinking";
        } else {
            $traits[] = "Flexible time investment based on personal relevance";
        }
        
        return $traits;
    }
    
    /**
     * Calculate coefficient of variation
     */
    private function calculateVariation($values) {
        $mean = array_sum($values) / count($values);
        $variance = array_sum(array_map(function($x) use ($mean) {
            return pow($x - $mean, 2);
        }, $values)) / count($values);
        
        return $mean > 0 ? sqrt($variance) / $mean : 0;
    }
    
    /**
     * Generate psychological insights
     */
    private function generatePsychologicalInsights($phases) {
        $insights = [];
        
        // Drawing sequence analysis
        $firstPhase = $phases[0];
        $lastPhase = end($phases);
        
        if ($firstPhase['timeSpent'] > $lastPhase['timeSpent']) {
            $insights[] = "Initial enthusiasm with gradual fatigue - common pattern indicating normal attention span";
        }
        
        // Complexity progression
        $strokeProgression = array_column($phases, 'strokeCount');
        if ($strokeProgression[0] < $strokeProgression[1] && $strokeProgression[1] < $strokeProgression[2]) {
            $insights[] = "Increasing complexity suggests growing comfort and engagement with the task";
        }
        
        // Color usage evolution
        $colorProgression = array_map(function($p) { return count($p['colorsUsed']); }, $phases);
        if (max($colorProgression) > 2) {
            $insights[] = "Creative expression indicates emotional openness and artistic sensitivity";
        }
        
        return $insights;
    }
    
    /**
     * Generate recommendations
     */
    private function generateRecommendations($phases) {
        $recommendations = [];
        
        $avgTime = array_sum(array_column($phases, 'timeSpent')) / (3 * 60000);
        $avgStrokes = array_sum(array_column($phases, 'strokeCount')) / 3;
        
        if ($avgTime < 2) {
            $recommendations[] = "Consider taking more time for self-reflection and introspection";
        } elseif ($avgTime > 6) {
            $recommendations[] = "Your attention to detail is excellent - trust your instincts more quickly";
        }
        
        if ($avgStrokes < 25) {
            $recommendations[] = "Explore adding more detail and complexity to your self-expression";
        } elseif ($avgStrokes > 100) {
            $recommendations[] = "Your rich inner world is evident - consider simplifying when needed";
        }
        
        // Phase-specific recommendations
        $maxTimePhase = array_search(max(array_column($phases, 'timeSpent')), array_column($phases, 'timeSpent'));
        $focusAreas = ['home and security', 'personal growth', 'self-image and relationships'];
        
        $recommendations[] = "Your primary focus on {$focusAreas[$maxTimePhase]} suggests this area deserves continued attention in your personal development";
        
        return $recommendations;
    }
    
    /**
     * Error response
     */
    private function errorResponse($message) {
        return json_encode(['error' => $message]);
    }
}

// API endpoint handling
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    header('Content-Type: application/json');
    header('Access-Control-Allow-Origin: *');
    header('Access-Control-Allow-Methods: POST');
    header('Access-Control-Allow-Headers: Content-Type');
    
    $analyzer = new HTPAnalyzer();
    $input = file_get_contents('php://input');
    echo $analyzer->analyzeDrawingData($input);
} elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // API documentation
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>HTP Analysis API</title>
        <style>
            body { font-family: Arial, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
            .endpoint { background: #f5f5f5; padding: 15px; margin: 10px 0; border-radius: 5px; }
            code { background: #e8e8e8; padding: 2px 5px; border-radius: 3px; }
        </style>
    </head>
    <body>
        <h1>🏠🌳👤 House-Tree-Person Analysis API</h1>
        
        <div class="endpoint">
            <h3>POST /analysis.php</h3>
            <p>Analyzes HTP test drawing data and returns psychological insights.</p>
            
            <h4>Request Format:</h4>
            <pre><code>{
  "phases": [
    {
      "phase": 1,
      "timeSpent": 120000,
      "strokeCount": 45,
      "colorsUsed": ["#000000", "#ff0000"],
      "coverage": 25
    },
    // ... additional phases
  ]
}</code></pre>
            
            <h4>Response includes:</h4>
            <ul>
                <li>Individual phase analysis</li>
                <li>Overall psychological profile</li>
                <li>Behavioral patterns</li>
                <li>Personality insights</li>
                <li>Recommendations</li>
            </ul>
        </div>
        
        <p><strong>Usage:</strong> Send drawing data from the client-side application to receive detailed psychological analysis.</p>
    </body>
    </html>
    <?php
}
?>
