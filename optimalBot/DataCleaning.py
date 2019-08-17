import pandas as pd
import re
from contractions import contractions_dict
from autocorrect import spell
import nltk


class DataCleaning:
        
    def clean(self, text):
        expanded_text = self.expand_contractions(text, contractions_dict)
        filtered_text = self.filter_symbols(expanded_text)
        #corrected_text = self.spell_check(filtered_text)
        return filtered_text

    def spell_check(self, text):
        """
        correct the spelling of the word.
        """
        spells = [spell(w) for w in (nltk.word_tokenize(text))]
        return " ".join(spells)

    def equations_removing(self, text):

        def check_sentence(sentence):
            try:
                sentence_return = re.findall(r'\.[\w|,|\s|\'|?|!|-]*\.', sentence)[0]
                if len(sentence_return) > 11:
                    return 1
            except:
                try:
                    sentence_return = re.findall(r'\.[\w|,|\s|\'|?|!|-]*[(]?[\w|,|\s|\'|?|!|-]*[)]?\.', sentence)[0]
                    if len(sentence_return) > 11:
                        return 1
                except:
                    if sentence == "\n":
                        return 1
            return 0

        sentences_list = []
        #^\b \b$
        for sentence in text.split("\n"):
            re.sub(r'[\[]\d*[\]]', " ", sentence)
            sentences_list.append("." + sentence + ".")
        sentences_df = pd.DataFrame(sentences_list)
        sentences_df['type'] = sentences_df[0].apply(check_sentence)

        cleaned_text = []
        for ss in sentences_df[sentences_df['type'] == 1][0]:
            ss = ss.replace(".", "").strip()
            cleaned_text.append(ss)

        return cleaned_text

    def expand_contractions(self, text, contractions_dict):
        contractions_pattern = re.compile('({})'.format('|'.join(contractions_dict.keys())),
                                          flags=re.IGNORECASE | re.DOTALL)

        def expand_match(contraction):
            match = contraction.group(0)
            first_char = match[0]
            expanded_contraction = contractions_dict.get(match) \
                if contractions_dict.get(match) \
                else contractions_dict.get(match.lower())
            expanded_contraction = expanded_contraction
            return expanded_contraction

        expanded_text = contractions_pattern.sub(expand_match, text)
        expanded_text = re.sub("'", "", expanded_text)
        expanded_text = re.sub("\s\s+", " ", expanded_text)
        return expanded_text

    def filter_symbols(self, filtered_text):
        return re.sub(u"(\u2018|\u2019)", "'", filtered_text)
