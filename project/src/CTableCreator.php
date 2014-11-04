<?php
class CTableCreator
{
	private $prefix = null;
	private $rows = null;
	private $name = null;
	
	public function __construct()
	{
		$this->rows = $_SESSION['creator']['columns'];
		$this->name = $_SESSION['creator']['name'];
		$this->prefix = $_SESSION['creator']['prefix'];
	}
	
	
	public function formCreator()
	{
		$html = 
		'<h1>Create '.$this->name.'</h1>
		<form method="post">';
		$columnName = array();
		$dataType = array();
			for($i= 0; $i < $this->rows;$i++)
			{
				$html .= '<p><label>Column name: </label><input type="text" name="columnName[]" value="">';
				$html .= '<label>Select list</label>
							 <select name="dataType[]"></p>
							   <option value = "INT">number</option>
							   <option value = "TEXT">text</option>
							   <option value = "DATETIME">current time</option>
							   <option value = "FLOAT">decimal</option>
							</select>';
			}
			
		$html .='<p><input type="submit" name="save" value="Save"></p>
		</form>';
		
		
		return $html;
	}
	
	
	public function queryBuilder($columnNames,$dataType)
	{
		$tableName = $this->prefix . $this->name;
		$columns = null;
		$forLog = 'INSERT INTO Custom_Tables(TableName,ColumnName, ColumnType) VALUES';
		for($i = 0; $i < $this->rows;$i++)
		{
			$forLog .= '("'.$this->name.'","'. $columnNames[$i] . '","'. $dataType[$i] . '"),';
			$columns .= $columnNames[$i] . ' '. $dataType[$i]. ',';
		}
		$columns = substr_replace($columns ,"",-1);
		$forLog = substr_replace($forLog ,"",-1);
		$forLog .= ';';
		$query = 'CREATE TABLE IF NOT EXISTS '.$tableName.'(Id INT PRIMARY KEY NOT NULL AUTO_INCREMENT,'. $columns. ');';
		$query .= $forLog;
		unset($_SESSION['creator']['columns']);
		unset($_SESSION['creator']['name']);
		unset($_SESSION['creator']['prefix']);
		return $query;
	}
	
	
	
	
	
	
}








?>