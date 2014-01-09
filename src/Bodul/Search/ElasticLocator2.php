<?php
namespace Bodul\Search;

class ElasticLocator2
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


        $fieldQueryTitle = new \Elastica\Query\Text();
        $fieldQueryTitle->setFieldQuery('productTitleTokenized', $this->getTerm());
        //$fieldQueryTitle->setFieldParam('productTitle', 'analyzer', 'searchAnalyzer');
        $fieldQueryTitle->setFieldParam('productTitleTokenized', 'operator', 'and');
        $boolQuery->addShould($fieldQueryTitle);

        $fieldQueryDescription = new \Elastica\Query\Text();
        $fieldQueryDescription->setFieldQuery('productDescriptionTokenized', $this->getTerm());
        //$fieldQueryDescription->setFieldParam('productDescription', 'analyzer', 'searchAnalyzer');
        $fieldQueryDescription->setFieldParam('productDescriptionTokenized', 'operator', 'and');
        $boolQuery->addShould($fieldQueryDescription);

        $query = new \Elastica\Query($boolQuery);
        $query->setFields(
            array(
                 'productId', 'productTitle'
            )
        );


        $search = new \Elastica\Search($this->connector);

        $rawResults = $search->addIndex('product')->search($query, 20);

        return $rawResults;
    }

}
