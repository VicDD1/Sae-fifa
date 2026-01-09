<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Adresse;
use Illuminate\Support\Facades\Http;

class GeocodeAdresses extends Command
{
    protected $signature = 'adresses:geocode';
    protected $description = 'Geocode adresses without coordinates';

    public function handle()
    {
        Adresse::where(function ($q) {
            $q->whereNull('latitude')
              ->orWhereNull('longitude');
        })
        ->orderBy('id_adresse')
        ->chunkById(50, function ($adresses) {

            foreach ($adresses as $adresse) {

                $query = implode(', ', array_filter([
                    $adresse->ville_adresse,
                    $adresse->code_postal,
                    $adresse->pays_adresse,
                ]));

                if (!$query) {
                    continue;
                }

                $response = Http::withHeaders([
                    'User-Agent' => 'LaravelApp/1.0 (contact@example.com)'
                ])->get('https://nominatim.openstreetmap.org/search', [
                    'q' => $query,
                    'format' => 'json',
                    'limit' => 1,
                ]);

                if ($response->successful() && !empty($response->json())) {
                    $data = $response->json()[0];

                    $adresse->update([
                        'latitude'  => (float) $data['lat'],
                        'longitude' => (float) $data['lon'],
                    ]);

                    $this->info("✔ {$query}");
                }

                sleep(1); // respect Nominatim rate limit
            }
        });
    }
}
