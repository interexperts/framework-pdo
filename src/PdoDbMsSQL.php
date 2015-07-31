<?php
namespace InterExperts\PDO;

use \InterExperts\Config;
/*
	(c) InterExperts	
*/

/**
 *	Deze class is een wrapperclass voor PDO. Doordat dit een singleton is, wordt altijd maar één database-object tegelijk gebruikt
 *	(besparing van resources). Met PdoDb::getInstance() wordt een PDO-object gegeven, dat is ingesteld volgens de instellingen van
 *	de configuratie in het algemene configuratiebestand.
 *	
 *	Gebruik als volgt:
 *	$db = PdoDb::getInstance();
 *	
 *	@brief		Wrapper-class voor PDO
 *  @SuppressWarnings(PHPMD.UnusedPrivateField)
**/
class PdoDbMsSQL {
	const BOOL_FALSE = '0';
	const BOOL_TRUE = '1';

	static private $instance = array();
	static private $defaultContext = 'ms';

	/**
	 * @codeCoverageIgnore
	 */
	private function __construct() {
	}
	
	/**
	 *	Geeft een PDO-object. Indien er nog geen object is geinitialiseerd wordt er een gecreëerd,
	 *	anders wordt het bestaande object geretourneerd.
	 *	@param	$type	(optional) Specifeer welke databaseconfiguratie gebruikt moet worden, standaard 'db' (in $config['db'][...]).
	*/
	static function getInstance($type = null, $name = 'default') {
		if(is_null($type)){
			$type = self::$defaultContext;
		}
		// Maak een PDO-object aan voor de gegeven $type
		if (!isset(PdoDbMsSQL::$instance[$type . '-' . $name])) {
			$config = Config::getInstance();
			$host     = $config->get($type, 'host');
			$database = $config->get($type, 'database');
			$user     = $config->get($type, 'user');
			$password = $config->get($type, 'pass');
			try {
				PdoDbMsSQL::$instance[$type . '-' . $name] = new \PDO("dblib:host=".$host.";dbname=".$database, $user, $password);
				PdoDbMsSQL::$instance[$type . '-' . $name]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
			}
			catch(\PDOException $e) {
				throw new \Exception("PDO error.".$e->getMessage());
			}
			
		}
		return PdoDbMsSQL::$instance[$type . '-' . $name];
	}

	static function setContext($context){
		self::$defaultContext = $context;
	}
	
	/**
	 *	Deze functie bereidt een string voor op verwerking door een SQL-query met LIKE. Asterisks (*) worden omgezet naar %. Indien er geen
	 *	asterisks voorkomen in de oorspronkelijke string, wordt de string aan beide kanten voorzien van wildcards.
	 *	Op deze manier wordt gebruikers een transparant zoekfilter aangeboden.
	 *	
	 *	Voorbeelden:
	 *	- "test*" geeft "test%"
	 *	- "te*st" geeft "te%st"
	 *	- "test" geeft "%test%"
	 *	
	 *	@brief	Bereidt een string voor op SQL-verwerking met LIKE
	 *	@param	$data	String die verwerkt moet worden.
	 *	@return	Verwerkte string
	**/
	static function parseLikeData($data) {
		$data = str_replace('*', '%', $data, $count);
		if ($count == 0) {
			$data ='%'.$data.'%';
		}
		
		return $data;
	}

	/**
	 * Convert a DateTime object to a DATETIME string that is understood by MySQL
	 * @param DateTime $time The DateTime object that should be converted
	 * @return string DATETIME string
	 * @codeCoverageIgnore
	 */
	static public function dateTimeToSqlString(\DateTime $time) {
		return $time->format('Y-m-d H:i:s');
	}
}
?>