<?php

namespace Database\Seeders;

use App\Models\Checklist;
use Illuminate\Database\Seeder;

class ChecklistSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            "Pajangan penuh dan bersih lorong 1",
            "Pajangan penuh dan bersih lorong 2",
            "Pajangan penuh dan bersih lorong 3",
            "Pajangan penuh dan bersih lorong 4",
            "Pajangan penuh dan bersih lorong 10",
            "Pajangan penuh dan bersih lorong 11",
            "Pajangan penuh dan bersih lorong 12",
            "Pajangan penuh dan bersih lorong 13",
            "Pajangan penuh dan bersih lorong 14",
            "Pajangan penuh chiller yoa",
            "Pajangan penuh chiller area fresh",
            "Pajangan penuh flooran beras",
            "Pajangan penuh flooran gula",
            "Pajangan penuh flooran tepung",
            "Pajangan penuh flooran B1G1",
            "Pajangan penuh wingstack",
            "Pajangan penuh end gondola",
            "Pajangan penuh area tematik",
            "Pajangan penuh rak promo",
            "Pajangan penuh galon lemineral",
            "Pajangan rapi dan penuh area coc kassa 5,7,8"
        ];

        foreach ($items as $item) {
            Checklist::create([
                'item' => $item,
                'status' => null,
                'komentar' => '',
                'foto' => null
            ]);
        }
    }
}