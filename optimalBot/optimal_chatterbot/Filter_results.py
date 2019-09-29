from chatterbot.logic import LogicAdapter
from chatterbot.logic import BestMatch
from chatterbot import filters
from optimalBot.db_manager import *
from optimalBot.settings import *
import optimalBot.chatBot_tags as CT
import math
from chatterbot.conversation import Statement

class FilterResults():

    @staticmethod
    def __getAllFAQ(flowAdapter):
        data = flowAdapter.DBManager.fetch_query("SELECT p_optimal_bot_q.question , CASE WHEN COUNT(p_optimal_bot_q.question) > 1 THEN (SELECT optimal_bot_q_a.answer from optimal_bot_q_a inner join optimal_bot_q on optimal_bot_q_a.id=optimal_bot_q.answer_id WHERE optimal_bot_q.answer_id = optimal_bot_q_a.id AND optimal_bot_q.question = p_optimal_bot_q.question AND optimal_bot_q_a.question != p_optimal_bot_q.question LIMIT 1 ) ELSE p_optimal_bot_q_a.answer END AS Answer FROM `optimal_bot_q` AS p_optimal_bot_q , `optimal_bot_q_a` AS p_optimal_bot_q_a WHERE p_optimal_bot_q.answer_id = p_optimal_bot_q_a.id and p_optimal_bot_q_a.client_id = "+str(flowAdapter.client_id)+" GROUP BY p_optimal_bot_q.question")

        return data

    @staticmethod
    def __select_similar_question(flowAdapter,statement , threshold_similar = 0.75):
        # create statement for all FA questions

        questionsAanswers = FilterResults.__getAllFAQ(flowAdapter)

        all_faq_statements = []
        for question,answer in questionsAanswers:

            faq_statement = Statement(text=question)
            faq_statement.in_response_to = answer
            faq_statement.conversation = 'training'
            all_faq_statements.append(faq_statement)

        max_statement = Statement(text='')
        for faq_statement in all_faq_statements:

            faq_statement.confidence = flowAdapter.search_algorithm.compare_statements(faq_statement, statement)
            if  faq_statement.confidence > max_statement.confidence and faq_statement.confidence >= threshold_similar:
                max_statement.confidence = faq_statement.confidence
                max_statement.text = faq_statement.text
                max_statement.in_response_to = faq_statement.in_response_to

        return max_statement , all_faq_statements

    @staticmethod
    def __get_tags(flowAdapter, statement):

        statement_id = flowAdapter.DBManager.get_value(table_name=FQ_TABLE_NAME, column_name='answer_id',
                                                conditions={QUESTION_SUBJECT_COLUMN: statement.text })

        if statement_id:
            tag_ids = flowAdapter.DBManager.get_value(table_name=JOIN_TAGS_TABLE_NAME, column_name=JOIN_TAGS_TAG_ID_COLUMN_NAME,
                                               conditions={JOIN_TAGS_Q_A_ID_COLUMN_NAME: str(statement_id)},multiple_values=True)
            tags = []
            for tag_id in tag_ids:
                tag = flowAdapter.DBManager.get_value(table_name=TAGS_TABLE_NAME, column_name='tag',
                                               conditions={'id': str(tag_id[0])})
                tags.append(tag)

            return tags
        else:
            similarity = CT.Similarity(flowAdapter.glove,flowAdapter.tags)
            tags, keywords = similarity.get_tags(statement.text)
            tags = list(set(tags + keywords))

            return tags

    @staticmethod
    def __filter_results_according_tagging(flowAdapter,input_statement ,search_results ,threshold_similar):
        # get tags for all search_results
        if not search_results:
            return []
        tag_results = []
        for result in search_results:
            tags = FilterResults.__get_tags(flowAdapter,result)
            tag_results.append({'result':result , 'tags':tags , 'vote':0})

        # get tags for input_statement
        max_input_statement , _ = FilterResults.__select_similar_question(flowAdapter,input_statement,threshold_similar)
        tags_input_statement = FilterResults.__get_tags(flowAdapter,max_input_statement)


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
        return [tag_result['result']  for tag_result in tag_results if tag_result['vote'] == max_vote and max_vote >=0]

    @staticmethod
    def getResultsFromFAQ(flowAdapter,input_statement,threshold_similar = 0.75):
        max_statement , all_faq_statements = FilterResults.__select_similar_question(flowAdapter,input_statement,threshold_similar)
        if max_statement.text:
            return [max_statement]
        results = FilterResults.__filter_results_according_tagging(flowAdapter,input_statement,all_faq_statements,threshold_similar)

        return results


    @staticmethod
    def getSimlarityForFAQ(flowAdapter,input_statement,threshold_similar = 0.75):
        all_faq_statements = []
        questions = flowAdapter.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name=QUESTION_SUBJECT_COLUMN,conditions={CLIENT_ID_COLUMN : str(flowAdapter.client_id)},multiple_values=True)
        ids = flowAdapter.DBManager.get_value(table_name=FAQ_TABLE_NAME, column_name='id',conditions={CLIENT_ID_COLUMN : str(flowAdapter.client_id)},multiple_values=True)
        search_results =[]
        tag_results = []


        for question,id in zip(questions,ids):
            faq_statement = Statement(text=question[0])
            faq_statement.confidence = flowAdapter.search_algorithm.compare_statements(faq_statement, input_statement)
            search_results.append(faq_statement)
            all_faq_statements.append({'id':id[0] , 'question':question[0] , 'confidence':faq_statement.confidence })

        for result in search_results:
                tags = FilterResults.__get_tags(flowAdapter,result)
                tag_results.append({'result':result , 'tags':tags , 'vote':1})

        # get tags for input_statement
        max_statement = Statement(text='')
        for faq_statement in search_results:

            faq_statement.confidence = flowAdapter.search_algorithm.compare_statements(faq_statement, input_statement)
            if  faq_statement.confidence > max_statement.confidence and faq_statement.confidence >= threshold_similar:
                max_statement.confidence = faq_statement.confidence
                max_statement.text = faq_statement.text
                max_statement.in_response_to = faq_statement.in_response_to

        tags_input_statement = FilterResults.__get_tags(flowAdapter,max_statement)


        # voting results according tags
        for tag_input_statement in tags_input_statement:
            for tag_result in tag_results:
                for tag in tag_result['tags']:
                    if tag == tag_input_statement:
                        tag_result['vote']+=1
                if len(tag_input_statement) == 0 and len(tag_result) == 0 :
                    tag_result['vote']+=1
        max_vote = max([tag_result['vote'] for tag_result in tag_results])

        for i in range(len(tag_results)):
            confidence_vote = tag_results[i]['vote']/max_vote
            all_faq_statements[i]['confidence'] = (all_faq_statements[i]['confidence'] + confidence_vote)/2
            all_faq_statements[i]['confidence'] = round(all_faq_statements[i]['confidence'],2)

        all_faq_statements = sorted(all_faq_statements, key=lambda k: k['confidence'] , reverse=True)
        return all_faq_statements

