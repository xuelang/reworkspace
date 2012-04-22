<?php defined('SYSPATH') or die('No direct script access.');
/*
 * Anthony.chen 
 * 2010 reserved
 */

class DB {

	// Query types
	const SELECT =  1;
	const INSERT =  2;
	const UPDATE =  3;
	const DELETE =  4;
	const T = 't';
	const F = 'f';
	/*
	 * Supporting return multiple data;
	 */
	public static function insert_table($table_name,$data,$return_id='id',$db='default'){
		if (!is_array($data)){
			return false;
		}

		if (is_null($return_id)){
			self::query(self::SELECT,self::insert($table_name,array_keys($data))->values(array_values($data)))->execute($db);
			return true;
		}

		if (is_string($return_id)){
			$id = self::query(self::SELECT,self::insert($table_name,array_keys($data))->values(array_values($data))." returning ".$return_id)->execute($db)->get($return_id);
			return $id;
		}else{
			if (is_array($return_id)){
				$ids = implode(',',$return_id);
				$r_ids = self::query(self::SELECT,self::insert($table_name,array_keys($data))->values(array_values($data))." returning ".$ids)->execute($db)->current();
				return $r_ids;
			}
		}

		return false;
	}


	public static function update_table($table_name,$id,$data,$id_name='id',$db='default'){
		if (!is_array($data)){
			return false;
		}
		return self::update($table_name)->set($data)->where($id_name ,'=',$id)->execute($db);
	}

	public static function quote($s,$db='default'){
		if(!is_array($s)){
			return Database::instance($db)->quote($s);
		}else{ //Quote array and implode
			$_qs = array();
			foreach ($s as $ele){
				$_qs[] = self::quote($ele,$db);
			}

			$_quoteString = implode(',',$_qs);
			return $_quoteString;
		}
	}

	public static function escape($s,$db='default'){
		return Database::instance($db)->escape($s);
	}

	public static function quote_table($s,$db='default'){
		return Database::instance($db)->quote_table($s);
	}


	public static function getConnection($db = 'default'){
		return Database::instance($db)->getConnection();
	}


	public static function getChildren($table,$returnSql = false ,$pid= '0',$idname='id',$pidname='pid' ,$db='default'){
		$_sql = 'select * from '.$table.' where '.$pidname.'='.self::escape($pid,$db).
			" and $idname <>".DB::escape($pid,$db); 
		if($returnSql){
			return $_sql;
		}

		$_res = self::query(self::SELECT,$_sql)->execute($db)->as_array();
		if($_res){
			return $_res;
		}else{
			return false;
		}
	}

	/*
	 *
	 */
	public static function getTree($tableName,$returnSql=false,$startWith='0',$idCol='id',$pidCol='pid', $orderCol='id', $maxDepth=0,$level = 0,$delimiter = ';',$db='default'){
		$_funcParas = array();
		$_funcParas[] = self::quote($tableName,$db); //Table|View 
		$_funcParas[] = self::quote($idCol,$db); //ID column
		$_funcParas[] = self::quote($pidCol,$db); //Parent ID Column
		$_funcParas[] = self::quote($orderCol,$db); //Default Order by ASC
		$_funcParas[] = self::quote($startWith,$db); //Begin NODE
		$_funcParas[] = self::quote($maxDepth,$db); //Begin Depth of traverse
		$_funcParas[] = self::quote($delimiter,$db); //Delimitor,default ';'

		$_sql = 'select * from connectby('
			.implode(',',$_funcParas).')'
			.' as t(id int, pid int, level int, branch text, pos int)';
		if($level > 0){
			$_sql .= ' where level >='.self::quote($level);
		}

		if($returnSql) return $_sql;
		$_res = self::query(self::SELECT,$_sql,true)->execute($db)->as_array();
		if($_res){
			return $_res;
		}else{
			return false;
		}
	}

	public static function begin($db='default'){
		DB::query(Database::UPDATE, "BEGIN")->execute($db);
	}

	public static function commit($db='default'){
		DB::query(Database::UPDATE, "COMMIT")->execute($db);
	}

	public static function rollback($db='default'){
		DB::query(Database::UPDATE, "ROLLBACK")->execute($db);
	}


	/**
	 * Create a new [Database_Query] of the given type.
	 *
	 *     // Create a new SELECT query
	 *     $query = DB::query(Database::SELECT, 'SELECT * FROM users');
	 *
	 *     // Create a new DELETE query
	 *     $query = DB::query(Database::DELETE, 'DELETE FROM users WHERE id = 5');
	 *
	 * Specifying the type changes the returned result. When using
	 * `Database::SELECT`, a [Database_Query_Result] will be returned.
	 * `Database::INSERT` queries will return the insert id and number of rows.
	 * For all other queries, the number of affected rows is returned.
	 *
	 * @param   integer  type: Database::SELECT, Database::UPDATE, etc
	 * @param   string   SQL statement
	 * @return  Database_Query
	 */
	public static function query($type, $sql,$as_object = false)
	{
		return new Database_Query($type, $sql,$as_object);
	}

