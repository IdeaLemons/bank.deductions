<?php

// Global variable for table object
$deductions = NULL;

//
// Table class for deductions
//
class cdeductions extends cTable {
	var $Deduction_ID;
	var $PF;
	var $L_Ref;
	var $YEAR;
	var $MONTH;
	var $Bank_ID;
	var $Acc_ID;
	var $AMOUNT;
	var $STARTED;
	var $ENDED;
	var $TYPE;
	var $Batch;
	var $NOTES;

	//
	// Table class constructor
	//
	function __construct() {
		global $Language;

		// Language object
		if (!isset($Language)) $Language = new cLanguage();
		$this->TableVar = 'deductions';
		$this->TableName = 'deductions';
		$this->TableType = 'TABLE';

		// Update Table
		$this->UpdateTable = "`deductions`";
		$this->DBID = 'DB';
		$this->ExportAll = TRUE;
		$this->ExportPageBreakCount = 0; // Page break per every n record (PDF only)
		$this->ExportPageOrientation = "portrait"; // Page orientation (PDF only)
		$this->ExportPageSize = "a4"; // Page size (PDF only)
		$this->ExportExcelPageOrientation = ""; // Page orientation (PHPExcel only)
		$this->ExportExcelPageSize = ""; // Page size (PHPExcel only)
		$this->DetailAdd = TRUE; // Allow detail add
		$this->DetailEdit = FALSE; // Allow detail edit
		$this->DetailView = FALSE; // Allow detail view
		$this->ShowMultipleDetails = FALSE; // Show multiple details
		$this->GridAddRowCount = 5;
		$this->AllowAddDeleteRow = ew_AllowAddDeleteRow(); // Allow add/delete row
		$this->UserIDAllowSecurity = 0; // User ID Allow
		$this->BasicSearch = new cBasicSearch($this->TableVar);

		// Deduction_ID
		$this->Deduction_ID = new cField('deductions', 'deductions', 'x_Deduction_ID', 'Deduction_ID', '`Deduction_ID`', '`Deduction_ID`', 3, -1, FALSE, '`Deduction_ID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'NO');
		$this->Deduction_ID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Deduction_ID'] = &$this->Deduction_ID;

		// PF
		$this->PF = new cField('deductions', 'deductions', 'x_PF', 'PF', '`PF`', '`PF`', 3, -1, FALSE, '`EV__PF`', TRUE, TRUE, TRUE, 'FORMATTED TEXT', 'TEXT');
		$this->PF->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['PF'] = &$this->PF;

		// L_Ref
		$this->L_Ref = new cField('deductions', 'deductions', 'x_L_Ref', 'L_Ref', '`L_Ref`', '`L_Ref`', 201, -1, FALSE, '`L_Ref`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->fields['L_Ref'] = &$this->L_Ref;

		// YEAR
		$this->YEAR = new cField('deductions', 'deductions', 'x_YEAR', 'YEAR', '`YEAR`', '`YEAR`', 3, -1, FALSE, '`YEAR`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->YEAR->OptionCount = 18;
		$this->YEAR->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['YEAR'] = &$this->YEAR;

		// MONTH
		$this->MONTH = new cField('deductions', 'deductions', 'x_MONTH', 'MONTH', '`MONTH`', '`MONTH`', 3, -1, FALSE, '`MONTH`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->MONTH->OptionCount = 12;
		$this->MONTH->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['MONTH'] = &$this->MONTH;

		// Bank_ID
		$this->Bank_ID = new cField('deductions', 'deductions', 'x_Bank_ID', 'Bank_ID', '`Bank_ID`', '`Bank_ID`', 3, -1, FALSE, '`Bank_ID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Bank_ID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Bank_ID'] = &$this->Bank_ID;

		// Acc_ID
		$this->Acc_ID = new cField('deductions', 'deductions', 'x_Acc_ID', 'Acc_ID', '`Acc_ID`', '`Acc_ID`', 3, -1, FALSE, '`Acc_ID`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Acc_ID->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Acc_ID'] = &$this->Acc_ID;

		// AMOUNT
		$this->AMOUNT = new cField('deductions', 'deductions', 'x_AMOUNT', 'AMOUNT', '`AMOUNT`', '`AMOUNT`', 131, -1, FALSE, '`AMOUNT`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->AMOUNT->FldDefaultErrMsg = $Language->Phrase("IncorrectFloat");
		$this->fields['AMOUNT'] = &$this->AMOUNT;

		// STARTED
		$this->STARTED = new cField('deductions', 'deductions', 'x_STARTED', 'STARTED', '`STARTED`', 'DATE_FORMAT(`STARTED`, \'%Y/%m/%d\')', 133, 5, FALSE, '`STARTED`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->STARTED->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateYMD"));
		$this->fields['STARTED'] = &$this->STARTED;

		// ENDED
		$this->ENDED = new cField('deductions', 'deductions', 'x_ENDED', 'ENDED', '`ENDED`', 'DATE_FORMAT(`ENDED`, \'%Y/%m/%d\')', 133, 5, FALSE, '`ENDED`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXT');
		$this->ENDED->FldDefaultErrMsg = str_replace("%s", "/", $Language->Phrase("IncorrectDateYMD"));
		$this->fields['ENDED'] = &$this->ENDED;

		// TYPE
		$this->TYPE = new cField('deductions', 'deductions', 'x_TYPE', 'TYPE', '`TYPE`', '`TYPE`', 3, -1, FALSE, '`TYPE`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'RADIO');
		$this->TYPE->OptionCount = 4;
		$this->TYPE->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['TYPE'] = &$this->TYPE;

		// Batch
		$this->Batch = new cField('deductions', 'deductions', 'x_Batch', 'Batch', '`Batch`', '`Batch`', 3, -1, FALSE, '`Batch`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'SELECT');
		$this->Batch->FldDefaultErrMsg = $Language->Phrase("IncorrectInteger");
		$this->fields['Batch'] = &$this->Batch;

		// NOTES
		$this->NOTES = new cField('deductions', 'deductions', 'x_NOTES', 'NOTES', '`NOTES`', '`NOTES`', 200, -1, FALSE, '`NOTES`', FALSE, FALSE, FALSE, 'FORMATTED TEXT', 'TEXTAREA');
		$this->fields['NOTES'] = &$this->NOTES;
	}

	// Single column sort
	function UpdateSort(&$ofld) {
		if ($this->CurrentOrder == $ofld->FldName) {
			$sSortField = $ofld->FldExpression;
			$sLastSort = $ofld->getSort();
			if ($this->CurrentOrderType == "ASC" || $this->CurrentOrderType == "DESC") {
				$sThisSort = $this->CurrentOrderType;
			} else {
				$sThisSort = ($sLastSort == "ASC") ? "DESC" : "ASC";
			}
			$ofld->setSort($sThisSort);
			$this->setSessionOrderBy($sSortField . " " . $sThisSort); // Save to Session
			$sSortFieldList = ($ofld->FldVirtualExpression <> "") ? $ofld->FldVirtualExpression : $sSortField;
			$this->setSessionOrderByList($sSortFieldList . " " . $sThisSort); // Save to Session
		} else {
			$ofld->setSort("");
		}
	}

	// Session ORDER BY for List page
	function getSessionOrderByList() {
		return @$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY_LIST];
	}

	function setSessionOrderByList($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_ORDER_BY_LIST] = $v;
	}

	// Table level SQL
	var $_SqlFrom = "";

	function getSqlFrom() { // From
		return ($this->_SqlFrom <> "") ? $this->_SqlFrom : "`deductions`";
	}

	function SqlFrom() { // For backward compatibility
    	return $this->getSqlFrom();
	}

	function setSqlFrom($v) {
    	$this->_SqlFrom = $v;
	}
	var $_SqlSelect = "";

	function getSqlSelect() { // Select
		return ($this->_SqlSelect <> "") ? $this->_SqlSelect : "SELECT * FROM " . $this->getSqlFrom();
	}

	function SqlSelect() { // For backward compatibility
    	return $this->getSqlSelect();
	}

	function setSqlSelect($v) {
    	$this->_SqlSelect = $v;
	}
	var $_SqlSelectList = "";

	function getSqlSelectList() { // Select for List page
		$select = "";
		$select = "SELECT * FROM (" .
			"SELECT *, (SELECT CONCAT(`PF`,'" . ew_ValueSeparator(1, $this->PF) . "',`Name`) FROM `emp` `EW_TMP_LOOKUPTABLE` WHERE `EW_TMP_LOOKUPTABLE`.`PF` = `deductions`.`PF` LIMIT 1) AS `EV__PF` FROM `deductions`" .
			") `EW_TMP_TABLE`";
		return ($this->_SqlSelectList <> "") ? $this->_SqlSelectList : $select;
	}

	function SqlSelectList() { // For backward compatibility
    	return $this->getSqlSelectList();
	}

	function setSqlSelectList($v) {
    	$this->_SqlSelectList = $v;
	}
	var $_SqlWhere = "";

	function getSqlWhere() { // Where
		$sWhere = ($this->_SqlWhere <> "") ? $this->_SqlWhere : "";
		$this->TableFilter = "";
		ew_AddFilter($sWhere, $this->TableFilter);
		return $sWhere;
	}

	function SqlWhere() { // For backward compatibility
    	return $this->getSqlWhere();
	}

	function setSqlWhere($v) {
    	$this->_SqlWhere = $v;
	}
	var $_SqlGroupBy = "";

	function getSqlGroupBy() { // Group By
		return ($this->_SqlGroupBy <> "") ? $this->_SqlGroupBy : "";
	}

	function SqlGroupBy() { // For backward compatibility
    	return $this->getSqlGroupBy();
	}

	function setSqlGroupBy($v) {
    	$this->_SqlGroupBy = $v;
	}
	var $_SqlHaving = "";

	function getSqlHaving() { // Having
		return ($this->_SqlHaving <> "") ? $this->_SqlHaving : "";
	}

	function SqlHaving() { // For backward compatibility
    	return $this->getSqlHaving();
	}

	function setSqlHaving($v) {
    	$this->_SqlHaving = $v;
	}
	var $_SqlOrderBy = "";

	function getSqlOrderBy() { // Order By
		return ($this->_SqlOrderBy <> "") ? $this->_SqlOrderBy : "`Deduction_ID` DESC";
	}

	function SqlOrderBy() { // For backward compatibility
    	return $this->getSqlOrderBy();
	}

	function setSqlOrderBy($v) {
    	$this->_SqlOrderBy = $v;
	}

	// Apply User ID filters
	function ApplyUserIDFilters($sFilter) {
		return $sFilter;
	}

	// Check if User ID security allows view all
	function UserIDAllow($id = "") {
		$allow = EW_USER_ID_ALLOW;
		switch ($id) {
			case "add":
			case "copy":
			case "gridadd":
			case "register":
			case "addopt":
				return (($allow & 1) == 1);
			case "edit":
			case "gridedit":
			case "update":
			case "changepwd":
			case "forgotpwd":
				return (($allow & 4) == 4);
			case "delete":
				return (($allow & 2) == 2);
			case "view":
				return (($allow & 32) == 32);
			case "search":
				return (($allow & 64) == 64);
			default:
				return (($allow & 8) == 8);
		}
	}

	// Get SQL
	function GetSQL($where, $orderby) {
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$where, $orderby);
	}

	// Table SQL
	function SQL() {
		$sFilter = $this->CurrentFilter;
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$sSort = $this->getSessionOrderBy();
		return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(),
			$this->getSqlGroupBy(), $this->getSqlHaving(), $this->getSqlOrderBy(),
			$sFilter, $sSort);
	}

	// Table SQL with List page filter
	function SelectSQL() {
		$sFilter = $this->getSessionWhere();
		ew_AddFilter($sFilter, $this->CurrentFilter);
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$this->Recordset_Selecting($sFilter);
		if ($this->UseVirtualFields()) {
			$sSort = $this->getSessionOrderByList();
			return ew_BuildSelectSql($this->getSqlSelectList(), $this->getSqlWhere(), $this->getSqlGroupBy(),
				$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
		} else {
			$sSort = $this->getSessionOrderBy();
			return ew_BuildSelectSql($this->getSqlSelect(), $this->getSqlWhere(), $this->getSqlGroupBy(),
				$this->getSqlHaving(), $this->getSqlOrderBy(), $sFilter, $sSort);
		}
	}

	// Get ORDER BY clause
	function GetOrderBy() {
		$sSort = ($this->UseVirtualFields()) ? $this->getSessionOrderByList() : $this->getSessionOrderBy();
		return ew_BuildSelectSql("", "", "", "", $this->getSqlOrderBy(), "", $sSort);
	}

	// Check if virtual fields is used in SQL
	function UseVirtualFields() {
		$sWhere = $this->getSessionWhere();
		$sOrderBy = $this->getSessionOrderByList();
		if ($sWhere <> "")
			$sWhere = " " . str_replace(array("(",")"), array("",""), $sWhere) . " ";
		if ($sOrderBy <> "")
			$sOrderBy = " " . str_replace(array("(",")"), array("",""), $sOrderBy) . " ";
		if ($this->BasicSearch->getKeyword() <> "")
			return TRUE;
		if ($this->PF->AdvancedSearch->SearchValue <> "" ||
			$this->PF->AdvancedSearch->SearchValue2 <> "" ||
			strpos($sWhere, " " . $this->PF->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		if (strpos($sOrderBy, " " . $this->PF->FldVirtualExpression . " ") !== FALSE)
			return TRUE;
		return FALSE;
	}

	// Try to get record count
	function TryGetRecordCount($sSql) {
		$cnt = -1;
		if (($this->TableType == 'TABLE' || $this->TableType == 'VIEW' || $this->TableType == 'LINKTABLE') && preg_match("/^SELECT \* FROM/i", $sSql)) {
			$sSql = "SELECT COUNT(*) FROM" . preg_replace('/^SELECT\s([\s\S]+)?\*\sFROM/i', "", $sSql);
			$sOrderBy = $this->GetOrderBy();
			if (substr($sSql, strlen($sOrderBy) * -1) == $sOrderBy)
				$sSql = substr($sSql, 0, strlen($sSql) - strlen($sOrderBy)); // Remove ORDER BY clause
		} else {
			$sSql = "SELECT COUNT(*) FROM (" . $sSql . ") EW_COUNT_TABLE";
		}
		$conn = &$this->Connection();
		if ($rs = $conn->Execute($sSql)) {
			if (!$rs->EOF && $rs->FieldCount() > 0) {
				$cnt = $rs->fields[0];
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// Get record count based on filter (for detail record count in master table pages)
	function LoadRecordCount($sFilter) {
		$origFilter = $this->CurrentFilter;
		$this->CurrentFilter = $sFilter;
		$this->Recordset_Selecting($this->CurrentFilter);

		//$sSql = $this->SQL();
		$sSql = $this->GetSQL($this->CurrentFilter, "");
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			if ($rs = $this->LoadRs($this->CurrentFilter)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		$this->CurrentFilter = $origFilter;
		return intval($cnt);
	}

	// Get record count (for current List page)
	function SelectRecordCount() {
		$sSql = $this->SelectSQL();
		$cnt = $this->TryGetRecordCount($sSql);
		if ($cnt == -1) {
			$conn = &$this->Connection();
			if ($rs = $conn->Execute($sSql)) {
				$cnt = $rs->RecordCount();
				$rs->Close();
			}
		}
		return intval($cnt);
	}

	// INSERT statement
	function InsertSQL(&$rs) {
		$names = "";
		$values = "";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$names .= $this->fields[$name]->FldExpression . ",";
			$values .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($names, -1) == ",")
			$names = substr($names, 0, -1);
		while (substr($values, -1) == ",")
			$values = substr($values, 0, -1);
		return "INSERT INTO " . $this->UpdateTable . " ($names) VALUES ($values)";
	}

	// Insert
	function Insert(&$rs) {
		$conn = &$this->Connection();
		return $conn->Execute($this->InsertSQL($rs));
	}

	// UPDATE statement
	function UpdateSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "UPDATE " . $this->UpdateTable . " SET ";
		foreach ($rs as $name => $value) {
			if (!isset($this->fields[$name]) || $this->fields[$name]->FldIsCustom)
				continue;
			$sql .= $this->fields[$name]->FldExpression . "=";
			$sql .= ew_QuotedValue($value, $this->fields[$name]->FldDataType, $this->DBID) . ",";
		}
		while (substr($sql, -1) == ",")
			$sql = substr($sql, 0, -1);
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		ew_AddFilter($filter, $where);
		if ($filter <> "")	$sql .= " WHERE " . $filter;
		return $sql;
	}

	// Update
	function Update(&$rs, $where = "", $rsold = NULL, $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->UpdateSQL($rs, $where, $curfilter));
	}

	// DELETE statement
	function DeleteSQL(&$rs, $where = "", $curfilter = TRUE) {
		$sql = "DELETE FROM " . $this->UpdateTable . " WHERE ";
		if (is_array($where))
			$where = $this->ArrayToFilter($where);
		if ($rs) {
			if (array_key_exists('Deduction_ID', $rs))
				ew_AddFilter($where, ew_QuotedName('Deduction_ID', $this->DBID) . '=' . ew_QuotedValue($rs['Deduction_ID'], $this->Deduction_ID->FldDataType, $this->DBID));
		}
		$filter = ($curfilter) ? $this->CurrentFilter : "";
		ew_AddFilter($filter, $where);
		if ($filter <> "")
			$sql .= $filter;
		else
			$sql .= "0=1"; // Avoid delete
		return $sql;
	}

	// Delete
	function Delete(&$rs, $where = "", $curfilter = TRUE) {
		$conn = &$this->Connection();
		return $conn->Execute($this->DeleteSQL($rs, $where, $curfilter));
	}

	// Key filter WHERE clause
	function SqlKeyFilter() {
		return "`Deduction_ID` = @Deduction_ID@";
	}

	// Key filter
	function KeyFilter() {
		$sKeyFilter = $this->SqlKeyFilter();
		if (!is_numeric($this->Deduction_ID->CurrentValue))
			$sKeyFilter = "0=1"; // Invalid key
		$sKeyFilter = str_replace("@Deduction_ID@", ew_AdjustSql($this->Deduction_ID->CurrentValue, $this->DBID), $sKeyFilter); // Replace key value
		return $sKeyFilter;
	}

	// Return page URL
	function getReturnUrl() {
		$name = EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL;

		// Get referer URL automatically
		if (ew_ServerVar("HTTP_REFERER") <> "" && ew_ReferPage() <> ew_CurrentPage() && ew_ReferPage() <> "login.php") // Referer not same page or login page
			$_SESSION[$name] = ew_ServerVar("HTTP_REFERER"); // Save to Session
		if (@$_SESSION[$name] <> "") {
			return $_SESSION[$name];
		} else {
			return "deductionslist.php";
		}
	}

	function setReturnUrl($v) {
		$_SESSION[EW_PROJECT_NAME . "_" . $this->TableVar . "_" . EW_TABLE_RETURN_URL] = $v;
	}

	// List URL
	function GetListUrl() {
		return "deductionslist.php";
	}

	// View URL
	function GetViewUrl($parm = "") {
		if ($parm <> "")
			$url = $this->KeyUrl("deductionsview.php", $this->UrlParm($parm));
		else
			$url = $this->KeyUrl("deductionsview.php", $this->UrlParm(EW_TABLE_SHOW_DETAIL . "="));
		return $this->AddMasterUrl($url);
	}

	// Add URL
	function GetAddUrl($parm = "") {
		if ($parm <> "")
			$url = "deductionsadd.php?" . $this->UrlParm($parm);
		else
			$url = "deductionsadd.php";
		return $this->AddMasterUrl($url);
	}

	// Edit URL
	function GetEditUrl($parm = "") {
		$url = $this->KeyUrl("deductionsedit.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline edit URL
	function GetInlineEditUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=edit"));
		return $this->AddMasterUrl($url);
	}

	// Copy URL
	function GetCopyUrl($parm = "") {
		$url = $this->KeyUrl("deductionsadd.php", $this->UrlParm($parm));
		return $this->AddMasterUrl($url);
	}

	// Inline copy URL
	function GetInlineCopyUrl() {
		$url = $this->KeyUrl(ew_CurrentPage(), $this->UrlParm("a=copy"));
		return $this->AddMasterUrl($url);
	}

	// Delete URL
	function GetDeleteUrl() {
		return $this->KeyUrl("deductionsdelete.php", $this->UrlParm());
	}

	// Add master url
	function AddMasterUrl($url) {
		return $url;
	}

	function KeyToJson() {
		$json = "";
		$json .= "Deduction_ID:" . ew_VarToJson($this->Deduction_ID->CurrentValue, "number", "'");
		return "{" . $json . "}";
	}

	// Add key value to URL
	function KeyUrl($url, $parm = "") {
		$sUrl = $url . "?";
		if ($parm <> "") $sUrl .= $parm . "&";
		if (!is_null($this->Deduction_ID->CurrentValue)) {
			$sUrl .= "Deduction_ID=" . urlencode($this->Deduction_ID->CurrentValue);
		} else {
			return "javascript:ew_Alert(ewLanguage.Phrase('InvalidRecord'));";
		}
		return $sUrl;
	}

	// Sort URL
	function SortUrl(&$fld) {
		if ($this->CurrentAction <> "" || $this->Export <> "" ||
			in_array($fld->FldType, array(128, 204, 205))) { // Unsortable data type
				return "";
		} elseif ($fld->Sortable) {
			$sUrlParm = $this->UrlParm("order=" . urlencode($fld->FldName) . "&amp;ordertype=" . $fld->ReverseSort());
			return ew_CurrentPage() . "?" . $sUrlParm;
		} else {
			return "";
		}
	}

	// Get record keys from $_POST/$_GET/$_SESSION
	function GetRecordKeys() {
		global $EW_COMPOSITE_KEY_SEPARATOR;
		$arKeys = array();
		$arKey = array();
		if (isset($_POST["key_m"])) {
			$arKeys = ew_StripSlashes($_POST["key_m"]);
			$cnt = count($arKeys);
		} elseif (isset($_GET["key_m"])) {
			$arKeys = ew_StripSlashes($_GET["key_m"]);
			$cnt = count($arKeys);
		} elseif (!empty($_GET) || !empty($_POST)) {
			$isPost = ew_IsHttpPost();
			$arKeys[] = $isPost ? ew_StripSlashes(@$_POST["Deduction_ID"]) : ew_StripSlashes(@$_GET["Deduction_ID"]); // Deduction_ID

			//return $arKeys; // Do not return yet, so the values will also be checked by the following code
		}

		// Check keys
		$ar = array();
		foreach ($arKeys as $key) {
			if (!is_numeric($key))
				continue;
			$ar[] = $key;
		}
		return $ar;
	}

	// Get key filter
	function GetKeyFilter() {
		$arKeys = $this->GetRecordKeys();
		$sKeyFilter = "";
		foreach ($arKeys as $key) {
			if ($sKeyFilter <> "") $sKeyFilter .= " OR ";
			$this->Deduction_ID->CurrentValue = $key;
			$sKeyFilter .= "(" . $this->KeyFilter() . ")";
		}
		return $sKeyFilter;
	}

	// Load rows based on filter
	function &LoadRs($sFilter) {

		// Set up filter (SQL WHERE clause) and get return SQL
		//$this->CurrentFilter = $sFilter;
		//$sSql = $this->SQL();

		$sSql = $this->GetSQL($sFilter, "");
		$conn = &$this->Connection();
		$rs = $conn->Execute($sSql);
		return $rs;
	}

	// Load row values from recordset
	function LoadListRowValues(&$rs) {
		$this->Deduction_ID->setDbValue($rs->fields('Deduction_ID'));
		$this->PF->setDbValue($rs->fields('PF'));
		$this->L_Ref->setDbValue($rs->fields('L_Ref'));
		$this->YEAR->setDbValue($rs->fields('YEAR'));
		$this->MONTH->setDbValue($rs->fields('MONTH'));
		$this->Bank_ID->setDbValue($rs->fields('Bank_ID'));
		$this->Acc_ID->setDbValue($rs->fields('Acc_ID'));
		$this->AMOUNT->setDbValue($rs->fields('AMOUNT'));
		$this->STARTED->setDbValue($rs->fields('STARTED'));
		$this->ENDED->setDbValue($rs->fields('ENDED'));
		$this->TYPE->setDbValue($rs->fields('TYPE'));
		$this->Batch->setDbValue($rs->fields('Batch'));
		$this->NOTES->setDbValue($rs->fields('NOTES'));
	}

	// Render list row values
	function RenderListRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

   // Common render codes
		// Deduction_ID

		$this->Deduction_ID->CellCssStyle = "white-space: nowrap;";

		// PF
		// L_Ref
		// YEAR
		// MONTH
		// Bank_ID
		// Acc_ID
		// AMOUNT
		// STARTED
		// ENDED
		// TYPE
		// Batch
		// NOTES
		// Deduction_ID

		$this->Deduction_ID->ViewValue = $this->Deduction_ID->CurrentValue;
		$this->Deduction_ID->ViewCustomAttributes = "";

		// PF
		if ($this->PF->VirtualValue <> "") {
			$this->PF->ViewValue = $this->PF->VirtualValue;
		} else {
			$this->PF->ViewValue = $this->PF->CurrentValue;
		if (strval($this->PF->CurrentValue) <> "") {
			$sFilterWrk = "`PF`" . ew_SearchString("=", $this->PF->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `emp`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PF, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->PF->ViewValue = $this->PF->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PF->ViewValue = $this->PF->CurrentValue;
			}
		} else {
			$this->PF->ViewValue = NULL;
		}
		}
		$this->PF->CellCssStyle .= "text-align: left;";
		$this->PF->ViewCustomAttributes = "";

		// L_Ref
		$this->L_Ref->ViewValue = $this->L_Ref->CurrentValue;
		$this->L_Ref->CssStyle = "font-weight: bold;";
		$this->L_Ref->CellCssStyle .= "text-align: center;";
		$this->L_Ref->ViewCustomAttributes = "";

		// YEAR
		if (strval($this->YEAR->CurrentValue) <> "") {
			$this->YEAR->ViewValue = $this->YEAR->OptionCaption($this->YEAR->CurrentValue);
		} else {
			$this->YEAR->ViewValue = NULL;
		}
		$this->YEAR->CellCssStyle .= "text-align: center;";
		$this->YEAR->ViewCustomAttributes = "";

		// MONTH
		if (strval($this->MONTH->CurrentValue) <> "") {
			$this->MONTH->ViewValue = $this->MONTH->OptionCaption($this->MONTH->CurrentValue);
		} else {
			$this->MONTH->ViewValue = NULL;
		}
		$this->MONTH->CellCssStyle .= "text-align: center;";
		$this->MONTH->ViewCustomAttributes = "";

		// Bank_ID
		if (strval($this->Bank_ID->CurrentValue) <> "") {
			$sFilterWrk = "`Bank_ID`" . ew_SearchString("=", $this->Bank_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Bank_ID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Bank_ID->ViewValue = $this->Bank_ID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Bank_ID->ViewValue = $this->Bank_ID->CurrentValue;
			}
		} else {
			$this->Bank_ID->ViewValue = NULL;
		}
		$this->Bank_ID->ViewCustomAttributes = "";

		// Acc_ID
		if (strval($this->Acc_ID->CurrentValue) <> "") {
			$sFilterWrk = "`PF`" . ew_SearchString("=", $this->Acc_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `PF`, `Acc_NO` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `accounts`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Acc_ID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Acc_ID->ViewValue = $this->Acc_ID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Acc_ID->ViewValue = $this->Acc_ID->CurrentValue;
			}
		} else {
			$this->Acc_ID->ViewValue = NULL;
		}
		$this->Acc_ID->CellCssStyle .= "text-align: right;";
		$this->Acc_ID->ViewCustomAttributes = "";

		// AMOUNT
		$this->AMOUNT->ViewValue = $this->AMOUNT->CurrentValue;
		$this->AMOUNT->ViewValue = ew_FormatCurrency($this->AMOUNT->ViewValue, 2, 0, -1, -1);
		$this->AMOUNT->CellCssStyle .= "text-align: right;";
		$this->AMOUNT->ViewCustomAttributes = "";

		// STARTED
		$this->STARTED->ViewValue = $this->STARTED->CurrentValue;
		$this->STARTED->ViewValue = ew_FormatDateTime($this->STARTED->ViewValue, 5);
		$this->STARTED->CellCssStyle .= "text-align: right;";
		$this->STARTED->ViewCustomAttributes = "";

		// ENDED
		$this->ENDED->ViewValue = $this->ENDED->CurrentValue;
		$this->ENDED->ViewValue = ew_FormatDateTime($this->ENDED->ViewValue, 5);
		$this->ENDED->CellCssStyle .= "text-align: right;";
		$this->ENDED->ViewCustomAttributes = "";

		// TYPE
		if (strval($this->TYPE->CurrentValue) <> "") {
			$this->TYPE->ViewValue = $this->TYPE->OptionCaption($this->TYPE->CurrentValue);
		} else {
			$this->TYPE->ViewValue = NULL;
		}
		$this->TYPE->CellCssStyle .= "text-align: left;";
		$this->TYPE->ViewCustomAttributes = "";

		// Batch
		if (strval($this->Batch->CurrentValue) <> "") {
			$sFilterWrk = "`Batch_ID`" . ew_SearchString("=", $this->Batch->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Batch_ID`, `Batch_Number` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `batches`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Batch, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Batch->ViewValue = $this->Batch->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Batch->ViewValue = $this->Batch->CurrentValue;
			}
		} else {
			$this->Batch->ViewValue = NULL;
		}
		$this->Batch->ViewCustomAttributes = "";

		// NOTES
		$this->NOTES->ViewValue = $this->NOTES->CurrentValue;
		$this->NOTES->CellCssStyle .= "text-align: left;";
		$this->NOTES->ViewCustomAttributes = "";

		// Deduction_ID
		$this->Deduction_ID->LinkCustomAttributes = "";
		$this->Deduction_ID->HrefValue = "";
		$this->Deduction_ID->TooltipValue = "";

		// PF
		$this->PF->LinkCustomAttributes = "";
		$this->PF->HrefValue = "";
		if ($this->Export == "") {
			$this->PF->TooltipValue = strval($this->NOTES->CurrentValue);
			if ($this->PF->HrefValue == "") $this->PF->HrefValue = "javascript:void(0);";
			$this->PF->LinkAttrs["class"] = "ewTooltipLink";
			$this->PF->LinkAttrs["data-tooltip-id"] = "tt_deductions_x" . (($this->RowType <> EW_ROWTYPE_MASTER) ? @$this->RowCnt : "") . "_PF";
			$this->PF->LinkAttrs["data-tooltip-width"] = $this->PF->TooltipWidth;
			$this->PF->LinkAttrs["data-placement"] = EW_CSS_FLIP ? "left" : "right";
		}

		// L_Ref
		$this->L_Ref->LinkCustomAttributes = "";
		$this->L_Ref->HrefValue = "";
		$this->L_Ref->TooltipValue = "";

		// YEAR
		$this->YEAR->LinkCustomAttributes = "";
		$this->YEAR->HrefValue = "";
		$this->YEAR->TooltipValue = "";

		// MONTH
		$this->MONTH->LinkCustomAttributes = "";
		$this->MONTH->HrefValue = "";
		$this->MONTH->TooltipValue = "";

		// Bank_ID
		$this->Bank_ID->LinkCustomAttributes = "";
		$this->Bank_ID->HrefValue = "";
		$this->Bank_ID->TooltipValue = "";

		// Acc_ID
		$this->Acc_ID->LinkCustomAttributes = "";
		$this->Acc_ID->HrefValue = "";
		$this->Acc_ID->TooltipValue = "";

		// AMOUNT
		$this->AMOUNT->LinkCustomAttributes = "";
		$this->AMOUNT->HrefValue = "";
		$this->AMOUNT->TooltipValue = "";

		// STARTED
		$this->STARTED->LinkCustomAttributes = "";
		$this->STARTED->HrefValue = "";
		$this->STARTED->TooltipValue = "";

		// ENDED
		$this->ENDED->LinkCustomAttributes = "";
		$this->ENDED->HrefValue = "";
		$this->ENDED->TooltipValue = "";

		// TYPE
		$this->TYPE->LinkCustomAttributes = "";
		$this->TYPE->HrefValue = "";
		$this->TYPE->TooltipValue = "";

		// Batch
		$this->Batch->LinkCustomAttributes = "";
		$this->Batch->HrefValue = "";
		$this->Batch->TooltipValue = "";

		// NOTES
		$this->NOTES->LinkCustomAttributes = "";
		$this->NOTES->HrefValue = "";
		$this->NOTES->TooltipValue = "";

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Render edit row values
	function RenderEditRow() {
		global $Security, $gsLanguage, $Language;

		// Call Row Rendering event
		$this->Row_Rendering();

		// Deduction_ID
		$this->Deduction_ID->EditAttrs["class"] = "form-control";
		$this->Deduction_ID->EditCustomAttributes = "";
		$this->Deduction_ID->EditValue = $this->Deduction_ID->CurrentValue;
		$this->Deduction_ID->ViewCustomAttributes = "";

		// PF
		$this->PF->EditAttrs["class"] = "form-control";
		$this->PF->EditCustomAttributes = "";
		$this->PF->EditValue = $this->PF->CurrentValue;
		$this->PF->PlaceHolder = ew_RemoveHtml($this->PF->FldCaption());

		// L_Ref
		$this->L_Ref->EditAttrs["class"] = "form-control";
		$this->L_Ref->EditCustomAttributes = "";
		$this->L_Ref->EditValue = $this->L_Ref->CurrentValue;
		$this->L_Ref->PlaceHolder = ew_RemoveHtml($this->L_Ref->FldCaption());

		// YEAR
		$this->YEAR->EditCustomAttributes = "";
		$this->YEAR->EditValue = $this->YEAR->Options(TRUE);

		// MONTH
		$this->MONTH->EditCustomAttributes = "";
		$this->MONTH->EditValue = $this->MONTH->Options(TRUE);

		// Bank_ID
		$this->Bank_ID->EditCustomAttributes = "";

		// Acc_ID
		$this->Acc_ID->EditCustomAttributes = "";

		// AMOUNT
		$this->AMOUNT->EditAttrs["class"] = "form-control";
		$this->AMOUNT->EditCustomAttributes = "";
		$this->AMOUNT->EditValue = $this->AMOUNT->CurrentValue;
		$this->AMOUNT->PlaceHolder = ew_RemoveHtml($this->AMOUNT->FldCaption());
		if (strval($this->AMOUNT->EditValue) <> "" && is_numeric($this->AMOUNT->EditValue)) $this->AMOUNT->EditValue = ew_FormatNumber($this->AMOUNT->EditValue, -2, 0, -1, -1);

		// STARTED
		$this->STARTED->EditAttrs["class"] = "form-control";
		$this->STARTED->EditCustomAttributes = "";
		$this->STARTED->EditValue = ew_FormatDateTime($this->STARTED->CurrentValue, 5);
		$this->STARTED->PlaceHolder = ew_RemoveHtml($this->STARTED->FldCaption());

		// ENDED
		$this->ENDED->EditAttrs["class"] = "form-control";
		$this->ENDED->EditCustomAttributes = "";
		$this->ENDED->EditValue = ew_FormatDateTime($this->ENDED->CurrentValue, 5);
		$this->ENDED->PlaceHolder = ew_RemoveHtml($this->ENDED->FldCaption());

		// TYPE
		$this->TYPE->EditCustomAttributes = "";
		$this->TYPE->EditValue = $this->TYPE->Options(FALSE);

		// Batch
		$this->Batch->EditCustomAttributes = "";

		// NOTES
		$this->NOTES->EditAttrs["class"] = "form-control";
		$this->NOTES->EditCustomAttributes = "";
		$this->NOTES->EditValue = $this->NOTES->CurrentValue;
		$this->NOTES->PlaceHolder = ew_RemoveHtml($this->NOTES->FldCaption());

		// Call Row Rendered event
		$this->Row_Rendered();
	}

	// Aggregate list row values
	function AggregateListRowValues() {
	}

	// Aggregate list row (for rendering)
	function AggregateListRow() {

		// Call Row Rendered event
		$this->Row_Rendered();
	}
	var $ExportDoc;

	// Export data in HTML/CSV/Word/Excel/Email/PDF format
	function ExportDocument(&$Doc, &$Recordset, $StartRec, $StopRec, $ExportPageType = "") {
		if (!$Recordset || !$Doc)
			return;
		if (!$Doc->ExportCustom) {

			// Write header
			$Doc->ExportTableHeader();
			if ($Doc->Horizontal) { // Horizontal format, write header
				$Doc->BeginExportRow();
				if ($ExportPageType == "view") {
					if ($this->PF->Exportable) $Doc->ExportCaption($this->PF);
					if ($this->L_Ref->Exportable) $Doc->ExportCaption($this->L_Ref);
					if ($this->YEAR->Exportable) $Doc->ExportCaption($this->YEAR);
					if ($this->MONTH->Exportable) $Doc->ExportCaption($this->MONTH);
					if ($this->Bank_ID->Exportable) $Doc->ExportCaption($this->Bank_ID);
					if ($this->Acc_ID->Exportable) $Doc->ExportCaption($this->Acc_ID);
					if ($this->AMOUNT->Exportable) $Doc->ExportCaption($this->AMOUNT);
					if ($this->STARTED->Exportable) $Doc->ExportCaption($this->STARTED);
					if ($this->ENDED->Exportable) $Doc->ExportCaption($this->ENDED);
					if ($this->TYPE->Exportable) $Doc->ExportCaption($this->TYPE);
					if ($this->Batch->Exportable) $Doc->ExportCaption($this->Batch);
					if ($this->NOTES->Exportable) $Doc->ExportCaption($this->NOTES);
				} else {
					if ($this->PF->Exportable) $Doc->ExportCaption($this->PF);
					if ($this->YEAR->Exportable) $Doc->ExportCaption($this->YEAR);
					if ($this->MONTH->Exportable) $Doc->ExportCaption($this->MONTH);
					if ($this->Bank_ID->Exportable) $Doc->ExportCaption($this->Bank_ID);
					if ($this->Acc_ID->Exportable) $Doc->ExportCaption($this->Acc_ID);
					if ($this->AMOUNT->Exportable) $Doc->ExportCaption($this->AMOUNT);
					if ($this->STARTED->Exportable) $Doc->ExportCaption($this->STARTED);
					if ($this->ENDED->Exportable) $Doc->ExportCaption($this->ENDED);
					if ($this->TYPE->Exportable) $Doc->ExportCaption($this->TYPE);
					if ($this->Batch->Exportable) $Doc->ExportCaption($this->Batch);
					if ($this->NOTES->Exportable) $Doc->ExportCaption($this->NOTES);
				}
				$Doc->EndExportRow();
			}
		}

		// Move to first record
		$RecCnt = $StartRec - 1;
		if (!$Recordset->EOF) {
			$Recordset->MoveFirst();
			if ($StartRec > 1)
				$Recordset->Move($StartRec - 1);
		}
		while (!$Recordset->EOF && $RecCnt < $StopRec) {
			$RecCnt++;
			if (intval($RecCnt) >= intval($StartRec)) {
				$RowCnt = intval($RecCnt) - intval($StartRec) + 1;

				// Page break
				if ($this->ExportPageBreakCount > 0) {
					if ($RowCnt > 1 && ($RowCnt - 1) % $this->ExportPageBreakCount == 0)
						$Doc->ExportPageBreak();
				}
				$this->LoadListRowValues($Recordset);

				// Render row
				$this->RowType = EW_ROWTYPE_VIEW; // Render view
				$this->ResetAttrs();
				$this->RenderListRow();
				if (!$Doc->ExportCustom) {
					$Doc->BeginExportRow($RowCnt); // Allow CSS styles if enabled
					if ($ExportPageType == "view") {
						if ($this->PF->Exportable) $Doc->ExportField($this->PF);
						if ($this->L_Ref->Exportable) $Doc->ExportField($this->L_Ref);
						if ($this->YEAR->Exportable) $Doc->ExportField($this->YEAR);
						if ($this->MONTH->Exportable) $Doc->ExportField($this->MONTH);
						if ($this->Bank_ID->Exportable) $Doc->ExportField($this->Bank_ID);
						if ($this->Acc_ID->Exportable) $Doc->ExportField($this->Acc_ID);
						if ($this->AMOUNT->Exportable) $Doc->ExportField($this->AMOUNT);
						if ($this->STARTED->Exportable) $Doc->ExportField($this->STARTED);
						if ($this->ENDED->Exportable) $Doc->ExportField($this->ENDED);
						if ($this->TYPE->Exportable) $Doc->ExportField($this->TYPE);
						if ($this->Batch->Exportable) $Doc->ExportField($this->Batch);
						if ($this->NOTES->Exportable) $Doc->ExportField($this->NOTES);
					} else {
						if ($this->PF->Exportable) $Doc->ExportField($this->PF);
						if ($this->YEAR->Exportable) $Doc->ExportField($this->YEAR);
						if ($this->MONTH->Exportable) $Doc->ExportField($this->MONTH);
						if ($this->Bank_ID->Exportable) $Doc->ExportField($this->Bank_ID);
						if ($this->Acc_ID->Exportable) $Doc->ExportField($this->Acc_ID);
						if ($this->AMOUNT->Exportable) $Doc->ExportField($this->AMOUNT);
						if ($this->STARTED->Exportable) $Doc->ExportField($this->STARTED);
						if ($this->ENDED->Exportable) $Doc->ExportField($this->ENDED);
						if ($this->TYPE->Exportable) $Doc->ExportField($this->TYPE);
						if ($this->Batch->Exportable) $Doc->ExportField($this->Batch);
						if ($this->NOTES->Exportable) $Doc->ExportField($this->NOTES);
					}
					$Doc->EndExportRow();
				}
			}

			// Call Row Export server event
			if ($Doc->ExportCustom)
				$this->Row_Export($Recordset->fields);
			$Recordset->MoveNext();
		}
		if (!$Doc->ExportCustom) {
			$Doc->ExportTableFooter();
		}
	}

	// Get auto fill value
	function GetAutoFill($id, $val) {
		$rsarr = array();
		$rowcnt = 0;

		// Output
		if (is_array($rsarr) && $rowcnt > 0) {
			$fldcnt = count($rsarr[0]);
			for ($i = 0; $i < $rowcnt; $i++) {
				for ($j = 0; $j < $fldcnt; $j++) {
					$str = strval($rsarr[$i][$j]);
					$str = ew_ConvertToUtf8($str);
					if (isset($post["keepCRLF"])) {
						$str = str_replace(array("\r", "\n"), array("\\r", "\\n"), $str);
					} else {
						$str = str_replace(array("\r", "\n"), array(" ", " "), $str);
					}
					$rsarr[$i][$j] = $str;
				}
			}
			return ew_ArrayToJson($rsarr);
		} else {
			return FALSE;
		}
	}

	// Table level events
	// Recordset Selecting event
	function Recordset_Selecting(&$filter) {

		// Enter your code here	
	}

	// Recordset Selected event
	function Recordset_Selected(&$rs) {

		//echo "Recordset Selected";
	}

	// Recordset Search Validated event
	function Recordset_SearchValidated() {

		// Example:
		//$this->MyField1->AdvancedSearch->SearchValue = "your search criteria"; // Search value

	}

	// Recordset Searching event
	function Recordset_Searching(&$filter) {

		// Enter your code here	
	}

	// Row_Selecting event
	function Row_Selecting(&$filter) {

		// Enter your code here	
	}

	// Row Selected event
	function Row_Selected(&$rs) {

		//echo "Row Selected";
	}

	// Row Inserting event
	function Row_Inserting($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Inserted event
	function Row_Inserted($rsold, &$rsnew) {

		//echo "Row Inserted"
	}

	// Row Updating event
	function Row_Updating($rsold, &$rsnew) {

		// Enter your code here
		// To cancel, set return value to FALSE

		return TRUE;
	}

	// Row Updated event
	function Row_Updated($rsold, &$rsnew) {

		//echo "Row Updated";
	}

	// Row Update Conflict event
	function Row_UpdateConflict($rsold, &$rsnew) {

		// Enter your code here
		// To ignore conflict, set return value to FALSE

		return TRUE;
	}

	// Grid Inserting event
	function Grid_Inserting() {

		// Enter your code here
		// To reject grid insert, set return value to FALSE

		return TRUE;
	}

	// Grid Inserted event
	function Grid_Inserted($rsnew) {

		//echo "Grid Inserted";
	}

	// Grid Updating event
	function Grid_Updating($rsold) {

		// Enter your code here
		// To reject grid update, set return value to FALSE

		return TRUE;
	}

	// Grid Updated event
	function Grid_Updated($rsold, $rsnew) {

		//echo "Grid Updated";
	}

	// Row Deleting event
	function Row_Deleting(&$rs) {

		// Enter your code here
		// To cancel, set return value to False

		return TRUE;
	}

	// Row Deleted event
	function Row_Deleted(&$rs) {

		//echo "Row Deleted";
	}

	// Email Sending event
	function Email_Sending(&$Email, &$Args) {

		//var_dump($Email); var_dump($Args); exit();
		return TRUE;
	}

	// Lookup Selecting event
	function Lookup_Selecting($fld, &$filter) {

		//var_dump($fld->FldName, $fld->LookupFilters, $filter); // Uncomment to view the filter
		// Enter your code here

	}

	// Row Rendering event
	function Row_Rendering() {

		// Enter your code here	
	}

	// Row Rendered event
	function Row_Rendered() {
	if ($this->TYPE->ViewValue == "NEW") // List page only
			$this->RowAttrs["class"] = "success";
	if ($this->TYPE->ViewValue == "CHANGE") // List page only
			$this->RowAttrs["class"] = "info";
	if ($this->TYPE->ViewValue == "CONTINUE") // List page only
			$this->RowAttrs["class"] = "warning";
	if ($this->TYPE->ViewValue == "DELETE") // List page only
			$this->RowAttrs["class"] = "danger";
	}

	// User ID Filtering event
	function UserID_Filtering(&$filter) {

		// Enter your code here
	}
}
?>
