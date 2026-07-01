# LOOP Plugin für Moodle

## Übersicht

Das LOOP-Plugin ist ein Moodle-Modul, das eine nahtlose Integration zwischen Moodle-Kursen und externen LOOP-Systemen ermöglicht. LOOP-Systeme sind Wiki-basierte Lernplattformen, die über eine MediaWiki-API verfügen und personalisierte Lerninhalte bereitstellen.

## Funktionen

### Kernfunktionalitäten

- **Externe Systemintegration**: Verbindung mit LOOP-Systemen über MediaWiki-API
- **Single Sign-On (SSO)**: Automatische Authentifizierung von Moodle-Benutzern in LOOP-Systemen
- **Dynamische Inhaltsauswahl**: Auswahl spezifischer Kapitel und Seiten aus LOOP-Systemen
- **Theme-Unterstützung**: Verschiedene visuelle Themes für LOOP-Inhalte
- **Rollenbasierte Zugriffssteuerung**: Unterschiedliche Berechtigungen für Studierende und Lehrende

### Technische Features

- **Sichere Authentifizierung**: Token-basierte Authentifizierung mit verschlüsselter Session-Übertragung
- **Dynamische Struktur-Abfrage**: Automatische Abfrage der Inhaltsstruktur von LOOP-Systemen
- **AJAX-basierte Benutzeroberfläche**: Dynamische Aktualisierung von Kapitel- und Theme-Auswahl
- **Backup/Restore-Unterstützung**: Vollständige Moodle-Backup-Integration

## Installation

### Voraussetzungen

- Moodle 4.5+ (Moodle 2023100900)
- PHP 8.1+
- cURL-Unterstützung
- OpenSSL-Unterstützung

### Installationsschritte

1. Kopieren Sie das Plugin in das `mod/loop/` Verzeichnis Ihrer Moodle-Installation
2. Führen Sie die Moodle-Installation aus (Admin → Notifications)
3. Konfigurieren Sie das Plugin in den Administrator-Einstellungen

## Konfiguration

### Administrator-Einstellungen

#### Loop Token

- **Pfad**: Site administration → Plugins → Activity modules → LOOP
- **Beschreibung**: Token für die Authentifizierung mit LOOP-Systemen
- **Erforderlich**: Ja
- **Format**: Text

#### Default Theme

- **Beschreibung**: Standard-Theme für LOOP-Inhalte
- **Erforderlich**: Nein
- **Format**: Text

### Externe LOOP-Systeme

Das Plugin synchronisiert automatisch verfügbare LOOP-Systeme über eine API von `moodalis.oncampus.de`. Die Synchronisation erfolgt über einen geplanten Task.

## Verwendung

### LOOP-Aktivität erstellen

1. Aktivieren Sie den Bearbeitungsmodus in Ihrem Moodle-Kurs
2. Klicken Sie auf "Aktivität oder Material hinzufügen"
3. Wählen Sie "LOOP" aus der Aktivitätenliste
4. Konfigurieren Sie die LOOP-Instanz:
    - **Name**: Bezeichnung der Aktivität
    - **Einführung**: Optionale Beschreibung
    - **LOOP**: Auswahl des verfügbaren LOOP-Systems
    - **Kapitel**: Spezifisches Kapitel aus der LOOP-Struktur
    - **Seite**: Optionale spezifische Seite
    - **Theme**: Visuelles Theme für die LOOP-Inhalte

### Benutzererfahrung

- Studierende und Lehrende klicken auf die LOOP-Aktivität
- Automatische Weiterleitung zum LOOP-System mit SSO
- Nahtlose Integration in das Moodle-Design
- Rollenbasierte Berechtigungen werden automatisch übertragen

## Datenbankstruktur

### Tabelle: `loop`

Speichert LOOP-Instanzen in Moodle-Kursen:

