import numpy as np
from .keywordsExtractor import *
from chatterbot.comparisons import SynsetDistance
from chatterbot.conversation import Statement

class Similarity (SynsetDistance):



    def __init__(self,glove,tags):
        super().__init__()
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

    def compare(self, statement, other_statement):
        # TODO : get lemma for tokenize
        """
        Compare the two input statements.

        :return: The percent of similarity between the closest synset distance.
        :rtype: float

        .. _wordnet: http://www.nltk.org/howto/wordnet.html
        .. _NLTK: http://www.nltk.org/
        """
        import itertools

        word_tokenizer = self.get_word_tokenizer()

        tokens1 = word_tokenizer.tokenize(statement.text.lower())
        tokens2 = word_tokenizer.tokenize(other_statement.text.lower())

        # Get the stopwords for the current language
        stop_word_set = set(self.get_stopwords())

        # Remove all stop words from the list of word tokens
        tokens1 = set(tokens1) - stop_word_set
        tokens2 = set(tokens2) - stop_word_set

        # The maximum possible similarity is an exact match
        # Because path_similarity returns a value between 0 and 1,
        # max_possible_similarity is the number of words in the longer
        # of the two input statements.
        max_possible_similarity = max(
            len(tokens1),
            len(tokens2)
        ) / min(
            len(tokens1),
            len(tokens2)
        )

        max_similarity = 0.0

        # Get the highest matching value for each possible combination of words
        for combination in itertools.product(*[tokens1, tokens2]):

            similarity = self.similarity(combination[0],combination[1])

            if similarity and (similarity > max_similarity):
                max_similarity = similarity



        if max_possible_similarity == 0:
            return 0

        return max_similarity / max_possible_similarity

    def get_tags(self,statement , threshold_similar = 0.4):
        keywords_extractor = KeywordsExtractor()
        keyphrases_sorted  = keywords_extractor.score_keyphrases_by_textrank(statement)
        statement_tags = []
        for keyword in keyphrases_sorted :
            highestTag = (None,0)
            for tag in self.tags:
                tagStatement = Statement(text=tag)
                keywordStatement = Statement(text=keyword[0])
                score = self.compare(tagStatement,keywordStatement)
                if score > highestTag[1] :
                    highestTag = (tag,score)

            if highestTag[0] and highestTag[1]>threshold_similar:
                statement_tags.append(highestTag[0])
            statement_tags.append(keyword[0])
        statement_tags = list( set(statement_tags) )
        return statement_tags






