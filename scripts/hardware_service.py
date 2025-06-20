import serial
import time
import requests
import os

# --- Configuration ---
# Assurez-vous que ces IDs correspondent à votre base de données
BUTTON_SENSOR_ID = 4    # L'ID de votre capteur "Bouton"
HUMIDITY_SENSOR_ID = 5   # L'ID de votre capteur d'humidité

SERIAL_PORT = 'COM3'
BAUD_RATE = 9600
API_URL_SENSORS = 'http://localhost/APP-COMMUN-SERRE/?controller=api&action=syncSensors'
API_KEY = 'serre_admin_master_key'

# Fichier utilisé comme "boîte aux lettres" pour les commandes
COMMAND_FILE_PATH = os.path.join(os.path.dirname(__file__), "command.txt")

print(f"--- Démarrage du service matériel sur {SERIAL_PORT} ---")
print("Ce service lira les données toutes les 10 secondes.")
print("Appuyez sur Ctrl+C pour arrêter.")

try:
    ser = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=0.1)
    time.sleep(2) # Laisse le temps à la carte TIVA de s'initialiser
except Exception as e:
    print(f"❌ Erreur: Impossible d'ouvrir le port série {SERIAL_PORT}. {e}")
    exit()

def send_sensor_data(button_state, humidity_value):
    """Envoie l'état des capteurs à l'API PHP."""
    payload = {
        'api_key': API_KEY,
        'button_sensor_id': BUTTON_SENSOR_ID,
        'button_state': button_state,
        'humidity_sensor_id': HUMIDITY_SENSOR_ID,
        'humidity_value': humidity_value,
    }
    try:
        requests.post(API_URL_SENSORS, data=payload, timeout=2)
        print(f"-> Données envoyées: Bouton={button_state}, Humidité={humidity_value}")
    except requests.exceptions.RequestException:
        print("⚠️ Erreur réseau, impossible d'envoyer les données des capteurs.")

def check_for_commands():
    """Vérifie et exécute les commandes reçues du site web."""
    if os.path.exists(COMMAND_FILE_PATH):
        try:
            with open(COMMAND_FILE_PATH, "r+") as f:
                command = f.read().strip().upper()
                if command in ["ON", "OFF"]:
                    signal = b'1' if command == "ON" else b'0'
                    print(f"[COMMANDE REÇUE] -> {command}. Envoi du signal '{signal.decode()}' à la TIVA.")
                    ser.write(signal)
                    f.seek(0)
                    f.truncate()
        except Exception as e:
            print(f"Erreur lors de la lecture du fichier de commande: {e}")

# Boucle principale du service
while True:
    try:
        # Tâche 1: Lire les données des capteurs
        line = ser.readline().decode('utf-8').strip()
        if line and ';' in line:
            try:
                button_str, humidity_str = line.split(';')
                send_sensor_data(int(button_str), float(humidity_str))
            except (ValueError, IndexError):
                pass # Ignore les lignes mal formées

        # Tâche 2: Vérifier les commandes en attente
        check_for_commands()
        
        # --- MODIFICATION ICI ---
        # On change la pause pour qu'elle soit de 10 secondes.
        time.sleep(1)

    except KeyboardInterrupt:
        print("\nArrêt du service.")
        break
    except Exception as e:
        print(f"Une erreur inattendue est survenue: {e}")
        time.sleep(5)

ser.close()
