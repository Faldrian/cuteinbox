cuteinbox
=========

Ein Diaspora-Bot, der automatisch zu bestimmten Uhrzeiten Beiträge posted. Grundsätzlich kann man den Bot anpassen, dass er beliebigen Kram posten kann, aber aktuell macht er folgendes:

* Kann eine Liste von URLs entgegennehmen und extrahiert Titel und "Bild" heraus, speichert das in eine Datenbank
* In einem Webinterface hat man die Möglichkeit, Kommentare und Tags zu jedem Bild zu schreiben und es freizuschalten
* Aus allen freigeschalteten Einträgen wird bei jedem Aufruf des cronjobs einer ausgewählt, geposted und als "geposted" in der Datenbank markiert
* Bereits gepostete Einträge werden im Webinterface nicht mehr angezeigt

Installation / Abhängigkeiten
------
* Python3
* [diaspy](https://github.com/Javafant/diaspy)
* Eine MySQL-Datenbank
* PHP >= 5.3
* Einen Webserver, auf dem das PHP-Zeug ausgeführt wird
* Einen Cronjob muss man auch einrichten

Es gibt 2 Konfigurationsdateien:

1. `config.php` (einfach `config.php.example` kopieren und anpassen), in der die Informationen für die PHP-Anbindung an das MySQL stehen
2. `scripts/diaspora_config.ini` (genauso wie bei 1. verfahren), dort wird der Zugang zum Diaspora-Account und der Zugang zur MySQL-Datenbank für den Cronjob gespeichert.

Bei meiner Instanz habe ich mit [virtualenv](https://pypi.python.org/pypi/virtualenv) gearbeitet. Dabei hab ich dann einfach das Repository von `diaspy` ins `lib/python3.2/site-packages/` reingeclont. Hässlich, aber effektiv. ;)

Wenn man direkt die benötigten Pakete systemweit installieren kann, kann man die entsprechende "source"-Zeile aus der `cronjob.sh` weglassen.