<?php

namespace Database\Seeders;

use App\Models\Player;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class PlayerSeeder extends Seeder
{
    public function run(): void
    {
        $csvFile = database_path('seeders/InazumaVRDB.csv');

        if (!File::exists($csvFile)) {
            $this->command->error('CSV file not found!');
            return;
        }

        $file = fopen($csvFile, 'r');

        // Skip header
        $header = fgetcsv($file);

        // Trouver les indices des colonnes qui nous intéressent
        $idIndex = array_search('ID', $header);
        $nameIndex = array_search('🇬🇧 Name', $header);
        $nicknameIndex = array_search('🇬🇧 Nickname', $header);
        $positionIndex = array_search('Position', $header);
        $elementIndex = array_search('Élément', $header);
        $rarityIndex = array_search('Rareté', $header);
        $teamIndex = array_search('École/Club/Équipe', $header);
        $descIndex = array_search('Description', $header);
        $urlIndex = array_search('url', $header);

        // Hissatsu 

        $skill1Index = array_search('🇬🇧 Skill lvl 1', $header);
        $skill2Index = array_search('🇬🇧 Skill lvl 13', $header);
        $skill3Index = array_search('🇬🇧 Skill lvl 20', $header);
        $skill4Index = array_search('🇬🇧 Skill lvl 30', $header);

        // Stats normalisées (niveau 50)
        $kickIndex = array_search('N Kick', $header);
        $ctrlIndex = array_search('N Ctrl', $header);
        $techIndex = array_search('N Tech', $header);
        $intlIndex = array_search('N Intl', $header);
        $presIndex = array_search('N Pres', $header);
        $physIndex = array_search('N Phys', $header);
        $agilIndex = array_search('N Agil', $header);
        $totalIndex = array_search('N Total', $header);

        $count = 0;

        while (($row = fgetcsv($file)) !== false) {
            // Ignorer les lignes vides ou invalides
            if (empty($row[$idIndex]) || !is_numeric($row[$idIndex])) {
                continue;
            }

            Player::create([
                'player_id' => (int) $row[$idIndex],
                'name' => $row[$nameIndex] ?? 'Unknown',
                'nickname' => $row[$nicknameIndex] ?? null,
                'position' => $row[$positionIndex] ?? 'MF',
                'element' => $row[$elementIndex] ?? 'Fire',
                'rarity' => $row[$rarityIndex] ?? null,
                'team_origin' => $row[$teamIndex] ?? null,
                'description' => $row[$descIndex] ?? null,
                'kick' => !empty($row[$kickIndex]) ? (int) $row[$kickIndex] : null,
                'control' => !empty($row[$ctrlIndex]) ? (int) $row[$ctrlIndex] : null,
                'technique' => !empty($row[$techIndex]) ? (int) $row[$techIndex] : null,
                'intelligence' => !empty($row[$intlIndex]) ? (int) $row[$intlIndex] : null,
                'pressure' => !empty($row[$presIndex]) ? (int) $row[$presIndex] : null,
                'physical' => !empty($row[$physIndex]) ? (int) $row[$physIndex] : null,
                'agility' => !empty($row[$agilIndex]) ? (int) $row[$agilIndex] : null,
                'total' => !empty($row[$totalIndex]) ? (int) $row[$totalIndex] : null,
                'image_url' => $row[$urlIndex] ?? null,
                'skill_1' => $row[$skill1Index] ?? null,
                'skill_2' => $row[$skill2Index] ?? null,
                'skill_3' => $row[$skill3Index] ?? null,
                'skill_4' => $row[$skill4Index] ?? null,
            ]);

            $count++;
        }

        fclose($file);

        $this->command->info("Imported {$count} players successfully!");
    }
}
