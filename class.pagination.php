<?php
class Pagination {
	
	var $table = '';
	var $fields = '*';
	var $page = 1;
	var $perPage = 10;
	var $where = '';
	var $order = '';
	var $module = '';
	var $sql = '';
	
	var $pluginUrl = '';
	
	var $allRecords = array();
	
	var $pagination = '';
	
	function Pagination($table = '', $fields = '',$module= '',$sql= '', $where= '', $order='') {
	
		if (!empty($table)) {
			$this -> table = $table;
		}
		
		if (!empty($fields)) {
			$this -> fields = $fields;
		}

		if (!empty($module)) {
			$this -> module = $module;
		}

		if (!empty($sql)) {
			$this -> sql = $sql;
		}	
		
		if (!empty($where)) {
			$this -> where = $where;
		}
		
		if (!empty($order)) {
			$this -> order = $order;
		}
		
		
	}
	
	function startPaging($page = '') {
	
		if (!empty($page)) {
			$this -> page = $page;
		}
		
		if($this->module == 'application')
		  {
		     
         $query = $this->sql;
      }
    else
      {      
		    $query = "SELECT " . $this -> fields . " FROM " . $this -> table . "";
    		
    		if (!empty($this -> where)) {
  			$query .= " WHERE ";
			$query .= $this -> where;
    			/*foreach ($this -> where as $key => $val) {
    				echo 'dsdsdsds'.$key;
    				echo "<br>";
    				$query .= "`" . $key . "` = '" . $val . "'";
    			} by author */ 
    		}
    		
    		if (!empty($this -> order)) {
  			$query .= " ORDER BY ".$this -> order." ASC" ;
  			
    		}	
		  }
		
		$result = mysql_query($query) or print("Could not execute pagination query");
		
		$r = 1;
		
		if ($this -> page > 1) {
			$begRecord = (($this -> page * $this -> perPage) - ($this -> perPage)) + 1;
		} else {
			$begRecord = 1;
		}
			
		$endRecord = $begRecord + $this -> perPage;
		
		while ($row = mysql_fetch_array($result)) {
		
			$this -> allRecords[] = $row;
			
			if ($r >= $begRecord && $r < $endRecord) {
				$records[] = $row;
			}
			
			$r++;
		}
		
		$allRecordsCount = count($this -> allRecords);
		
		if (count($records) < $allRecordsCount) {
			
			if ($this -> page > 1) {
				$this -> pagination .= '<a class="page-numbers" href="' . $this -> pluginUrl . 'wpMailinglistPage=' . ($this -> page - 1) . '" title="">&laquo;&nbsp;Prev</a> ';
			}
			
			$p = 1;
			$k = 1;
			
			while ($p <= $allRecordsCount) {
				if ($k != $this -> page) {
					$this -> pagination .= '<a class="page-numbers" href="' . $this -> pluginUrl . 'wpMailinglistPage=' . $k . '" title="">' . $k . '</a> ';
				} else {
					$this -> pagination .= '<span class="page-numbers current">' . $k . '</span> ';
				}
				
				$p = $p + $this -> perPage;
				$k++;
			}
			
			if ((count($records) + $begRecord) <= $allRecordsCount) {
				$this -> pagination .= '<a class="page-numbers" href="' . $this -> pluginUrl . 'wpMailinglistPage=' . ($this -> page + 1) . '" title="">Next&nbsp;&raquo;</a>';
			}
		}
		
		return $records;
	}
}

?>