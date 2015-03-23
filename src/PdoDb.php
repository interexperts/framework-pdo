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
 *	@author		Geert Wirken <geert@interexperts.nl>
 *  @SuppressWarnings(PHPMD.UnusedPrivateField)
**/
class PdoDb {
	const BOOL_FALSE = '0';
	const BOOL_TRUE = '1';

	static private $instance = array();

	/**
	 * @codeCoverageIgnore
	 */
	private function __construct() {
	}

	/**
	 *	Geeft een PDO-object. Indien er nog geen object is geinitialiseerd wordt er een gecreëerd,
	 *	anders wordt het bestaande object geretourneerd.
	 *	@param	$type	(optional) Specifeer welke databaseconfiguratie gebruikt moet worden, standaard 'db' (in $config['db'][...]).
	 *	@param	$errors	(optional) Zorgt er voor dat PDO een exception geeft bij SQL-fouten.
	 * @codeCoverageIgnore
	*/
	static function getInstance($type = 'db') {
		// Maak een PDO-object aan voor de gegeven $type
		if (!isset(PdoDb::$instance[$type])) {
			$config = Config::getInstance();
			$host = $config->get($type, 'host');
			$database = $config->get($type, 'database');
			$user = $config->get($type, 'user');
			$password = $config->get($type, 'pass');

			if($config->get('system', 'debugmode')){
				$pdoObject = "\InterExperts\PDO\PdoDebug";
			}else{
				$pdoObject = "\PDO";
			}

			PdoDb::$instance[$type] = new $pdoObject("mysql:host=".$host.";dbname=".$database, $user, $password);
			PdoDb::$instance[$type]->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			PdoDb::$instance[$type]->exec('SET NAMES utf8');
		}
		return PdoDb::$instance[$type];
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
	 * @codeCoverageIgnore
	**/
	static function parseLikeData($data) {
		$data = str_replace('*', '%', $data, $count);
		if ($count == 0) {
			$data ='%'.$data.'%';
		}

		return $data;
	}

}
