<?php

namespace Zhandos717\MoonshineMonitoring\Support;

class Format
{
    /**
     * Format bytes to human readable format
     *
     * @param int $bytes
     * @param int $precision
     * @return string
     */
    public static function bytes(int $bytes, int $precision = 2): string
    {
        if ($bytes === 0) {
            return '0 Bytes';
        }

        $units = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
        $base = log($bytes, 1024);
        $floor = floor($base);
        $pow = pow(1024, $floor);
        $value = round($bytes / $pow, $precision);
        
        return sprintf('%.' . $precision . 'f %s', $value, $units[(int)$floor]);
    }

    /**
     * Format percentage with specified precision
     *
     * @param float|null $value
     * @param int $precision
     * @return string
     */
    public static function percentage(?float $value, int $precision = 2): string
    {
        if ($value === null) {
            return 'N/A';
        }

        return sprintf('%.' . $precision . 'f%%', $value);
    }

    /**
     * Format CPU cores information
     *
     * @param float|null $usage
     * @param int|null $cores
     * @return string
     */
    public static function cpu(?float $usage, ?int $cores = null): string
    {
        if ($usage === null) {
            return 'N/A';
        }

        $result = self::percentage($usage, 2);
        
        if ($cores) {
            $result .= " ({$cores} " . (($cores === 1) ? 'core' : 'cores') . ")";
        }

        return $result;
    }

    /**
     * Format memory information with total memory
     *
     * @param float|null $usage Percentage
     * @param int|null $totalBytes Total memory in bytes
     * @return string
     */
    public static function memory(?float $usage, ?int $totalBytes = null): string
    {
        if ($usage === null) {
            return 'N/A';
        }

        $result = self::percentage($usage, 2);
        
        if ($totalBytes) {
            $usedBytes = (int)(($usage / 100) * $totalBytes);
            $result .= ' (' . self::bytes($usedBytes) . ' / ' . self::bytes($totalBytes) . ')';
        }

        return $result;
    }

    /**
     * Format disk information with total space
     *
     * @param float|null $usage Percentage
     * @param int|null $totalBytes Total disk space in bytes
     * @return string
     */
    public static function disk(?float $usage, ?int $totalBytes = null): string
    {
        if ($usage === null) {
            return 'N/A';
        }

        $result = self::percentage($usage, 2);
        
        if ($totalBytes) {
            $usedBytes = (int)(($usage / 100) * $totalBytes);
            $result .= ' (' . self::bytes($usedBytes) . ' / ' . self::bytes($totalBytes) . ')';
        }

        return $result;
    }
}