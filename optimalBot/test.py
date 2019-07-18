from chatterbot.trainers import ListTrainer, ChatterBotCorpusTrainer
from chatterbot import ChatBot
from sentence_classification import *
import yaml

"""faq_corpus_file = open("sample.yml", "w", encoding="utf-8")
corpus_content = "categories:\n- faq\nconversations:"
for key, value in Q_A.items():
    corpus_content += "\n- - " + key + "\n  - " + value

faq_corpus_file.write(corpus_content)
faq_corpus_file.close()
"""

faq_table_name = 'faq_question'
Q_A = get_faq_Q_A_Pairs(faq_table_name)
conversation = list()
for key, value in Q_A.items():
    conversation.append(key)
    conversation.append(value)

chatbot = ChatBot("Optimal_1",
                  storage_adapter="chatterbot.storage.SQLStorageAdapter",
                  database_uri="mysql://root@localhost:3306/test")


trainer = ChatterBotCorpusTrainer(chatbot)

trainer.train(
    "chatterbot.corpus.english.greetings",
    "chatterbot.corpus.english.conversations"
)

trainer = ListTrainer(chatbot)
#print(conversation)

trainer.train(conversation)

#response = chatbot.get_response("what is Da7i7a?")
#print(response)
