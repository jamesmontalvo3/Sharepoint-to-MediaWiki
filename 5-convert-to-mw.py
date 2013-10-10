#!/usr/bin/python

from subprocess import call
import os
from os.path import join, getsize


wikiHtml = "/var/www/sp/wiki-html"
LOwikiHtml = "/var/www/sp/5-libre-wiki-html"
wikiFiles = "/var/www/sp/6-wiki-files"

for f in os.listdir(wikiHtml):
	if f.endswith(".html"):
		# soffice --headless --convert-to html:HTML /path/to/file.html
		call(['soffice', '--headless', '--convert-to', 'html:HTML', '-outdir', LOwikiHtml, wikiHtml+'/'+f])
	else:
		continue


for f in os.listdir(LOwikiHtml):
	if f.endswith(".html"):
		# html2wiki --dialect MediaWiki /path/to/file.html > /path/to/file.wiki
		pathToSource = LOwikiHtml+'/'+f
		pathToDestination = wikiFiles+'/'+os.path.splitext(f)[0]+'.wiki'
		os.system("html2wiki --dialect MediaWiki \"%s\" > \"%s\"" % (pathToSource, pathToDestination))
		print "Created file: %s" % pathToDestination
	else:
		continue
