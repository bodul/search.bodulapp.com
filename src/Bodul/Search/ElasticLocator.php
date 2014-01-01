<?php
namespace Bodul\Search;

class ElasticLocator
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

        $queryTerm = new \Elastica\Query\QueryString();
        $queryTerm->setQuery($this->getTerm());
        $queryTerm->setFields(
            array(
                 'productTitle',
                 'productDescription'
            )
        );
        $queryTerm->setDefaultOperator('and');

        $query = new \Elastica\Query($queryTerm);
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
