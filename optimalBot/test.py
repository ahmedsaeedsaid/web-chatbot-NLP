from keywordsExtractor import KeywordsExtractor

statement = "Multi-branched Student organization, its vision is to become a vital organization among faculties across all universities in Egypt for integrating those faculties communities into one big community."
keywords_extractor = KeywordsExtractor()
keyphrases_sorted = keywords_extractor.score_keyphrases_by_textrank(statement)
print(keyphrases_sorted)
