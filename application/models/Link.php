<?php
class Model_Link extends Zend_Db_Table
{
	const TYPE_TWITTER = 1;
	const TYPE_FACEBOOK = 2;
	
	protected $_name = 'links';

	public static function getTypeLabel($type){
		switch($type){
			case self::TYPE_TWITTER:
				return 'Twitter';
			case self::TYPE_FACEBOOK:
				return 'Facebook';
			default:
				return 'Type Inconnu';
		}
	}
	
	public function deleteById($id){
		$where = array('links.id = ?' => $id);
		return $this->delete($where);
	}
	
	/**
	 * Get a table row from an array of attributes to match
	 * @param array $attributes - array of key=>values to match
	 */
	public function findAllByAttributes($attributes){
		
		$select = $this->_db->select()
				->from('links',array('*'));
		
		foreach($attributes as $key=>$attr){
			$select = $select->where('links.'.$key.' = ?', $attr );
		}
		
		return $select->query()->fetchAll();
	}
	
	public function findByAttributes($attributes){
		$all = $this->findAllByAttributes($attributes);
		return $all[0];
	}
	
	
	public function insert(array $data){
		//TODO check data
		parent::insert($data);
	}	
}