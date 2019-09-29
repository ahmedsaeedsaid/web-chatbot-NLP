from chatterbot.logic import LogicAdapter
from chatterbot.logic import BestMatch
from chatterbot import filters
from optimalBot.db_manager import *
from optimalBot.settings import *
from optimalBot.optimal_chatterbot.Filter_results import FilterResults
import optimalBot.chatBot_tags as CT
import math
from chatterbot.conversation import Statement
import itertools
import copy
class FlowAdapter(LogicAdapter):
    """
    A logic adapter that returns a response based on known responses to
    the closest matches to the input statement.

    :param excluded_words:
        The excluded_words parameter allows a list of words to be set that will
        prevent the logic adapter from returning statements that have text
        containing any of those words. This can be useful for preventing your
        chat bot from saying swears when it is being demonstrated in front of
        an audience.
        Defaults to None
    :type excluded_words: list
    """

    def __init__(self, chatbot, **kwargs):
        super().__init__(chatbot, **kwargs)

        self.excluded_words = kwargs.get('excluded_words')
        self.Story_ID = kwargs.get('Story_ID')
        self.glove = kwargs.get('glove')
        self.tags = kwargs.get('tags')
        bot_name, db_server, db_name, db_username, db_password, db_driver, client_id, _, _, _ = kwargs.get('bot_information')
        self.DBManager =    DBManager(user=DB_USERNAME,
                                      password=DB_PASSWORD,
                                      host=DB_SERVER,
                                      database=DB_NAME)
        self.client_id = client_id


    def process(self, input_statement, additional_response_selection_parameters=None):

        faq_results = FilterResults.getResultsFromFAQ(self,input_statement)
        search_results_general = self.search_algorithm.search(input_statement,client_id=0)
        search_results = self.search_algorithm.search(input_statement,client_id= self.client_id)

        results = faq_results + list(search_results_general) + list(search_results)
        # Use the input statement as the closest match if no other results are found


        accepted_results = []
        story_id_changed = True
        closest_match_story_id = self.Story_ID
        children_questions = []
        means_questions = []



        # Filter results according tags

        closest_match = input_statement
        # Search for the closest match to the input statement
        for result in results:
            # Stop searching if a match that is close enough is found

            if result.confidence >= self.maximum_similarity_threshold :

                if result.confidence > closest_match.confidence :
                    closest_match = result
                if result.in_response_to:
                    story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                        conditions={ANSWER_COLUMN_NAME: result.in_response_to  , CLIENT_ID_COLUMN : str(self.client_id)})
                else:
                    story_id = 0
                if story_id > 0:
                    closest_match_story_id = story_id
                accepted_results.append(closest_match)



        for result in accepted_results:

            if result.in_response_to:
                story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                    conditions={ANSWER_COLUMN_NAME: result.in_response_to  , CLIENT_ID_COLUMN : str(self.client_id)})
            else:
                story_id = 0
            if story_id == self.Story_ID:
                if story_id_changed:
                    closest_match = result
                    story_id_changed = False
                elif result.confidence > closest_match.confidence:
                    closest_match = result


        if accepted_results and story_id_changed:
            self.Story_ID = closest_match_story_id




        # Suggested questions
        if closest_match.in_response_to:
            question_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_ID_COLUMN,
                                               conditions={ANSWER_COLUMN_NAME: closest_match.in_response_to , CLIENT_ID_COLUMN : str(self.client_id)})
        else:
            question_id = 0

        if question_id != 0:
            children_questions = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUBJECT_COLUMN,
                                                          conditions={PARENT_ID_COLUMN: str(question_id) , CLIENT_ID_COLUMN : str(self.client_id)}, multiple_values=True)
        answer = closest_match.in_response_to
        if not closest_match.in_response_to:
            answer = self.DBManager.get_value(table_name=TABLE_BOT_1, column_name="text",
                                              conditions={"in_response_to": closest_match.text , CLIENT_ID_COLUMN : str(self.client_id)})
            if answer == 0:
                answer = self.DBManager.get_value(table_name=TABLE_BOT_1, column_name="text",
                                                  conditions={"in_response_to": closest_match.text , CLIENT_ID_COLUMN : str(0)})


        if not answer :
            answer = "i can't reply"
            closest_match.text = "i can't reply"
            for faq_result in faq_results:
                means_questions.append(faq_result.text)


        self.chatbot.logger.info('Using "{}" as a close match to "{}" with a confidence of {}'.format(
            closest_match.text, input_statement.text, closest_match.confidence
        ))

        recent_repeated_responses = filters.get_recent_repeated_responses(
            self.chatbot,
            input_statement.conversation

        )

        for index, recent_repeated_response in enumerate(recent_repeated_responses):
            self.chatbot.logger.info('{}. Excluding recent repeated response of "{}"'.format(
                index, recent_repeated_response
            ))

        response_selection_parameters = {
            'search_in_response_to': closest_match.search_text,
            'exclude_text': recent_repeated_responses,
            'exclude_text_words': self.excluded_words
        }

        alternate_response_selection_parameters = {
            'search_in_response_to': self.chatbot.storage.tagger.get_bigram_pair_string(
                input_statement.text
            ),
            'exclude_text': recent_repeated_responses,
            'exclude_text_words': self.excluded_words
        }

        if additional_response_selection_parameters:
            response_selection_parameters.update(additional_response_selection_parameters)
            alternate_response_selection_parameters.update(additional_response_selection_parameters)

        # Get all statements that are in response to the closest match
        response_list = list(self.chatbot.storage.filter(**response_selection_parameters))

        alternate_response_list = []

        if not response_list:
            self.chatbot.logger.info('No responses found. Generating alternate response list.')
            alternate_response_list = list(self.chatbot.storage.filter(**alternate_response_selection_parameters))

        if response_list :
            self.chatbot.logger.info(
                'Selecting response from {} optimal responses.'.format(
                    len(response_list)
                )
            )

            response = self.select_response(
                input_statement,
                response_list,
                self.chatbot.storage
            )

            response.confidence = closest_match.confidence
            self.chatbot.logger.info('Response selected. Using "{}"'.format(response.text))
        elif alternate_response_list:
            '''
            The case where there was no responses returned for the selected match
            but a value exists for the statement the match is in response to.
            '''
            self.chatbot.logger.info(
                'Selecting response from {} optimal alternate responses.'.format(
                    len(alternate_response_list)
                )
            )
            response = self.select_response(
                input_statement,
                alternate_response_list,
                self.chatbot.storage
            )

            response.confidence = closest_match.confidence
            self.chatbot.logger.info('Alternate response selected. Using "{}"'.format(response.text))
        else:
            response = self.get_default_response(input_statement)





        if answer:
            response.text = answer
            return response, self.Story_ID ,children_questions,means_questions

        return response, self.Story_ID ,children_questions,means_questions



    def getAccuracyOfQuestions(self, input_statement):

        FAQ_simarities = FilterResults.getSimlarityForFAQ(self,input_statement)
        return FAQ_simarities


