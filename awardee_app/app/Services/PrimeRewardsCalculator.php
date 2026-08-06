<?php

namespace App\Services;

/**
 * PRIME REWARDS PROGRAM - Points Calculator Service
 * 
 * Calculates loyalty points based on product sales targets and volumes
 */
class PrimeRewardsCalculator
{
    // Product minimum monthly targets
    private const TARGETS = [
        'prime_bended' => 50,
        'prime_spandrel' => 41,
        'steel_deck' => 50,
        'cpurlins' => 33,
    ];

    // Base points for reaching minimum
    private const BASE_POINTS = [
        'prime_bended' => 10,
        'prime_spandrel' => 13,
        'steel_deck' => 10,
        'cpurlins' => 13,
    ];

    // Bonus points for exceeding targets
    private const BONUS_THRESHOLDS = [
        'prime_bended' => ['threshold' => 75, 'points' => 5],
        'prime_spandrel' => ['threshold' => 61, 'points' => 7],  // 61-62 sheets
        'steel_deck' => ['threshold' => 75, 'points' => 5],
        'cpurlins' => ['threshold' => 49, 'points' => 7],  // 49-50 sheets
    ];

    /**
     * Calculate points for a single product
     */
    public function calculateProductPoints(string $product, int $sheets): array
    {
        if (!isset(self::TARGETS[$product])) {
            return ['points' => 0, 'reason' => 'Invalid product'];
        }

        $basePoints = 0;
        $bonusPoints = 0;
        $reasons = [];
        $targetMet = false;

        $minTarget = self::TARGETS[$product];
        $basePointsValue = self::BASE_POINTS[$product];
        $bonusConfig = self::BONUS_THRESHOLDS[$product];

        // Check minimum target
        if ($sheets >= $minTarget) {
            $basePoints = $basePointsValue;
            $targetMet = true;
            $reasons[] = "✓ Minimum target reached ({$sheets} / {$minTarget} sheets)";

            // Check bonus threshold
            if ($sheets >= $bonusConfig['threshold']) {
                $bonusPoints = $bonusConfig['points'];
                $reasons[] = "✓ Bonus target reached ({$sheets} / {$bonusConfig['threshold']} sheets): +{$bonusConfig['points']} pts";
            }
        } else {
            $shortage = $minTarget - $sheets;
            $reasons[] = "✗ Below target ({$sheets} / {$minTarget} sheets, short by {$shortage})";
        }

        return [
            'product' => $product,
            'sheets' => $sheets,
            'base_points' => $basePoints,
            'bonus_points' => $bonusPoints,
            'total_points' => $basePoints + $bonusPoints,
            'target_met' => $targetMet,
            'reasons' => $reasons,
        ];
    }

    /**
     * Calculate all products for a dealer in a month
     */
    public function calculateMonthlyPoints(array $salesData): array
    {
        $products = [
            'prime_bended' => $salesData['prime_bended_sheets'] ?? 0,
            'prime_spandrel' => $salesData['prime_spandrel_sheets'] ?? 0,
            'steel_deck' => $salesData['steel_deck_sheets'] ?? 0,
            'cpurlins' => $salesData['cpurlins_sheets'] ?? 0,
        ];

        $productResults = [];
        $totalPoints = 0;
        $targetsMet = 0;

        foreach ($products as $product => $sheets) {
            $result = $this->calculateProductPoints($product, $sheets);
            $productResults[$product] = $result;
            $totalPoints += $result['total_points'];
            if ($result['target_met']) {
                $targetsMet++;
            }
        }

        // Calculate multi-product bonus
        $multiProductBonus = $this->calculateMultiProductBonus($targetsMet);

        return [
            'dealer_id' => $salesData['dealer_id'] ?? 'N/A',
            'dealer_name' => $salesData['dealer_name'] ?? 'N/A',
            'year' => $salesData['year'] ?? null,
            'month' => $salesData['month'] ?? null,
            'products' => $productResults,
            'product_points_total' => $totalPoints,
            'targets_met' => $targetsMet,
            'targets_met_percent' => ($targetsMet / 4) * 100,
            'multi_product_bonus' => $multiProductBonus,
            'total_points' => $totalPoints + $multiProductBonus['points'],
            'summary' => $this->generateSummary($targetsMet, $totalPoints, $multiProductBonus),
        ];
    }

    /**
     * Calculate multi-product bonus
     */
    private function calculateMultiProductBonus(int $targetsMet): array
    {
        $bonusPoints = 0;
        $reasons = [];

        if ($targetsMet >= 2) {
            $bonusPoints = $targetsMet; // 2 products = 2 pts, 3 = 3 pts, 4 = 4 pts
            $reasons[] = "{$targetsMet} products met minimum targets: +{$bonusPoints} bonus points";
        } else {
            $reasons[] = 'No multi-product bonus (less than 2 targets met)';
        }

        return [
            'targets_met' => $targetsMet,
            'points' => $bonusPoints,
            'reasons' => $reasons,
        ];
    }

    /**
     * Generate human-readable summary
     */
    private function generateSummary(int $targetsMet, int $productPoints, array $multiProductBonus): string
    {
        $lines = [];
        $lines[] = "Performance: {$targetsMet}/4 product targets met (" . round(($targetsMet / 4) * 100) . "%)";

        if ($targetsMet === 4) {
            $lines[] = '🏆 GRAND SLAM - All products met targets!';
        } elseif ($targetsMet === 3) {
            $lines[] = '⭐ EXCELLENT - 3 out of 4 targets achieved';
        } elseif ($targetsMet === 2) {
            $lines[] = '👍 GOOD - 2 out of 4 targets achieved';
        } else {
            $lines[] = '📈 NEEDS IMPROVEMENT - Focus on target products';
        }

        $lines[] = "Points: {$productPoints} (products) + {$multiProductBonus['points']} (bonus) = " . ($productPoints + $multiProductBonus['points']) . ' total';

        return implode(' | ', $lines);
    }

    /**
     * Get target information for a product
     */
    public function getProductTargetInfo(string $product): ?array
    {
        if (!isset(self::TARGETS[$product])) {
            return [];
        }

        return [
            'product' => $product,
            'minimum_target' => self::TARGETS[$product],
            'base_points' => self::BASE_POINTS[$product],
            'bonus_threshold' => self::BONUS_THRESHOLDS[$product]['threshold'],
            'bonus_points' => self::BONUS_THRESHOLDS[$product]['points'],
        ];
    }

    /**
     * Get all product targets
     */
    public function getAllProductTargets(): array
    {
        $targets = [];
        foreach (array_keys(self::TARGETS) as $product) {
            $targets[$product] = $this->getProductTargetInfo($product);
        }
        return $targets;
    }
}
