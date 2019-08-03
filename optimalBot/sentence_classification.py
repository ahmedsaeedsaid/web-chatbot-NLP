import nltk
import pickle
from db_manager import DBManager
from bs4 import BeautifulSoup
import time
import gensim
import numpy as np

# nltk.download('nps_chat')
posts = nltk.corpus.nps_chat.xml_posts()


def dialogue_act_features(post):
    features = {}
    for word in nltk.word_tokenize(post):
        features['contains({})'.format(word.lower())] = True
    return features


featuresets = [(dialogue_act_features(post.text), post.get('class')) for post in posts]

size = int(len(featuresets) * 0.1)
train_set, test_set = featuresets[size:], featuresets[:size]
"""cutoffs = dict()
classifier = nltk.ConditionalExponentialClassifier.train(train_set, **cutoffs)

f = open('sentence_classifier.pickle', 'wb')
pickle.dump(classifier, f)
f.close()"""

f = open('sentence_classifier.pickle', 'rb')
classifier = pickle.load(f)
f.close()


def get_faq_Q_A_Pairs(faq_table_name, db):
    faq_table_data = db.get_table_data(faq_table_name)
    Q_A = dict()
    for item in faq_table_data:
        question = item[1]
        answer = item[2]
        Q_A[question] = answer
        """for idx, value in enumerate(item):
            if str(value).isdigit() or len(nltk.word_tokenize(str(value))) < 3:
                continue
            else:
                res = classifier.classify(dialogue_act_features(value))
                res = res.lower()
                if "question" in res:
                    question = value
                elif "statement" in res:
                    answer = value

        question = BeautifulSoup(question, "lxml").text
        answer = BeautifulSoup(answer, "lxml").text
        # Removing Whitespaces
        question = " ".join(question.split()).replace(u"\u2018", "'").replace(u"\u2019", "'")
        answer = " ".join(answer.split()).replace(u"\u2018", "'").replace(u"\u2019", "'")
        Q_A[question] = answer"""
    return Q_A