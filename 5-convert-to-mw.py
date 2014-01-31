#!/usr/bin/python

from subprocess import call
import os
from os.path import join, getsize

scriptPath = os.path.dirname(os.path.realpath(__file__))
wikiHtml = scriptPath + "/usr/sharepoint-content"
LibreOfficeOutput = scriptPath + "/usr/LibreOfficeOutput"
WikitextOutput = scriptPath + "/usr/WikitextOutput"

for f in os.listdir(wikiHtml):
	if f.endswith(".html"):
		# soffice --headless --convert-to html:HTML /path/to/file.html
		call(['soffice', '--headless', '--convert-to', 'html:HTML', '-outdir', LibreOfficeOutput, wikiHtml+'/'+f])
	else:
		continue


for f in os.listdir(LibreOfficeOutput):
	if f.endswith(".html"):
		# html2wiki --dialect MediaWiki /path/to/file.html > /path/to/file.wiki
		pathToSource = LibreOfficeOutput+'/'+f
		pathToDestination = WikitextOutput+'/'+os.path.splitext(f)[0]+'.wiki'
		os.system("html2wiki --dialect MediaWiki \"%s\" > \"%s\"" % (pathToSource, pathToDestination))
		print "Created file: %s" % pathToDestination
	else:
		continue
