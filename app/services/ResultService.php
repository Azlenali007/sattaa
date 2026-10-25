<?php
/**
 * Result Domain Service
 */

class ResultService {
    public static function computeMarketStatus(array $market): string {
        if (empty($market['status']) || $market['status'] !== 'active') {
            return 'Closed';
        }
        if (!empty($market['is_live'])) {
            return 'Live';
        }
        return 'Upcoming';
    }

    public static function formatDisplayBadge(string $status): array {
        switch (strtolower($status)) {
            case 'live':
            case 'open':
                return [
                    'label' => $status,
                    'classes' => 'bg-emerald-50 text-emerald-600 border border-emerald-200'
                ];
            case 'upcoming':
                return [
                    'label' => 'Upcoming',
                    'classes' => 'bg-purple-50 text-purple-700 border border-purple-200'
                ];
            default:
                return [
                    'label' => 'Closed',
                    'classes' => 'bg-slate-100 text-slate-500 border border-slate-200'
                ];
        }
    }
}
