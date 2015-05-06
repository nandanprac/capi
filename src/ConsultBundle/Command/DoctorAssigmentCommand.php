<?php

namespace ConsultBundle\Command;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\HttpFoundation\Request;
use Elasticsearch;
/**
 * Command to merge the accounts and Make the necessary updates.
 */
class DoctorAssigmentCommand extends ContainerAwareCommand
{
    protected $queue;
    protected $general_words;

    /**
     * Initialize Connections
     * @param InputInterface  $input  input
     * @param OutputInterface $output output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        parent::initialize($input, $output);
        $this->container = $this->getContainer();
        $this->index  = $this->getContainer()->getParameter('elastic_index_name');
        $this->daa_debug = $this->container->getParameter('daa_debug');
        $this->general_words = array('have', 'that', 'with');
        $this->c_words = array('pace'=>array('neurosurgeons'=> 0, 'cardiologists'=> 1),
                               'neuro'=>array('neurosurgeons'=> 1, 'cardiologists'=> 0));
        $this->c_words_categories = array('neurosurgeons'=> 39, 'cardiologists'=> 48);
        $this->c_categories = array('neurosurgeons'=>5, 'cardiologists'=>5);
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
            ->addArgument('question', InputArgument::REQUIRED, 'Questions')
            ->addArgument('city', InputArgument::REQUIRED, 'Location');
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
        $question = $input->getArgument('question');
        $city = $input->getArgument('city');
        list($speciality, $speciality_prob) = $this->classifier($question);
        if(!$speciality){
          $doctors_id = array("red","green","blue","yellow","brown");
          return $random_keys=array_rand($doctors_id,3);
        }else{
          if(!$city){
            $city = "bangalore";
          }
          $client = new Elasticsearch\Client();

          $params['index'] = 'fabric_search_new';
          $params['type']  = 'search';
          $params['_source']  = array('doctor_id', 'doctor_name');
          //$params['body']  = $json;
          $params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.specialties.specialty';
          $params['body']['query']['bool']['must']['query_string']['query']  = $speciality;

          $params['body']['query']['bool']['must']['query_string']['default_field']  = 'search.city';
          $params['body']['query']['bool']['must']['query_string']['query']  = $city;
          $params['body']['from']  = 0;
          $params['body']['size']  = 100;
          $results = $client->search($params);

          $doctorIds = array();
          foreach ($results['hits']['hits'] as $result){
              array_push($doctorIds,$result["_source"]["doctor_id"]);
          }
        }
        return $doctorIds;
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
