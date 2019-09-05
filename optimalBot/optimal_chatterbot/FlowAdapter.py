from chatterbot.logic import LogicAdapter
from chatterbot.logic import BestMatch
from chatterbot import filters
from optimalBot.db_manager import *
from optimalBot.settings import *
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
        bot_name, db_server, db_name, db_username, db_password, db_driver, _, _, _, _ = kwargs.get('bot_information')
        self.DBManager = DBManager(user=db_username,
                                   password=db_password,
                                   host=db_server,
                                   database=db_name)


    def __getAllFAQ(self):
        questions = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUBJECT_COLUMN,multiple_values=True)
        answers = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=ANSWER_COLUMN_NAME,multiple_values=True)

        return zip(questions,answers)

    def __select_similar_question(self,statement , threshold_similar = 0.75):
        # create statement for all FA questions

        questionsAanswers = self.__getAllFAQ()

        all_faq_statements = []
        for question,answer in questionsAanswers:

            faq_statement = Statement(text=question[0])
            faq_statement.in_response_to = answer[0]
            faq_statement.conversation = 'training'
            all_faq_statements.append(faq_statement)

        max_statement = Statement(text='')
        for faq_statement in all_faq_statements:

            faq_statement.confidence = self.search_algorithm.compare_statements(faq_statement, statement)
            if  faq_statement.confidence > max_statement.confidence and faq_statement.confidence >= threshold_similar:
                max_statement.confidence = faq_statement.confidence
                max_statement.text = faq_statement.text
                max_statement.in_response_to = faq_statement.in_response_to

        return max_statement , all_faq_statements


    def __get_tags(self, statement):
        print(statement.text)

        statement_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name='id',
                                 conditions={QUESTION_SUBJECT_COLUMN: statement.text})
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
            return similarity.get_tags(statement.text)

    def __filter_results_according_tagging(self,input_statement ,search_results ,threshold_similar):
        # get tags for all search_results
        tag_results = []
        for result in search_results:
            tags = self.__get_tags(result)
            tag_results.append({'result':result , 'tags':tags , 'vote':0})

        # get tags for input_statement
        max_input_statement , _ = self.__select_similar_question(input_statement,threshold_similar)
        tags_input_statement = self.__get_tags(max_input_statement)

        # voting results according tags
        for tag_input_statement in tags_input_statement:
            for tag_result in tag_results:
                for tag in tag_result['tags']:
                    if tag == tag_input_statement:
                        tag_result['vote']+=1
                if len(tag_input_statement) == 0 and len(tag_result) == 0 :
                    tag_result['vote']+=1

        # choose max vote
        max_vote = max([tag_result['vote'] for tag_result in tag_results])
        print(tag_results)
        return [tag_result['result']  for tag_result in tag_results if tag_result['vote'] == max_vote and max_vote >=0]





    def __getResultsFromFAQ(self,input_statement,threshold_similar = 0.75):
        max_statement , all_faq_statements = self.__select_similar_question(input_statement,threshold_similar)
        if max_statement.text:
            return [max_statement]
        results = self.__filter_results_according_tagging(input_statement,all_faq_statements,threshold_similar)

        return results





    def process(self, input_statement, additional_response_selection_parameters=None):
        faq_results = self.__getResultsFromFAQ(input_statement)
        search_results = self.search_algorithm.search(input_statement)
        results = faq_results + list(search_results)
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
            print("result.text "+result.text+" result.confidence" + str(result.confidence) +" with closest_match.confidence" + str(closest_match.confidence))
            # Stop searching if a match that is close enough is found
            print(result.conversation)
            print(result.in_response_to)
            if result.confidence >= self.maximum_similarity_threshold and result.conversation == 'training':
                if result.confidence > closest_match.confidence :
                    closest_match = result
                story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                    conditions={QUESTION_SUBJECT_COLUMN: result.text})
                if story_id > 0:
                    closest_match_story_id = story_id
                accepted_results.append(closest_match)



        for result in accepted_results:
            story_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=STORY_ID_COLUMN,
                                                conditions={QUESTION_SUBJECT_COLUMN: result.text})
            if story_id == self.Story_ID:
                if story_id_changed:
                    closest_match = result
                    story_id_changed = False
                elif result.confidence > closest_match.confidence:
                    closest_match = result


        if accepted_results and story_id_changed:
            self.Story_ID = closest_match_story_id




        # Suggested questions
        question_id = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_ID_COLUMN,
                                               conditions={QUESTION_SUBJECT_COLUMN: closest_match.text})
        if question_id != 0:
            children_questions = self.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUBJECT_COLUMN,
                                                          conditions={PARENT_ID_COLUMN: str(question_id)}, multiple_values=True)
        answer = closest_match.in_response_to

        if not answer and closest_match.conversation !='training':
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
