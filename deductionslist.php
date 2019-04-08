<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "deductionsinfo.php" ?>
<?php include_once "empinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$deductions_list = NULL; // Initialize page object first

class cdeductions_list extends cdeductions {

	// Page ID
	var $PageID = 'list';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'deductions';

	// Page object name
	var $PageObjName = 'deductions_list';

	// Grid form hidden field names
	var $FormName = 'fdeductionslist';
	var $FormActionName = 'k_action';
	var $FormKeyName = 'k_key';
	var $FormOldKeyName = 'k_oldkey';
	var $FormBlankRowName = 'k_blankrow';
	var $FormKeyCountName = 'key_count';

	// Page name
	function PageName() {
		return ew_CurrentPage();
	}

	// Page URL
	function PageUrl() {
		$PageUrl = ew_CurrentPage() . "?";
		if ($this->UseTokenInUrl) $PageUrl .= "t=" . $this->TableVar . "&"; // Add page token
		return $PageUrl;
	}

	// Page URLs
	var $AddUrl;
	var $EditUrl;
	var $CopyUrl;
	var $DeleteUrl;
	var $ViewUrl;
	var $ListUrl;

	// Export URLs
	var $ExportPrintUrl;
	var $ExportHtmlUrl;
	var $ExportExcelUrl;
	var $ExportWordUrl;
	var $ExportXmlUrl;
	var $ExportCsvUrl;
	var $ExportPdfUrl;

	// Custom export
	var $ExportExcelCustom = FALSE;
	var $ExportWordCustom = FALSE;
	var $ExportPdfCustom = FALSE;
	var $ExportEmailCustom = FALSE;

	// Update URLs
	var $InlineAddUrl;
	var $InlineCopyUrl;
	var $InlineEditUrl;
	var $GridAddUrl;
	var $GridEditUrl;
	var $MultiDeleteUrl;
	var $MultiUpdateUrl;
    var $AuditTrailOnAdd = FALSE;
    var $AuditTrailOnEdit = FALSE;
    var $AuditTrailOnDelete = FALSE;
    var $AuditTrailOnView = FALSE;
    var $AuditTrailOnViewData = FALSE;
    var $AuditTrailOnSearch = FALSE;

	// Message
	function getMessage() {
		return @$_SESSION[EW_SESSION_MESSAGE];
	}

