<?php

namespace SineFine\RobloxApi\Infrastructure\Shortcode;

use Exception;
use SineFine\RobloxApi\Data\Source\IDataSource;

class ShortcodeProcessor
{
    private const ARG_MAPPING = [
        'userid' => 'user_id',
        'groupid' => 'group_id',
        'assetid' => 'asset_id',
        'universeid' => 'universe_id',
        'placeid' => 'place_id',
        'badgeid' => 'badge_id',
        'roleid' => 'role_id',
    ];

    /**
     * @param IDataSource $dataSource
     * @param array<string, mixed> $attrs
     * @return string
     */
    public function process(IDataSource $dataSource, array $attrs): string
    {
        try {
            $argSpec = $dataSource->getArgumentSpecification();
            $requiredArgs = [];
            
            foreach ($argSpec->requiredArgs as $index => $argName) {
                $key = strtolower($argName);
                
                if (isset($attrs[$key])) {
                    $requiredArgs[] = $attrs[$key];
                } elseif (isset($attrs[$index])) {
                    $requiredArgs[] = $attrs[$index];
                } else {
                    $mappedKey = self::ARG_MAPPING[$key] ?? null;
                    if ($mappedKey && isset($attrs[$mappedKey])) {
                        $requiredArgs[] = $attrs[$mappedKey];
                    } else {
                        return sprintf("Error: Required argument '%s' (attr: '%s') is missing", $argName, $key);
                    }
                }
            }

            $optionalArgs = [];
            foreach ($argSpec->optionalArgs as $argName => $type) {
                if (isset($attrs[$argName])) {
                    $optionalArgs[$argName] = $attrs[$argName];
                }
            }
            
            $result = $dataSource->exec($requiredArgs, $optionalArgs);

            if (is_array($result) || is_object($result)) {
                return json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            }

            return (string)$result;

        } catch (Exception $e) {
            return 'Error: ' . $e->getMessage();
        }
    }
}
