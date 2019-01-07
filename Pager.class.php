<?php
/**
 * unit-pager:/index.php
 *
 * @creation  2018-06-12
 * @version   1.0
 * @package   unit-pager
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */

/** namespace
 *
 * @created   2018-02-01
 */
namespace OP\UNIT;

/** Pager
 *
 * @creation  2018-06-12
 * @version   1.0
 * @package   unit-pager
 * @author    Tomoaki Nagahara <tomoaki.nagahara@gmail.com>
 * @copyright Tomoaki Nagahara All right reserved.
 */
class pager
{
	/** trait
	 *
	 */
	use \OP_CORE;

	/** Configurations.
	 *
	 * @var array
	 */
	private $_config;

	/** Generate configuration.
	 *
	 * @param	 array		 $config;
	 * @param	\IF_DATABASE $db
	 */
	function Config($config=[], $db)
	{
		//	...
		if( empty($config['database']) or empty($config['table']) ){
			throw new \Exception("Has not been set database or table.");
		}

		//	Count conditions.
		$this->_config['database'] = $config['database'] ?? null;
		$this->_config['table'] = $config['table'] ?? null;
		$this->_config['where'] = $config['where'] ?? null;
		$this->_config['order'] = $config['order'] ?? null;
		$this->_config['option'] = [];
		$this->_config['option']['wings'] = $config['wings'] ?? 2;

		//	If empty where case is generate where condition.
		if(!$this->_config['where'] ){
			if( $pkey = $db->PKey($this->_config['database'], $this->_config['table']) ?? null ){
				$this->_config['where'][$pkey]['evalu'] = '!=';
				$this->_config['where'][$pkey]['value'] = null;
			}else{
				throw new \Exception("This table has not been set primary key. ({$this->_config['database']}.{$this->_config['table']})");
			}
		}

		//	URL-Query key name.
		$this->_config['url-query-key-name'] = $config['url-query-key-name'] ?? 'page';

		//	Current page.
		$this->_config['current-page']  = (int)($config['current-page']  ?? $_GET[$this->_config['url-query-key-name']] ?? 1);

		//	Total record.
		$this->_config['count'] = $db->Count($this->_config, 'count');

		//	Page per record.
		$this->_config['limit'] = $config['limit']  ?? 10;

		//	Total pages.
		$this->_config['total-pages'] = (int)ceil( $this->_config['count'] / $this->_config['limit'] );

		//	Adjustment.
		if( $this->_config['current-page'] > $this->_config['total-pages'] ){
			$this->_config['current-page'] = $this->_config['total-pages'];
		};

		//	Start record positoin.
		$this->_config['offset'] = $config['offset'] ?? (((int)$this->_config['current-page']) -1) * $this->_config['limit'];

		//	...
		if( $this->_config['limit'] > 100 ){
			$this->_config['limit'] = 100;
		}

		//	...
		if( $this->_config['offset'] < 0 ){
			$this->_config['offset'] = 0;
		}

		//	Return SQL config. (Remove pagination config)
		return array_diff_key( $this->_config, ['url-query-key-name'=>null, 'current-page'=>null] );
	}

	/** Do display.
	 *
	 * @param	 array		 $config;
	 * @param	\IF_DATABASE $db
	 */
	function Display()
	{
		//	...
		$total_pages = $this->_config['total-pages'];

		//	...
		$current_page = $this->_config['current-page'];

		//	...
		$key_name = $this->_config['url-query-key-name'];

		//	...
		$option = $this->_config['option'];

		//	...
		include(__DIR__.'/pager-nav.phtml');

		//	...
		if( false ){
			var_dump($total_pages, $current_page, $key_name, $option);
		};
	}

	/** For developer
	 *
	 */
	function Debug()
	{
		D($this->_config);
	}
}
