from chatterbot.trainers import *


class ChatterBotCorpusTrainerOverridden(ChatterBotCorpusTrainer):
    """
    Allows the chat bot to be trained using data from the
    ChatterBot dialog corpus.
    """

    def train(self, *corpus_paths):
        from chatterbot.corpus import load_corpus, list_corpus_files

        data_file_paths = []

        # Get the paths to each file the bot will be trained with
        for corpus_path in corpus_paths:
            data_file_paths.extend(list_corpus_files(corpus_path))

        for corpus, categories, file_path in load_corpus(*data_file_paths):

            statements_to_create = []

            # Train the chat bot with each statement and response pair
            for conversation_count, conversation in enumerate(corpus):

                if self.show_training_progress:
                    utils.print_progress_bar(
                        'Training ' + str(os.path.basename(file_path)),
                        conversation_count + 1,
                        len(corpus)
                    )

                previous_statement_text = None
                previous_statement_search_text = ''

                for text in conversation:

                    statement_search_text = self.chatbot.storage.tagger.get_bigram_pair_string(text)

                    statement = Statement(
                        text=text,
                        search_text=statement_search_text,
                        in_response_to=previous_statement_text,
                        search_in_response_to=previous_statement_search_text,
                        conversation='training',

                    )

                    statement.add_tags(*categories)

                    statement = self.get_preprocessed_statement(statement)

                    previous_statement_text = statement.text
                    previous_statement_search_text = statement_search_text

                    statements_to_create.append(statement)
            self.chatbot.storage.create_many_customized(statements_to_create,client_id=0)

class ListTrainerOverridden(ListTrainer):
    """
    Allows a chat bot to be trained using a list of strings
    where the list represents a conversation.
    """

    def train(self, conversation_dict):
        """
        Train the chat bot based on the provided list of
        statements that represents a single conversation.
        """
        conversation = conversation_dict['conversation']
        client_id = conversation_dict['client_id']

        previous_statement_text = None
        previous_statement_search_text = ''

        statements_to_create = []

        for conversation_count, text in enumerate(conversation):
            if self.show_training_progress:
                utils.print_progress_bar(
                    'List Trainer',
                    conversation_count + 1, len(conversation)
                )

            statement_search_text = self.chatbot.storage.tagger.get_bigram_pair_string(text)

            statement = self.get_preprocessed_statement(
                Statement(
                    text=text,
                    search_text=statement_search_text,
                    in_response_to=previous_statement_text,
                    search_in_response_to=previous_statement_search_text,
                    conversation='training'
                )
            )

            previous_statement_text = statement.text
            previous_statement_search_text = statement_search_text

            statements_to_create.append(statement)

        self.chatbot.storage.create_many_customized(statements_to_create,client_id=client_id)