	function setMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_MESSAGE], $v);
	}

	function getFailureMessage() {
		return @$_SESSION[EW_SESSION_FAILURE_MESSAGE];
	}

	function setFailureMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_FAILURE_MESSAGE], $v);
	}

	function getSuccessMessage() {
		return @$_SESSION[EW_SESSION_SUCCESS_MESSAGE];
	}

	function setSuccessMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_SUCCESS_MESSAGE], $v);
	}

	function getWarningMessage() {
		return @$_SESSION[EW_SESSION_WARNING_MESSAGE];
	}

	function setWarningMessage($v) {
		ew_AddMessage($_SESSION[EW_SESSION_WARNING_MESSAGE], $v);
	}

	// Methods to clear message
	function ClearMessage() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
	}

	function ClearFailureMessage() {
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
	}

	function ClearSuccessMessage() {
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
	}

	function ClearWarningMessage() {
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	function ClearMessages() {
		$_SESSION[EW_SESSION_MESSAGE] = "";
		$_SESSION[EW_SESSION_FAILURE_MESSAGE] = "";
		$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = "";
		$_SESSION[EW_SESSION_WARNING_MESSAGE] = "";
	}

	// Show message
	function ShowMessage() {
		$hidden = TRUE;
		$html = "";

		// Message
		$sMessage = $this->getMessage();
		$this->Message_Showing($sMessage, "");
		if ($sMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sMessage;
			$html .= "<div class=\"alert alert-info ewInfo\">" . $sMessage . "</div>";
			$_SESSION[EW_SESSION_MESSAGE] = ""; // Clear message in Session
		}

		// Warning message
		$sWarningMessage = $this->getWarningMessage();
		$this->Message_Showing($sWarningMessage, "warning");
		if ($sWarningMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sWarningMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sWarningMessage;
			$html .= "<div class=\"alert alert-warning ewWarning\">" . $sWarningMessage . "</div>";
			$_SESSION[EW_SESSION_WARNING_MESSAGE] = ""; // Clear message in Session
		}

		// Success message
		$sSuccessMessage = $this->getSuccessMessage();
		$this->Message_Showing($sSuccessMessage, "success");
		if ($sSuccessMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sSuccessMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sSuccessMessage;
			$html .= "<div class=\"alert alert-success ewSuccess\">" . $sSuccessMessage . "</div>";
			$_SESSION[EW_SESSION_SUCCESS_MESSAGE] = ""; // Clear message in Session
		}

		// Failure message
		$sErrorMessage = $this->getFailureMessage();
		$this->Message_Showing($sErrorMessage, "failure");
		if ($sErrorMessage <> "") { // Message in Session, display
			if (!$hidden)
				$sErrorMessage = "<button type=\"button\" class=\"close\" data-dismiss=\"alert\">&times;</button>" . $sErrorMessage;
			$html .= "<div class=\"alert alert-danger ewError\">" . $sErrorMessage . "</div>";
			$_SESSION[EW_SESSION_FAILURE_MESSAGE] = ""; // Clear message in Session
		}
		echo "<div class=\"ewMessageDialog\"" . (($hidden) ? " style=\"display: none;\"" : "") . ">" . $html . "</div>";
	}
	var $PageHeader;
	var $PageFooter;

	// Show Page Header
	function ShowPageHeader() {
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		if ($sHeader <> "") { // Header exists, display
			echo "<p>" . $sHeader . "</p>";
		}
	}

	// Show Page Footer
	function ShowPageFooter() {
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		if ($sFooter <> "") { // Footer exists, display
			echo "<p>" . $sFooter . "</p>";
		}
	}

	// Validate page request
	function IsPageRequest() {
		global $objForm;
		if ($this->UseTokenInUrl) {
			if ($objForm)
				return ($this->TableVar == $objForm->GetValue("t"));
			if (@$_GET["t"] <> "")
				return ($this->TableVar == $_GET["t"]);
		} else {
			return TRUE;
		}
	}
	var $Token = "";
	var $TokenTimeout = 0;
	var $CheckToken = EW_CHECK_TOKEN;
	var $CheckTokenFn = "ew_CheckToken";
	var $CreateTokenFn = "ew_CreateToken";

	// Valid Post
	function ValidPost() {
		if (!$this->CheckToken || !ew_IsHttpPost())
			return TRUE;
		if (!isset($_POST[EW_TOKEN_NAME]))
			return FALSE;
		$fn = $this->CheckTokenFn;
		if (is_callable($fn))
			return $fn($_POST[EW_TOKEN_NAME], $this->TokenTimeout);
		return FALSE;
	}

	// Create Token
	function CreateToken() {
		global $gsToken;
		if ($this->CheckToken) {
			$fn = $this->CreateTokenFn;
			if ($this->Token == "" && is_callable($fn)) // Create token
				$this->Token = $fn();
			$gsToken = $this->Token; // Save to global variable
		}
	}

	//
	// Page class constructor
	//
	function __construct() {
		global $conn, $Language;
		global $UserTable, $UserTableConn;
		$GLOBALS["Page"] = &$this;
		$this->TokenTimeout = ew_SessionTimeoutTime();

		// Language object
		if (!isset($Language)) $Language = new cLanguage();

		// Parent constuctor
		parent::__construct();

		// Table object (deductions)
		if (!isset($GLOBALS["deductions"]) || get_class($GLOBALS["deductions"]) == "cdeductions") {
			$GLOBALS["deductions"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["deductions"];
		}

		// Initialize URLs
		$this->ExportPrintUrl = $this->PageUrl() . "export=print";
		$this->ExportExcelUrl = $this->PageUrl() . "export=excel";
		$this->ExportWordUrl = $this->PageUrl() . "export=word";
		$this->ExportHtmlUrl = $this->PageUrl() . "export=html";
		$this->ExportXmlUrl = $this->PageUrl() . "export=xml";
		$this->ExportCsvUrl = $this->PageUrl() . "export=csv";
		$this->ExportPdfUrl = $this->PageUrl() . "export=pdf";
		$this->AddUrl = "deductionsadd.php";
		$this->InlineAddUrl = $this->PageUrl() . "a=add";
		$this->GridAddUrl = $this->PageUrl() . "a=gridadd";
		$this->GridEditUrl = $this->PageUrl() . "a=gridedit";
		$this->MultiDeleteUrl = "deductionsdelete.php";
		$this->MultiUpdateUrl = "deductionsupdate.php";

		// Table object (emp)
		if (!isset($GLOBALS['emp'])) $GLOBALS['emp'] = new cemp();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'list', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'deductions', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);

		// User table object (emp)
		if (!isset($UserTable)) {
			$UserTable = new cemp();
			$UserTableConn = Conn($UserTable->DBID);
		}

		// List options
		$this->ListOptions = new cListOptions();
		$this->ListOptions->TableVar = $this->TableVar;

		// Export options
		$this->ExportOptions = new cListOptions();
		$this->ExportOptions->Tag = "div";
		$this->ExportOptions->TagClassName = "ewExportOption";

		// Other options
		$this->OtherOptions['addedit'] = new cListOptions();
		$this->OtherOptions['addedit']->Tag = "div";
		$this->OtherOptions['addedit']->TagClassName = "ewAddEditOption";
		$this->OtherOptions['detail'] = new cListOptions();
		$this->OtherOptions['detail']->Tag = "div";
		$this->OtherOptions['detail']->TagClassName = "ewDetailOption";
		$this->OtherOptions['action'] = new cListOptions();
		$this->OtherOptions['action']->Tag = "div";
		$this->OtherOptions['action']->TagClassName = "ewActionOption";

		// Filter options
		$this->FilterOptions = new cListOptions();
		$this->FilterOptions->Tag = "div";
		$this->FilterOptions->TagClassName = "ewFilterOption fdeductionslistsrch";

		// List actions
		$this->ListActions = new cListActions();
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

		// Security
		$Security = new cAdvancedSecurity();
		if (!$Security->IsLoggedIn()) $Security->AutoLogin();
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loading();
		$Security->LoadCurrentUserLevel($this->ProjectID . $this->TableName);
		if ($Security->IsLoggedIn()) $Security->TablePermission_Loaded();
		if (!$Security->CanList()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			$this->Page_Terminate(ew_GetUrl("index.php"));
		}

		// Get export parameters
		$custom = "";
		if (@$_GET["export"] <> "") {
			$this->Export = $_GET["export"];
			$custom = @$_GET["custom"];
		} elseif (@$_POST["export"] <> "") {
			$this->Export = $_POST["export"];
			$custom = @$_POST["custom"];
		} elseif (ew_IsHttpPost()) {
			if (@$_POST["exporttype"] <> "")
				$this->Export = $_POST["exporttype"];
			$custom = @$_POST["custom"];
		} else {
			$this->setExportReturnUrl(ew_CurrentUrl());
		}
		$gsExportFile = $this->TableVar; // Get export file, used in header

		// Get custom export parameters
		if ($this->Export <> "" && $custom <> "") {
			$this->CustomExport = $this->Export;
			$this->Export = "print";
		}
		$gsCustomExport = $this->CustomExport;
		$gsExport = $this->Export; // Get export parameter, used in header

		// Update Export URLs
		if (defined("EW_USE_PHPEXCEL"))
			$this->ExportExcelCustom = FALSE;
		if ($this->ExportExcelCustom)
			$this->ExportExcelUrl .= "&amp;custom=1";
		if (defined("EW_USE_PHPWORD"))
			$this->ExportWordCustom = FALSE;
		if ($this->ExportWordCustom)
			$this->ExportWordUrl .= "&amp;custom=1";
		if ($this->ExportPdfCustom)
			$this->ExportPdfUrl .= "&amp;custom=1";
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

		// Get grid add count
		$gridaddcnt = @$_GET[EW_TABLE_GRID_ADD_ROW_COUNT];
		if (is_numeric($gridaddcnt) && $gridaddcnt > 0)
			$this->GridAddRowCount = $gridaddcnt;

		// Set up list options
		$this->SetupListOptions();

		// Setup export options
		$this->SetupExportOptions();

		// Global Page Loading event (in userfn*.php)
		Page_Loading();

		// Page Load event
		$this->Page_Load();

		// Check token
		if (!$this->ValidPost()) {
			echo $Language->Phrase("InvalidPostRequest");
			$this->Page_Terminate();
			exit();
		}

		// Process auto fill
		if (@$_POST["ajax"] == "autofill") {
			$results = $this->GetAutoFill(@$_POST["name"], @$_POST["q"]);
			if ($results) {

				// Clean output buffer
				if (!EW_DEBUG_ENABLED && ob_get_length())
					ob_end_clean();
				echo $results;
				$this->Page_Terminate();
				exit();
			}
		}

		// Create Token
		$this->CreateToken();

		// Setup other options
		$this->SetupOtherOptions();

		// Set up custom action (compatible with old version)
		foreach ($this->CustomActions as $name => $action)
			$this->ListActions->Add($name, $action);

		// Show checkbox column if multiple action
		foreach ($this->ListActions->Items as $listaction) {
			if ($listaction->Select == EW_ACTION_MULTIPLE && $listaction->Allow) {
				$this->ListOptions->Items["checkbox"]->Visible = TRUE;
				break;
			}
		}
	}

	//
	// Page_Terminate
	//
	function Page_Terminate($url = "") {
		global $gsExportFile, $gTmpImages;

		// Page Unload event
		$this->Page_Unload();

		// Global Page Unloaded event (in userfn*.php)
		Page_Unloaded();

		// Export
		global $EW_EXPORT, $deductions;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($deductions);
				$doc->Text = $sContent;
				if ($this->Export == "email")
					echo $this->ExportEmail($doc->Text);
				else
					$doc->Export();
				ew_DeleteTmpImages(); // Delete temp images
				exit();
			}
		}
		$this->Page_Redirecting($url);

		 // Close connection
		ew_CloseConn();

		// Go to URL if specified
		if ($url <> "") {
			if (!EW_DEBUG_ENABLED && ob_get_length())
				ob_end_clean();
			header("Location: " . $url);
		}
		exit();
	}

	// Class variables
	var $ListOptions; // List options
	var $ExportOptions; // Export options
	var $SearchOptions; // Search options
	var $OtherOptions = array(); // Other options
	var $FilterOptions; // Filter options
	var $ListActions; // List actions
	var $SelectedCount = 0;
	var $SelectedIndex = 0;
	var $DisplayRecs = 20;
	var $StartRec;
	var $StopRec;
	var $TotalRecs = 0;
	var $RecRange = 10;
	var $Pager;
	var $DefaultSearchWhere = ""; // Default search WHERE clause
	var $SearchWhere = ""; // Search WHERE clause
	var $RecCnt = 0; // Record count
	var $EditRowCnt;
	var $StartRowCnt = 1;
	var $RowCnt = 0;
	var $Attrs = array(); // Row attributes and cell attributes
	var $RowIndex = 0; // Row index
	var $KeyCount = 0; // Key count
	var $RowAction = ""; // Row action
	var $RowOldKey = ""; // Row old key (for copy)
	var $RecPerRow = 0;
	var $MultiColumnClass;
	var $MultiColumnEditClass = "col-sm-12";
	var $MultiColumnCnt = 12;
	var $MultiColumnEditCnt = 12;
	var $GridCnt = 0;
	var $ColCnt = 0;
	var $DbMasterFilter = ""; // Master filter
	var $DbDetailFilter = ""; // Detail filter
	var $MasterRecordExists;	
	var $MultiSelectKey;
	var $Command;
	var $RestoreSearch = FALSE;
	var $DetailPages;
	var $Recordset;
	var $OldRecordset;

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError, $gsSearchError, $Security;

		// Search filters
		$sSrchAdvanced = ""; // Advanced search filter
		$sSrchBasic = ""; // Basic search filter
		$sFilter = "";

		// Get command
		$this->Command = strtolower(@$_GET["cmd"]);
		if ($this->IsPageRequest()) { // Validate request

			// Process list action first
			if ($this->ProcessListAction()) // Ajax request
				$this->Page_Terminate();

			// Handle reset command
			$this->ResetCmd();

			// Set up Breadcrumb
			if ($this->Export == "")
				$this->SetupBreadcrumb();

			// Hide list options
			if ($this->Export <> "") {
				$this->ListOptions->HideAllOptions(array("sequence"));
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			} elseif ($this->CurrentAction == "gridadd" || $this->CurrentAction == "gridedit") {
				$this->ListOptions->HideAllOptions();
				$this->ListOptions->UseDropDownButton = FALSE; // Disable drop down button
				$this->ListOptions->UseButtonGroup = FALSE; // Disable button group
			}

			// Hide options
			if ($this->Export <> "" || $this->CurrentAction <> "") {
				$this->ExportOptions->HideAllOptions();
				$this->FilterOptions->HideAllOptions();
			}

			// Hide other options
			if ($this->Export <> "") {
				foreach ($this->OtherOptions as &$option)
					$option->HideAllOptions();
			}

			// Get default search criteria
			ew_AddFilter($this->DefaultSearchWhere, $this->BasicSearchWhere(TRUE));
			ew_AddFilter($this->DefaultSearchWhere, $this->AdvancedSearchWhere(TRUE));

			// Get basic search values
			$this->LoadBasicSearchValues();

			// Get and validate search values for advanced search
			$this->LoadSearchValues(); // Get search values

			// Restore filter list
			$this->RestoreFilterList();
			if (!$this->ValidateSearch())
				$this->setFailureMessage($gsSearchError);

			// Restore search parms from Session if not searching / reset / export
			if (($this->Export <> "" || $this->Command <> "search" && $this->Command <> "reset" && $this->Command <> "resetall") && $this->CheckSearchParms())
				$this->RestoreSearchParms();

			// Call Recordset SearchValidated event
			$this->Recordset_SearchValidated();

			// Set up sorting order
			$this->SetUpSortOrder();

			// Get basic search criteria
			if ($gsSearchError == "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Get search criteria for advanced search
			if ($gsSearchError == "")
				$sSrchAdvanced = $this->AdvancedSearchWhere();
		}

		// Restore display records
		if ($this->getRecordsPerPage() <> "") {
			$this->DisplayRecs = $this->getRecordsPerPage(); // Restore from Session
		} else {
			$this->DisplayRecs = 20; // Load default
		}

		// Load Sorting Order
		$this->LoadSortOrder();

		// Load search default if no existing search criteria
		if (!$this->CheckSearchParms()) {

			// Load basic search from default
			$this->BasicSearch->LoadDefault();
			if ($this->BasicSearch->Keyword != "")
				$sSrchBasic = $this->BasicSearchWhere();

			// Load advanced search from default
			if ($this->LoadAdvancedSearchDefault()) {
				$sSrchAdvanced = $this->AdvancedSearchWhere();
			}
		}

		// Build search criteria
		ew_AddFilter($this->SearchWhere, $sSrchAdvanced);
		ew_AddFilter($this->SearchWhere, $sSrchBasic);

		// Call Recordset_Searching event
		$this->Recordset_Searching($this->SearchWhere);

		// Save search criteria
		if ($this->Command == "search" && !$this->RestoreSearch) {
			$this->setSearchWhere($this->SearchWhere); // Save to Session
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} else {
			$this->SearchWhere = $this->getSearchWhere();
		}

		// Build filter
		$sFilter = "";
		if (!$Security->CanList())
			$sFilter = "(0=1)"; // Filter all records
		ew_AddFilter($sFilter, $this->DbDetailFilter);
		ew_AddFilter($sFilter, $this->SearchWhere);

		// Set up filter in session
		$this->setSessionWhere($sFilter);
		$this->CurrentFilter = "";

		// Export data only
		if ($this->CustomExport == "" && in_array($this->Export, array("html","word","excel","xml","csv","email","pdf"))) {
			$this->ExportData();
			$this->Page_Terminate(); // Terminate response
			exit();
		}

		// Load record count first
		if (!$this->IsAddOrEdit()) {
			$bSelectLimit = $this->UseSelectLimit;
			if ($bSelectLimit) {
				$this->TotalRecs = $this->SelectRecordCount();
			} else {
				if ($this->Recordset = $this->LoadRecordset())
					$this->TotalRecs = $this->Recordset->RecordCount();
			}
		}

		// Search options
		$this->SetupSearchOptions();
	}

	// Build filter for all keys
	function BuildKeyFilter() {
		global $objForm;
		$sWrkFilter = "";

		// Update row index and get row key
		$rowindex = 1;
		$objForm->Index = $rowindex;
		$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		while ($sThisKey <> "") {
			if ($this->SetupKeyValues($sThisKey)) {
				$sFilter = $this->KeyFilter();
				if ($sWrkFilter <> "") $sWrkFilter .= " OR ";
				$sWrkFilter .= $sFilter;
			} else {
				$sWrkFilter = "0=1";
				break;
			}

			// Update row index and get row key
			$rowindex++; // Next row
			$objForm->Index = $rowindex;
			$sThisKey = strval($objForm->GetValue($this->FormKeyName));
		}
		return $sWrkFilter;
	}

	// Set up key values
	function SetupKeyValues($key) {
		$arrKeyFlds = explode($GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"], $key);
		if (count($arrKeyFlds) >= 1) {
			$this->Deduction_ID->setFormValue($arrKeyFlds[0]);
			if (!is_numeric($this->Deduction_ID->FormValue))
				return FALSE;
		}
		return TRUE;
	}

	// Get list of filters
	function GetFilterList() {

		// Initialize
		$sFilterList = "";
		$sFilterList = ew_Concat($sFilterList, $this->PF->AdvancedSearch->ToJSON(), ","); // Field PF
		$sFilterList = ew_Concat($sFilterList, $this->L_Ref->AdvancedSearch->ToJSON(), ","); // Field L_Ref
		$sFilterList = ew_Concat($sFilterList, $this->YEAR->AdvancedSearch->ToJSON(), ","); // Field YEAR
		$sFilterList = ew_Concat($sFilterList, $this->MONTH->AdvancedSearch->ToJSON(), ","); // Field MONTH
		$sFilterList = ew_Concat($sFilterList, $this->Acc_ID->AdvancedSearch->ToJSON(), ","); // Field Acc_ID
		$sFilterList = ew_Concat($sFilterList, $this->AMOUNT->AdvancedSearch->ToJSON(), ","); // Field AMOUNT
		$sFilterList = ew_Concat($sFilterList, $this->STARTED->AdvancedSearch->ToJSON(), ","); // Field STARTED
		$sFilterList = ew_Concat($sFilterList, $this->ENDED->AdvancedSearch->ToJSON(), ","); // Field ENDED
		$sFilterList = ew_Concat($sFilterList, $this->TYPE->AdvancedSearch->ToJSON(), ","); // Field TYPE
		$sFilterList = ew_Concat($sFilterList, $this->Batch->AdvancedSearch->ToJSON(), ","); // Field Batch
		$sFilterList = ew_Concat($sFilterList, $this->NOTES->AdvancedSearch->ToJSON(), ","); // Field NOTES
		if ($this->BasicSearch->Keyword <> "") {
			$sWrk = "\"" . EW_TABLE_BASIC_SEARCH . "\":\"" . ew_JsEncode2($this->BasicSearch->Keyword) . "\",\"" . EW_TABLE_BASIC_SEARCH_TYPE . "\":\"" . ew_JsEncode2($this->BasicSearch->Type) . "\"";
			$sFilterList = ew_Concat($sFilterList, $sWrk, ",");
		}

		// Return filter list in json
		return ($sFilterList <> "") ? "{" . $sFilterList . "}" : "null";
	}

	// Restore list of filters
	function RestoreFilterList() {

		// Return if not reset filter
		if (@$_POST["cmd"] <> "resetfilter")
			return FALSE;
		$filter = json_decode(ew_StripSlashes(@$_POST["filter"]), TRUE);
		$this->Command = "search";

		// Field PF
		$this->PF->AdvancedSearch->SearchValue = @$filter["x_PF"];
		$this->PF->AdvancedSearch->SearchOperator = @$filter["z_PF"];
		$this->PF->AdvancedSearch->SearchCondition = @$filter["v_PF"];
		$this->PF->AdvancedSearch->SearchValue2 = @$filter["y_PF"];
		$this->PF->AdvancedSearch->SearchOperator2 = @$filter["w_PF"];
		$this->PF->AdvancedSearch->Save();

		// Field L_Ref
		$this->L_Ref->AdvancedSearch->SearchValue = @$filter["x_L_Ref"];
		$this->L_Ref->AdvancedSearch->SearchOperator = @$filter["z_L_Ref"];
		$this->L_Ref->AdvancedSearch->SearchCondition = @$filter["v_L_Ref"];
		$this->L_Ref->AdvancedSearch->SearchValue2 = @$filter["y_L_Ref"];
		$this->L_Ref->AdvancedSearch->SearchOperator2 = @$filter["w_L_Ref"];
		$this->L_Ref->AdvancedSearch->Save();

		// Field YEAR
		$this->YEAR->AdvancedSearch->SearchValue = @$filter["x_YEAR"];
		$this->YEAR->AdvancedSearch->SearchOperator = @$filter["z_YEAR"];
		$this->YEAR->AdvancedSearch->SearchCondition = @$filter["v_YEAR"];
		$this->YEAR->AdvancedSearch->SearchValue2 = @$filter["y_YEAR"];
		$this->YEAR->AdvancedSearch->SearchOperator2 = @$filter["w_YEAR"];
		$this->YEAR->AdvancedSearch->Save();

		// Field MONTH
		$this->MONTH->AdvancedSearch->SearchValue = @$filter["x_MONTH"];
		$this->MONTH->AdvancedSearch->SearchOperator = @$filter["z_MONTH"];
		$this->MONTH->AdvancedSearch->SearchCondition = @$filter["v_MONTH"];
		$this->MONTH->AdvancedSearch->SearchValue2 = @$filter["y_MONTH"];
		$this->MONTH->AdvancedSearch->SearchOperator2 = @$filter["w_MONTH"];
		$this->MONTH->AdvancedSearch->Save();

		// Field Acc_ID
		$this->Acc_ID->AdvancedSearch->SearchValue = @$filter["x_Acc_ID"];
		$this->Acc_ID->AdvancedSearch->SearchOperator = @$filter["z_Acc_ID"];
		$this->Acc_ID->AdvancedSearch->SearchCondition = @$filter["v_Acc_ID"];
		$this->Acc_ID->AdvancedSearch->SearchValue2 = @$filter["y_Acc_ID"];
		$this->Acc_ID->AdvancedSearch->SearchOperator2 = @$filter["w_Acc_ID"];
		$this->Acc_ID->AdvancedSearch->Save();

		// Field AMOUNT
		$this->AMOUNT->AdvancedSearch->SearchValue = @$filter["x_AMOUNT"];
		$this->AMOUNT->AdvancedSearch->SearchOperator = @$filter["z_AMOUNT"];
		$this->AMOUNT->AdvancedSearch->SearchCondition = @$filter["v_AMOUNT"];
		$this->AMOUNT->AdvancedSearch->SearchValue2 = @$filter["y_AMOUNT"];
		$this->AMOUNT->AdvancedSearch->SearchOperator2 = @$filter["w_AMOUNT"];
		$this->AMOUNT->AdvancedSearch->Save();

		// Field STARTED
		$this->STARTED->AdvancedSearch->SearchValue = @$filter["x_STARTED"];
		$this->STARTED->AdvancedSearch->SearchOperator = @$filter["z_STARTED"];
		$this->STARTED->AdvancedSearch->SearchCondition = @$filter["v_STARTED"];
		$this->STARTED->AdvancedSearch->SearchValue2 = @$filter["y_STARTED"];
		$this->STARTED->AdvancedSearch->SearchOperator2 = @$filter["w_STARTED"];
		$this->STARTED->AdvancedSearch->Save();

		// Field ENDED
		$this->ENDED->AdvancedSearch->SearchValue = @$filter["x_ENDED"];
		$this->ENDED->AdvancedSearch->SearchOperator = @$filter["z_ENDED"];
		$this->ENDED->AdvancedSearch->SearchCondition = @$filter["v_ENDED"];
		$this->ENDED->AdvancedSearch->SearchValue2 = @$filter["y_ENDED"];
		$this->ENDED->AdvancedSearch->SearchOperator2 = @$filter["w_ENDED"];
		$this->ENDED->AdvancedSearch->Save();

		// Field TYPE
		$this->TYPE->AdvancedSearch->SearchValue = @$filter["x_TYPE"];
		$this->TYPE->AdvancedSearch->SearchOperator = @$filter["z_TYPE"];
		$this->TYPE->AdvancedSearch->SearchCondition = @$filter["v_TYPE"];
		$this->TYPE->AdvancedSearch->SearchValue2 = @$filter["y_TYPE"];
		$this->TYPE->AdvancedSearch->SearchOperator2 = @$filter["w_TYPE"];
		$this->TYPE->AdvancedSearch->Save();

		// Field Batch
		$this->Batch->AdvancedSearch->SearchValue = @$filter["x_Batch"];
		$this->Batch->AdvancedSearch->SearchOperator = @$filter["z_Batch"];
		$this->Batch->AdvancedSearch->SearchCondition = @$filter["v_Batch"];
		$this->Batch->AdvancedSearch->SearchValue2 = @$filter["y_Batch"];
		$this->Batch->AdvancedSearch->SearchOperator2 = @$filter["w_Batch"];
		$this->Batch->AdvancedSearch->Save();

		// Field NOTES
		$this->NOTES->AdvancedSearch->SearchValue = @$filter["x_NOTES"];
		$this->NOTES->AdvancedSearch->SearchOperator = @$filter["z_NOTES"];
		$this->NOTES->AdvancedSearch->SearchCondition = @$filter["v_NOTES"];
		$this->NOTES->AdvancedSearch->SearchValue2 = @$filter["y_NOTES"];
		$this->NOTES->AdvancedSearch->SearchOperator2 = @$filter["w_NOTES"];
		$this->NOTES->AdvancedSearch->Save();
		$this->BasicSearch->setKeyword(@$filter[EW_TABLE_BASIC_SEARCH]);
		$this->BasicSearch->setType(@$filter[EW_TABLE_BASIC_SEARCH_TYPE]);
	}

	// Advanced search WHERE clause based on QueryString
	function AdvancedSearchWhere($Default = FALSE) {
		global $Security;
		$sWhere = "";
		if (!$Security->CanSearch()) return "";
		$this->BuildSearchSql($sWhere, $this->PF, $Default, FALSE); // PF
		$this->BuildSearchSql($sWhere, $this->L_Ref, $Default, FALSE); // L_Ref
		$this->BuildSearchSql($sWhere, $this->YEAR, $Default, FALSE); // YEAR
		$this->BuildSearchSql($sWhere, $this->MONTH, $Default, FALSE); // MONTH
		$this->BuildSearchSql($sWhere, $this->Acc_ID, $Default, FALSE); // Acc_ID
		$this->BuildSearchSql($sWhere, $this->AMOUNT, $Default, FALSE); // AMOUNT
		$this->BuildSearchSql($sWhere, $this->STARTED, $Default, FALSE); // STARTED
		$this->BuildSearchSql($sWhere, $this->ENDED, $Default, FALSE); // ENDED
		$this->BuildSearchSql($sWhere, $this->TYPE, $Default, FALSE); // TYPE
		$this->BuildSearchSql($sWhere, $this->Batch, $Default, FALSE); // Batch
		$this->BuildSearchSql($sWhere, $this->NOTES, $Default, FALSE); // NOTES

		// Set up search parm
		if (!$Default && $sWhere <> "") {
			$this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->PF->AdvancedSearch->Save(); // PF
			$this->L_Ref->AdvancedSearch->Save(); // L_Ref
			$this->YEAR->AdvancedSearch->Save(); // YEAR
			$this->MONTH->AdvancedSearch->Save(); // MONTH
			$this->Acc_ID->AdvancedSearch->Save(); // Acc_ID
			$this->AMOUNT->AdvancedSearch->Save(); // AMOUNT
			$this->STARTED->AdvancedSearch->Save(); // STARTED
			$this->ENDED->AdvancedSearch->Save(); // ENDED
			$this->TYPE->AdvancedSearch->Save(); // TYPE
			$this->Batch->AdvancedSearch->Save(); // Batch
			$this->NOTES->AdvancedSearch->Save(); // NOTES
		}
		return $sWhere;
	}

	// Build search SQL
	function BuildSearchSql(&$Where, &$Fld, $Default, $MultiValue) {
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = ($Default) ? $Fld->AdvancedSearch->SearchValueDefault : $Fld->AdvancedSearch->SearchValue; // @$_GET["x_$FldParm"]
		$FldOpr = ($Default) ? $Fld->AdvancedSearch->SearchOperatorDefault : $Fld->AdvancedSearch->SearchOperator; // @$_GET["z_$FldParm"]
		$FldCond = ($Default) ? $Fld->AdvancedSearch->SearchConditionDefault : $Fld->AdvancedSearch->SearchCondition; // @$_GET["v_$FldParm"]
		$FldVal2 = ($Default) ? $Fld->AdvancedSearch->SearchValue2Default : $Fld->AdvancedSearch->SearchValue2; // @$_GET["y_$FldParm"]
		$FldOpr2 = ($Default) ? $Fld->AdvancedSearch->SearchOperator2Default : $Fld->AdvancedSearch->SearchOperator2; // @$_GET["w_$FldParm"]
		$sWrk = "";

		//$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);

		//$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		if ($FldOpr == "") $FldOpr = "=";
		$FldOpr2 = strtoupper(trim($FldOpr2));
		if ($FldOpr2 == "") $FldOpr2 = "=";
		if (EW_SEARCH_MULTI_VALUE_OPTION == 1 || $FldOpr <> "LIKE" ||
			($FldOpr2 <> "LIKE" && $FldVal2 <> ""))
			$MultiValue = FALSE;
		if ($MultiValue) {
			$sWrk1 = ($FldVal <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr, $FldVal, $this->DBID) : ""; // Field value 1
			$sWrk2 = ($FldVal2 <> "") ? ew_GetMultiSearchSql($Fld, $FldOpr2, $FldVal2, $this->DBID) : ""; // Field value 2
			$sWrk = $sWrk1; // Build final SQL
			if ($sWrk2 <> "")
				$sWrk = ($sWrk <> "") ? "($sWrk) $FldCond ($sWrk2)" : $sWrk2;
		} else {
			$FldVal = $this->ConvertSearchValue($Fld, $FldVal);
			$FldVal2 = $this->ConvertSearchValue($Fld, $FldVal2);
			$sWrk = ew_GetSearchSql($Fld, $FldVal, $FldOpr, $FldCond, $FldVal2, $FldOpr2, $this->DBID);
		}
		ew_AddFilter($Where, $sWrk);
	}

	// Convert search value
	function ConvertSearchValue(&$Fld, $FldVal) {
		if ($FldVal == EW_NULL_VALUE || $FldVal == EW_NOT_NULL_VALUE)
			return $FldVal;
		$Value = $FldVal;
		if ($Fld->FldDataType == EW_DATATYPE_BOOLEAN) {
			if ($FldVal <> "") $Value = ($FldVal == "1" || strtolower(strval($FldVal)) == "y" || strtolower(strval($FldVal)) == "t") ? $Fld->TrueValue : $Fld->FalseValue;
		} elseif ($Fld->FldDataType == EW_DATATYPE_DATE) {
			if ($FldVal <> "") $Value = ew_UnFormatDateTime($FldVal, $Fld->FldDateTimeFormat);
		}
		return $Value;
	}

	// Return basic search SQL
	function BasicSearchSQL($arKeywords, $type) {
		$sWhere = "";
		$this->BuildBasicSearchSQL($sWhere, $this->PF, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->L_Ref, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->YEAR, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->MONTH, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->STARTED, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->TYPE, $arKeywords, $type);
		$this->BuildBasicSearchSQL($sWhere, $this->NOTES, $arKeywords, $type);
		return $sWhere;
	}

	// Build basic search SQL
	function BuildBasicSearchSql(&$Where, &$Fld, $arKeywords, $type) {
		$sDefCond = ($type == "OR") ? "OR" : "AND";
		$arSQL = array(); // Array for SQL parts
		$arCond = array(); // Array for search conditions
		$cnt = count($arKeywords);
		$j = 0; // Number of SQL parts
		for ($i = 0; $i < $cnt; $i++) {
			$Keyword = $arKeywords[$i];
			$Keyword = trim($Keyword);
			if (EW_BASIC_SEARCH_IGNORE_PATTERN <> "") {
				$Keyword = preg_replace(EW_BASIC_SEARCH_IGNORE_PATTERN, "\\", $Keyword);
				$ar = explode("\\", $Keyword);
			} else {
				$ar = array($Keyword);
			}
			foreach ($ar as $Keyword) {
				if ($Keyword <> "") {
					$sWrk = "";
					if ($Keyword == "OR" && $type == "") {
						if ($j > 0)
							$arCond[$j-1] = "OR";
					} elseif ($Keyword == EW_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NULL";
					} elseif ($Keyword == EW_NOT_NULL_VALUE) {
						$sWrk = $Fld->FldExpression . " IS NOT NULL";
					} elseif ($Fld->FldIsVirtual && $Fld->FldVirtualSearch) {
						$sWrk = $Fld->FldVirtualExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					} elseif ($Fld->FldDataType != EW_DATATYPE_NUMBER || is_numeric($Keyword)) {
						$sWrk = $Fld->FldBasicSearchExpression . ew_Like(ew_QuotedValue("%" . $Keyword . "%", EW_DATATYPE_STRING, $this->DBID), $this->DBID);
					}
					if ($sWrk <> "") {
						$arSQL[$j] = $sWrk;
						$arCond[$j] = $sDefCond;
						$j += 1;
					}
				}
			}
		}
		$cnt = count($arSQL);
		$bQuoted = FALSE;
		$sSql = "";
		if ($cnt > 0) {
			for ($i = 0; $i < $cnt-1; $i++) {
				if ($arCond[$i] == "OR") {
					if (!$bQuoted) $sSql .= "(";
					$bQuoted = TRUE;
				}
				$sSql .= $arSQL[$i];
				if ($bQuoted && $arCond[$i] <> "OR") {
					$sSql .= ")";
					$bQuoted = FALSE;
				}
				$sSql .= " " . $arCond[$i] . " ";
			}
			$sSql .= $arSQL[$cnt-1];
			if ($bQuoted)
				$sSql .= ")";
		}
		if ($sSql <> "") {
			if ($Where <> "") $Where .= " OR ";
			$Where .=  "(" . $sSql . ")";
		}
	}

	// Return basic search WHERE clause based on search keyword and type
	function BasicSearchWhere($Default = FALSE) {
		global $Security;
		$sSearchStr = "";
		if (!$Security->CanSearch()) return "";
		$sSearchKeyword = ($Default) ? $this->BasicSearch->KeywordDefault : $this->BasicSearch->Keyword;
		$sSearchType = ($Default) ? $this->BasicSearch->TypeDefault : $this->BasicSearch->Type;
		if ($sSearchKeyword <> "") {
			$sSearch = trim($sSearchKeyword);
			if ($sSearchType <> "=") {
				$ar = array();

				// Match quoted keywords (i.e.: "...")
				if (preg_match_all('/"([^"]*)"/i', $sSearch, $matches, PREG_SET_ORDER)) {
					foreach ($matches as $match) {
						$p = strpos($sSearch, $match[0]);
						$str = substr($sSearch, 0, $p);
						$sSearch = substr($sSearch, $p + strlen($match[0]));
						if (strlen(trim($str)) > 0)
							$ar = array_merge($ar, explode(" ", trim($str)));
						$ar[] = $match[1]; // Save quoted keyword
					}
				}

				// Match individual keywords
				if (strlen(trim($sSearch)) > 0)
					$ar = array_merge($ar, explode(" ", trim($sSearch)));

				// Search keyword in any fields
				if (($sSearchType == "OR" || $sSearchType == "AND") && $this->BasicSearch->BasicSearchAnyFields) {
					foreach ($ar as $sKeyword) {
						if ($sKeyword <> "") {
							if ($sSearchStr <> "") $sSearchStr .= " " . $sSearchType . " ";
							$sSearchStr .= "(" . $this->BasicSearchSQL(array($sKeyword), $sSearchType) . ")";
						}
					}
				} else {
					$sSearchStr = $this->BasicSearchSQL($ar, $sSearchType);
				}
			} else {
				$sSearchStr = $this->BasicSearchSQL(array($sSearch), $sSearchType);
			}
			if (!$Default) $this->Command = "search";
		}
		if (!$Default && $this->Command == "search") {
			$this->BasicSearch->setKeyword($sSearchKeyword);
			$this->BasicSearch->setType($sSearchType);
		}
		return $sSearchStr;
	}

	// Check if search parm exists
	function CheckSearchParms() {

		// Check basic search
		if ($this->BasicSearch->IssetSession())
			return TRUE;
		if ($this->PF->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->L_Ref->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->YEAR->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->MONTH->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Acc_ID->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->AMOUNT->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->STARTED->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->ENDED->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->TYPE->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->Batch->AdvancedSearch->IssetSession())
			return TRUE;
		if ($this->NOTES->AdvancedSearch->IssetSession())
			return TRUE;
		return FALSE;
	}

	// Clear all search parameters
	function ResetSearchParms() {

		// Clear search WHERE clause
		$this->SearchWhere = "";
		$this->setSearchWhere($this->SearchWhere);

		// Clear basic search parameters
		$this->ResetBasicSearchParms();

		// Clear advanced search parameters
		$this->ResetAdvancedSearchParms();
	}

	// Load advanced search default values
	function LoadAdvancedSearchDefault() {
		return FALSE;
	}

	// Clear all basic search parameters
	function ResetBasicSearchParms() {
		$this->BasicSearch->UnsetSession();
	}

	// Clear all advanced search parameters
	function ResetAdvancedSearchParms() {
		$this->PF->AdvancedSearch->UnsetSession();
		$this->L_Ref->AdvancedSearch->UnsetSession();
		$this->YEAR->AdvancedSearch->UnsetSession();
		$this->MONTH->AdvancedSearch->UnsetSession();
		$this->Acc_ID->AdvancedSearch->UnsetSession();
		$this->AMOUNT->AdvancedSearch->UnsetSession();
		$this->STARTED->AdvancedSearch->UnsetSession();
		$this->ENDED->AdvancedSearch->UnsetSession();
		$this->TYPE->AdvancedSearch->UnsetSession();
		$this->Batch->AdvancedSearch->UnsetSession();
		$this->NOTES->AdvancedSearch->UnsetSession();
	}

	// Restore all search parameters
	function RestoreSearchParms() {
		$this->RestoreSearch = TRUE;

		// Restore basic search values
		$this->BasicSearch->Load();

		// Restore advanced search values
		$this->PF->AdvancedSearch->Load();
		$this->L_Ref->AdvancedSearch->Load();
		$this->YEAR->AdvancedSearch->Load();
		$this->MONTH->AdvancedSearch->Load();
		$this->Acc_ID->AdvancedSearch->Load();
		$this->AMOUNT->AdvancedSearch->Load();
		$this->STARTED->AdvancedSearch->Load();
		$this->ENDED->AdvancedSearch->Load();
		$this->TYPE->AdvancedSearch->Load();
		$this->Batch->AdvancedSearch->Load();
		$this->NOTES->AdvancedSearch->Load();
	}

	// Set up sort parameters
	function SetUpSortOrder() {

		// Check for "order" parameter
		if (@$_GET["order"] <> "") {
			$this->CurrentOrder = ew_StripSlashes(@$_GET["order"]);
			$this->CurrentOrderType = @$_GET["ordertype"];
			$this->UpdateSort($this->PF); // PF
			$this->UpdateSort($this->L_Ref); // L_Ref
			$this->UpdateSort($this->YEAR); // YEAR
			$this->UpdateSort($this->MONTH); // MONTH
			$this->UpdateSort($this->Acc_ID); // Acc_ID
			$this->UpdateSort($this->AMOUNT); // AMOUNT
			$this->UpdateSort($this->STARTED); // STARTED
			$this->UpdateSort($this->ENDED); // ENDED
			$this->UpdateSort($this->TYPE); // TYPE
			$this->UpdateSort($this->Batch); // Batch
			$this->setStartRecordNumber(1); // Reset start position
		}
	}

	// Load sort order parameters
	function LoadSortOrder() {
		$sOrderBy = $this->getSessionOrderBy(); // Get ORDER BY from Session
		if ($sOrderBy == "") {
			if ($this->getSqlOrderBy() <> "") {
				$sOrderBy = $this->getSqlOrderBy();
				$this->setSessionOrderBy($sOrderBy);
			}
		}
	}

	// Reset command
	// - cmd=reset (Reset search parameters)
	// - cmd=resetall (Reset search and master/detail parameters)
	// - cmd=resetsort (Reset sort parameters)
	function ResetCmd() {

		// Check if reset command
		if (substr($this->Command,0,5) == "reset") {

			// Reset search criteria
			if ($this->Command == "reset" || $this->Command == "resetall")
				$this->ResetSearchParms();

			// Reset sorting order
			if ($this->Command == "resetsort") {
				$sOrderBy = "";
				$this->setSessionOrderBy($sOrderBy);
				$this->setSessionOrderByList($sOrderBy);
				$this->PF->setSort("");
				$this->L_Ref->setSort("");
				$this->YEAR->setSort("");
				$this->MONTH->setSort("");
				$this->Acc_ID->setSort("");
				$this->AMOUNT->setSort("");
				$this->STARTED->setSort("");
				$this->ENDED->setSort("");
				$this->TYPE->setSort("");
				$this->Batch->setSort("");
			}

			// Reset start position
			$this->StartRec = 1;
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Set up list options
	function SetupListOptions() {
		global $Security, $Language;

		// Add group option item
		$item = &$this->ListOptions->Add($this->ListOptions->GroupOptionName);
		$item->Body = "";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;

		// "view"
		$item = &$this->ListOptions->Add("view");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanView();
		$item->OnLeft = FALSE;

		// "edit"
		$item = &$this->ListOptions->Add("edit");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanEdit();
		$item->OnLeft = FALSE;

		// "delete"
		$item = &$this->ListOptions->Add("delete");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = $Security->CanDelete();
		$item->OnLeft = FALSE;

		// List actions
		$item = &$this->ListOptions->Add("listactions");
		$item->CssStyle = "white-space: nowrap;";
		$item->OnLeft = FALSE;
		$item->Visible = FALSE;
		$item->ShowInButtonGroup = FALSE;
		$item->ShowInDropDown = FALSE;

		// "checkbox"
		$item = &$this->ListOptions->Add("checkbox");
		$item->Visible = FALSE;
		$item->OnLeft = FALSE;
		$item->Header = "<input type=\"checkbox\" name=\"key\" id=\"key\" onclick=\"ew_SelectAllKey(this);\">";
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// "sequence"
		$item = &$this->ListOptions->Add("sequence");
		$item->CssStyle = "white-space: nowrap;";
		$item->Visible = TRUE;
		$item->OnLeft = TRUE; // Always on left
		$item->ShowInDropDown = FALSE;
		$item->ShowInButtonGroup = FALSE;

		// Drop down button for ListOptions
		$this->ListOptions->UseImageAndText = TRUE;
		$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->DropDownButtonPhrase = $Language->Phrase("ButtonListOptions");
		$this->ListOptions->UseButtonGroup = FALSE;
		if ($this->ListOptions->UseButtonGroup && ew_IsMobile())
			$this->ListOptions->UseDropDownButton = TRUE;
		$this->ListOptions->ButtonClass = "btn-sm"; // Class for button group

		// Call ListOptions_Load event
		$this->ListOptions_Load();
		$this->SetupListOptionsExt();
		$item = &$this->ListOptions->GetItem($this->ListOptions->GroupOptionName);
		$item->Visible = $this->ListOptions->GroupOptionVisible();
	}

	// Render list options
	function RenderListOptions() {
		global $Security, $Language, $objForm;
		$this->ListOptions->LoadDefault();

		// "sequence"
		$oListOpt = &$this->ListOptions->Items["sequence"];
		$oListOpt->Body = ew_FormatSeqNo($this->RecCnt);

		// "view"
		$oListOpt = &$this->ListOptions->Items["view"];
		if ($Security->CanView())
			$oListOpt->Body = "<a class=\"ewRowLink ewView\" title=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("ViewLink")) . "\" href=\"" . ew_HtmlEncode($this->ViewUrl) . "\">" . $Language->Phrase("ViewLink") . "</a>";
		else
			$oListOpt->Body = "";

		// "edit"
		$oListOpt = &$this->ListOptions->Items["edit"];
		if ($Security->CanEdit()) {
			$oListOpt->Body = "<a class=\"ewRowLink ewEdit\" title=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("EditLink")) . "\" href=\"" . ew_HtmlEncode($this->EditUrl) . "\">" . $Language->Phrase("EditLink") . "</a>";
		} else {
			$oListOpt->Body = "";
		}

		// "delete"
		$oListOpt = &$this->ListOptions->Items["delete"];
		if ($Security->CanDelete())
			$oListOpt->Body = "<a class=\"ewRowLink ewDelete\"" . "" . " title=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("DeleteLink")) . "\" href=\"" . ew_HtmlEncode($this->DeleteUrl) . "\">" . $Language->Phrase("DeleteLink") . "</a>";
		else
			$oListOpt->Body = "";

		// Set up list action buttons
		$oListOpt = &$this->ListOptions->GetItem("listactions");
		if ($oListOpt) {
			$body = "";
			$links = array();
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_SINGLE && $listaction->Allow) {
					$action = $listaction->Action;
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode(str_replace(" ewIcon", "", $listaction->Icon)) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\"></span> " : "";
					$links[] = "<li><a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . $listaction->Caption . "</a></li>";
					if (count($links) == 1) // Single button
						$body = "<a class=\"ewAction ewListAction\" data-action=\"" . ew_HtmlEncode($action) . "\" title=\"" . ew_HtmlTitle($caption) . "\" data-caption=\"" . ew_HtmlTitle($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({key:" . $this->KeyToJson() . "}," . $listaction->ToJson(TRUE) . "));return false;\">" . $Language->Phrase("ListActionButton") . "</a>";
				}
			}
			if (count($links) > 1) { // More than one buttons, use dropdown
				$body = "<button class=\"dropdown-toggle btn btn-default btn-sm ewActions\" title=\"" . ew_HtmlTitle($Language->Phrase("ListActionButton")) . "\" data-toggle=\"dropdown\">" . $Language->Phrase("ListActionButton") . "<b class=\"caret\"></b></button>";
				$content = "";
				foreach ($links as $link)
					$content .= "<li>" . $link . "</li>";
				$body .= "<ul class=\"dropdown-menu" . ($oListOpt->OnLeft ? "" : " dropdown-menu-right") . "\">". $content . "</ul>";
				$body = "<div class=\"btn-group\">" . $body . "</div>";
			}
			if (count($links) > 0) {
				$oListOpt->Body = $body;
				$oListOpt->Visible = TRUE;
			}
		}

		// "checkbox"
		$oListOpt = &$this->ListOptions->Items["checkbox"];
		$oListOpt->Body = "<input type=\"checkbox\" name=\"key_m[]\" value=\"" . ew_HtmlEncode($this->Deduction_ID->CurrentValue) . "\" onclick='ew_ClickMultiCheckbox(event);'>";
		$this->RenderListOptionsExt();

		// Call ListOptions_Rendered event
		$this->ListOptions_Rendered();
	}

	// Set up other options
	function SetupOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
		$option = $options["addedit"];

		// Add
		$item = &$option->Add("add");
		$item->Body = "<a class=\"ewAddEdit ewAdd\" title=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" data-caption=\"" . ew_HtmlTitle($Language->Phrase("AddLink")) . "\" href=\"" . ew_HtmlEncode($this->AddUrl) . "\">" . $Language->Phrase("AddLink") . "</a>";
		$item->Visible = ($this->AddUrl <> "" && $Security->CanAdd());
		$option = $options["action"];

		// Set up options default
		foreach ($options as &$option) {
			$option->UseImageAndText = TRUE;
			$option->UseDropDownButton = FALSE;
			$option->UseButtonGroup = TRUE;
			$option->ButtonClass = "btn-sm"; // Class for button group
			$item = &$option->Add($option->GroupOptionName);
			$item->Body = "";
			$item->Visible = FALSE;
		}
		$options["addedit"]->DropDownButtonPhrase = $Language->Phrase("ButtonAddEdit");
		$options["detail"]->DropDownButtonPhrase = $Language->Phrase("ButtonDetails");
		$options["action"]->DropDownButtonPhrase = $Language->Phrase("ButtonActions");

		// Filter button
		$item = &$this->FilterOptions->Add("savecurrentfilter");
		$item->Body = "<a class=\"ewSaveFilter\" data-form=\"fdeductionslistsrch\" href=\"#\">" . $Language->Phrase("SaveCurrentFilter") . "</a>";
		$item->Visible = TRUE;
		$item = &$this->FilterOptions->Add("deletefilter");
		$item->Body = "<a class=\"ewDeleteFilter\" data-form=\"fdeductionslistsrch\" href=\"#\">" . $Language->Phrase("DeleteFilter") . "</a>";
		$item->Visible = TRUE;
		$this->FilterOptions->UseDropDownButton = TRUE;
		$this->FilterOptions->UseButtonGroup = !$this->FilterOptions->UseDropDownButton;
		$this->FilterOptions->DropDownButtonPhrase = $Language->Phrase("Filters");

		// Add group option item
		$item = &$this->FilterOptions->Add($this->FilterOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Render other options
	function RenderOtherOptions() {
		global $Language, $Security;
		$options = &$this->OtherOptions;
			$option = &$options["action"];

			// Set up list action buttons
			foreach ($this->ListActions->Items as $listaction) {
				if ($listaction->Select == EW_ACTION_MULTIPLE) {
					$item = &$option->Add("custom_" . $listaction->Action);
					$caption = $listaction->Caption;
					$icon = ($listaction->Icon <> "") ? "<span class=\"" . ew_HtmlEncode($listaction->Icon) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\"></span> " : $caption;
					$item->Body = "<a class=\"ewAction ewListAction\" title=\"" . ew_HtmlEncode($caption) . "\" data-caption=\"" . ew_HtmlEncode($caption) . "\" href=\"\" onclick=\"ew_SubmitAction(event,jQuery.extend({f:document.fdeductionslist}," . $listaction->ToJson(TRUE) . "));return false;\">" . $icon . "</a>";
					$item->Visible = $listaction->Allow;
				}
			}

			// Hide grid edit and other options
			if ($this->TotalRecs <= 0) {
				$option = &$options["addedit"];
				$item = &$option->GetItem("gridedit");
				if ($item) $item->Visible = FALSE;
				$option = &$options["action"];
				$option->HideAllOptions();
			}
	}

	// Process list action
	function ProcessListAction() {
		global $Language, $Security;
		$userlist = "";
		$user = "";
		$sFilter = $this->GetKeyFilter();
		$UserAction = @$_POST["useraction"];
		if ($sFilter <> "" && $UserAction <> "") {

			// Check permission first
			$ActionCaption = $UserAction;
			if (array_key_exists($UserAction, $this->ListActions->Items)) {
				$ActionCaption = $this->ListActions->Items[$UserAction]->Caption;
				if (!$this->ListActions->Items[$UserAction]->Allow) {
					$errmsg = str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionNotAllowed"));
					if (@$_POST["ajax"] == $UserAction) // Ajax
						echo "<p class=\"text-danger\">" . $errmsg . "</p>";
					else
						$this->setFailureMessage($errmsg);
					return FALSE;
				}
			}
			$this->CurrentFilter = $sFilter;
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rs = $conn->Execute($sSql);
			$conn->raiseErrorFn = '';
			$this->CurrentAction = $UserAction;

			// Call row action event
			if ($rs && !$rs->EOF) {
				$conn->BeginTrans();
				$this->SelectedCount = $rs->RecordCount();
				$this->SelectedIndex = 0;
				while (!$rs->EOF) {
					$this->SelectedIndex++;
					$row = $rs->fields;
					$Processed = $this->Row_CustomAction($UserAction, $row);
					if (!$Processed) break;
					$rs->MoveNext();
				}
				if ($Processed) {
					$conn->CommitTrans(); // Commit the changes
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionCompleted"))); // Set up success message
				} else {
					$conn->RollbackTrans(); // Rollback changes

					// Set up error message
					if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

						// Use the message, do nothing
					} elseif ($this->CancelMessage <> "") {
						$this->setFailureMessage($this->CancelMessage);
						$this->CancelMessage = "";
					} else {
						$this->setFailureMessage(str_replace('%s', $ActionCaption, $Language->Phrase("CustomActionFailed")));
					}
				}
			}
			if ($rs)
				$rs->Close();
			if (@$_POST["ajax"] == $UserAction) { // Ajax
				if ($this->getSuccessMessage() <> "") {
					echo "<p class=\"text-success\">" . $this->getSuccessMessage() . "</p>";
					$this->ClearSuccessMessage(); // Clear message
				}
				if ($this->getFailureMessage() <> "") {
					echo "<p class=\"text-danger\">" . $this->getFailureMessage() . "</p>";
					$this->ClearFailureMessage(); // Clear message
				}
				return TRUE;
			}
		}
		return FALSE; // Not ajax request
	}

	// Set up search options
	function SetupSearchOptions() {
		global $Language;
		$this->SearchOptions = new cListOptions();
		$this->SearchOptions->Tag = "div";
		$this->SearchOptions->TagClassName = "ewSearchOption";

		// Search button
		$item = &$this->SearchOptions->Add("searchtoggle");
		$SearchToggleClass = ($this->SearchWhere <> "") ? " active" : "";
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewSearchToggle" . $SearchToggleClass . "\" title=\"" . $Language->Phrase("SearchPanel") . "\" data-caption=\"" . $Language->Phrase("SearchPanel") . "\" data-toggle=\"button\" data-form=\"fdeductionslistsrch\">" . $Language->Phrase("SearchBtn") . "</button>";
		$item->Visible = TRUE;

		// Show all button
		$item = &$this->SearchOptions->Add("showall");
		$item->Body = "<a class=\"btn btn-default ewShowAll\" title=\"" . $Language->Phrase("ShowAll") . "\" data-caption=\"" . $Language->Phrase("ShowAll") . "\" href=\"" . $this->PageUrl() . "cmd=reset\">" . $Language->Phrase("ShowAllBtn") . "</a>";
		$item->Visible = ($this->SearchWhere <> $this->DefaultSearchWhere && $this->SearchWhere <> "0=101");

		// Advanced search button
		$item = &$this->SearchOptions->Add("advancedsearch");
		if (ew_IsMobile())
			$item->Body = "<a class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" href=\"deductionssrch.php\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		else
			$item->Body = "<button type=\"button\" class=\"btn btn-default ewAdvancedSearch\" title=\"" . $Language->Phrase("AdvancedSearch") . "\" data-caption=\"" . $Language->Phrase("AdvancedSearch") . "\" onclick=\"ew_SearchDialogShow({lnk:this,url:'deductionssrch.php'});\">" . $Language->Phrase("AdvancedSearchBtn") . "</a>";
		$item->Visible = TRUE;

		// Search highlight button
		$item = &$this->SearchOptions->Add("searchhighlight");
		$item->Body = "<button type=\"button\" class=\"btn btn-default ewHighlight active\" title=\"" . $Language->Phrase("Highlight") . "\" data-caption=\"" . $Language->Phrase("Highlight") . "\" data-toggle=\"button\" data-form=\"fdeductionslistsrch\" data-name=\"" . $this->HighlightName() . "\">" . $Language->Phrase("HighlightBtn") . "</button>";
		$item->Visible = ($this->SearchWhere <> "" && $this->TotalRecs > 0);

		// Button group for search
		$this->SearchOptions->UseDropDownButton = FALSE;
		$this->SearchOptions->UseImageAndText = TRUE;
		$this->SearchOptions->UseButtonGroup = TRUE;
		$this->SearchOptions->DropDownButtonPhrase = $Language->Phrase("ButtonSearch");

		// Add group option item
		$item = &$this->SearchOptions->Add($this->SearchOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;

		// Hide search options
		if ($this->Export <> "" || $this->CurrentAction <> "")
			$this->SearchOptions->HideAllOptions();
		global $Security;
		if (!$Security->CanSearch()) {
			$this->SearchOptions->HideAllOptions();
			$this->FilterOptions->HideAllOptions();
		}
	}

	function SetupListOptionsExt() {
		global $Security, $Language;
	}

	function RenderListOptionsExt() {
		global $Security, $Language;
	}

	// Set up starting record parameters
	function SetUpStartRec() {
		if ($this->DisplayRecs == 0)
			return;
		if ($this->IsPageRequest()) { // Validate request
			if (@$_GET[EW_TABLE_START_REC] <> "") { // Check for "start" parameter
				$this->StartRec = $_GET[EW_TABLE_START_REC];
				$this->setStartRecordNumber($this->StartRec);
			} elseif (@$_GET[EW_TABLE_PAGE_NO] <> "") {
				$PageNo = $_GET[EW_TABLE_PAGE_NO];
				if (is_numeric($PageNo)) {
					$this->StartRec = ($PageNo-1)*$this->DisplayRecs+1;
					if ($this->StartRec <= 0) {
						$this->StartRec = 1;
					} elseif ($this->StartRec >= intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1) {
						$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1;
					}
					$this->setStartRecordNumber($this->StartRec);
				}
			}
		}
		$this->StartRec = $this->getStartRecordNumber();

		// Check if correct start record counter
		if (!is_numeric($this->StartRec) || $this->StartRec == "") { // Avoid invalid start record counter
			$this->StartRec = 1; // Reset start record counter
			$this->setStartRecordNumber($this->StartRec);
		} elseif (intval($this->StartRec) > intval($this->TotalRecs)) { // Avoid starting record > total records
			$this->StartRec = intval(($this->TotalRecs-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to last page first record
			$this->setStartRecordNumber($this->StartRec);
		} elseif (($this->StartRec-1) % $this->DisplayRecs <> 0) {
			$this->StartRec = intval(($this->StartRec-1)/$this->DisplayRecs)*$this->DisplayRecs+1; // Point to page boundary
			$this->setStartRecordNumber($this->StartRec);
		}
	}

	// Load basic search values
	function LoadBasicSearchValues() {
		$this->BasicSearch->Keyword = @$_GET[EW_TABLE_BASIC_SEARCH];
		if ($this->BasicSearch->Keyword <> "") $this->Command = "search";
		$this->BasicSearch->Type = @$_GET[EW_TABLE_BASIC_SEARCH_TYPE];
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// PF

		$this->PF->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_PF"]);
		if ($this->PF->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->PF->AdvancedSearch->SearchOperator = @$_GET["z_PF"];

		// L_Ref
		$this->L_Ref->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_L_Ref"]);
		if ($this->L_Ref->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->L_Ref->AdvancedSearch->SearchOperator = @$_GET["z_L_Ref"];

		// YEAR
		$this->YEAR->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_YEAR"]);
		if ($this->YEAR->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->YEAR->AdvancedSearch->SearchOperator = @$_GET["z_YEAR"];

		// MONTH
		$this->MONTH->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_MONTH"]);
		if ($this->MONTH->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->MONTH->AdvancedSearch->SearchOperator = @$_GET["z_MONTH"];

		// Acc_ID
		$this->Acc_ID->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Acc_ID"]);
		if ($this->Acc_ID->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Acc_ID->AdvancedSearch->SearchOperator = @$_GET["z_Acc_ID"];

		// AMOUNT
		$this->AMOUNT->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_AMOUNT"]);
		if ($this->AMOUNT->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->AMOUNT->AdvancedSearch->SearchOperator = @$_GET["z_AMOUNT"];

		// STARTED
		$this->STARTED->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_STARTED"]);
		if ($this->STARTED->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->STARTED->AdvancedSearch->SearchOperator = @$_GET["z_STARTED"];

		// ENDED
		$this->ENDED->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_ENDED"]);
		if ($this->ENDED->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->ENDED->AdvancedSearch->SearchOperator = @$_GET["z_ENDED"];

		// TYPE
		$this->TYPE->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_TYPE"]);
		if ($this->TYPE->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->TYPE->AdvancedSearch->SearchOperator = @$_GET["z_TYPE"];

		// Batch
		$this->Batch->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_Batch"]);
		if ($this->Batch->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->Batch->AdvancedSearch->SearchOperator = @$_GET["z_Batch"];

		// NOTES
		$this->NOTES->AdvancedSearch->SearchValue = ew_StripSlashes(@$_GET["x_NOTES"]);
		if ($this->NOTES->AdvancedSearch->SearchValue <> "") $this->Command = "search";
		$this->NOTES->AdvancedSearch->SearchOperator = @$_GET["z_NOTES"];
	}

	// Load recordset
	function LoadRecordset($offset = -1, $rowcnt = -1) {

		// Load List page SQL
		$sSql = $this->SelectSQL();
		$conn = &$this->Connection();

		// Load recordset
		$dbtype = ew_GetConnectionType($this->DBID);
		if ($this->UseSelectLimit) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			if ($dbtype == "MSSQL") {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderByList())));
			} else {
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset);
			}
			$conn->raiseErrorFn = '';
		} else {
			$rs = ew_LoadRecordset($sSql, $conn);
		}

		// Call Recordset Selected event
		$this->Recordset_Selected($rs);
		return $rs;
	}

	// Load row based on key values
	function LoadRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();

		// Call Row Selecting event
		$this->Row_Selecting($sFilter);

		// Load SQL based on filter
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$res = FALSE;
		$rs = ew_LoadRecordset($sSql, $conn);
		if ($rs && !$rs->EOF) {
			$res = TRUE;
			$this->LoadRowValues($rs); // Load row values
			$rs->Close();
		}
		return $res;
	}

	// Load row values from recordset
	function LoadRowValues(&$rs) {
		if (!$rs || $rs->EOF) return;

		// Call Row Selected event
		$row = &$rs->fields;
		$this->Row_Selected($row);
		$this->Deduction_ID->setDbValue($rs->fields('Deduction_ID'));
		$this->PF->setDbValue($rs->fields('PF'));
		if (array_key_exists('EV__PF', $rs->fields)) {
			$this->PF->VirtualValue = $rs->fields('EV__PF'); // Set up virtual field value
		} else {
			$this->PF->VirtualValue = ""; // Clear value
		}
		$this->L_Ref->setDbValue($rs->fields('L_Ref'));
		$this->YEAR->setDbValue($rs->fields('YEAR'));
		$this->MONTH->setDbValue($rs->fields('MONTH'));
		$this->Acc_ID->setDbValue($rs->fields('Acc_ID'));
		$this->AMOUNT->setDbValue($rs->fields('AMOUNT'));
		$this->STARTED->setDbValue($rs->fields('STARTED'));
		$this->ENDED->setDbValue($rs->fields('ENDED'));
		$this->TYPE->setDbValue($rs->fields('TYPE'));
		$this->Batch->setDbValue($rs->fields('Batch'));
		$this->NOTES->setDbValue($rs->fields('NOTES'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Deduction_ID->DbValue = $row['Deduction_ID'];
		$this->PF->DbValue = $row['PF'];
		$this->L_Ref->DbValue = $row['L_Ref'];
		$this->YEAR->DbValue = $row['YEAR'];
		$this->MONTH->DbValue = $row['MONTH'];
		$this->Acc_ID->DbValue = $row['Acc_ID'];
		$this->AMOUNT->DbValue = $row['AMOUNT'];
		$this->STARTED->DbValue = $row['STARTED'];
		$this->ENDED->DbValue = $row['ENDED'];
		$this->TYPE->DbValue = $row['TYPE'];
		$this->Batch->DbValue = $row['Batch'];
		$this->NOTES->DbValue = $row['NOTES'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Deduction_ID")) <> "")
			$this->Deduction_ID->CurrentValue = $this->getKey("Deduction_ID"); // Deduction_ID
		else
			$bValidKey = FALSE;

		// Load old recordset
		if ($bValidKey) {
			$this->CurrentFilter = $this->KeyFilter();
			$sSql = $this->SQL();
			$conn = &$this->Connection();
			$this->OldRecordset = ew_LoadRecordset($sSql, $conn);
			$this->LoadRowValues($this->OldRecordset); // Load row values
		} else {
			$this->OldRecordset = NULL;
		}
		return $bValidKey;
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		$this->ViewUrl = $this->GetViewUrl();
		$this->EditUrl = $this->GetEditUrl();
		$this->InlineEditUrl = $this->GetInlineEditUrl();
		$this->CopyUrl = $this->GetCopyUrl();
		$this->InlineCopyUrl = $this->GetInlineCopyUrl();
		$this->DeleteUrl = $this->GetDeleteUrl();

		// Convert decimal values if posted back
		if ($this->AMOUNT->FormValue == $this->AMOUNT->CurrentValue && is_numeric(ew_StrToFloat($this->AMOUNT->CurrentValue)))
			$this->AMOUNT->CurrentValue = ew_StrToFloat($this->AMOUNT->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Deduction_ID

		$this->Deduction_ID->CellCssStyle = "white-space: nowrap;";

		// PF
		// L_Ref
		// YEAR
		// MONTH
		// Acc_ID
		// AMOUNT
		// STARTED
		// ENDED
		// TYPE
		// Batch
		// NOTES

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

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

		// Acc_ID
		if (strval($this->Acc_ID->CurrentValue) <> "") {
			$sFilterWrk = "`PF`" . ew_SearchString("=", $this->Acc_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT DISTINCT `PF`, `Bank_Name` AS `DispFld`, `Acc_NO` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `accounts`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Acc_ID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->Acc_ID->ViewValue = $this->Acc_ID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Acc_ID->ViewValue = $this->Acc_ID->CurrentValue;
			}
		} else {
			$this->Acc_ID->ViewValue = NULL;
		}
		$this->Acc_ID->CellCssStyle .= "text-align: left;";
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

			// PF
			$this->PF->LinkCustomAttributes = "";
			$this->PF->HrefValue = "";
			if ($this->Export == "") {
				$this->PF->TooltipValue = strval($this->NOTES->CurrentValue);
				if ($this->PF->HrefValue == "") $this->PF->HrefValue = "javascript:void(0);";
				$this->PF->LinkAttrs["class"] = "ewTooltipLink";
				$this->PF->LinkAttrs["data-tooltip-id"] = "tt_deductions_x" . $this->RowCnt . "_PF";
				$this->PF->LinkAttrs["data-tooltip-width"] = $this->PF->TooltipWidth;
				$this->PF->LinkAttrs["data-placement"] = EW_CSS_FLIP ? "left" : "right";
			}
			if ($this->Export == "")
				$this->PF->ViewValue = ew_Highlight($this->HighlightName(), $this->PF->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->PF->AdvancedSearch->getValue("x"), "");

			// L_Ref
			$this->L_Ref->LinkCustomAttributes = "";
			$this->L_Ref->HrefValue = "";
			$this->L_Ref->TooltipValue = "";
			if ($this->Export == "")
				$this->L_Ref->ViewValue = ew_Highlight($this->HighlightName(), $this->L_Ref->ViewValue, $this->BasicSearch->getKeyword(), $this->BasicSearch->getType(), $this->L_Ref->AdvancedSearch->getValue("x"), "");

			// YEAR
			$this->YEAR->LinkCustomAttributes = "";
			$this->YEAR->HrefValue = "";
			$this->YEAR->TooltipValue = "";

			// MONTH
			$this->MONTH->LinkCustomAttributes = "";
			$this->MONTH->HrefValue = "";
			$this->MONTH->TooltipValue = "";

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
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;

		// Return validate result
		$ValidateSearch = ($gsSearchError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateSearch = $ValidateSearch && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsSearchError, $sFormCustomError);
		}
		return $ValidateSearch;
	}

	// Load advanced search
	function LoadAdvancedSearch() {
		$this->PF->AdvancedSearch->Load();
		$this->L_Ref->AdvancedSearch->Load();
		$this->YEAR->AdvancedSearch->Load();
		$this->MONTH->AdvancedSearch->Load();
		$this->Acc_ID->AdvancedSearch->Load();
		$this->AMOUNT->AdvancedSearch->Load();
		$this->STARTED->AdvancedSearch->Load();
		$this->ENDED->AdvancedSearch->Load();
		$this->TYPE->AdvancedSearch->Load();
		$this->Batch->AdvancedSearch->Load();
		$this->NOTES->AdvancedSearch->Load();
	}

	// Set up export options
	function SetupExportOptions() {
		global $Language;

		// Printer friendly
		$item = &$this->ExportOptions->Add("print");
		$item->Body = "<a href=\"" . $this->ExportPrintUrl . "\" class=\"ewExportLink ewPrint\" title=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("PrinterFriendlyText")) . "\">" . $Language->Phrase("PrinterFriendly") . "</a>";
		$item->Visible = TRUE;

		// Export to Excel
		$item = &$this->ExportOptions->Add("excel");
		$item->Body = "<a href=\"" . $this->ExportExcelUrl . "\" class=\"ewExportLink ewExcel\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToExcelText")) . "\">" . $Language->Phrase("ExportToExcel") . "</a>";
		$item->Visible = TRUE;

		// Export to Word
		$item = &$this->ExportOptions->Add("word");
		$item->Body = "<a href=\"" . $this->ExportWordUrl . "\" class=\"ewExportLink ewWord\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToWordText")) . "\">" . $Language->Phrase("ExportToWord") . "</a>";
		$item->Visible = TRUE;

		// Export to Html
		$item = &$this->ExportOptions->Add("html");
		$item->Body = "<a href=\"" . $this->ExportHtmlUrl . "\" class=\"ewExportLink ewHtml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToHtmlText")) . "\">" . $Language->Phrase("ExportToHtml") . "</a>";
		$item->Visible = FALSE;

		// Export to Xml
		$item = &$this->ExportOptions->Add("xml");
		$item->Body = "<a href=\"" . $this->ExportXmlUrl . "\" class=\"ewExportLink ewXml\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToXmlText")) . "\">" . $Language->Phrase("ExportToXml") . "</a>";
		$item->Visible = FALSE;

		// Export to Csv
		$item = &$this->ExportOptions->Add("csv");
		$item->Body = "<a href=\"" . $this->ExportCsvUrl . "\" class=\"ewExportLink ewCsv\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToCsvText")) . "\">" . $Language->Phrase("ExportToCsv") . "</a>";
		$item->Visible = TRUE;

		// Export to Pdf
		$item = &$this->ExportOptions->Add("pdf");
		$item->Body = "<a href=\"" . $this->ExportPdfUrl . "\" class=\"ewExportLink ewPdf\" title=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\" data-caption=\"" . ew_HtmlEncode($Language->Phrase("ExportToPDFText")) . "\">" . $Language->Phrase("ExportToPDF") . "</a>";
		$item->Visible = FALSE;

		// Export to Email
		$item = &$this->ExportOptions->Add("email");
		$url = "";
		$item->Body = "<button id=\"emf_deductions\" class=\"ewExportLink ewEmail\" title=\"" . $Language->Phrase("ExportToEmailText") . "\" data-caption=\"" . $Language->Phrase("ExportToEmailText") . "\" onclick=\"ew_EmailDialogShow({lnk:'emf_deductions',hdr:ewLanguage.Phrase('ExportToEmailText'),f:document.fdeductionslist,sel:false" . $url . "});\">" . $Language->Phrase("ExportToEmail") . "</button>";
		$item->Visible = TRUE;

		// Drop down button for export
		$this->ExportOptions->UseButtonGroup = TRUE;
		$this->ExportOptions->UseImageAndText = TRUE;
		$this->ExportOptions->UseDropDownButton = TRUE;
		if ($this->ExportOptions->UseButtonGroup && ew_IsMobile())
			$this->ExportOptions->UseDropDownButton = TRUE;
		$this->ExportOptions->DropDownButtonPhrase = $Language->Phrase("ButtonExport");

		// Add group option item
		$item = &$this->ExportOptions->Add($this->ExportOptions->GroupOptionName);
		$item->Body = "";
		$item->Visible = FALSE;
	}

	// Export data in HTML/CSV/Word/Excel/XML/Email/PDF format
	function ExportData() {
		$utf8 = (strtolower(EW_CHARSET) == "utf-8");
		$bSelectLimit = $this->UseSelectLimit;

		// Load recordset
		if ($bSelectLimit) {
			$this->TotalRecs = $this->SelectRecordCount();
		} else {
			if (!$this->Recordset)
				$this->Recordset = $this->LoadRecordset();
			$rs = &$this->Recordset;
			if ($rs)
				$this->TotalRecs = $rs->RecordCount();
		}
		$this->StartRec = 1;

		// Export all
		if ($this->ExportAll) {
			set_time_limit(EW_EXPORT_ALL_TIME_LIMIT);
			$this->DisplayRecs = $this->TotalRecs;
			$this->StopRec = $this->TotalRecs;
		} else { // Export one page only
			$this->SetUpStartRec(); // Set up start record position

			// Set the last record to display
			if ($this->DisplayRecs <= 0) {
				$this->StopRec = $this->TotalRecs;
			} else {
				$this->StopRec = $this->StartRec + $this->DisplayRecs - 1;
			}
		}
		if ($bSelectLimit)
			$rs = $this->LoadRecordset($this->StartRec-1, $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs);
		if (!$rs) {
			header("Content-Type:"); // Remove header
			header("Content-Disposition:");
			$this->ShowMessage();
			return;
		}
		$this->ExportDoc = ew_ExportDocument($this, "h");
		$Doc = &$this->ExportDoc;
		if ($bSelectLimit) {
			$this->StartRec = 1;
			$this->StopRec = $this->DisplayRecs <= 0 ? $this->TotalRecs : $this->DisplayRecs;
		} else {

			//$this->StartRec = $this->StartRec;
			//$this->StopRec = $this->StopRec;

		}

		// Call Page Exporting server event
		$this->ExportDoc->ExportCustom = !$this->Page_Exporting();
		$ParentTable = "";
		$sHeader = $this->PageHeader;
		$this->Page_DataRendering($sHeader);
		$Doc->Text .= $sHeader;
		$this->ExportDocument($Doc, $rs, $this->StartRec, $this->StopRec, "");
		$sFooter = $this->PageFooter;
		$this->Page_DataRendered($sFooter);
		$Doc->Text .= $sFooter;

		// Close recordset
		$rs->Close();

		// Call Page Exported server event
		$this->Page_Exported();

		// Export header and footer
		$Doc->ExportHeaderAndFooter();

		// Clean output buffer
		if (!EW_DEBUG_ENABLED && ob_get_length())
			ob_end_clean();

		// Write debug message if enabled
		if (EW_DEBUG_ENABLED)
			echo ew_DebugMsg();

		// Output data
		if ($this->Export == "email") {
			echo $this->ExportEmail($Doc->Text);
		} else {
			$Doc->Export();
		}
	}

	// Export email
	function ExportEmail($EmailContent) {
		global $gTmpImages, $Language;
		$sSender = @$_POST["sender"];
		$sRecipient = @$_POST["recipient"];
		$sCc = @$_POST["cc"];
		$sBcc = @$_POST["bcc"];
		$sContentType = @$_POST["contenttype"];

		// Subject
		$sSubject = ew_StripSlashes(@$_POST["subject"]);
		$sEmailSubject = $sSubject;

		// Message
		$sContent = ew_StripSlashes(@$_POST["message"]);
		$sEmailMessage = $sContent;

		// Check sender
		if ($sSender == "") {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterSenderEmail") . "</p>";
		}
		if (!ew_CheckEmail($sSender)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperSenderEmail") . "</p>";
		}

		// Check recipient
		if (!ew_CheckEmailList($sRecipient, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperRecipientEmail") . "</p>";
		}

		// Check cc
		if (!ew_CheckEmailList($sCc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperCcEmail") . "</p>";
		}

		// Check bcc
		if (!ew_CheckEmailList($sBcc, EW_MAX_EMAIL_RECIPIENT)) {
			return "<p class=\"text-danger\">" . $Language->Phrase("EnterProperBccEmail") . "</p>";
		}

		// Check email sent count
		if (!isset($_SESSION[EW_EXPORT_EMAIL_COUNTER]))
			$_SESSION[EW_EXPORT_EMAIL_COUNTER] = 0;
		if (intval($_SESSION[EW_EXPORT_EMAIL_COUNTER]) > EW_MAX_EMAIL_SENT_COUNT) {
			return "<p class=\"text-danger\">" . $Language->Phrase("ExceedMaxEmailExport") . "</p>";
		}

		// Send email
		$Email = new cEmail();
		$Email->Sender = $sSender; // Sender
		$Email->Recipient = $sRecipient; // Recipient
		$Email->Cc = $sCc; // Cc
		$Email->Bcc = $sBcc; // Bcc
		$Email->Subject = $sEmailSubject; // Subject
		$Email->Format = ($sContentType == "url") ? "text" : "html";
		if ($sEmailMessage <> "") {
			$sEmailMessage = ew_RemoveXSS($sEmailMessage);
			$sEmailMessage .= ($sContentType == "url") ? "\r\n\r\n" : "<br><br>";
		}
		if ($sContentType == "url") {
			$sUrl = ew_ConvertFullUrl(ew_CurrentPage() . "?" . $this->ExportQueryString());
			$sEmailMessage .= $sUrl; // Send URL only
		} else {
			foreach ($gTmpImages as $tmpimage)
				$Email->AddEmbeddedImage($tmpimage);
			$sEmailMessage .= ew_CleanEmailContent($EmailContent); // Send HTML
		}
		$Email->Content = $sEmailMessage; // Content
		$EventArgs = array();
		if ($this->Recordset) {
			$this->RecCnt = $this->StartRec - 1;
			$this->Recordset->MoveFirst();
			if ($this->StartRec > 1)
				$this->Recordset->Move($this->StartRec - 1);
			$EventArgs["rs"] = &$this->Recordset;
		}
		$bEmailSent = FALSE;
		if ($this->Email_Sending($Email, $EventArgs))
			$bEmailSent = $Email->Send();

		// Check email sent status
		if ($bEmailSent) {

			// Update email sent count
			$_SESSION[EW_EXPORT_EMAIL_COUNTER]++;

			// Sent email success
			return "<p class=\"text-success\">" . $Language->Phrase("SendEmailSuccess") . "</p>"; // Set up success message
		} else {

			// Sent email failure
			return "<p class=\"text-danger\">" . $Email->SendErrDescription . "</p>";
		}
	}

	// Export QueryString
	function ExportQueryString() {

		// Initialize
		$sQry = "export=html";

		// Build QueryString for search
		if ($this->BasicSearch->getKeyword() <> "") {
			$sQry .= "&" . EW_TABLE_BASIC_SEARCH . "=" . urlencode($this->BasicSearch->getKeyword()) . "&" . EW_TABLE_BASIC_SEARCH_TYPE . "=" . urlencode($this->BasicSearch->getType());
		}
		$this->AddSearchQueryString($sQry, $this->PF); // PF
		$this->AddSearchQueryString($sQry, $this->L_Ref); // L_Ref
		$this->AddSearchQueryString($sQry, $this->YEAR); // YEAR
		$this->AddSearchQueryString($sQry, $this->MONTH); // MONTH
		$this->AddSearchQueryString($sQry, $this->Acc_ID); // Acc_ID
		$this->AddSearchQueryString($sQry, $this->AMOUNT); // AMOUNT
		$this->AddSearchQueryString($sQry, $this->STARTED); // STARTED
		$this->AddSearchQueryString($sQry, $this->ENDED); // ENDED
		$this->AddSearchQueryString($sQry, $this->TYPE); // TYPE
		$this->AddSearchQueryString($sQry, $this->Batch); // Batch
		$this->AddSearchQueryString($sQry, $this->NOTES); // NOTES

		// Build QueryString for pager
		$sQry .= "&" . EW_TABLE_REC_PER_PAGE . "=" . urlencode($this->getRecordsPerPage()) . "&" . EW_TABLE_START_REC . "=" . urlencode($this->getStartRecordNumber());
		return $sQry;
	}

	// Add search QueryString
	function AddSearchQueryString(&$Qry, &$Fld) {
		$FldSearchValue = $Fld->AdvancedSearch->getValue("x");
		$FldParm = substr($Fld->FldVar,2);
		if (strval($FldSearchValue) <> "") {
			$Qry .= "&x_" . $FldParm . "=" . urlencode($FldSearchValue) .
				"&z_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("z"));
		}
		$FldSearchValue2 = $Fld->AdvancedSearch->getValue("y");
		if (strval($FldSearchValue2) <> "") {
			$Qry .= "&v_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("v")) .
				"&y_" . $FldParm . "=" . urlencode($FldSearchValue2) .
				"&w_" . $FldParm . "=" . urlencode($Fld->AdvancedSearch->getValue("w"));
		}
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$url = preg_replace('/\?cmd=reset(all){0,1}$/i', '', $url); // Remove cmd=reset / cmd=resetall
		$Breadcrumb->Add("list", $this->TableVar, $url, "", $this->TableVar, TRUE);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'deductions';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Page Load event
	function Page_Load() {

		//echo "Page Load";
	}

	// Page Unload event
	function Page_Unload() {

		//echo "Page Unload";
	}

	// Page Redirecting event
	function Page_Redirecting(&$url) {

		// Example:
		//$url = "your URL";

	}

	// Message Showing event
	// $type = ''|'success'|'failure'|'warning'
	function Message_Showing(&$msg, $type) {
		if ($type == 'success') {

			//$msg = "your success message";
		} elseif ($type == 'failure') {

			//$msg = "your failure message";
		} elseif ($type == 'warning') {

			//$msg = "your warning message";
		} else {

			//$msg = "your message";
		}
	}

	// Page Render event
	function Page_Render() {

		//echo "Page Render";
	}

	// Page Data Rendering event
	function Page_DataRendering(&$header) {

		// Example:
		//$header = "your header";

	}

	// Page Data Rendered event
	function Page_DataRendered(&$footer) {

		// Example:
		//$footer = "your footer";

	}

	// Form Custom Validate event
	function Form_CustomValidate(&$CustomError) {

		// Return error message in CustomError
		return TRUE;
	}

	// ListOptions Load event
	function ListOptions_Load() {

		// Example:
		//$opt = &$this->ListOptions->Add("new");
		//$opt->Header = "xxx";
		//$opt->OnLeft = TRUE; // Link on left
		//$opt->MoveTo(0); // Move to first column

	}

	// ListOptions Rendered event
	function ListOptions_Rendered() {

		// Example: 
		//$this->ListOptions->Items["new"]->Body = "xxx";

	}

	// Row Custom Action event
	function Row_CustomAction($action, $row) {

		// Return FALSE to abort
		return TRUE;
	}

	// Page Exporting event
	// $this->ExportDoc = export document object
	function Page_Exporting() {

		//$this->ExportDoc->Text = "my header"; // Export header
		//return FALSE; // Return FALSE to skip default export and use Row_Export event

		return TRUE; // Return TRUE to use default export and skip Row_Export event
	}

	// Row Export event
	// $this->ExportDoc = export document object
	function Row_Export($rs) {

	    //$this->ExportDoc->Text .= "my content"; // Build HTML with field value: $rs["MyField"] or $this->MyField->ViewValue
	}

	// Page Exported event
	// $this->ExportDoc = export document object
	function Page_Exported() {

		//$this->ExportDoc->Text .= "my footer"; // Export footer
		//echo $this->ExportDoc->Text;

	}
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($deductions_list)) $deductions_list = new cdeductions_list();

// Page init
$deductions_list->Page_Init();

// Page main
$deductions_list->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$deductions_list->Page_Render();
?>
<?php include_once "header.php" ?>
<?php if ($deductions->Export == "") { ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "list";
var CurrentForm = fdeductionslist = new ew_Form("fdeductionslist", "list");
fdeductionslist.FormKeyCountName = '<?php echo $deductions_list->FormKeyCountName ?>';

// Form_CustomValidate event
fdeductionslist.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fdeductionslist.ValidateRequired = true;
<?php } else { ?>
fdeductionslist.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fdeductionslist.Lists["x_PF"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_PF","x_Name","",""],"ParentFields":[],"ChildFields":["x_Acc_ID"],"FilterFields":[],"Options":[],"Template":""};
fdeductionslist.Lists["x_YEAR"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionslist.Lists["x_YEAR"].Options = <?php echo json_encode($deductions->YEAR->Options()) ?>;
fdeductionslist.Lists["x_MONTH"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionslist.Lists["x_MONTH"].Options = <?php echo json_encode($deductions->MONTH->Options()) ?>;
fdeductionslist.Lists["x_Acc_ID"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_Bank_Name","x_Acc_NO","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionslist.Lists["x_TYPE"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionslist.Lists["x_TYPE"].Options = <?php echo json_encode($deductions->TYPE->Options()) ?>;
fdeductionslist.Lists["x_Batch"] = {"LinkField":"x_Batch_ID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Batch_Number","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
var CurrentSearchForm = fdeductionslistsrch = new ew_Form("fdeductionslistsrch");

// Init search panel as collapsed
if (fdeductionslistsrch) fdeductionslistsrch.InitSearchPanel = true;
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php } ?>
<?php if ($deductions->Export == "") { ?>
<div class="ewToolbar">
<?php if ($deductions->Export == "") { ?>
<?php $Breadcrumb->Render(); ?>
<?php } ?>
<?php if ($deductions_list->TotalRecs > 0 && $deductions_list->ExportOptions->Visible()) { ?>
<?php $deductions_list->ExportOptions->Render("body") ?>
<?php } ?>
<?php if ($deductions_list->SearchOptions->Visible()) { ?>
<?php $deductions_list->SearchOptions->Render("body") ?>
<?php } ?>
<?php if ($deductions_list->FilterOptions->Visible()) { ?>
<?php $deductions_list->FilterOptions->Render("body") ?>
<?php } ?>
<?php if ($deductions->Export == "") { ?>
<?php echo $Language->SelectionForm(); ?>
<?php } ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php
	$bSelectLimit = $deductions_list->UseSelectLimit;
	if ($bSelectLimit) {
		if ($deductions_list->TotalRecs <= 0)
			$deductions_list->TotalRecs = $deductions->SelectRecordCount();
	} else {
		if (!$deductions_list->Recordset && ($deductions_list->Recordset = $deductions_list->LoadRecordset()))
			$deductions_list->TotalRecs = $deductions_list->Recordset->RecordCount();
	}
	$deductions_list->StartRec = 1;
	if ($deductions_list->DisplayRecs <= 0 || ($deductions->Export <> "" && $deductions->ExportAll)) // Display all records
		$deductions_list->DisplayRecs = $deductions_list->TotalRecs;
	if (!($deductions->Export <> "" && $deductions->ExportAll))
		$deductions_list->SetUpStartRec(); // Set up start record position
	if ($bSelectLimit)
		$deductions_list->Recordset = $deductions_list->LoadRecordset($deductions_list->StartRec-1, $deductions_list->DisplayRecs);

	// Set no record found message
	if ($deductions->CurrentAction == "" && $deductions_list->TotalRecs == 0) {
		if (!$Security->CanList())
			$deductions_list->setWarningMessage($Language->Phrase("NoPermission"));
		if ($deductions_list->SearchWhere == "0=101")
			$deductions_list->setWarningMessage($Language->Phrase("EnterSearchCriteria"));
		else
			$deductions_list->setWarningMessage($Language->Phrase("NoRecord"));
	}

	// Audit trail on search
	if ($deductions_list->AuditTrailOnSearch && $deductions_list->Command == "search" && !$deductions_list->RestoreSearch) {
		$searchparm = ew_ServerVar("QUERY_STRING");
		$searchsql = $deductions_list->getSessionWhere();
		$deductions_list->WriteAuditTrailOnSearch($searchparm, $searchsql);
	}
$deductions_list->RenderOtherOptions();
?>
<?php if ($Security->CanSearch()) { ?>
<?php if ($deductions->Export == "" && $deductions->CurrentAction == "") { ?>
<form name="fdeductionslistsrch" id="fdeductionslistsrch" class="form-inline ewForm" action="<?php echo ew_CurrentPage() ?>">
<?php $SearchPanelClass = ($deductions_list->SearchWhere <> "") ? " in" : ""; ?>
<div id="fdeductionslistsrch_SearchPanel" class="ewSearchPanel collapse<?php echo $SearchPanelClass ?>">
<input type="hidden" name="cmd" value="search">
<input type="hidden" name="t" value="deductions">
	<div class="ewBasicSearch">
<div id="xsr_1" class="ewRow">
	<div class="ewQuickSearch input-group">
	<input type="text" name="<?php echo EW_TABLE_BASIC_SEARCH ?>" id="<?php echo EW_TABLE_BASIC_SEARCH ?>" class="form-control" value="<?php echo ew_HtmlEncode($deductions_list->BasicSearch->getKeyword()) ?>" placeholder="<?php echo ew_HtmlEncode($Language->Phrase("Search")) ?>">
	<input type="hidden" name="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" id="<?php echo EW_TABLE_BASIC_SEARCH_TYPE ?>" value="<?php echo ew_HtmlEncode($deductions_list->BasicSearch->getType()) ?>">
	<div class="input-group-btn">
		<button type="button" data-toggle="dropdown" class="btn btn-default"><span id="searchtype"><?php echo $deductions_list->BasicSearch->getTypeNameShort() ?></span><span class="caret"></span></button>
		<ul class="dropdown-menu pull-right" role="menu">
			<li<?php if ($deductions_list->BasicSearch->getType() == "") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this)"><?php echo $Language->Phrase("QuickSearchAuto") ?></a></li>
			<li<?php if ($deductions_list->BasicSearch->getType() == "=") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'=')"><?php echo $Language->Phrase("QuickSearchExact") ?></a></li>
			<li<?php if ($deductions_list->BasicSearch->getType() == "AND") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'AND')"><?php echo $Language->Phrase("QuickSearchAll") ?></a></li>
			<li<?php if ($deductions_list->BasicSearch->getType() == "OR") echo " class=\"active\""; ?>><a href="javascript:void(0);" onclick="ew_SetSearchType(this,'OR')"><?php echo $Language->Phrase("QuickSearchAny") ?></a></li>
		</ul>
	<button class="btn btn-primary ewButton" name="btnsubmit" id="btnsubmit" type="submit"><?php echo $Language->Phrase("QuickSearchBtn") ?></button>
	</div>
	</div>
</div>
	</div>
</div>
</form>
<?php } ?>
<?php } ?>
<?php $deductions_list->ShowPageHeader(); ?>
<?php
$deductions_list->ShowMessage();
?>
<?php if ($deductions_list->TotalRecs > 0 || $deductions->CurrentAction <> "") { ?>
<div class="panel panel-default ewGrid">
<form name="fdeductionslist" id="fdeductionslist" class="form-inline ewForm ewListForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($deductions_list->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $deductions_list->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="deductions">
<div id="gmp_deductions" class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<?php if ($deductions_list->TotalRecs > 0) { ?>
<table id="tbl_deductionslist" class="table ewTable">
<?php echo $deductions->TableCustomInnerHtml ?>
<thead><!-- Table header -->
	<tr class="ewTableHeader">
<?php

// Header row
$deductions_list->RowType = EW_ROWTYPE_HEADER;

// Render list options
$deductions_list->RenderListOptions();

// Render list options (header, left)
$deductions_list->ListOptions->Render("header", "left");
?>
<?php if ($deductions->PF->Visible) { // PF ?>
	<?php if ($deductions->SortUrl($deductions->PF) == "") { ?>
		<th data-name="PF"><div id="elh_deductions_PF" class="deductions_PF"><div class="ewTableHeaderCaption"><?php echo $deductions->PF->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="PF"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->PF) ?>',1);"><div id="elh_deductions_PF" class="deductions_PF">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->PF->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($deductions->PF->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->PF->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
	<?php if ($deductions->SortUrl($deductions->L_Ref) == "") { ?>
		<th data-name="L_Ref"><div id="elh_deductions_L_Ref" class="deductions_L_Ref"><div class="ewTableHeaderCaption"><?php echo $deductions->L_Ref->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="L_Ref"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->L_Ref) ?>',1);"><div id="elh_deductions_L_Ref" class="deductions_L_Ref">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->L_Ref->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($deductions->L_Ref->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->L_Ref->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->YEAR->Visible) { // YEAR ?>
	<?php if ($deductions->SortUrl($deductions->YEAR) == "") { ?>
		<th data-name="YEAR"><div id="elh_deductions_YEAR" class="deductions_YEAR"><div class="ewTableHeaderCaption"><?php echo $deductions->YEAR->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="YEAR"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->YEAR) ?>',1);"><div id="elh_deductions_YEAR" class="deductions_YEAR">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->YEAR->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->YEAR->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->YEAR->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->MONTH->Visible) { // MONTH ?>
	<?php if ($deductions->SortUrl($deductions->MONTH) == "") { ?>
		<th data-name="MONTH"><div id="elh_deductions_MONTH" class="deductions_MONTH"><div class="ewTableHeaderCaption"><?php echo $deductions->MONTH->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="MONTH"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->MONTH) ?>',1);"><div id="elh_deductions_MONTH" class="deductions_MONTH">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->MONTH->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->MONTH->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->MONTH->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
	<?php if ($deductions->SortUrl($deductions->Acc_ID) == "") { ?>
		<th data-name="Acc_ID"><div id="elh_deductions_Acc_ID" class="deductions_Acc_ID"><div class="ewTableHeaderCaption"><?php echo $deductions->Acc_ID->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Acc_ID"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->Acc_ID) ?>',1);"><div id="elh_deductions_Acc_ID" class="deductions_Acc_ID">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->Acc_ID->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->Acc_ID->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->Acc_ID->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
	<?php if ($deductions->SortUrl($deductions->AMOUNT) == "") { ?>
		<th data-name="AMOUNT"><div id="elh_deductions_AMOUNT" class="deductions_AMOUNT"><div class="ewTableHeaderCaption"><?php echo $deductions->AMOUNT->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="AMOUNT"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->AMOUNT) ?>',1);"><div id="elh_deductions_AMOUNT" class="deductions_AMOUNT">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->AMOUNT->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->AMOUNT->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->AMOUNT->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->STARTED->Visible) { // STARTED ?>
	<?php if ($deductions->SortUrl($deductions->STARTED) == "") { ?>
		<th data-name="STARTED"><div id="elh_deductions_STARTED" class="deductions_STARTED"><div class="ewTableHeaderCaption"><?php echo $deductions->STARTED->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="STARTED"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->STARTED) ?>',1);"><div id="elh_deductions_STARTED" class="deductions_STARTED">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->STARTED->FldCaption() ?><?php echo $Language->Phrase("SrchLegend") ?></span><span class="ewTableHeaderSort"><?php if ($deductions->STARTED->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->STARTED->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->ENDED->Visible) { // ENDED ?>
	<?php if ($deductions->SortUrl($deductions->ENDED) == "") { ?>
		<th data-name="ENDED"><div id="elh_deductions_ENDED" class="deductions_ENDED"><div class="ewTableHeaderCaption"><?php echo $deductions->ENDED->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="ENDED"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->ENDED) ?>',1);"><div id="elh_deductions_ENDED" class="deductions_ENDED">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->ENDED->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->ENDED->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->ENDED->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->TYPE->Visible) { // TYPE ?>
	<?php if ($deductions->SortUrl($deductions->TYPE) == "") { ?>
		<th data-name="TYPE"><div id="elh_deductions_TYPE" class="deductions_TYPE"><div class="ewTableHeaderCaption"><?php echo $deductions->TYPE->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="TYPE"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->TYPE) ?>',1);"><div id="elh_deductions_TYPE" class="deductions_TYPE">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->TYPE->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->TYPE->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->TYPE->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php if ($deductions->Batch->Visible) { // Batch ?>
	<?php if ($deductions->SortUrl($deductions->Batch) == "") { ?>
		<th data-name="Batch"><div id="elh_deductions_Batch" class="deductions_Batch"><div class="ewTableHeaderCaption"><?php echo $deductions->Batch->FldCaption() ?></div></div></th>
	<?php } else { ?>
		<th data-name="Batch"><div class="ewPointer" onclick="ew_Sort(event,'<?php echo $deductions->SortUrl($deductions->Batch) ?>',1);"><div id="elh_deductions_Batch" class="deductions_Batch">
			<div class="ewTableHeaderBtn"><span class="ewTableHeaderCaption"><?php echo $deductions->Batch->FldCaption() ?></span><span class="ewTableHeaderSort"><?php if ($deductions->Batch->getSort() == "ASC") { ?><span class="caret ewSortUp"></span><?php } elseif ($deductions->Batch->getSort() == "DESC") { ?><span class="caret"></span><?php } ?></span></div>
        </div></div></th>
	<?php } ?>
<?php } ?>		
<?php

// Render list options (header, right)
$deductions_list->ListOptions->Render("header", "right");
?>
	</tr>
</thead>
<tbody>
<?php
if ($deductions->ExportAll && $deductions->Export <> "") {
	$deductions_list->StopRec = $deductions_list->TotalRecs;
} else {

	// Set the last record to display
	if ($deductions_list->TotalRecs > $deductions_list->StartRec + $deductions_list->DisplayRecs - 1)
		$deductions_list->StopRec = $deductions_list->StartRec + $deductions_list->DisplayRecs - 1;
	else
		$deductions_list->StopRec = $deductions_list->TotalRecs;
}
$deductions_list->RecCnt = $deductions_list->StartRec - 1;
if ($deductions_list->Recordset && !$deductions_list->Recordset->EOF) {
	$deductions_list->Recordset->MoveFirst();
	$bSelectLimit = $deductions_list->UseSelectLimit;
	if (!$bSelectLimit && $deductions_list->StartRec > 1)
		$deductions_list->Recordset->Move($deductions_list->StartRec - 1);
} elseif (!$deductions->AllowAddDeleteRow && $deductions_list->StopRec == 0) {
	$deductions_list->StopRec = $deductions->GridAddRowCount;
}

// Initialize aggregate
$deductions->RowType = EW_ROWTYPE_AGGREGATEINIT;
$deductions->ResetAttrs();
$deductions_list->RenderRow();
while ($deductions_list->RecCnt < $deductions_list->StopRec) {
	$deductions_list->RecCnt++;
	if (intval($deductions_list->RecCnt) >= intval($deductions_list->StartRec)) {
		$deductions_list->RowCnt++;

		// Set up key count
		$deductions_list->KeyCount = $deductions_list->RowIndex;

		// Init row class and style
		$deductions->ResetAttrs();
		$deductions->CssClass = "";
		if ($deductions->CurrentAction == "gridadd") {
		} else {
			$deductions_list->LoadRowValues($deductions_list->Recordset); // Load row values
		}
		$deductions->RowType = EW_ROWTYPE_VIEW; // Render view

		// Set up row id / data-rowindex
		$deductions->RowAttrs = array_merge($deductions->RowAttrs, array('data-rowindex'=>$deductions_list->RowCnt, 'id'=>'r' . $deductions_list->RowCnt . '_deductions', 'data-rowtype'=>$deductions->RowType));

		// Render row
		$deductions_list->RenderRow();

		// Render list options
		$deductions_list->RenderListOptions();
?>
	<tr<?php echo $deductions->RowAttributes() ?>>
<?php

// Render list options (body, left)
$deductions_list->ListOptions->Render("body", "left", $deductions_list->RowCnt);
?>
	<?php if ($deductions->PF->Visible) { // PF ?>
		<td data-name="PF"<?php echo $deductions->PF->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_PF" class="deductions_PF">
<span<?php echo $deductions->PF->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($deductions->PF->TooltipValue)) && $deductions->PF->LinkAttributes() <> "") { ?>
<a<?php echo $deductions->PF->LinkAttributes() ?>><?php echo $deductions->PF->ListViewValue() ?></a>
<?php } else { ?>
<?php echo $deductions->PF->ListViewValue() ?>
<?php } ?>
<span id="tt_deductions_x<?php echo $deductions_list->RowCnt ?>_PF" style="display: none">
<?php echo $deductions->PF->TooltipValue ?>
</span></span>
</span>
<a id="<?php echo $deductions_list->PageObjName . "_row_" . $deductions_list->RowCnt ?>"></a></td>
	<?php } ?>
	<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
		<td data-name="L_Ref"<?php echo $deductions->L_Ref->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_L_Ref" class="deductions_L_Ref">
<span<?php echo $deductions->L_Ref->ViewAttributes() ?>>
<?php echo $deductions->L_Ref->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->YEAR->Visible) { // YEAR ?>
		<td data-name="YEAR"<?php echo $deductions->YEAR->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_YEAR" class="deductions_YEAR">
<span<?php echo $deductions->YEAR->ViewAttributes() ?>>
<?php echo $deductions->YEAR->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->MONTH->Visible) { // MONTH ?>
		<td data-name="MONTH"<?php echo $deductions->MONTH->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_MONTH" class="deductions_MONTH">
<span<?php echo $deductions->MONTH->ViewAttributes() ?>>
<?php echo $deductions->MONTH->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
		<td data-name="Acc_ID"<?php echo $deductions->Acc_ID->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_Acc_ID" class="deductions_Acc_ID">
<span<?php echo $deductions->Acc_ID->ViewAttributes() ?>>
<?php echo $deductions->Acc_ID->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
		<td data-name="AMOUNT"<?php echo $deductions->AMOUNT->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_AMOUNT" class="deductions_AMOUNT">
<span<?php echo $deductions->AMOUNT->ViewAttributes() ?>>
<?php echo $deductions->AMOUNT->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->STARTED->Visible) { // STARTED ?>
		<td data-name="STARTED"<?php echo $deductions->STARTED->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_STARTED" class="deductions_STARTED">
<span<?php echo $deductions->STARTED->ViewAttributes() ?>>
<?php echo $deductions->STARTED->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->ENDED->Visible) { // ENDED ?>
		<td data-name="ENDED"<?php echo $deductions->ENDED->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_ENDED" class="deductions_ENDED">
<span<?php echo $deductions->ENDED->ViewAttributes() ?>>
<?php echo $deductions->ENDED->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->TYPE->Visible) { // TYPE ?>
		<td data-name="TYPE"<?php echo $deductions->TYPE->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_TYPE" class="deductions_TYPE">
<span<?php echo $deductions->TYPE->ViewAttributes() ?>>
<?php echo $deductions->TYPE->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
	<?php if ($deductions->Batch->Visible) { // Batch ?>
		<td data-name="Batch"<?php echo $deductions->Batch->CellAttributes() ?>>
<span id="el<?php echo $deductions_list->RowCnt ?>_deductions_Batch" class="deductions_Batch">
<span<?php echo $deductions->Batch->ViewAttributes() ?>>
<?php echo $deductions->Batch->ListViewValue() ?></span>
</span>
</td>
	<?php } ?>
<?php

// Render list options (body, right)
$deductions_list->ListOptions->Render("body", "right", $deductions_list->RowCnt);
?>
	</tr>
<?php
	}
	if ($deductions->CurrentAction <> "gridadd")
		$deductions_list->Recordset->MoveNext();
}
?>
</tbody>
</table>
<?php } ?>
<?php if ($deductions->CurrentAction == "") { ?>
<input type="hidden" name="a_list" id="a_list" value="">
<?php } ?>
</div>
</form>
<?php

// Close recordset
if ($deductions_list->Recordset)
	$deductions_list->Recordset->Close();
?>
<?php if ($deductions->Export == "") { ?>
<div class="panel-footer ewGridLowerPanel">
<?php if ($deductions->CurrentAction <> "gridadd" && $deductions->CurrentAction <> "gridedit") { ?>
<form name="ewPagerForm" class="ewForm form-inline ewPagerForm" action="<?php echo ew_CurrentPage() ?>">
<?php if (!isset($deductions_list->Pager)) $deductions_list->Pager = new cPrevNextPager($deductions_list->StartRec, $deductions_list->DisplayRecs, $deductions_list->TotalRecs) ?>
<?php if ($deductions_list->Pager->RecordCount > 0) { ?>
<div class="ewPager">
<span><?php echo $Language->Phrase("Page") ?>&nbsp;</span>
<div class="ewPrevNext"><div class="input-group">
<div class="input-group-btn">
<!--first page button-->
	<?php if ($deductions_list->Pager->FirstButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerFirst") ?>" href="<?php echo $deductions_list->PageUrl() ?>start=<?php echo $deductions_list->Pager->FirstButton->Start ?>"><span class="icon-first ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerFirst") ?>"><span class="icon-first ewIcon"></span></a>
	<?php } ?>
<!--previous page button-->
	<?php if ($deductions_list->Pager->PrevButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerPrevious") ?>" href="<?php echo $deductions_list->PageUrl() ?>start=<?php echo $deductions_list->Pager->PrevButton->Start ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerPrevious") ?>"><span class="icon-prev ewIcon"></span></a>
	<?php } ?>
</div>
<!--current page number-->
	<input class="form-control input-sm" type="text" name="<?php echo EW_TABLE_PAGE_NO ?>" value="<?php echo $deductions_list->Pager->CurrentPage ?>">
<div class="input-group-btn">
<!--next page button-->
	<?php if ($deductions_list->Pager->NextButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerNext") ?>" href="<?php echo $deductions_list->PageUrl() ?>start=<?php echo $deductions_list->Pager->NextButton->Start ?>"><span class="icon-next ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerNext") ?>"><span class="icon-next ewIcon"></span></a>
	<?php } ?>
<!--last page button-->
	<?php if ($deductions_list->Pager->LastButton->Enabled) { ?>
	<a class="btn btn-default btn-sm" title="<?php echo $Language->Phrase("PagerLast") ?>" href="<?php echo $deductions_list->PageUrl() ?>start=<?php echo $deductions_list->Pager->LastButton->Start ?>"><span class="icon-last ewIcon"></span></a>
	<?php } else { ?>
	<a class="btn btn-default btn-sm disabled" title="<?php echo $Language->Phrase("PagerLast") ?>"><span class="icon-last ewIcon"></span></a>
	<?php } ?>
</div>
</div>
</div>
<span>&nbsp;<?php echo $Language->Phrase("of") ?>&nbsp;<?php echo $deductions_list->Pager->PageCount ?></span>
</div>
<div class="ewPager ewRec">
	<span><?php echo $Language->Phrase("Record") ?>&nbsp;<?php echo $deductions_list->Pager->FromIndex ?>&nbsp;<?php echo $Language->Phrase("To") ?>&nbsp;<?php echo $deductions_list->Pager->ToIndex ?>&nbsp;<?php echo $Language->Phrase("Of") ?>&nbsp;<?php echo $deductions_list->Pager->RecordCount ?></span>
</div>
<?php } ?>
</form>
<?php } ?>
<div class="ewListOtherOptions">
<?php
	foreach ($deductions_list->OtherOptions as &$option)
		$option->Render("body", "bottom");
?>
</div>
<div class="clearfix"></div>
</div>
<?php } ?>
</div>
<?php } ?>
<?php if ($deductions_list->TotalRecs == 0 && $deductions->CurrentAction == "") { // Show other options ?>
<div class="ewListOtherOptions">
<?php
	foreach ($deductions_list->OtherOptions as &$option) {
		$option->ButtonClass = "";
		$option->Render("body", "");
	}
?>
</div>
<div class="clearfix"></div>
<?php } ?>
<?php if ($deductions->Export == "") { ?>
<script type="text/javascript">
fdeductionslistsrch.Init();
fdeductionslistsrch.FilterList = <?php echo $deductions_list->GetFilterList() ?>;
fdeductionslist.Init();
</script>
<?php } ?>
<?php
$deductions_list->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<?php if ($deductions->Export == "") { ?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php } ?>
<?php include_once "footer.php" ?>
<?php
$deductions_list->Page_Terminate();
?>
