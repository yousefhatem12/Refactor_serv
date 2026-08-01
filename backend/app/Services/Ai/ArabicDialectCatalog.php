<?php

namespace App\Services\Ai;

class ArabicDialectCatalog
{
    public static function options(bool $isArabicUi = false): array
    {
        $options = [];
        foreach (self::definitions() as $key => $definition) {
            $label = $definition[$isArabicUi ? 'ar' : 'en'];
            if ($key === 'egyptian') {
                $label .= $isArabicUi ? ' (افتراضي)' : ' (Default)';
            }
            $options[$key] = $label;
        }
        return $options;
    }

    public static function instruction(string $dialect): string
    {
        $definition = self::definitions()[$dialect] ?? self::definitions()['egyptian'];
        return $definition['instruction'];
    }

    public static function normalize(string $dialect): string
    {
        return array_key_exists($dialect, self::definitions()) ? $dialect : 'egyptian';
    }

    private static function definitions(): array
    {
        return [
            'egyptian' => ['ar' => 'المصرية', 'en' => 'Egyptian', 'instruction' => 'natural professional Egyptian Arabic for readers in Egypt'],
            'msa' => ['ar' => 'العربية الفصحى المعاصرة', 'en' => 'Modern Standard Arabic', 'instruction' => 'clear contemporary Modern Standard Arabic with no regional colloquialisms'],
            'saudi' => ['ar' => 'السعودية العامة', 'en' => 'Saudi (General)', 'instruction' => 'natural professional Saudi Arabic that is broadly understood across Saudi Arabia'],
            'hijazi' => ['ar' => 'الحجازية', 'en' => 'Hijazi', 'instruction' => 'natural professional Hijazi Arabic used around Jeddah, Makkah, and Madinah'],
            'najdi' => ['ar' => 'النجدية', 'en' => 'Najdi', 'instruction' => 'natural professional Najdi Arabic used in central Saudi Arabia'],
            'saudi_southern' => ['ar' => 'السعودية الجنوبية', 'en' => 'Southern Saudi', 'instruction' => 'natural professional Southern Saudi Arabic without exaggerated local slang'],
            'saudi_eastern' => ['ar' => 'السعودية الشرقية', 'en' => 'Eastern Saudi', 'instruction' => 'natural professional Eastern Saudi Arabic without exaggerated local slang'],
            'gulf' => ['ar' => 'الخليجية العامة', 'en' => 'Gulf Arabic', 'instruction' => 'natural professional Gulf Arabic understood across GCC audiences'],
            'emirati' => ['ar' => 'الإماراتية', 'en' => 'Emirati', 'instruction' => 'natural professional Emirati Arabic for readers in the UAE'],
            'kuwaiti' => ['ar' => 'الكويتية', 'en' => 'Kuwaiti', 'instruction' => 'natural professional Kuwaiti Arabic for readers in Kuwait'],
            'qatari' => ['ar' => 'القطرية', 'en' => 'Qatari', 'instruction' => 'natural professional Qatari Arabic for readers in Qatar'],
            'bahraini' => ['ar' => 'البحرينية', 'en' => 'Bahraini', 'instruction' => 'natural professional Bahraini Arabic for readers in Bahrain'],
            'omani' => ['ar' => 'العُمانية', 'en' => 'Omani', 'instruction' => 'natural professional Omani Arabic for readers in Oman'],
            'yemeni' => ['ar' => 'اليمنية', 'en' => 'Yemeni', 'instruction' => 'natural professional Yemeni Arabic without overly local or obscure vocabulary'],
            'iraqi' => ['ar' => 'العراقية', 'en' => 'Iraqi', 'instruction' => 'natural professional Iraqi Arabic for readers in Iraq'],
            'levantine' => ['ar' => 'الشامية العامة', 'en' => 'Levantine Arabic', 'instruction' => 'natural professional Levantine Arabic understood across Syria, Lebanon, Jordan, and Palestine'],
            'syrian' => ['ar' => 'السورية', 'en' => 'Syrian', 'instruction' => 'natural professional Syrian Arabic for readers in Syria'],
            'lebanese' => ['ar' => 'اللبنانية', 'en' => 'Lebanese', 'instruction' => 'natural professional Lebanese Arabic for readers in Lebanon'],
            'jordanian' => ['ar' => 'الأردنية', 'en' => 'Jordanian', 'instruction' => 'natural professional Jordanian Arabic for readers in Jordan'],
            'palestinian' => ['ar' => 'الفلسطينية', 'en' => 'Palestinian', 'instruction' => 'natural professional Palestinian Arabic for Palestinian readers'],
            'sudanese' => ['ar' => 'السودانية', 'en' => 'Sudanese', 'instruction' => 'natural professional Sudanese Arabic for readers in Sudan'],
            'libyan' => ['ar' => 'الليبية', 'en' => 'Libyan', 'instruction' => 'natural professional Libyan Arabic for readers in Libya'],
            'maghrebi' => ['ar' => 'المغاربية العامة', 'en' => 'Maghrebi Arabic', 'instruction' => 'accessible professional Maghrebi Arabic understood across North-West Africa'],
            'moroccan' => ['ar' => 'المغربية (الدارجة)', 'en' => 'Moroccan Darija', 'instruction' => 'natural professional Moroccan Darija while keeping technical terms clear'],
            'algerian' => ['ar' => 'الجزائرية', 'en' => 'Algerian', 'instruction' => 'natural professional Algerian Arabic while keeping technical terms clear'],
            'tunisian' => ['ar' => 'التونسية', 'en' => 'Tunisian', 'instruction' => 'natural professional Tunisian Arabic while keeping technical terms clear'],
            'mauritanian' => ['ar' => 'الموريتانية (الحسانية)', 'en' => 'Mauritanian Hassaniya', 'instruction' => 'accessible professional Mauritanian Hassaniya Arabic while keeping technical terms clear'],
            'somali_arabic' => ['ar' => 'العربية الصومالية', 'en' => 'Somali Arabic', 'instruction' => 'clear professional Arabic adapted for Arabic-speaking readers in Somalia'],
            'djiboutian_arabic' => ['ar' => 'العربية الجيبوتية', 'en' => 'Djiboutian Arabic', 'instruction' => 'clear professional Arabic adapted for Arabic-speaking readers in Djibouti'],
            'comorian_arabic' => ['ar' => 'العربية القمرية', 'en' => 'Comorian Arabic', 'instruction' => 'clear professional Arabic adapted for Arabic-speaking readers in Comoros'],
        ];
    }
}
