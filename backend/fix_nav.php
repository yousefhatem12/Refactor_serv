<?php

$map = [
    'app/Filament/Pages/ManageHomePage.php' => ['Home Page', 1, 'heroicon-o-home'],
    'app/Filament/Pages/ManageAboutPage.php' => ['About Us', 2, 'heroicon-o-information-circle'],
    'app/Filament/Pages/ManageHeader.php' => ['Header', 3, 'heroicon-o-bars-3'],
    'app/Filament/Pages/ManageFooter.php' => ['Footer', 4, 'heroicon-o-bars-3-bottom-right'],
    'app/Filament/Resources/ServiceResource.php' => ['Services', 5, 'heroicon-o-briefcase'],
    'app/Filament/Resources/ProjectResource.php' => ['Our Work', 6, 'heroicon-o-folder-open'],
    'app/Filament/Resources/Testimonials/TestimonialResource.php' => ['Testimonials', 7, 'heroicon-o-chat-bubble-left-right'],
    'app/Filament/Resources/Pages/PageResource.php' => ['Site Pages', 8, 'heroicon-o-document-duplicate'],
    'app/Filament/Resources/PostResource.php' => ['Blog', 9, 'heroicon-o-newspaper'],
    'app/Filament/Pages/ManageSeoSettings.php' => ['SEO Settings', 10, 'heroicon-o-magnifying-glass'],
    'app/Filament/Pages/ManageMediaSettings.php' => ['Images & Media', 11, 'heroicon-o-photo'],
    'app/Filament/Resources/TeamMembers/TeamMemberResource.php' => ['Company Team', 12, 'heroicon-o-users'],
];

foreach ($map as $file => $config) {
    if (!file_exists($file)) {
        // If it's a page that doesn't exist, we skip or create a basic one. We will create it if it's a settings page.
        if (strpos($file, 'Pages/Manage') !== false) {
            $className = basename($file, '.php');
            $content = "<?php\nnamespace App\Filament\Pages;\nuse Filament\Pages\Page;\nclass {$className} extends Page\n{\n    protected static string \$view = 'filament.pages.empty';\n}\n";
            file_put_contents($file, $content);
            // Also create a dummy view
            $viewDir = 'resources/views/filament/pages';
            if (!is_dir($viewDir)) mkdir($viewDir, 0755, true);
            file_put_contents($viewDir.'/empty.blade.php', '<x-filament::page><div>Coming Soon</div></x-filament::page>');
        } else {
            echo "File not found: $file\n";
            continue;
        }
    }
    
    $content = file_get_contents($file);
    
    // Remove existing navigation properties
    $content = preg_replace('/protected static \??string.*?\$navigationLabel.*?;\n/', '', $content);
    $content = preg_replace('/protected static string\\\\\|\\\\BackedEnum\\\\\|null \$navigationGroup.*?;\n/', '', $content);
    $content = preg_replace('/protected static string\\\\\|\\\\UnitEnum\\\\\|null \$navigationGroup.*?;\n/', '', $content);
    $content = preg_replace('/protected static \??string.*?\$navigationGroup.*?;\n/', '', $content);
    $content = preg_replace('/protected static \??int.*?\$navigationSort.*?;\n/', '', $content);
    $content = preg_replace('/protected static string\\\\\|\\\\BackedEnum\\\\\|null \$navigationIcon.*?;\n/', '', $content);
    $content = preg_replace('/protected static \??string.*?\$navigationIcon.*?;\n/', '', $content);
    $content = preg_replace('/public static function getNavigationLabel\(\): string \{ return __\(\'.*?\'\); \}\n/', '', $content);
    $content = preg_replace('/public static function getPluralModelLabel\(\): string\s*\{\s*return __\(\'.*?\'\);\s*\}/s', '', $content);
    
    // Create new ones
    $label = $config[0];
    $sort = $config[1];
    $icon = $config[2];
    
    $insert = "    protected static ?string \$navigationGroup = null;\n";
    $insert .= "    protected static ?int \$navigationSort = $sort;\n";
    $insert .= "    protected static ?string \$navigationIcon = '$icon';\n";
    $insert .= "    public static function getNavigationLabel(): string { return __('$label'); }\n";
    if (strpos($file, 'Resource') !== false) {
        $insert .= "    public static function getPluralModelLabel(): string { return __('$label'); }\n";
    }
    
    // Insert into class
    $content = preg_replace('/(class\s+[a-zA-Z0-9_]+\s+extends\s+[a-zA-Z0-9_]+[^{]*\{)/', "$1\n$insert", $content);
    
    file_put_contents($file, $content);
    echo "Updated $file\n";
}