	/**
	 * Create a new [Database_Query_Builder_Select]. Each argument will be
	 * treated as a column. To generate a `foo AS bar` alias, use an array.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select('id', 'username');
	 *
	 *     // SELECT id AS user_id
	 *     $query = DB::select(array('id', 'user_id'));
	 *
	 * @param   mixed   column name or array($column, $alias) or object
	 * @param   ...
	 * @return  Database_Query_Builder_Select
	 */
	public static function select($columns = NULL)
	{
		return new Database_Query_Builder_Select(func_get_args());
	}

	/**
	 * Create a new [Database_Query_Builder_Select] from an array of columns.
	 *
	 *     // SELECT id, username
	 *     $query = DB::select_array(array('id', 'username'));
	 *
	 * @param   array   columns to select
	 * @return  Database_Query_Builder_Select
	 */
	public static function select_array(array $columns = NULL)
	{
		return new Database_Query_Builder_Select($columns);
	}

	/**
	 * Create a new [Database_Query_Builder_Insert].
	 *
	 *     // INSERT INTO users (id, username)
	 *     $query = DB::insert('users', array('id', 'username'));
	 *
	 * @param   string  table to insert into
	 * @param   array   list of column names or array($column, $alias) or object
	 * @return  Database_Query_Builder_Insert
	 */
	public static function insert($table = NULL, array $columns = NULL)
	{
		return new Database_Query_Builder_Insert($table, $columns);
	}

	/**
	 * Create a new [Database_Query_Builder_Update].
	 *
	 *     // UPDATE users
	 *     $query = DB::update('users');
	 *
	 * @param   string  table to update
	 * @return  Database_Query_Builder_Update
	 */
	public static function update($table = NULL)
	{
		return new Database_Query_Builder_Update($table);
	}

	/**
	 * Create a new [Database_Query_Builder_Delete].
	 *
	 *     // DELETE FROM users
	 *     $query = DB::delete('users');
	 *
	 * @param   string  table to delete from
	 * @return  Database_Query_Builder_Delete
	 */
	public static function delete($table = NULL)
	{
		return new Database_Query_Builder_Delete($table);
	}

	/**
	 * Create a new [Database_Expression] which is not escaped. An expression
	 * is the only way to use SQL functions within query builders.
	 *
	 *     $expression = DB::expr('COUNT(users.id)');
	 *
	 * @param   string  expression
	 * @return  Database_Expression
	 */
	public static function expr($string){
		return new Database_Expression($string);
	}

	/*
	 * Gettting paginated page
	 */
	public static function getPage($_sql,&$page,$orderBy ='updated desc', $dataPro='data',$pagePro = 'pagination',
		$config = NULL,$db = 'default',$as_object= true){

		$_csql = 'select count(1) as c from ('.$_sql.') st'; 
		$_c  = DB::query(DB::SELECT,$_csql)->execute($db)->get('c');

		if($config){
			$config['total_items'] = $_c;
			$_pagination = new Pagination($config);
		}else{
			$config = array();
			$config['total_items'] = $_c;
			$_pagination = new Pagination($config);
		}

		$_sql .= ' order by '.$orderBy;

		if($_pagination->offset){
			$_sql .= ' offset '.$_pagination->offset;
		}
		$_sql .= ' limit '.$_pagination->items_per_page;

		$_data = DB::query(DB::SELECT,$_sql,$as_object)->execute($db)->as_array();
		if(!$_data){
            $page->{$dataPro} = false;
            $page->{$pagePro} = false;
			return false;	
		}

		$page->{$dataPro} = $_data;
		$page->{$pagePro} = $_pagination;
		return true;
	}

	/*
	 * Get All roles 
	 * level to control the statrt 
	 */

	public static function getRoleTreeSql($role_id,$quote = false,$role_table,$level=0,$db='default'){
		$_sql = 'select id from ('.self::getTree($role_table,true,$role_id,'id','pid','id',
			0, //Maxdepth
			$level, //Level
			';',$db).') utree';
		if(!$quote) return $_sql;
		else return '('.$_sql.')';
	}

	/*
	 * Return sub query on Objects authorization 
	 * Child role objects and owned objects
	 * Parent control
	 */
	public static function getCURTreeSql($role_id,$user_id,$role_table,$quote = true,
		$role='role_id',$owner = 'owner_id' ,$db='default'){
			$_sql = ' '.$role.' in '.self::getRoleTreeSql($role_id,true,$role_table,
				1, //Level start with 1
				$db). ' or '.$owner.'='.DB::quote($user_id);
		if(!$quote) return $_sql;
		else return '('.$_sql.')';

	}
}

