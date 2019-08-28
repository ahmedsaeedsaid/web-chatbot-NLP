import numpy as np
from .keywordsExtractor import *

class Similarity :
    def __init__(self,glove,tags):
        self.glove = glove
        self.tags = tags


    def similarity(self ,word1,word2):
        try:
            u = self.glove ['w2v'][word1]
            v = self.glove ['w2v'][word2]
            numerator_ = u.dot(v)
            denominator_= np.sqrt(np.sum(np.square(u))) * np.sqrt(np.sum(np.square(v)))
            return numerator_/denominator_
        except:
            return 0

    def get_tags(self,statement):
        keywords_extractor = KeywordsExtractor()
        keyphrases_sorted = keywords_extractor.score_keyphrases_by_textrank(statement)
        statement_tags = []
        for keyword in keyphrases_sorted :
            highestTag = (None,0)
            for tag in self.tags:
                score = self.similarity(tag,keyword[0])
                if score > highestTag[1]:
                    highestTag = (tag,score)
            if not highestTag[0]:
                statement_tags.append(highestTag[0])

        return statement_tags



