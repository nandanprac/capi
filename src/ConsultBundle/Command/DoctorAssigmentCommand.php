<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use ConsultBundle\Queue\AbstractQueue as Queue;
use ConsultBundle\Constants\ConsultFeatureData;
use ConsultBundle\ConsultDomain;
use Elasticsearch;

/**
 * Command to merge the accounts and Make the necessary updates.
 */
class DoctorAssigmentCommand extends ContainerAwareCommand
{
    /**
     * Initialize Connections
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->queue = $this->container->get('consult.consult_queue');
        $this->daa_debug = $this->container->getParameter('daa_debug');
        $this->client = new Elasticsearch\Client();
        $this->fabric_search = $this->container->getParameter('elastic_index_name');
        $this->general_words = array('have', 'that', 'with');
        $this->c_words = array('pace'=>array('Somnologist'=> 0, 'Cardiologist'=> 1),
                               'neuro'=>array('Somnologist'=> 1, 'Cardiologist'=> 0));
        $this->c_words_categories = array('Somnologist'=> 39, 'Cardiologist'=> 48);
        $this->c_categories = array('Somnologist'=>5, 'Cardiologist'=>5);
        $this->p1_words = ConsultFeatureData::$P1_WORDS;
        $this->categories_grouping = ConsultFeatureData::$CATEGORY_GROUPING;
        $this->c_texts = 10;
        $this->c_tot_words = 81;
    }

    /**
     * Configure the task with options and arguments
     */
    protected function configure()
    {
        $this->setName('consult:question:doctorassignment:queue')
            ->setDescription('Queue to assign new questions to doctors')
            ->addArgument('domain', InputArgument::OPTIONAL, 'Consult Domain', 'https://consult.practo.com');
    }
    /**
     * Command executes logic
     *
     * @param InputInterface  $input  - InputInterface
     * @param OutputInterface $output - OutputInterface
     *
     * @return null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $request = Request::create($input->getArgument('domain'));
        $consultDomain = new ConsultDomain($request);
        $this->container->set('consult.consult_domain', $consultDomain);
        $this->queue->setConsultDomain($consultDomain);
        while (1) {
          $newJob = $this->queue
            ->setQueueName(Queue::DAA)
            ->receiveMessage();
          if ($newJob) {
              $jobData = json_decode($newJob, true);
              try{
                  $question_id = array_key_exists('question_id', $jobData) ? $jobData['question_id'] : null;
                  $question = array_key_exists('question', $jobData) ? $jobData['question'] : null;
                  $city = array_key_exists('city', $jobData) ? $jobData['city'] : null;
                  $tag = array_key_exists('tags', $jobData) ? $jobData['tags'] : null;
                  $specialities = array();
                  if ($tag) {
                      foreach (array_map('strtolower', explode(",",$tag)) as $keyword){
                          if (array_key_exists(strtolower(trim($keyword)), array_change_key_case($this->categories_grouping))){
                              array_push($specialities, array_change_key_case($this->categories_grouping)[strtolower(trim($keyword))]);
                          }
                      }
                      if (empty($specialities)){
                          foreach (array_map('strtolower', explode(",",$tag)) as $keyword) {
                              if (array_key_exists(strtolower(trim($keyword)), array_change_key_case($this->p1_words))){
                                  if(empty($specialities)){
                                      $specialities = $this->p1_words[$keyword];
                                  } else {
                                      $specialities = array_intersect($specialities, $this->p1_words[$keyword]);
                                  }
                              }
                          }
                      }
                  }
                  if ($specialities){
                      list($speciality, $speciality_prob) = $this->classifier($question);
                      $speciality = shuffle($specialities)[0];
                  }
                  list($speciality, $speciality_prob) = $this->classifier($question);
                  if ($specialities && in_array($speciality,$specialities)) {
                      $state = 'ASSIGNED';
                  } elseif ($specialities && !in_array($speciality,$specialities)) {
                      $state = 'MISMATCH';
                  } elseif (empty($specialities)) {
                      if ($speciality && $speciality != 'GENERIC') {
                          $state = 'ASSIGNED';
                      } elseif ($speciality == '') {
                          $state = 'UNCLASSIFIED';
                      } elseif ($speciality == 'GENERIC') {
                          $state = 'GENERIC';
                      }
                  }
                  $questionAction = array();
                  $questionAction['question_id'] = $question_id;
                  if($state == 'UNCLASSIFIED' or $state == 'MISMATCH'){
                      $questionAction['classified'] = ($state == 'UNCLASSIFIED') ? 0 : 1;
                      $questionAction['state'] = $state;
                      $questionAction['speciality'] = $speciality;
                      $questionAction['doctors'] = null;
                  } elseif ($state == 'ASSIGNED') {
                      if(!$city){
                        $city = "bangalore";
                      }

                      $params['index'] = $this->fabric_search;
                      $params['type']  = 'search';
                      $params['_source']  = array('practo_account_id', 'doctor_name');
                      $params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.specialties.specialty';
                      $params['body']['query']['bool']['must']['query_string']['query']  = $speciality;

                      $params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.city';
                      $params['body']['query']['bool']['must']['query_string']['query']  = $city;
                      $params['body']['from']  = 0;
                      $params['body']['size']  = 100;
                      $results = $this->client->search($params);
                      $doctorIds = array();
                      foreach ($results['hits']['hits'] as $result){
                          if (array_key_exists("practo_account_id", $result["_source"]))
                              array_push($doctorIds,$result["_source"]["practo_account_id"]);
                      }
                      $questionAction['classified'] = 1;
                      $questionAction['speciality'] = $speciality;
                      if ( $doctorIds ) {
                          $questionAction['state'] = $state;
                          shuffle($doctorIds);
                          $questionAction['doctors'] = array_slice($doctorIds, 0, 3);
                      } else {
                          $questionAction['state'] = 'DOCNOTFOUND';
                          $questionAction['doctors'] = null;
                      }
                  } elseif ($state = 'GENERIC') {
                      $questionAction['classified'] = 1;
                      $questionAction['speciality'] = $speciality;
                      $questionAction['state'] = $state;
                      $questionAction['doctors'] = null;
                  }
                  echo json_encode($questionAction);
                  $this->queue
                    ->setQueueName(Queue::ASSIGNMENT_UPDATE)
                    ->sendMessage(json_encode($questionAction));
              } catch (\Exception $e) {
                  $output->writeln($e->getMessage());
                  $output->writeln($newJob);
              }
              $this->queue->setQueueName(Queue::DAA)->deleteMessage($newJob);
          }
        }
    }

    protected function list_words($question){
        $text = preg_replace('/[^A-Za-z0-9\-\']/', ' ', $question);
        $words = array();
        $words_tmp = preg_split('/\s+/', strtolower($text));
        foreach ($words_tmp as $word) {
          if (!in_array($word, $words) and strlen($word) > 3 and !in_array($word, $this->general_words)) {
              array_push($words, $word);
          }
        }
        return $words;
    }

    protected function classifier($question){
        $category = '';
        $category_prob = 0;
        $tot_prob = 0;
        if ($this->daa_debug) {
            return array(false, false);
        }else{
            $words = $this->list_words($question);
            foreach ($this->c_categories as $c => $c_count) {
              $prob_c = floatval($this->c_categories[$c])/floatval($this->c_texts);
              $prob_total_c = $prob_c;
              foreach ($words as $p){
                if (in_array($p, array_keys($this->c_words))) {
                  $prob_p= floatval($this->c_words[$p][$c])/floatval(array_sum(array_values($this->c_words[$p])));
                  $prob_cond = $prob_p/$prob_c;
                  $prob = $prob_cond * floatval(array_sum(array_values($this->c_words[$p])))/floatval($this->c_tot_words);
                  $prob_total_c = $prob_total_c * $prob;
                }
                if ($category_prob < $prob_total_c) {
                  $category = $c;
                  $category_prob = $prob_total_c;
                }
             }
            }
            return array($category, $category_prob);
        }
      }
  }
