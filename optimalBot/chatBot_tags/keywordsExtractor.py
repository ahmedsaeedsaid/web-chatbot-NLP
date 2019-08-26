import itertools
import nltk
import string
from itertools import takewhile, tee
import networkx



class KeywordsExtractor:

    def extract_candidate_words(self, tokenized_words):
        """
         This function simply extract possible candidate key words from given text.

            This function split the given text to sentences, then split sentences to words,
            then it extracts nouns, noun phrases, etc...

            Args:
                list: list of tokenized words

            Returns:
                list: Possible candidates key words

            Raises:
                None.
        """
        good_tags = set(['JJ', 'JJR', 'JJS', 'NN', 'NNP', 'NNS', 'NNPS'])
        # exclude candidates that are stop words or entirely punctuation
        punct = set(string.punctuation)
        stop_words = set(nltk.corpus.stopwords.words('english'))
        # tokenize and POS-tag words

        pos_tags = nltk.pos_tag(tokenized_words)
        tagged_words = itertools.chain.from_iterable([pos_tags])

        # filter on certain POS tags and lowercase all words
        candidates = [word.lower()
                      for word, tag in tagged_words
                      if tag in good_tags and word.lower() not in
                      stop_words and not all(char in punct for char in word)]

        return candidates

    # update docstrings
    def score_keyphrases_by_textrank(self, statement, n_keywords=0.75):
        """ Construct weighted & undirected graph with key phrases, ranked by the score of each key phrase.

            This function takes the output of the extract_candidate_words, then it represents
            every key word as a node in the graph, then the graph is evaluated by page rank
            algorithm to rank each node with the others according to their dependencies.

            Args:
                statement (string): Text from the output of the context selection phase
                n_keywords (float): The percentage of key words to be taken, this can be tuned

            Returns:
                list: Sorted list of tuples with every key phrase and its score

            Raises:
                None.

            Examples:
                >>> score_keyphrases_by_textrank("Nowadays, it is widely recognized that test construction " \
                "is really time-consuming and expensive for teachers. " \
                "The use of Computer Assisted Assessment reduces considera-bly " \
                "the time spent by teachers on constructing examination papers [11].", n_keywords=0.5)
                [('teachers', 0.15066941652592866)]
        """
        # tosentkenize for all words, and extract *candidate* words
        tokenized_words = nltk.word_tokenize(statement)
        words = [word.lower() for word in tokenized_words]
        candidates = self.extract_candidate_words(words)
        # build graph, each node is a unique candidate
        graph = networkx.Graph()
        graph.add_nodes_from(set(candidates))

        # Iterate over word-pairs, add unweighted edges into graph
        # N-gram
        def pairwise(iterable):
            """s -> (s0,s1), (s1,s2), (s2, s3), ..."""
            a, b = tee(iterable)
            next(b, None)
            return zip(a, b)

        for w1, w2 in pairwise(candidates):
            if w2:
                graph.add_edge(*sorted([w1, w2]))
        # score nodes using default pagerank algorithm,
        # sort by score, keep top n_keywords
        ranks = networkx.pagerank(graph)
        if 0 < n_keywords < 1:
            n_keywords = int(round(len(candidates) * n_keywords))

        # word_ranks={'keywords':rank value},
        # make a dict for first n_keywords order by ranks descending
        word_ranks = [(word_rank[0], word_rank[1])
                      for word_rank in sorted(ranks.items(),
                                              key=lambda x: x[1],
                                              reverse=True)[:n_keywords]]

        return word_ranks
