<?php
namespace Bodul\Search\Cache;

class Apc
{

    protected $term;
    protected $cacheKey;

    protected $result;

    public function __construct($term)
    {
        $this->setTerm($term);
    }

    public function setTerm($term)
    {
        $this->term = $term;

        $this->setCacheKey($term);

        return $this;
    }

    public function setCacheKey($term)
    {
        $this->cacheKey = 'search_term_' . sha1($term);
    }

    public function fetch()
    {
        $result = apc_fetch($this->cacheKey);
        $this->setResult($result);

        return $this->result;
    }

    public function setResult($result)
    {
        $this->result = $result;

        return $this;
    }

    public function exists()
    {
        if ($this->result === false) {
            return false;
        } else {
            return true;
        }
    }

    public function saveResult($result)
    {
        $this->setResult($result);

        apc_add($this->cacheKey, $this->getResult(), 10);
    }

    public function getResult()
    {
        return $this->result;
    }

}
