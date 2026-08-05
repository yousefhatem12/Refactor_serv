<?php

namespace Tests\Unit;

use App\Support\AboutPagePeople;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class AboutPagePeopleTest extends TestCase
{
    #[Test]
    public function it_extracts_translated_fields_and_uses_one_set_of_shared_fields(): void
    {
        $sections = $this->sections();
        $sections['en'][0]['data']['items'][0]['image'] = null;

        $people = AboutPagePeople::extract($sections);

        $this->assertSame('John Doe', $people['founders'][0]['name_en']);
        $this->assertSame('جون دو', $people['founders'][0]['name_ar']);
        $this->assertSame('founder-ar.webp', $people['founders'][0]['image']);
        $this->assertSame('Developer', $people['team'][0]['role_en']);
        $this->assertSame('مطور', $people['team'][0]['role_ar']);
        $this->assertSame('https://github.com/john', $people['team'][0]['github']);
    }

    #[Test]
    public function it_syncs_shared_media_and_social_accounts_to_both_languages(): void
    {
        $sections = AboutPagePeople::sync($this->sections(), [
            'founders' => [[
                'name_en' => 'Jane Doe',
                'name_ar' => 'جين دو',
                'role_en' => 'Founder',
                'role_ar' => 'مؤسسة',
                'image' => 'pages/founder-shared.webp',
            ]],
            'team' => [[
                'name_en' => 'Jane Doe',
                'name_ar' => 'جين دو',
                'role_en' => 'Designer',
                'role_ar' => 'مصممة',
                'dept' => 'design',
                'image' => 'pages/team-shared.webp',
                'linkedin' => 'https://linkedin.com/in/jane',
                'github' => 'https://github.com/jane',
                'twitter' => 'https://x.com/jane',
            ]],
        ]);

        $englishFounder = $sections['en'][0]['data']['items'][0];
        $arabicFounder = $sections['ar'][0]['data']['items'][0];
        $englishMember = $sections['en'][1]['data']['members'][0];
        $arabicMember = $sections['ar'][1]['data']['members'][0];

        $this->assertSame('Jane Doe', $englishFounder['name']);
        $this->assertSame('جين دو', $arabicFounder['name']);
        $this->assertSame($englishFounder['image'], $arabicFounder['image']);
        $this->assertSame('Designer', $englishMember['role']);
        $this->assertSame('مصممة', $arabicMember['role']);
        $this->assertSame($englishMember['image'], $arabicMember['image']);
        $this->assertSame($englishMember['linkedin'], $arabicMember['linkedin']);
        $this->assertSame($englishMember['github'], $arabicMember['github']);
        $this->assertSame($englishMember['twitter'], $arabicMember['twitter']);
        $this->assertSame('design', $arabicMember['dept']);
    }

    private function sections(): array
    {
        return [
            'en' => [
                [
                    'type' => 'team',
                    'data' => [
                        'title' => 'Founders',
                        'items' => [[
                            'name' => 'John Doe',
                            'role' => 'Founder',
                            'image' => 'founder-en.webp',
                        ]],
                    ],
                ],
                [
                    'type' => 'team_work',
                    'data' => [
                        'title' => 'Team',
                        'members' => [[
                            'name' => 'John Doe',
                            'role' => 'Developer',
                            'dept' => 'development',
                            'image' => 'team-en.webp',
                            'linkedin' => 'https://linkedin.com/in/john',
                            'github' => 'https://github.com/john',
                            'twitter' => 'https://x.com/john',
                        ]],
                    ],
                ],
            ],
            'ar' => [
                [
                    'type' => 'team',
                    'data' => [
                        'title' => 'المؤسسون',
                        'items' => [[
                            'name' => 'جون دو',
                            'role' => 'مؤسس',
                            'image' => 'founder-ar.webp',
                        ]],
                    ],
                ],
                [
                    'type' => 'team_work',
                    'data' => [
                        'title' => 'الفريق',
                        'members' => [[
                            'name' => 'جون دو',
                            'role' => 'مطور',
                            'dept' => 'design',
                            'image' => 'team-ar.webp',
                            'linkedin' => 'https://linkedin.com/in/arabic-john',
                            'github' => 'https://github.com/arabic-john',
                            'twitter' => 'https://x.com/arabic-john',
                        ]],
                    ],
                ],
            ],
        ];
    }
}
