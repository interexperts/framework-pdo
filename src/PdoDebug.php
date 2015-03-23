<?php

namespace InterExperts\PDO;

/**
 * PDO wrapper die bijhoudt welke queries worden uitgevoerd, met tellers per query.
 */
class PdoDebug extends \PDO{
	private $queryCount = 0;
	private $statements = array();

	/**
	 * @codeCoverageIgnore
	 */
	public function query($query){
		// Increment the counter.
		$this->queryCount+=1;
		if(!isset($this->statements[$query])){
			$this->statements[$query] = 0;
		}
		$this->statements[$query] += 1;

		// Run the query.
		return parent::query($query);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function exec($statement){
		// Increment the counter.
		$this->queryCount+=1;
		if(!isset($this->statements[$statement])){
			$this->statements[$statement] = 0;
		}
		$this->statements[$statement] += 1;

		// Execute the statement.
		return parent::exec($statement);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function prepare($statement, $options=array()){
		// Increment the counter.
		$this->queryCount+=1;
		if(!isset($this->statements[$statement])){
			$this->statements[$statement] = 0;
		}
		$this->statements[$statement] += 1;

		// Execute the statement.
		return parent::prepare($statement, $options);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function execute($statement){
		// Increment the counter.
		$this->queryCount+=1;
		if(!isset($this->statements[$statement])){
			$this->statements[$statement] = 0;
		}
		$this->statements[$statement] += 1;

		// Execute the statement.
		return parent::execute($statement);
	}

	/**
	 * @codeCoverageIgnore
	 */
	public function getQueryCount(){
		return $this->queryCount;
	}
}