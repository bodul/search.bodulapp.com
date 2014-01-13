<?php
namespace Bodul\Search;

class ElasticTagLocator
{

    protected $connector;
    protected $term;

    public function __construct($connector, $term)
    {
        $this->connector = $connector;

        $this->setTerm($term);
    }

    public function setTerm($term)
    {
        $this->term = urldecode($term);

        return $this;
    }

    public function getTerm()
    {
        return $this->term;
    }

    public function getData()
    {
        $boolQuery = new \Elastica\Query\Bool();

        //Try to find desired word in tagName itself (untokenized version)
        $fieldQueryTitle = new \Elastica\Query\Text();
        $fieldQueryTitle->setFieldQuery('tagName', $this->getTerm());
        $fieldQueryTitle->setFieldParam('tagName', 'operator', 'and');
        $fieldQueryTitle->setFieldParam('tagName', 'boost', 10);
        $boolQuery->addShould($fieldQueryTitle);

        //Term must be located in tokenized tagName.
        $fieldQueryTitleTokenized = new \Elastica\Query\Text();
        $fieldQueryTitleTokenized->setFieldQuery('tagNameTokenized', $this->getTerm());
        $fieldQueryTitleTokenized->setFieldParam('tagNameTokenized', 'operator', 'and');
        $boolQuery->addMust($fieldQueryTitleTokenized);

        //Tag status should be: new, not enough and enabled.
        $filter = new \Elastica\Query\Terms('tagStatus', array(0, 11, 81));
        $boolQuery->addMust($filter);


        $query = new \Elastica\Query($boolQuery);
        $query->setFields(
            array(
                 'id', 'tagName'
            )
        );
        /*
        $query->setSort(
            array(
                 'tagStatus' =>
                 array('order' => 'DESC')
            ),
            array(
                 '_score' =>
                 array('order' => 'DESC')
            )
        );
        */


        $search = new \Elastica\Search($this->connector);

        $rawResults = $search->addIndex('product')->search($query, 20);

        return $rawResults;
    }

}
