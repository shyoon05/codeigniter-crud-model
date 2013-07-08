<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * 기본 모델
 * 
 * @author 이지훈 <zeroion83@gmail.com>
 * @version 1.0.0
 */
class MY_Model extends CI_Model
{
	protected $table_name; // 테이블명(필수)
	protected $primary_key; // 기본키(필수)
		
	/**
	 * 삭제체크용 필드명을 입력하세요.
	 * 빈칸이면 이용하지 않습니다.	 
	 */
	protected $soft_delete_key = '';  // 예: is_delete
		
	/**
	 * 생성자, 수정자  필드명을 입력하세요. 
	 * 빈칸이면 이용하지 않습니다.	 	 
	 */
	protected $creator_key = '';  // 예: creator_user_id
	protected $modifier_key = '';  // 예: modifier_user_id
	protected $created_key = 'created';	

	function __construct()
	{
		parent::__construct();
	}

	//--------------------------------------------------------------------

	/**
	 * 읽기	 	 
	 */
	public function read($options = array())
	{
		if($options && !is_array($options))
		{
			return $this->read_one($options);
		}

		if(method_exists($this,'_before_read')) $this->_before_read($options);

		$limit = $options['limit'];
		$offset = $options['offset'];
		if(isset($limit,$offset)) $this->db->limit($limit,$offset);
		if(method_exists($this,'_where')) $this->_where($options);
		if($options['order_by'])
		{
			switch( gettype($options['order_by']) )
			{
				case "array":
					foreach($options['order_by'] as $key => $val)
					{
						if( ! $val) $val = 'asc';
						if( strpos($key, '.') === false)
						{							
							$key = $this->table_name.'.'.$key; //기본 테이블명 붙여주기
						}
						$this->db->order_by($key,$val);
					}
					break;
				case "string":
					$this->db->order_by($options['order_by']);
					break;
			}
		}
		$query = $this->db->get($this->table_name);

		if(method_exists($this,'_after_read')) $this->_after_read($options);

		if($query->num_rows()) return $query->result();
		else return NULL;
	}

	//--------------------------------------------------------------------

	/**
	 * 하나만 읽기
	 */
	public function read_one($id)
	{
		if(method_exists($this,'_before_read')) $this->_before_read($options);

		$this->db->where($this->table_name.'.'.$this->primary_key,$id);
		$query = $this->db->get($this->table_name);

		if(method_exists($this,'_after_read')) $this->_after_read($options);
		return $query->row();
	}

	//--------------------------------------------------------------------

	/**
	 * 카운트	 
	 */
	public function count($options = array())
	{
		if(method_exists($this,'_where')) $this->_where($options);
		$this->db->select('count(*) as cnt');
		$query = $this->db->get($this->table_name);
		$row = $query->row();
		return $row->cnt;
	}

	//--------------------------------------------------------------------

	/**
	 * 조건문	 
	 */
	//protected function _where($options = array());

	//--------------------------------------------------------------------

	/**
	 * 생성	 
	 */
	public function create($p)
	{
		if(method_exists($this,'_before_create')) $this->_before_create($p);

		//로그
		if(method_exists($this,'_create_set')) $this->_create_set($p);
		if(method_exists($this,'_set')) $this->_set($p);
		$this->db->set($this->created_key,'NOW()',false);		
		if($this->creator_key)
		{
			$this->db->set($this->creator_key, $p[$this->creator_key]);
		}
		$this->db->insert($this->table_name);

		//리턴
		$insert_id = $this->db->insert_id();
		$p['insert_id'] = $insert_id;

		if(method_exists($this,'_after_create')) $this->_after_create($p);

		if($insert_id) return $insert_id;
		else return false;
	}

	//--------------------------------------------------------------------

	/**
	 * 갱신
	 */
	public function update($id,$p)
	{
		if(method_exists($this,'_before_update')) $this->_before_update($id,$p);
		
		//로그
		$row = $this->read($id);

		$this->_set($p);
		$this->db->set('modifier_user_id',$userinfo['user_id']);
		$this->db->where($this->primary_key,$id);
		$this->db->limit(1);
		$this->db->update($this->table_name);

		//리턴
		$affected_rows = $this->db->affected_rows();

		if(method_exists($this,'_after_update')) $this->_after_update($id,$p);

		return $affected_rows;
	}

	//--------------------------------------------------------------------

	/**
	 * 데이터세팅	 
	 */
	//protected function _set($p);

	//--------------------------------------------------------------------

	/**
	 * 삭제하기	 
	 */
	public function real_delete($id)
	{
		if(method_exists($this,'_before_delete')) $this->_before_delete($id, TRUE);

		//삭제
		$this->db->where($this->primary_key,$id);
		$this->db->delete($this->table_name);

		//리턴
		$affected_rows = $this->db->affected_rows();

		if(method_exists($this,'_after_delete')) $this->_after_delete($id,  TRUE);

		return $affected_rows;
	}
	
	//--------------------------------------------------------------------

	/**
	 * 삭제하기 소프트
	 */
	public function delete($id,$is_real = FALSE)
	{
		if($is_real or ! $this->soft_delete_key)
		{
			return $this->real_delete($id);
		}

		if(method_exists($this,'_before_delete')) $this->_before_delete($id, FALSE);

		$this->db->where($this->primary_key, $id);
		$this->db->set($this->soft_delete_key, 1);
		$this->db->update($this->table_name);

		//리턴
		$affected_rows = $this->db->affected_rows();

		if(method_exists($this,'_after_delete')) $this->_after_delete($id, FALSE);

		return $affected_rows;
	}
	
	//--------------------------------------------------------------------

	/**
	 * 복구하기 소프트
	 */
	public function restore($id)
	{
		if(method_exists($this,'_before_restore')) $this->_before_restore($id);
		
		$this->db->where($this->primary_key, $id);
		$this->db->set($this->soft_delete_key, 1);
		$this->db->update($this->table_name);
		
		//리턴
		$affected_rows = $this->db->affected_rows();
		
		if(method_exists($this,'_after_restore')) $this->_after_restore($id);
		
		return $affected_rows;
	}

}

/* End of file MY_Model.php */
/* Location: ./application/core/MY_Model.php */