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
class post_model extends MY_Model
{
	protected $table_name = 'posts';
	protected $primary_key = 'post_id';

	function __construct()
	{
		parent::__construct();
	}

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
	"field1" => "...",
	"field2" => "..."
);
$this->sample_model->create($param);
```
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
$this->sample_model->read($id);
```
```php
$this->sample_model->read(1); 
```

###Multi
```php
$options = array(
	"your_option1" => "...",
	"your_option2" => "..."
);
$this->sample_model->read($options);
```
```php
$options = array(
	"post_title" => "mark",
);
$this->sample_model->read($options);
```

Update
------
```php
$param = array(
	"field1" => "...",
	"field2" => "..."
);
$this->sample_model->update($id, $param);
```
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