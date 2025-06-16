<?php
// models/Weather.php

class Weather {
    // REMPLACEZ 'VOTRE_CLE_API' PAR LA VRAIE CLÉ OBTENUE SUR OPENWEATHERMAP
    private const API_KEY = '69a513d2c5acb77dc352fe7e8e9e4ac8';
    private const API_URL = 'https://api.openweathermap.org/data/2.5/weather';

    /**
     * Récupère la météo actuelle pour une ville donnée.
     * @param string $city La ville pour laquelle récupérer la météo.
     * @return array|null Les données météo ou null en cas d'erreur.
     */
    public function getCurrentWeather($city) {
        $queryParams = http_build_query([
            'q' => $city,
            'appid' => self::API_KEY,
            'units' => 'metric', // Pour avoir les températures en Celsius
            'lang' => 'fr'       // Pour avoir les descriptions en français
        ]);
        
        $url = self::API_URL . '?' . $queryParams;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && $response) {
            $data = json_decode($response, true);
            
            // On vérifie que toutes les données attendues sont présentes
            if (isset($data['main']['temp'], $data['weather'][0], $data['main']['humidity'], $data['wind']['speed'])) {
                return [
                    'temperature' => $data['main']['temp'],
                    'description' => ucfirst($data['weather'][0]['description']),
                    'icon' => $data['weather'][0]['icon'],
                    'humidity' => $data['main']['humidity'],
                    // On convertit la vitesse du vent de m/s en km/h
                    'wind_speed' => round($data['wind']['speed'] * 3.6),
                    'pressure' => $data['main']['pressure']
                ];
            }
        }
        
        error_log("Erreur lors de l'appel à l'API météo. Code HTTP: " . $httpCode);
        return null;
    }
}