- `id`: Primärschlüssel
- `course`: Kurs-ID
- `name`: Aktivitätsname
- `intro`: Einführungstext
- `url`: LOOP-System-URL
- `chapter`: Ausgewähltes Kapitel
- `page`: Spezifische Seite
- `personalized_access`: Personalisierter Zugriff (0/1)
- `student_role_allocation`: Rollenzuweisung für Studierende
- `teacher_role_allocation`: Rollenzuweisung für Lehrende
- `theme`: Ausgewähltes Theme

### Tabelle: `loop_systems`

Speichert verfügbare LOOP-Systeme:

- `id`: Primärschlüssel
- `externalid`: Externe System-ID
- `name`: Systemname
- `url`: System-URL
- `personalized_access`: Personalisierter Zugriff erlaubt
- `student_role_allocation`: Standard-Rolle für Studierende
- `teacher_role_allocation`: Standard-Rolle für Lehrende
- `allowed_themes`: Verfügbare Themes (JSON)

## API-Integration

### Externe API-Endpunkte

- **LOOP-Systeme**: `https://moodalis.oncampus.de/files/lms_loops.php`
- **Struktur-Abfrage**: `{loop_url}/mediawiki/api.php?action=loopauth-structure`
- **Authentifizierung**: Token-basierte Authentifizierung

### Sicherheitsfeatures

- **Session-Verschlüsselung**: AES-128-ECB Verschlüsselung der Session-ID
- **Token-basierte Authentifizierung**: MD5-Hash aus Benutzername und Token
- **URL-Validierung**: Sichere Parameter-Validierung

## Entwicklung

### Plugin-Struktur

```
mod/loop/
├── amd/src/loop.js          # JavaScript für dynamische UI
├── backup/                  # Backup/Restore-Funktionalität
├── classes/task/           # Geplante Tasks
├── db/                     # Datenbankdefinitionen
├── lang/                   # Sprachdateien
├── lib.php                 # Hauptfunktionen
├── locallib.php           # Lokale Hilfsfunktionen
├── mod_form.php           # Aktivitätsformular
├── view.php               # Aktivitätsansicht
└── link.php               # SSO-Weiterleitung
```

### Erweiterte Funktionen

- **Geplante Tasks**: Automatische Synchronisation von LOOP-Systemen
- **AJAX-Services**: Dynamische Abfrage von Struktur und Themes
- **Fehlerbehandlung**: Umfassende Fehlerbehandlung mit spezifischen Meldungen

## Troubleshooting

### Häufige Probleme

#### "No active session found"

- **Ursache**: Benutzer-Session ist abgelaufen
- **Lösung**: Benutzer muss sich erneut in Moodle anmelden

#### "Loop token is not configured"

- **Ursache**: Administrator-Token ist nicht konfiguriert
- **Lösung**: Token in den Administrator-Einstellungen konfigurieren

#### "Failed to encrypt session data"

- **Ursache**: OpenSSL-Probleme oder ungültiger Token
- **Lösung**: OpenSSL-Installation prüfen, Token validieren

### Debugging

Das Plugin unterstützt umfassendes Debugging über die Moodle-Debug-Funktionen:

- Aktivieren Sie DEBUG_DEVELOPER in der Moodle-Konfiguration
- Überprüfen Sie die Debug-Ausgaben für detaillierte Informationen

## Lizenz

Dieses Plugin steht unter der GNU General Public License v3.0 oder höher.

## Autor

**Marc Vorreiter** - <tim-louis.rieck@oncampus.de>

## Version

- **Aktuelle Version**: 1.0
- **Moodle-Kompatibilität**: 3.5+ (2018111200)
- **Status**: Stable

## Support

Bei Fragen oder Problemen wenden Sie sich an:

- **Entwickler**: Tim-Louis Rieck (<tim-louis.rieck@oncampus.de>)
- **Institution**: Technische Hochschule Lübeck

## Changelog

### Version 1.0

- Initiale Veröffentlichung
- SSO-Integration mit LOOP-Systemen
- Dynamische Inhaltsauswahl
- Theme-Unterstützung
- Rollenbasierte Zugriffssteuerung
- Backup/Restore-Funktionalität
