import numpy as np
import json
# Class handle Loads
class RequirementData:

    @staticmethod
    def loadGlove():
        file_name = 'glove/glove.6B.300d.txt'
        with open(file_name,'r',encoding="utf-8") as f:
            word_vocab = set() # not using list to avoid duplicate entry
            word2vector = {}
            for line in f:
                line_ = line.strip() #Remove white space
                words_Vec = line_.split()
                word_vocab.add(words_Vec[0])
                word2vector[words_Vec[0]] = np.array(words_Vec[1:],dtype=float)
        return {'vocab':word_vocab,'w2v':word2vector}

    @staticmethod
    def loadTags():
        file_name = 'tags/tags.json'

        with open(file_name) as f:
            tags = json.load(f)
        return tags


