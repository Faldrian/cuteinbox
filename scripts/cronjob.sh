#!/bin/bash

# Wechsel in die richtige Python-Installation (Nur, wenn man virtualenv benutzt)
source /home/faldrian/cuteinboxPython/bin/activate

# Starte den Bot
cd /home/faldrian/www/cuteinbox/scripts/
./diasporabot.py
