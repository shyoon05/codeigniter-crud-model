codeigniter-crud-model
======================

Base CRUD model for codeigniter.

CI 용 기본모델입니다.


Installation
------------

Just copy the core folder in your application folder.

core 폴더를 당신의 application 폴더 아래에 복사하세요.


Sample
------
```php
class sample_model extends MY_Model
{
	protected $table_name = 'posts';
	protected $primary_key = 'post_id';
	
	protected function _where($options = array())
	{
		if($options['post_title']) $this->db->like('post_title',$options['post_title']);		
	}
	
	protected function _set($p)
	{
		$this->db->set('post_title',$p['post_title']);
		$this->db->set('post_content',$p['post_content']);
	}

}
```

Create
------
```php
$param = array(
	"post_title" => "This is title.",
	"post_content" => "This is content."
);
$this->sample_model->create($param);
```

Read
----
###One
```php
$row = $this->sample_model->read($id);
```
###Multi
```php
$options = array(
	"post_title" => "mark",
);
$rows = $this->sample_model->read($options);
```
###Count
```php
$options = array(
	"post_title" => "mark",
);
$count = $this->sample_model->count($options);
```

Update
------
```php
$param = array(
	"post_title" => "This is modified title.",
	"post_content" => "This is modified content."
);
$this->sample_model->update(1, $param);
```

Delete
------
###Soft delete
```php
$this->sample_model->delete($id);
```

###Hard delete
```php
$this->sample_model->delete($id, true);
```

Advanced sample
---------------

```php
class sample_model extends MY_Model
{
	protected $table_name = 'posts';
	protected $primary_key = 'post_id';
	
	protected function _where($options = array())
	{
		if($options['post_title']) $this->db->like('post_title',$options['post_title']);		
	}
	
	protected function _set($p)
	{
		$this->db->set('post_title',$p['post_title']);
		$this->db->set('post_content',$p['post_content']);
	}
		
	// Before read
	protected function _before_read($options)
	{
		
	}
	
	// After read
	protected function _after_read($options)
	{
		
	}
	
	// Before create
	protected function _before_create($param)
	{
		
	}
	
	// After create
	protected function _after_create($param)
	{
		
	}
	
	// Before update
	protected function _before_update($id, $param)
	{
		
	}
	
	// After update
	protected function _after_update($id, $param)
	{
		
	}
	
	// Before delete
	protected function _before_delete($id, $is_real)
	{
		
	}
	
	// After delete
	protected function _after_delete($id, $is_real)
	{
		
	}
	
	// Join
	public function read_with_category($options = array())
	{
		$this->db->select('posts.*, categories.*');
		$this->db->join('categories','posts.category_id = categories.category_id','left');
		return $this->read($options);		
	}

}
```