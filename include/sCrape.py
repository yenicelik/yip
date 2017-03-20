#!/home/guyfawkes/anaconda2/bin/python


"""ExTract: Generate List of Tags from URL."""
__version__ = "0.9.3"
__author__ = "David Yenicelik (yedavid@student.ethz.com)"

try:
	import re
	import requests
	from collections import Counter
#	import time
	import json
	import sys
	import math
	import getopt
except ImportError:
	raise 
	print ImportError('Something wrong with the default libraries!')

try:
	import lxml
	import nltk
	from nltk.tokenize import sent_tokenize,word_tokenize
	from nltk.corpus import stopwords
	from collections import defaultdict
	from string import punctuation
except ImportError:
	raise 
	print ImportError('External modules not imported!')

try:
	import numpy as np
	from unidecode import unidecode
	from bs4 import BeautifulSoup
except ImportError:
	raise 
	print ImportError('External modules not imported!')

##TODO
#Get Data from Web
	#Regard that PDF, DOCX etc must be converted
	#Regard that there are different languages to be converted in
	#Need sth like javascripts 'get element by tag' if we really want fine-tuning
#Weight different word-lists depending on how many words each has
	""" FAST LANGUAGE DETECTION """
	#Modular, depending on if text to title ration is too small ### mirrored exp
class sCrapeClass:
	""" Retrieves the main content form an HTML page. sCrape.get_contents to get the dictionary of tags.
		Creationg procedure: obj = sCrape(url) """

	#@profile
	def __init__(self, min_cut=0.1, max_cut=0.9):
	    """
	     Initilize the text summarizer.
	     Words that have a frequency term lower than min_cut 
	     or higer than max_cut will be ignored.
	    """
	    self._min_cut = min_cut
	    self._max_cut = max_cut 
	    self._stopwords = set(stopwords.words('english') + list(punctuation)) #implement language detection # This should be done right after the language was identified!!! #Get rid of language detection!
	    self.lang = 'english'

	    self.res = {'title': '', 'descr': '', 'text': '' }

	    self.counts = {'title': 0, 'descr': 0, 'text': 0, 'total': 0}

	#@profile
	def shitgotreal(self, url):
		try:
			#Assign self.res[] to the respective html-snippets / texts
			self.get_text(url)

			#Create array of words, Create Counter from that array and Select respective cumulative frequency
			for i in ['title', 'descr', 'text' ]:
				self.res[i] = self.find_words(self.res[i])
				self.res[i] = Counter(np.array([word for word in self.res[i] if word not in self._stopwords]))
				self.counts[i] = sum(self.res[i].values())

			#Get total counts
			self.counts['total'] = self.counts['title'] + self.counts['descr'] + self.counts['text']

			#Normalize counts
			self.normalize('title', 7)
			self.normalize('descr', 15)

			#Merge dictionaries			
			out = self.merge_dicts(self.res['title'], self.res['descr'], self.res['text'])
			out = sorted(out, key=out.get, reverse=True)

			print json.dumps(out)
			return json.dumps(out)

		except:
			raise
			print ("Shit just got real!")
			sys.exit(12)

	#@profile
	def get_text(self, url):
		""" change self.res[] to the fetched title, description and (main) text contents (for a specified url) """
		try :
			#If this flag is 3 at the end, exit the script
			exit_flag = 0

			#Get response / Crawl html from web ???: Maybe use python scrapy
			#start = time.time()
	 		response = requests.get(url, timeout=5.000)
	 		response.encoding = 'ISO-8859-1'
	 		#end = time.time()
	 		#print "Getting url takes"
	 		#print (end - start)
	 		#Create a bs-parser class
	 		soup = BeautifulSoup(response.content, 'lxml')

	 		#Remove script and style tags from html			
			for script in soup(["script", "style"]) :
				script.extract()

			#Try to catch title, description and (main) text contents
			#Title
			try:
				self.res['title'] = unidecode(soup.title.string).lower() #Previously soup.title.string = ...
			except:
				exit_flag += 1
				self.res['title'] = ''
			#Description
			try:
				tmpall = soup.findAll('meta')
				for meta in tmpall:
				    if 'description' == meta.get('name', '').lower():
				        self.res['descr'] = meta['content'].lower()
				        break
			except:
				exit_flag += 1
				self.res['descr'] = ''
			#(Main) Text
			try:
				self.res['text'] = unidecode(' '.join(map( lambda p: p.text, soup.find_all('p') ))).lower()
			except:
				exit_flag += 1
				self.res['text'] = ''

			#Exit script if no text given at all
			if (exit_flag >= 3):
				sys.exit(10) #All error codes should be of two-digits
			else:
				return True

		except:
			raise
			print "Something went wrong while fetching the url-html"
			sys.exit(11)

	#@profile
	def merge_dicts(self, cont1, cont2, cont3=Counter()):
		""" Merges two dictionary and sums their respective values """
		return cont1 + cont2 + cont3

	#@profile
	def find_words(self, text):
		""" Remove any unnesseasiry punctuaiton from whole text """
		# Should implement another function to turn punctuated words "don't" etc. into normal words! # only really dont texts, but not " waitin' " etc
		text = re.sub(r"""["?,$!0-9]|'(?!(?<! ')[ts])""", "", text)
		return re.findall(r'\w+', text)

	#@profile
	def normalize(self, cont_name, p_weight):
		""" Normalize relation between text corpus and meta-corpus with 
		inverse of weight p."""
		# Take other normalizer - bell curve (I think it was called)
		#calculate an alpha
		alpha = self.counts['total'] // p_weight
		if (self.counts['text'] != 0) and (self.counts[cont_name] != 0):
			for word in self.res[cont_name]: 
				self.res[cont_name][word] += alpha
			return True
		else:
			return False

def usage():
	print "BHP Net Tool"
	print "[*] Welcome to sCrape v0.6!"
	print "Usage: sCrape.py -u target_url"
	print "-h --help        - show help dialog "
	print "-u --url         - give a url to retrieve input from "
	print
	print "Examples: "
	print "sCrape.py -u 'https://en.wikipedia.org/wiki/Winsor_McCay'"
	print "Currently supports English Language and HTML format only"
	sys.exit(0)

def main():
	global url

	if not len(sys.argv[1:]):
		usage()

	# read the commandline options 
	try:
		opts, args = getopt.getopt(sys.argv[1:],"hu:", ["url", "help"]) 
	except getopt.GetoptError as err:
		print str(err)
		usage()

	for o,a in opts:
		if o in ("-h","--help"):
			usage()
		elif o in ("-u", "--url"):
			url = a
			sc = sCrapeClass()
			sc.shitgotreal(url)
		else:
			assert False,"Unhandled Option"
			sys.exit(14)


if __name__ == '__main__':
	#import time
	#start = time.time()
	main()

	#url = 'https://www.inf.ethz.ch/news-and-events/spotlights/we-are-eth/we-are-eth-jie-song.html'
	
	#end = time.time()
	#print(end - start)

#Additional Resource to keep in mind:
#Building a article excerpt extractor: http://blog.davidziegler.net/post/122176962/a-python-script-to-automatically-extract-excerpts

	
