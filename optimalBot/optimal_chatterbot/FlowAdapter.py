from chatterbot.logic import LogicAdapter
from chatterbot import filters
from optimalBot.db_manager import *
from optimalBot.settings import *
import optimalBot.chatBot_tags as CT

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
        bot_name, db_server, db_name, db_username, db_password, db_driver, _, _, _, _ = kwargs.get('bot_information')
        self.DBManager = DBManager(user=db_username,
                                   password=db_password,
                                   host=db_server,
                                   database=db_name)

    def __get_tags(self, statement):
        statement_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name='id',
                                 conditions={QUESTION_SUBJECT_COLUMN: str(statement)}, like=True)
        if statement_id:
            tag_ids = self.DBManager.get_value(table_name=JOIN_TAGS_TABLE_NAME, column_name=JOIN_TAGS_TAG_ID_COLUMN_NAME,
                                    conditions={JOIN_TAGS_Q_A_ID_COLUMN_NAME: str(statement_id)},multiple_values=True)
            tags = []
            for tag_id in tag_ids:
                tag = self.DBManager.get_value(table_name=TAGS_TABLE_NAME, column_name='tag',
                                    conditions={'id': str(tag_id[0])})
                tags.append(tag)
            return tags
        else:
            similarity = CT.Similarity(self.glove,self.tags)
            return similarity.get_tags(statement)

    def __voting_results(self,tags_input_statement,tag_results):
        for tag_input_statement in tags_input_statement:
            for tag_result in tag_results:
                for tag in tag_result['tags']:
                    if tag == tag_input_statement:
                        tag_result['vote']+=1

                if len(tag_input_statement) == 0 and len(tag_result) == 0 :
                    tag_result['vote']+=1





    def process(self, input_statement, additional_response_selection_parameters=None):

        search_results = self.search_algorithm.search(input_statement)

        # Use the input statement as the closest match if no other results are found

        closest_match = None
        accepted_results = []
        story_id_changed = True
        closest_match_story_id = self.Story_ID
        children_questions = []


        # Search for the closest match to the input statement
        tag_results = []
        for result in search_results:
            tags = self.__get_tags(str(result))
            tag_results.append({'result':result , 'tags':tags , 'vote':0})

        tags_input_statement = self.__get_tags(str(input_statement))

        self.__voting_results(tags_input_statement,tag_results)


        for tag_result in tag_results:

            # Stop searching if a match that is close enough is found
            if tag_result['result'].confidence >= self.maximum_similarity_threshold or not closest_match:
                closest_match = tag_result['result']
                story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                    conditions={QUESTION_SUBJECT_COLUMN: str(tag_result['result'])}, like=True)
                if story_id > 0:
                    closest_match_story_id = story_id
                accepted_results.append(tag_result['result'])


        if not closest_match :
            closest_match = input_statement
        for result in accepted_results:
            story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                conditions={QUESTION_SUBJECT_COLUMN: str(result)}, like=True)
            if story_id == self.Story_ID:
                story_id_changed = False
                closest_match = result


        if accepted_results and story_id_changed:
            self.Story_ID = closest_match_story_id

        # Suggested questions
        question_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_ID_COLUMN,
                                               conditions={QUESTION_SUBJECT_COLUMN: str(closest_match)}, like=True)
        if question_id != 0:
            children_questions = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUBJECT_COLUMN,
                                                          conditions={PARENT_ID_COLUMN: str(question_id)}, multiple_values=True)
            
        answer = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=ANSWER_COLUMN_NAME,
                                                    conditions={QUESTION_SUBJECT_COLUMN: str(closest_match)}, like=True)



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

        if response_list:
            self.chatbot.logger.info(
                'Selecting response from {} optimal responses.'.format(
                    len(response_list)
                )
            )

            response = self.select_response(
                input_statement,
                response_list,
                self.Story_ID,
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

        if not children_questions:
            children_questions = []
        
        if answer:
            response.text = answer
            return response, self.Story_ID ,children_questions

        return response, self.Story_ID ,children_questions
