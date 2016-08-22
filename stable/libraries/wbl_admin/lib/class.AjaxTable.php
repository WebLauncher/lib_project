<?php


/**
	 * Ajax Table Class
	 */
	class AjaxTable
	{
		var $id='not_assigned_table_id';
		var $header=array();
		var $data_type='text';
		var $content=array();
		var $actions=array();
		var $total=0;
		var $update_action='update';
		var $edit_link='none';
		var $sort_dir='';
		var $sort_by='';
		var $sort_col_no=0;
		var $search_keyword='';
		
		/**
		 * Constructor
		 * @return
		 */
		function __construct()
		{
		}
		
		/**
		 * Process current request
		 * @return
		 */
		function process_request()
		{
			global $page;
			if(isset($_GET['startIndex']) && $_GET['startIndex']){
				$page->page_skip=$_GET['startIndex'];
			}
			if(isset($_GET['numberOfRows']) && $_GET['numberOfRows']){
				$page->page_offset=$_GET['numberOfRows'];
			}
			if(isset($_GET['sortBy']))
			{
				if(isset($_GET['sortAscending'])){
					if($_GET['sortAscending']=='false')$this->sort_dir='desc';
				}
					
				foreach($this->header as $k=>$v)
					if(!isset($v['sort']) && $_GET['sortBy']==$v['col'])
					{
						$this->sort_by=$v['col'];
						$this->header[$k]['sort_dir']=$this->sort_dir?0:1;
					}
			}
			if(isset($_GET['kwd']) && $_GET['kwd'])
			{
				$this->search_keyword=$_GET['kwd'];
			}
			@$page->session['pages'][$this->id]=$page->page_skip/$page->page_offset;
			$page->save_session();
		}
		
		/**
		 * Get active search fields
		 * @return array
		 */
		function get_search_fields()
		{
			$fields=array();
			foreach($this->header as $v)
				if(!isset($v['search']))
					$fields[]=$v['col'];
					
			return $fields;
		}
		
		/**
		 * Get form in template version
		 * @return
		 */
		function get_array($data=0)
		{
			$table=array();
			$table['id']=$this->id;
			$table['data']=$data;
			$table['data_type']=$this->data_type;
			$table['header']=$this->header;
			$table['content']=$this->content;
			$table['actions']=$this->actions;
			$table['sort_by']=$this->sort_by;
			$table['sort_dir']=$this->sort_dir;
			$table['total']=$this->total;
			$table['update_action']=$this->update_action;
			$table['edit_link']=$this->edit_link;
			
			return $table;
		}
		
		/**
		 * Add content from db
		 * @param object $content
		 * @return
		 */
		function process_content($content)
		{
			foreach($content as $row)
			{
				$this->add_row($row);
			}
		}
		
		/**
		 * Add row from db
		 * @param object $row
		 * @return
		 */
		function add_row($row)
		{
			$new_row=array();
			foreach($this->header as $col)
			{
				if(isset($row[$col['col']]))
					$new_row[$col['col']]['value']=$row[$col['col']];
			}
			$this->content[]=$new_row;
		}
		
		/**
		 * Add action to table
		 * @param object $title [optional]
		 * @param object $text [optional]
		 * @param object $link [optional]
		 * @param object $onclick [optional]
		 * @param object $refresh [optional]
		 * @param object $icon [optional]
		 * @return
		 */
		function add_action($title='',$text='',$link='',$onclick='',$refresh=1,$icon='',$confirm='')
		{
			$action=array(
				'title'=>$title,
				'text'=>$text,
				'link'=>$link,
				'onclick'=>$onclick,
				'refresh'=>$refresh,
				'icon'=>$icon,
				'confirm'=>$confirm
			);
			
			$this->actions[]=$action;
		}
		/**
		 * Display data function
		 */
		function display_data()
		{
			global $page;
			global $smarty;
			
			$smarty->assign('table',$this->get_array(1));			
			echo $smarty->fetch($page->objects['templates']['ajax_table']);			
			die;
		}
	}
?>