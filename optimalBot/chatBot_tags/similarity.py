import numpy as np

class Similarity :
    def __init__(self,glove):
        self.glove = glove


    def similarity(self ,word1,word2):
        try:
            u = self.glove ['w2v'][word1]
            v = self.glove ['w2v'][word2]
            numerator_ = u.dot(v)
            denominator_= np.sqrt(np.sum(np.square(u))) * np.sqrt(np.sum(np.square(v)))
            return numerator_/denominator_
        except:
            return 0




