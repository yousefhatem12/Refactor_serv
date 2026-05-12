<?php

$resources = [
    'ServiceResource.php' => ['group' => 'Business Settings', 'icon' => 'heroicon-o-briefcase', 'sort' => 1],
    'FeatureResource.php' => ['group' => 'Business Settings', 'icon' => 'heroicon-o-star', 'sort' => 2],
    'StatisticResource.php' => ['group' => 'Business Settings', 'icon' => 'heroicon-o-chart-pie', 'sort' => 3],

    'ProjectResource.php' => ['group' => 'Portfolio & Team', 'icon' => 'heroicon-o-rectangle-group', 'sort' => 1],
    'TeamMemberResource.php' => ['group' => 'Portfolio & Team', 'icon' => 'heroicon-o-users', 'sort' => 2],
    'TestimonialResource.php' => ['group' => 'Portfolio & Team', 'icon' => 'heroicon-o-chat-bubble-left-right', 'sort' => 3],

    'PostResource.php' => ['group' => 'Content & Pages', 'icon' => 'heroicon-o-document-text', 'sort' => 1],
    'PageResource.php' => ['group' => 'Content & Pages', 'icon' => 'heroicon-o-document-duplicate', 'sort' => 2],
    'FaqResource.php' => ['group' => 'Content & Pages', 'icon' => 'heroicon-o-question-mark-circle', 'sort' => 3],

    'NavLinkResource.php' => ['group' => 'Site Configuration', 'icon' => 'heroicon-o-bars-3-bottom-left', 'sort' => 1],
    'FooterLinkResource.php' => ['group' => 'Site Configuration', 'icon' => 'heroicon-o-link', 'sort' => 2],
    'SocialLinkResource.php' => ['group' => 'Site Configuration', 'icon' => 'heroicon-o-share', 'sort' => 3],

    'UserResource.php' => ['group' => 'System & Security', 'icon' => 'heroicon-o-shield-check', 'sort' => 1],
    'SettingResource.php' => ['group' => 'System & Security', 'icon' => 'heroicon-o-cog-6-tooth', 'sort' => 2],

    'ContactMessageResource.php' => ['group' => 'Support', 'icon' => 'heroicon-o-envelope-open', 'sort' => 1],
];

function updateResource($file, $group, $icon, $sort) {
    if (!file_exists($file)) return false;
    $content = file_get_contents($file);

    // Remove existing navigationIcon
    $content = preg_replace('/protected static \??(?:string\|\\\\?(?:BackedEnum|UnitEnum)\|null|string) \$navigationIcon\s*=[^;]+;/s', '', $content);
    
    // Remove existing navigationGroup
    $content = preg_replace('/protected static \??(?:string\|\\\\?(?:BackedEnum|UnitEnum)\|null|string) \$navigationGroup\s*=[^;]+;/s', '', $content);
    
    // Remove existing navigationSort
    $content = preg_replace('/protected static \??int \$navigationSort\s*=[^;]+;/s', '', $content);
    
    // Clean up extra blank lines
    $content = preg_replace('/(\n\s*){3,}/', "\n\n", $content);

    // Insert all three right after $model
    $props = "\n    protected static string|\\\\BackedEnum|null \$navigationIcon = '$icon';\n    protected static string|\\\\UnitEnum|null \$navigationGroup = '$group';\n    protected static ?int \$navigationSort = $sort;";
    $content = preg_replace('/(protected static \?string \$model = [^;]+;)/', "$1$props", $content);

    file_put_contents($file, $content);
    return true;
}

$dir = new RecursiveIteratorIterator(new RecursiveDirectoryIterator(__DIR__ . '/app/Filament/Resources'));
foreach ($dir as $file) {
    if ($file->isFile() && pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $filename = $file->getFilename();
        if (isset($resources[$filename])) {
            updateResource($file->getPathname(), $resources[$filename]['group'], $resources[$filename]['icon'], $resources[$filename]['sort']);
            echo "Updated $filename\n";
        }
    }
}
echo "Done.\n";
