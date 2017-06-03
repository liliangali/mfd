<?php
class Fabricbook extends Result
{
      function getTab(){
          $book_mod = m('fabricbook');
          $categorys = $book_mod->getCategory();
          $books = array();
          foreach((array)$categorys as $key =>$val){
              $books[] = array(
                  "id"    => $key,
                  "name"  => $val,
              );
          }
          $this->result = $books;
          return $this->sresult();
      }
      
	  function getList($data)
	  {
	      $cat       = isset($data->cat)       ? intval($data->cat) :   0;
	      $pageSize  = isset($data->pageSize)  ? intval($data->pageSize) : 10;
	      $pageIndex = isset($data->pageIndex) ? intval($data->pageIndex) : 1;
	      
	      if($pageIndex < 1) $pageIndex = 1;
	      if($pageSize < 1) $pageSize = 1;
	      
	      $limit     = $pageSize*($pageIndex-1).','.$pageSize;
	      $book_mod  = m('fabricbook');
	      $categorys = $book_mod->getCategory();
	      $current   = $cat;
	      if(empty($cat) && !empty($categorys)){
	          $current = current(array_keys($categorys));
	      }

	      $book_mod = m('fabricbook');
	      $bookArr  = $book_mod->find(array(
	         "conditions" => "category='{$current}' AND is_sale = 1",
	         'order'      => "add_time DESC",
	         'limit'      => $limit, 
	      ));
	      $books = array();
	      foreach((array) $bookArr as $key => $val){
	          $books[] = $val;
	      }
	      $this->result = $books;
	      return $this->sresult();
	}
	
	function getInfo($data){
	    $id       = isset($data->id)       ? intval($data->id) :   0;
	    
	    $book_mod = m('fabricbook');
	    
	    $book = $book_mod->get("id='{$id}' AND is_sale = 1");
	    
	    if(!empty($book)){
	        $galler_mod = m('bookgallery');
	        $gallerys   = $galler_mod->find(array(
	            "conditions" => "book_id = '{$id}'",
	        ));
	        $book["gallery"] = array();
	        $book['title'] = $book["aftersale"] == 1 ? "押金" : '金额';
	        $book['tips']  = $book["aftersale"] == 1 ? "退回面料时返还押金" : '';
	        foreach($gallerys as $key => $val){
	            $book["gallery"][] = $val;
	        }
	    }
	    $this->result = !empty($book) ? $book : array();
	    return $this->sresult();
	}
}

