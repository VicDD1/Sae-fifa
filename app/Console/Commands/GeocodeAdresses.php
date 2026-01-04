<?php

use App\Models\Adresse;
use Illuminate\Support\Facades\Http;

class GeocodeAdresses extends Command
{
    protected $signature = 'adresses:geocode';
    protected $description = 'Geocode adresses without coordinates';

    public function handle()
    {
        Adresse::whereNull('latitude')
            ->orWhereNull('longitude')
            ->chunk(50, function ($adresses) {
                foreach ($adresses as $adresse) {

                    $response = Http::get(
                        'https://nominatim.openstreetmap.org/search',
                        [
                            'q' => $adresse->full_address,
                            'format' => 'json',
                            'limit' => 1,
                        ]
                    );

                    if ($response->successful() && count($response->json()) > 0) {
                        $data = $response->json()[0];

                        $adresse->update([
                            'latitude' => $data['lat'],
                            'longitude' => $data['lon'],
                        ]);

                        $this->info("✔ {$adresse->full_address}");
                    }

                    sleep(1); // IMPORTANT: respect API rate limit
                }
            });
    }
    
}
