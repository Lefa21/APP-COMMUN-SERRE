<?php
// simulate_microcontroller.php
// Script pour simuler les données provenant du microcontrôleur TIVA

require_once 'config/Database.php';

class MicrocontrollerSimulator {
    private $apiUrl;
    private $apiKey;
    private $sensors;
    private $db;
    
    public function __construct() {
        $this->apiUrl = 'http://localhost/APP-COMMUN-SERRE/';
        $this->apiKey = 'serre_team_1_key_2025'; // Clé API pour les tests
        $this->db = Database::getInstance()->getConnection();
        $this->loadSensors();
    }
    
    /**
     * Charger la liste des capteurs depuis la base
     */
    private function loadSensors() {
        $stmt = $this->db->query("
            SELECT id, nom as name, type, unite as unit, team_id 
            FROM capteurs 
            WHERE COALESCE(is_active, 1) = 1
        ");
        $this->sensors = $stmt->fetchAll();
    }
    
    /**
     * Simulation en temps réel (boucle infinie)
     */
    public function simulateRealtimeData() {
        echo "🌱 Simulation des données de serre en temps réel...\n";
        echo "Capteurs trouvés: " . count($this->sensors) . "\n";
        echo "API URL: " . $this->apiUrl . "\n";
        echo "Appuyez sur Ctrl+C pour arrêter\n\n";
        
        $iteration = 0;
        while (true) {
            $iteration++;
            $timestamp = date('Y-m-d H:i:s');
            echo "--- Cycle #{$iteration} - {$timestamp} ---\n";
            
            foreach ($this->sensors as $sensor) {
                $value = $this->generateRealisticValue($sensor);
                $success = $this->sendDataToAPI($sensor['id'], $value);
                
                $status = $success ? '✅' : '❌';
                $alerts = $this->checkLocalAlerts($sensor['type'], $value);
                $alertStr = $alerts ? ' [ALERTE: ' . implode(', ', $alerts) . ']' : '';
                
                echo sprintf(
                    "%s %s: %.2f %s %s%s\n",
                    $status,
                    $sensor['name'],
                    $value,
                    $sensor['unit'],
                    $this->getValueStatus($sensor['type'], $value),
                    $alertStr
                );
            }
            
            echo "\nProchaine mesure dans 30 secondes...\n\n";
            sleep(30); // Envoi toutes les 30 secondes
        }
    }
    
    /**
     * Générer des données historiques
     */
    public function generateBatchData($hours = 24) {
        echo "🌱 Génération de données historiques pour {$hours}h...\n";
        
        $totalGenerated = 0;
        $startTime = time() - ($hours * 3600);
        $interval = 300; // 5 minutes
        
        echo "Période: " . date('Y-m-d H:i', $startTime) . " à " . date('Y-m-d H:i') . "\n";
        echo "Intervalle: {$interval} secondes\n\n";
        
        for ($time = $startTime; $time <= time(); $time += $interval) {
            $timestamp = date('Y-m-d H:i:s', $time);
            
            foreach ($this->sensors as $sensor) {
                $value = $this->generateRealisticValue($sensor, $time);
                
                if ($this->insertDirectToDB($sensor['id'], $value, $timestamp)) {
                    $totalGenerated++;
                }
            }
            
            // Afficher le progrès
            if ($totalGenerated % 50 == 0) {
                $progress = round((($time - $startTime) / ($hours * 3600)) * 100, 1);
                echo "Progrès: {$progress}% - {$totalGenerated} mesures générées\n";
            }
        }
        
        echo "\n✅ {$totalGenerated} mesures générées avec succès\n";
    }
    
    /**
     * Test de stress du système
     */
    public function stressTest($duration = 60) {
        echo "⚡ Test de stress du système pendant {$duration} secondes...\n\n";
        
        $startTime = time();
        $totalRequests = 0;
        $successCount = 0;
        $errorCount = 0;
        
        while ((time() - $startTime) < $duration) {
            foreach ($this->sensors as $sensor) {
                $value = $this->generateRealisticValue($sensor);
                $totalRequests++;
                
                if ($this->sendDataToAPI($sensor['id'], $value)) {
                    $successCount++;
                } else {
                    $errorCount++;
                }
                
                // Pause très courte entre les requêtes
                usleep(100000); // 0.1 seconde
            }
            
            // Afficher les stats toutes les 10 secondes
            if ($totalRequests % 50 == 0) {
                $elapsed = time() - $startTime;
                $rate = round($totalRequests / $elapsed, 2);
                echo "Temps: {$elapsed}s | Requêtes: {$totalRequests} | Taux: {$rate}/s | Succès: {$successCount} | Erreurs: {$errorCount}\n";
            }
        }
        
        $duration = time() - $startTime;
        $rate = round($totalRequests / $duration, 2);
        $successRate = round(($successCount / $totalRequests) * 100, 1);
        
        echo "\n📊 Résultats du test de stress:\n";
        echo "Durée: {$duration} secondes\n";
        echo "Requêtes totales: {$totalRequests}\n";
        echo "Taux moyen: {$rate} requêtes/seconde\n";
        echo "Taux de succès: {$successRate}%\n";
        echo "Erreurs: {$errorCount}\n";
    }
    
    /**
     * Générer une valeur réaliste selon le type de capteur
     */
    private function generateRealisticValue($sensor, $timestamp = null) {
        $hour = $timestamp ? date('H', $timestamp) : date('H');
        $minute = $timestamp ? date('i', $timestamp) : date('i');
        $baseValue = 20;
        $variation = 5;
        
        switch ($sensor['type']) {
            case 'temperature':
                // Variation journalière réaliste
                $baseValue = 18 + 10 * sin(($hour - 6) * M_PI / 12);
                // Variation saisonnière légère
                $month = date('n', $timestamp ?: time());
                $seasonalOffset = 3 * sin(($month - 3) * M_PI / 6);
                $baseValue += $seasonalOffset;
                $variation = 2;
                // Bruit aléatoire
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            case 'humidity':
                // Humidité plus élevée la nuit et tôt le matin
                $baseValue = 70 - 25 * sin(($hour - 6) * M_PI / 12);
                $variation = 8;
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            case 'soil_moisture':
                // Humidité du sol qui diminue progressivement
                $baseValue = 50;
                $hoursSinceWatering = $hour % 12; // Arrosage toutes les 12h
                $baseValue -= $hoursSinceWatering * 2; // Diminue de 2% par heure
                
                // Arrosage automatique si trop bas
                if ($baseValue < 30 && $hour >= 6 && $hour <= 18) {
                    $baseValue = 65; // Après arrosage
                }
                
                $variation = 5;
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            case 'light':
                // Simulation de luminosité selon l'heure
                if ($hour >= 6 && $hour <= 18) {
                    // Courbe de luminosité diurne
                    $baseValue = 200 + 800 * sin(($hour - 6) * M_PI / 12);
                    // Variation selon la météo simulée
                    $cloudiness = sin($minute * M_PI / 30) * 0.3; // Nuages simulés
                    $baseValue *= (1 - $cloudiness);
                } else {
                    // Éclairage artificiel la nuit (variable)
                    $baseValue = rand(10, 100);
                }
                $variation = $baseValue * 0.1; // 10% de variation
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            case 'ph':
                $baseValue = 6.5;
                $variation = 0.3;
                // Légère variation selon l'arrosage
                if ($hour % 6 == 0) { // Après arrosage
                    $baseValue += 0.2;
                }
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            case 'co2':
                // CO2 plus élevé la nuit (respiration des plantes)
                $baseValue = 400 + 150 * sin(($hour + 6) * M_PI / 12);
                // Ventilation réduit le CO2
                if ($hour >= 8 && $hour <= 16) {
                    $baseValue *= 0.8; // Ventilation active
                }
                $variation = 50;
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
                break;
                
            default:
                $value = $baseValue + (rand(-100, 100) / 100) * $variation;
        }
        
        // S'assurer que les valeurs restent dans des plages réalistes
        return max(0, round($value, 2));
    }
    
    /**
     * Envoyer des données via l'API
     */
    private function sendDataToAPI($sensorId, $value) {
        $url = $this->apiUrl . '?controller=api&action=addSensorData';
        
        $postData = [
            'sensor_id' => $sensorId,
            'value' => $value,
            'api_key' => $this->apiKey,
            'timestamp' => date('Y-m-d H:i:s')
        ];
        
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => http_build_query($postData),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_HTTPHEADER => [
                'API-KEY: ' . $this->apiKey,
                'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            return isset($data['success']) && $data['success'];
        }
        
        return false;
    }
    
    /**
     * Insérer directement en base (pour génération rapide)
     */
    private function insertDirectToDB($sensorId, $value, $timestamp) {
        try {
            $stmt = $this->db->prepare("
                INSERT INTO mesures (capteur_id, valeur, date_heure) 
                VALUES (?, ?, ?)
            ");
            return $stmt->execute([$sensorId, $value, $timestamp]);
        } catch (Exception $e) {
            return false;
        }
    }
    
    /**
     * Vérifier les alertes localement
     */
    private function checkLocalAlerts($type, $value) {
        $alerts = [];
        
        switch ($type) {
            case 'temperature':
                if ($value < 15) $alerts[] = 'FROID';
                if ($value > 35) $alerts[] = 'CHAUD';
                break;
            case 'humidity':
            case 'soil_moisture':
                if ($value < 25) $alerts[] = 'SEC';
                if ($value > 90) $alerts[] = 'HUMIDE';
                break;
            case 'ph':
                if ($value < 6.0 || $value > 7.5) $alerts[] = 'pH';
                break;
        }
        
        return $alerts;
    }
    
    /**
     * Obtenir le statut d'une valeur
     */
    private function getValueStatus($type, $value) {
        switch ($type) {
            case 'temperature':
                if ($value < 15 || $value > 35) return '🔴';
                if ($value < 18 || $value > 30) return '🟡';
                return '🟢';
            case 'humidity':
            case 'soil_moisture':
                if ($value < 30) return '🔴';
                if ($value > 90) return '🟡';
                return '🟢';
            default:
                return '🔵';
        }
    }
    
    /**
     * Test de connexion au système
     */
    public function testConnection() {
        echo "🔧 Test de connexion au système...\n\n";
        
        // Test API Health
        $healthUrl = $this->apiUrl . '?controller=api&action=health';
        $health = file_get_contents($healthUrl);
        $healthData = json_decode($health, true);
        
        echo "📡 Test de l'API Health:\n";
        if ($healthData && $healthData['status'] === 'healthy') {
            echo "✅ API accessible - Statut: " . $healthData['status'] . "\n";
            echo "🗄️ Base de données: " . $healthData['database'] . "\n";
            echo "📊 Capteurs actifs: " . $healthData['sensors']['active'] . "\n";
        } else {
            echo "❌ API non accessible ou en erreur\n";
            return false;
        }
        
        // Test d'envoi de données
        echo "\n📤 Test d'envoi de données:\n";
        if (!empty($this->sensors)) {
            $testSensor = $this->sensors[0];
            $testValue = $this->generateRealisticValue($testSensor);
            
            if ($this->sendDataToAPI($testSensor['id'], $testValue)) {
                echo "✅ Envoi de données réussi\n";
                echo "📊 Capteur: {$testSensor['name']}\n";
                echo "📈 Valeur: {$testValue} {$testSensor['unit']}\n";
            } else {
                echo "❌ Échec de l'envoi de données\n";
                return false;
            }
        }
        
        echo "\n🎉 Tous les tests sont passés avec succès !\n";
        return true;
    }
    
    /**
     * Afficher l'aide
     */
    public function showHelp() {
        echo "🌱 Simulateur de Microcontrôleur TIVA - Serre Connectée\n\n";
        echo "Usage: php simulate_microcontroller.php [commande] [options]\n\n";
        echo "Commandes disponibles:\n";
        echo "  realtime          Simulation en temps réel (boucle infinie)\n";
        echo "  batch [heures]    Générer des données historiques (défaut: 24h)\n";
        echo "  stress [durée]    Test de stress en secondes (défaut: 60s)\n";
        echo "  test             Tester la connexion au système\n";
        echo "  help             Afficher cette aide\n\n";
        echo "Exemples:\n";
        echo "  php simulate_microcontroller.php realtime\n";
        echo "  php simulate_microcontroller.php batch 48\n";
        echo "  php simulate_microcontroller.php stress 120\n";
        echo "  php simulate_microcontroller.php test\n\n";
    }
}

// === SCRIPT PRINCIPAL ===

if (php_sapi_name() !== 'cli') {
    die("Ce script doit être exécuté en ligne de commande.\n");
}

$simulator = new MicrocontrollerSimulator();

$command = $argv[1] ?? 'help';
$param = isset($argv[2]) ? (int)$argv[2] : null;

switch ($command) {
    case 'realtime':
        $simulator->simulateRealtimeData();
        break;
        
    case 'batch':
        $hours = $param ?: 24;
        $simulator->generateBatchData($hours);
        break;
        
    case 'stress':
        $duration = $param ?: 60;
        $simulator->stressTest($duration);
        break;
        
    case 'test':
        $simulator->testConnection();
        break;
        
    case 'help':
    default:
        $simulator->showHelp();
        break;
}
?>