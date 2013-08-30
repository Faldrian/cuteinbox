#!/usr/bin/env python3
# -*- coding: utf8 -*-

import diaspy_simple.client as diaspy #Javafants Spezialversion, https://github.com/Faldrian/diaspy_simple
import configparser
import datetime
import mysql.connector

config = configparser.ConfigParser()
config.read('diaspora_config.ini')

# Hole einen neuen Post - wenn es welche gibt
cnx = mysql.connector.connect(
	host	 = config['MySQL']['server'],
	database = config['MySQL']['database'],
	user	 = config['MySQL']['username'],
	password = config['MySQL']['password'])
cursor = cnx.cursor()

cursor.execute("SET NAMES utf8");
cursor.execute("SELECT * FROM post WHERE body IS NOT NULL AND posted IS NULL ORDER BY RAND() LIMIT 1")
row = cursor.fetchone()

if not row: # Wenn kein Treffer, dann haben wir nichts zu tun.
	cursor.close()
	cnx.close()
	exit()

post_id = row[0]
postbody = row[5]

# Post absetzen
c = diaspy.Client(config['Diaspora']['pod'], config['Diaspora']['username'], config['Diaspora']['password'])
c.post(postbody)

# Post als geposted markieren in der Datenbank
cursor.execute("UPDATE post SET posted = NOW() WHERE id = " + str(post_id))
cursor.close()
cnx.close()

