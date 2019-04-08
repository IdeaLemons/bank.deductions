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

$deductions_edit = NULL; // Initialize page object first

class cdeductions_edit extends cdeductions {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'deductions';

	// Page object name
	var $PageObjName = 'deductions_edit';

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
    var $AuditTrailOnAdd = FALSE;
    var $AuditTrailOnEdit = TRUE;
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

		// Table object (emp)
		if (!isset($GLOBALS['emp'])) $GLOBALS['emp'] = new cemp();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

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
		if (!$Security->CanEdit()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("deductionslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}

		// Create form object
		$objForm = new cFormObj();
		$this->CurrentAction = (@$_GET["a"] <> "") ? $_GET["a"] : @$_POST["a_list"]; // Set up current action

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
	var $FormClassName = "form-horizontal ewForm ewEditForm";
	var $DbMasterFilter;
	var $DbDetailFilter;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		$this->FormClassName = "ewForm ewEditForm";
		if (ew_IsMobile())
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");

		// Load key from QueryString
		if (@$_GET["Deduction_ID"] <> "") {
			$this->Deduction_ID->setQueryStringValue($_GET["Deduction_ID"]);
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Process form if post back
		if (@$_POST["a_edit"] <> "") {
			$this->CurrentAction = $_POST["a_edit"]; // Get action code
			$this->LoadFormValues(); // Get form values
		} else {
			$this->CurrentAction = "I"; // Default action is display
		}

		// Check if valid key
		if ($this->Deduction_ID->CurrentValue == "")
			$this->Page_Terminate("deductionslist.php"); // Invalid key, return to list

		// Validate form if post back
		if (@$_POST["a_edit"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = ""; // Form error, reset action
				$this->setFailureMessage($gsFormError);
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues();
			}
		}
		switch ($this->CurrentAction) {
			case "I": // Get a record to display
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("deductionslist.php"); // No matching record, return to list
				}
				break;
			Case "U": // Update
				$sReturnUrl = $this->getReturnUrl();
				$this->SendEmail = TRUE; // Send email on update success
				if ($this->EditRow()) { // Update record based on key
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("UpdateSuccess")); // Update success
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} elseif ($this->getFailureMessage() == $Language->Phrase("NoRecord")) {
					$this->Page_Terminate($sReturnUrl); // Return to caller
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Restore form values if update failed
				}
		}

		// Render the record
		if ($this->CurrentAction == "F") { // Confirm page
			$this->RowType = EW_ROWTYPE_VIEW; // Render as View
		} else {
			$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
		}
		$this->ResetAttrs();
		$this->RenderRow();
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

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->PF->FldIsDetailKey) {
			$this->PF->setFormValue($objForm->GetValue("x_PF"));
		}
		if (!$this->L_Ref->FldIsDetailKey) {
			$this->L_Ref->setFormValue($objForm->GetValue("x_L_Ref"));
		}
		if (!$this->YEAR->FldIsDetailKey) {
			$this->YEAR->setFormValue($objForm->GetValue("x_YEAR"));
		}
		if (!$this->MONTH->FldIsDetailKey) {
			$this->MONTH->setFormValue($objForm->GetValue("x_MONTH"));
		}
		if (!$this->Acc_ID->FldIsDetailKey) {
			$this->Acc_ID->setFormValue($objForm->GetValue("x_Acc_ID"));
		}
		if (!$this->AMOUNT->FldIsDetailKey) {
			$this->AMOUNT->setFormValue($objForm->GetValue("x_AMOUNT"));
		}
		if (!$this->STARTED->FldIsDetailKey) {
			$this->STARTED->setFormValue($objForm->GetValue("x_STARTED"));
			$this->STARTED->CurrentValue = ew_UnFormatDateTime($this->STARTED->CurrentValue, 5);
		}
		if (!$this->ENDED->FldIsDetailKey) {
			$this->ENDED->setFormValue($objForm->GetValue("x_ENDED"));
			$this->ENDED->CurrentValue = ew_UnFormatDateTime($this->ENDED->CurrentValue, 5);
		}
		if (!$this->TYPE->FldIsDetailKey) {
			$this->TYPE->setFormValue($objForm->GetValue("x_TYPE"));
		}
		if (!$this->Batch->FldIsDetailKey) {
			$this->Batch->setFormValue($objForm->GetValue("x_Batch"));
		}
		if (!$this->NOTES->FldIsDetailKey) {
			$this->NOTES->setFormValue($objForm->GetValue("x_NOTES"));
		}
		if (!$this->Deduction_ID->FldIsDetailKey)
			$this->Deduction_ID->setFormValue($objForm->GetValue("x_Deduction_ID"));
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->Deduction_ID->CurrentValue = $this->Deduction_ID->FormValue;
		$this->PF->CurrentValue = $this->PF->FormValue;
		$this->L_Ref->CurrentValue = $this->L_Ref->FormValue;
		$this->YEAR->CurrentValue = $this->YEAR->FormValue;
		$this->MONTH->CurrentValue = $this->MONTH->FormValue;
		$this->Acc_ID->CurrentValue = $this->Acc_ID->FormValue;
		$this->AMOUNT->CurrentValue = $this->AMOUNT->FormValue;
		$this->STARTED->CurrentValue = $this->STARTED->FormValue;
		$this->STARTED->CurrentValue = ew_UnFormatDateTime($this->STARTED->CurrentValue, 5);
		$this->ENDED->CurrentValue = $this->ENDED->FormValue;
		$this->ENDED->CurrentValue = ew_UnFormatDateTime($this->ENDED->CurrentValue, 5);
		$this->TYPE->CurrentValue = $this->TYPE->FormValue;
		$this->Batch->CurrentValue = $this->Batch->FormValue;
		$this->NOTES->CurrentValue = $this->NOTES->FormValue;
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

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Convert decimal values if posted back

		if ($this->AMOUNT->FormValue == $this->AMOUNT->CurrentValue && is_numeric(ew_StrToFloat($this->AMOUNT->CurrentValue)))
			$this->AMOUNT->CurrentValue = ew_StrToFloat($this->AMOUNT->CurrentValue);

		// Call Row_Rendering event
		$this->Row_Rendering();

		// Common render codes for all row types
		// Deduction_ID
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
				$this->PF->LinkAttrs["data-tooltip-id"] = "tt_deductions_x_PF";
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
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// PF
			$this->PF->EditAttrs["class"] = "form-control";
			$this->PF->EditCustomAttributes = "";
			$this->PF->EditValue = ew_HtmlEncode($this->PF->CurrentValue);
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
					$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
					$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
					$this->PF->EditValue = $this->PF->DisplayValue($arwrk);
					$rswrk->Close();
				} else {
					$this->PF->EditValue = ew_HtmlEncode($this->PF->CurrentValue);
				}
			} else {
				$this->PF->EditValue = NULL;
			}
			$this->PF->PlaceHolder = ew_RemoveHtml($this->PF->FldCaption());

			// L_Ref
			$this->L_Ref->EditAttrs["class"] = "form-control";
			$this->L_Ref->EditCustomAttributes = "";
			$this->L_Ref->EditValue = ew_HtmlEncode($this->L_Ref->CurrentValue);
			$this->L_Ref->PlaceHolder = ew_RemoveHtml($this->L_Ref->FldCaption());

			// YEAR
			$this->YEAR->EditCustomAttributes = "";
			$this->YEAR->EditValue = $this->YEAR->Options(TRUE);

			// MONTH
			$this->MONTH->EditCustomAttributes = "";
			$this->MONTH->EditValue = $this->MONTH->Options(TRUE);

			// Acc_ID
			$this->Acc_ID->EditCustomAttributes = "";
			if (trim(strval($this->Acc_ID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`PF`" . ew_SearchString("=", $this->Acc_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT DISTINCT `PF`, `Bank_Name` AS `DispFld`, `Acc_NO` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, `PF` AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `accounts`";
			$sWhereWrk = "";
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Acc_ID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$arwrk[2] = ew_HtmlEncode($rswrk->fields('Disp2Fld'));
				$this->Acc_ID->ViewValue = $this->Acc_ID->DisplayValue($arwrk);
			} else {
				$this->Acc_ID->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Acc_ID->EditValue = $arwrk;

			// AMOUNT
			$this->AMOUNT->EditAttrs["class"] = "form-control";
			$this->AMOUNT->EditCustomAttributes = "";
			$this->AMOUNT->EditValue = ew_HtmlEncode($this->AMOUNT->CurrentValue);
			$this->AMOUNT->PlaceHolder = ew_RemoveHtml($this->AMOUNT->FldCaption());
			if (strval($this->AMOUNT->EditValue) <> "" && is_numeric($this->AMOUNT->EditValue)) $this->AMOUNT->EditValue = ew_FormatNumber($this->AMOUNT->EditValue, -2, 0, -1, -1);

			// STARTED
			$this->STARTED->EditAttrs["class"] = "form-control";
			$this->STARTED->EditCustomAttributes = "";
			$this->STARTED->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->STARTED->CurrentValue, 5));
			$this->STARTED->PlaceHolder = ew_RemoveHtml($this->STARTED->FldCaption());

			// ENDED
			$this->ENDED->EditAttrs["class"] = "form-control";
			$this->ENDED->EditCustomAttributes = "";
			$this->ENDED->EditValue = ew_HtmlEncode(ew_FormatDateTime($this->ENDED->CurrentValue, 5));
			$this->ENDED->PlaceHolder = ew_RemoveHtml($this->ENDED->FldCaption());

			// TYPE
			$this->TYPE->EditCustomAttributes = "";
			$this->TYPE->EditValue = $this->TYPE->Options(FALSE);

			// Batch
			$this->Batch->EditCustomAttributes = "";
			if (trim(strval($this->Batch->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Batch_ID`" . ew_SearchString("=", $this->Batch->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Batch_ID`, `Batch_Number` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `batches`";
			$sWhereWrk = "";
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Batch, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = ew_HtmlEncode($rswrk->fields('DispFld'));
				$this->Batch->ViewValue = $this->Batch->DisplayValue($arwrk);
			} else {
				$this->Batch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Batch->EditValue = $arwrk;

			// NOTES
			$this->NOTES->EditAttrs["class"] = "form-control";
			$this->NOTES->EditCustomAttributes = "";
			$this->NOTES->EditValue = ew_HtmlEncode($this->NOTES->CurrentValue);
			$this->NOTES->PlaceHolder = ew_RemoveHtml($this->NOTES->FldCaption());

			// Edit refer script
			// PF

			$this->PF->HrefValue = "";

			// L_Ref
			$this->L_Ref->HrefValue = "";

			// YEAR
			$this->YEAR->HrefValue = "";

			// MONTH
			$this->MONTH->HrefValue = "";

			// Acc_ID
			$this->Acc_ID->HrefValue = "";

			// AMOUNT
			$this->AMOUNT->HrefValue = "";

			// STARTED
			$this->STARTED->HrefValue = "";

			// ENDED
			$this->ENDED->HrefValue = "";

			// TYPE
			$this->TYPE->HrefValue = "";

			// Batch
			$this->Batch->HrefValue = "";

			// NOTES
			$this->NOTES->HrefValue = "";
		}
		if ($this->RowType == EW_ROWTYPE_ADD ||
			$this->RowType == EW_ROWTYPE_EDIT ||
			$this->RowType == EW_ROWTYPE_SEARCH) { // Add / Edit / Search row
			$this->SetupFieldTitles();
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	// Validate form
	function ValidateForm() {
		global $Language, $gsFormError;

		// Initialize form error message
		$gsFormError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return ($gsFormError == "");
		if (!$this->PF->FldIsDetailKey && !is_null($this->PF->FormValue) && $this->PF->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->PF->FldCaption(), $this->PF->ReqErrMsg));
		}
		if (!$this->YEAR->FldIsDetailKey && !is_null($this->YEAR->FormValue) && $this->YEAR->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->YEAR->FldCaption(), $this->YEAR->ReqErrMsg));
		}
		if (!$this->MONTH->FldIsDetailKey && !is_null($this->MONTH->FormValue) && $this->MONTH->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->MONTH->FldCaption(), $this->MONTH->ReqErrMsg));
		}
		if (!$this->Acc_ID->FldIsDetailKey && !is_null($this->Acc_ID->FormValue) && $this->Acc_ID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Acc_ID->FldCaption(), $this->Acc_ID->ReqErrMsg));
		}
		if (!$this->AMOUNT->FldIsDetailKey && !is_null($this->AMOUNT->FormValue) && $this->AMOUNT->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->AMOUNT->FldCaption(), $this->AMOUNT->ReqErrMsg));
		}
		if (!ew_CheckNumber($this->AMOUNT->FormValue)) {
			ew_AddMessage($gsFormError, $this->AMOUNT->FldErrMsg());
		}
		if (!ew_CheckDate($this->STARTED->FormValue)) {
			ew_AddMessage($gsFormError, $this->STARTED->FldErrMsg());
		}
		if (!ew_CheckDate($this->ENDED->FormValue)) {
			ew_AddMessage($gsFormError, $this->ENDED->FldErrMsg());
		}
		if ($this->TYPE->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->TYPE->FldCaption(), $this->TYPE->ReqErrMsg));
		}
		if (!$this->Batch->FldIsDetailKey && !is_null($this->Batch->FormValue) && $this->Batch->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Batch->FldCaption(), $this->Batch->ReqErrMsg));
		}

		// Return validate result
		$ValidateForm = ($gsFormError == "");

		// Call Form_CustomValidate event
		$sFormCustomError = "";
		$ValidateForm = $ValidateForm && $this->Form_CustomValidate($sFormCustomError);
		if ($sFormCustomError <> "") {
			ew_AddMessage($gsFormError, $sFormCustomError);
		}
		return $ValidateForm;
	}

	// Update record based on key values
	function EditRow() {
		global $Security, $Language;
		$sFilter = $this->KeyFilter();
		$sFilter = $this->ApplyUserIDFilters($sFilter);
		$conn = &$this->Connection();
		if ($this->L_Ref->CurrentValue <> "") { // Check field with unique index
			$sFilterChk = "(`L_Ref` = '" . ew_AdjustSql($this->L_Ref->CurrentValue, $this->DBID) . "')";
			$sFilterChk .= " AND NOT (" . $sFilter . ")";
			$this->CurrentFilter = $sFilterChk;
			$sSqlChk = $this->SQL();
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$rsChk = $conn->Execute($sSqlChk);
			$conn->raiseErrorFn = '';
			if ($rsChk === FALSE) {
				return FALSE;
			} elseif (!$rsChk->EOF) {
				$sIdxErrMsg = str_replace("%f", $this->L_Ref->FldCaption(), $Language->Phrase("DupIndex"));
				$sIdxErrMsg = str_replace("%v", $this->L_Ref->CurrentValue, $sIdxErrMsg);
				$this->setFailureMessage($sIdxErrMsg);
				$rsChk->Close();
				return FALSE;
			}
			$rsChk->Close();
		}
		$this->CurrentFilter = $sFilter;
		$sSql = $this->SQL();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE)
			return FALSE;
		if ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // Set no record message
			$EditRow = FALSE; // Update Failed
		} else {

			// Save old values
			$rsold = &$rs->fields;
			$this->LoadDbValues($rsold);
			$rsnew = array();

			// PF
			$this->PF->SetDbValueDef($rsnew, $this->PF->CurrentValue, 0, $this->PF->ReadOnly);

			// L_Ref
			$this->L_Ref->SetDbValueDef($rsnew, $this->L_Ref->CurrentValue, NULL, $this->L_Ref->ReadOnly);

			// YEAR
			$this->YEAR->SetDbValueDef($rsnew, $this->YEAR->CurrentValue, 0, $this->YEAR->ReadOnly);

			// MONTH
			$this->MONTH->SetDbValueDef($rsnew, $this->MONTH->CurrentValue, 0, $this->MONTH->ReadOnly);

			// Acc_ID
			$this->Acc_ID->SetDbValueDef($rsnew, $this->Acc_ID->CurrentValue, 0, $this->Acc_ID->ReadOnly);

			// AMOUNT
			$this->AMOUNT->SetDbValueDef($rsnew, $this->AMOUNT->CurrentValue, 0, $this->AMOUNT->ReadOnly);

			// STARTED
			$this->STARTED->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->STARTED->CurrentValue, 5), NULL, $this->STARTED->ReadOnly);

			// ENDED
			$this->ENDED->SetDbValueDef($rsnew, ew_UnFormatDateTime($this->ENDED->CurrentValue, 5), NULL, $this->ENDED->ReadOnly);

			// TYPE
			$this->TYPE->SetDbValueDef($rsnew, $this->TYPE->CurrentValue, 0, $this->TYPE->ReadOnly);

			// Batch
			$this->Batch->SetDbValueDef($rsnew, $this->Batch->CurrentValue, 0, $this->Batch->ReadOnly);

			// NOTES
			$this->NOTES->SetDbValueDef($rsnew, $this->NOTES->CurrentValue, NULL, $this->NOTES->ReadOnly);

			// Call Row Updating event
			$bUpdateRow = $this->Row_Updating($rsold, $rsnew);
			if ($bUpdateRow) {
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				if (count($rsnew) > 0)
					$EditRow = $this->Update($rsnew, "", $rsold);
				else
					$EditRow = TRUE; // No field to update
				$conn->raiseErrorFn = '';
				if ($EditRow) {
				}
			} else {
				if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

					// Use the message, do nothing
				} elseif ($this->CancelMessage <> "") {
					$this->setFailureMessage($this->CancelMessage);
					$this->CancelMessage = "";
				} else {
					$this->setFailureMessage($Language->Phrase("UpdateCancelled"));
				}
				$EditRow = FALSE;
			}
		}

		// Call Row_Updated event
		if ($EditRow)
			$this->Row_Updated($rsold, $rsnew);
		if ($EditRow) {
			$this->WriteAuditTrailOnEdit($rsold, $rsnew);
		}
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "deductionslist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'deductions';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (edit page)
	function WriteAuditTrailOnEdit(&$rsold, &$rsnew) {
		global $Language;
		if (!$this->AuditTrailOnEdit) return;
		$table = 'deductions';

		// Get key value
		$key = "";
		if ($key <> "") $key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rsold['Deduction_ID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$usr = CurrentUserName();
		foreach (array_keys($rsnew) as $fldname) {
			if ($this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldDataType == EW_DATATYPE_DATE) { // DateTime field
					$modified = (ew_FormatDateTime($rsold[$fldname], 0) <> ew_FormatDateTime($rsnew[$fldname], 0));
				} else {
					$modified = !ew_CompareValue($rsold[$fldname], $rsnew[$fldname]);
				}
				if ($modified) {
					if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") { // Password Field
						$oldvalue = $Language->Phrase("PasswordMask");
						$newvalue = $Language->Phrase("PasswordMask");
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) { // Memo field
						if (EW_AUDIT_TRAIL_TO_DATABASE) {
							$oldvalue = $rsold[$fldname];
							$newvalue = $rsnew[$fldname];
						} else {
							$oldvalue = "[MEMO]";
							$newvalue = "[MEMO]";
						}
					} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) { // XML field
						$oldvalue = "[XML]";
						$newvalue = "[XML]";
					} else {
						$oldvalue = $rsold[$fldname];
						$newvalue = $rsnew[$fldname];
					}
					ew_WriteAuditTrail("log", $dt, $id, $usr, "U", $table, $fldname, $key, $oldvalue, $newvalue);
				}
			}
		}
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($deductions_edit)) $deductions_edit = new cdeductions_edit();

// Page init
$deductions_edit->Page_Init();

// Page main
$deductions_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$deductions_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fdeductionsedit = new ew_Form("fdeductionsedit", "edit");

// Validate form
fdeductionsedit.Validate = function() {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	var $ = jQuery, fobj = this.GetForm(), $fobj = $(fobj);
	if ($fobj.find("#a_confirm").val() == "F")
		return true;
	var elm, felm, uelm, addcnt = 0;
	var $k = $fobj.find("#" + this.FormKeyCountName); // Get key_count
	var rowcnt = ($k[0]) ? parseInt($k.val(), 10) : 1;
	var startcnt = (rowcnt == 0) ? 0 : 1; // Check rowcnt == 0 => Inline-Add
	var gridinsert = $fobj.find("#a_list").val() == "gridinsert";
	for (var i = startcnt; i <= rowcnt; i++) {
		var infix = ($k[0]) ? String(i) : "";
		$fobj.data("rowindex", infix);
			elm = this.GetElements("x" + infix + "_PF");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->PF->FldCaption(), $deductions->PF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_YEAR");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->YEAR->FldCaption(), $deductions->YEAR->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_MONTH");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->MONTH->FldCaption(), $deductions->MONTH->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Acc_ID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->Acc_ID->FldCaption(), $deductions->Acc_ID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_AMOUNT");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->AMOUNT->FldCaption(), $deductions->AMOUNT->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_AMOUNT");
			if (elm && !ew_CheckNumber(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->AMOUNT->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_STARTED");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->STARTED->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_ENDED");
			if (elm && !ew_CheckDate(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->ENDED->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_TYPE");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->TYPE->FldCaption(), $deductions->TYPE->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Batch");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $deductions->Batch->FldCaption(), $deductions->Batch->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}

	// Process detail forms
	var dfs = $fobj.find("input[name='detailpage']").get();
	for (var i = 0; i < dfs.length; i++) {
		var df = dfs[i], val = df.value;
		if (val && ewForms[val])
			if (!ewForms[val].Validate())
				return false;
	}
	return true;
}

// Form_CustomValidate event
fdeductionsedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fdeductionsedit.ValidateRequired = true;
<?php } else { ?>
fdeductionsedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fdeductionsedit.Lists["x_PF"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_PF","x_Name","",""],"ParentFields":[],"ChildFields":["x_Acc_ID"],"FilterFields":[],"Options":[],"Template":""};
fdeductionsedit.Lists["x_YEAR"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsedit.Lists["x_YEAR"].Options = <?php echo json_encode($deductions->YEAR->Options()) ?>;
fdeductionsedit.Lists["x_MONTH"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsedit.Lists["x_MONTH"].Options = <?php echo json_encode($deductions->MONTH->Options()) ?>;
fdeductionsedit.Lists["x_Acc_ID"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_Bank_Name","x_Acc_NO","",""],"ParentFields":["x_PF"],"ChildFields":[],"FilterFields":["x_PF"],"Options":[],"Template":""};
fdeductionsedit.Lists["x_TYPE"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsedit.Lists["x_TYPE"].Options = <?php echo json_encode($deductions->TYPE->Options()) ?>;
fdeductionsedit.Lists["x_Batch"] = {"LinkField":"x_Batch_ID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Batch_Number","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $deductions_edit->ShowPageHeader(); ?>
<?php
$deductions_edit->ShowMessage();
?>
<form name="fdeductionsedit" id="fdeductionsedit" class="<?php echo $deductions_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($deductions_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $deductions_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="deductions">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<?php if ($deductions->CurrentAction == "F") { // Confirm page ?>
<input type="hidden" name="a_confirm" id="a_confirm" value="F">
<?php } ?>
<div class="ewDesktop">
<div>
<table id="tbl_deductionsedit" class="table table-bordered table-striped ewDesktopTable">
<?php if ($deductions->PF->Visible) { // PF ?>
	<tr id="r_PF">
		<td><span id="elh_deductions_PF"><?php echo $deductions->PF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->PF->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_PF">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$deductions->PF->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$deductions->PF->EditAttrs["onchange"] = "";
?>
<span id="as_x_PF" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_PF" id="sv_x_PF" value="<?php echo $deductions->PF->EditValue ?>" size="6" placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>"<?php echo $deductions->PF->EditAttributes() ?>>
</span>
<input type="hidden" data-table="deductions" data-field="x_PF" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->PF->DisplayValueSeparator) ? json_encode($deductions->PF->DisplayValueSeparator) : $deductions->PF->DisplayValueSeparator) ?>" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($deductions->PF->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld` FROM `emp`";
$sWhereWrk = "`PF` LIKE '{query_value}%' OR CONCAT(`PF`,'" . ew_ValueSeparator(1, $Page->PF) . "',`Name`) LIKE '{query_value}%'";
$deductions->Lookup_Selecting($deductions->PF, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_PF" id="q_x_PF" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
fdeductionsedit.CreateAutoSuggest({"id":"x_PF","forceSelect":true});
</script>
</span>
<?php } else { ?>
<span id="el_deductions_PF">
<span<?php echo $deductions->PF->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($deductions->PF->TooltipValue)) && $deductions->PF->LinkAttributes() <> "") { ?>
<a<?php echo $deductions->PF->LinkAttributes() ?>><p class="form-control-static"><?php echo $deductions->PF->ViewValue ?></p></a>
<?php } else { ?>
<p class="form-control-static"><?php echo $deductions->PF->ViewValue ?></p>
<?php } ?>
<span id="tt_deductions_x_PF" style="display: none">
<?php echo $deductions->PF->TooltipValue ?>
</span></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_PF" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($deductions->PF->FormValue) ?>">
<?php } ?>
<?php echo $deductions->PF->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
	<tr id="r_L_Ref">
		<td><span id="elh_deductions_L_Ref"><?php echo $deductions->L_Ref->FldCaption() ?></span></td>
		<td<?php echo $deductions->L_Ref->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_L_Ref">
<input type="text" data-table="deductions" data-field="x_L_Ref" name="x_L_Ref" id="x_L_Ref" placeholder="<?php echo ew_HtmlEncode($deductions->L_Ref->getPlaceHolder()) ?>" value="<?php echo $deductions->L_Ref->EditValue ?>"<?php echo $deductions->L_Ref->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_deductions_L_Ref">
<span<?php echo $deductions->L_Ref->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->L_Ref->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_L_Ref" name="x_L_Ref" id="x_L_Ref" value="<?php echo ew_HtmlEncode($deductions->L_Ref->FormValue) ?>">
<?php } ?>
<?php echo $deductions->L_Ref->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->YEAR->Visible) { // YEAR ?>
	<tr id="r_YEAR">
		<td><span id="elh_deductions_YEAR"><?php echo $deductions->YEAR->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->YEAR->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_YEAR">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->YEAR->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_YEAR" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php
$arwrk = $deductions->YEAR->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($deductions->YEAR->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "")
			$emptywrk = FALSE;
?>
<input type="radio" data-table="deductions" data-field="x_YEAR" name="x_YEAR" id="x_YEAR_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $deductions->YEAR->EditAttributes() ?>><?php echo $deductions->YEAR->DisplayValue($arwrk[$rowcntwrk]) ?>
<?php
	}
	if ($emptywrk && strval($deductions->YEAR->CurrentValue) <> "") {
?>
<input type="radio" data-table="deductions" data-field="x_YEAR" name="x_YEAR" id="x_YEAR_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($deductions->YEAR->CurrentValue) ?>" checked<?php echo $deductions->YEAR->EditAttributes() ?>><?php echo $deductions->YEAR->CurrentValue ?>
<?php
    }
}
?>
		</div>
	</div>
	<div id="tp_x_YEAR" class="ewTemplate"><input type="radio" data-table="deductions" data-field="x_YEAR" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->YEAR->DisplayValueSeparator) ? json_encode($deductions->YEAR->DisplayValueSeparator) : $deductions->YEAR->DisplayValueSeparator) ?>" name="x_YEAR" id="x_YEAR" value="{value}"<?php echo $deductions->YEAR->EditAttributes() ?>></div>
</div>
</span>
<?php } else { ?>
<span id="el_deductions_YEAR">
<span<?php echo $deductions->YEAR->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->YEAR->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_YEAR" name="x_YEAR" id="x_YEAR" value="<?php echo ew_HtmlEncode($deductions->YEAR->FormValue) ?>">
<?php } ?>
<?php echo $deductions->YEAR->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->MONTH->Visible) { // MONTH ?>
	<tr id="r_MONTH">
		<td><span id="elh_deductions_MONTH"><?php echo $deductions->MONTH->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->MONTH->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_MONTH">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->MONTH->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_MONTH" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php
$arwrk = $deductions->MONTH->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($deductions->MONTH->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "")
			$emptywrk = FALSE;
?>
<input type="radio" data-table="deductions" data-field="x_MONTH" name="x_MONTH" id="x_MONTH_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $deductions->MONTH->EditAttributes() ?>><?php echo $deductions->MONTH->DisplayValue($arwrk[$rowcntwrk]) ?>
<?php
	}
	if ($emptywrk && strval($deductions->MONTH->CurrentValue) <> "") {
?>
<input type="radio" data-table="deductions" data-field="x_MONTH" name="x_MONTH" id="x_MONTH_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($deductions->MONTH->CurrentValue) ?>" checked<?php echo $deductions->MONTH->EditAttributes() ?>><?php echo $deductions->MONTH->CurrentValue ?>
<?php
    }
}
?>
		</div>
	</div>
	<div id="tp_x_MONTH" class="ewTemplate"><input type="radio" data-table="deductions" data-field="x_MONTH" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->MONTH->DisplayValueSeparator) ? json_encode($deductions->MONTH->DisplayValueSeparator) : $deductions->MONTH->DisplayValueSeparator) ?>" name="x_MONTH" id="x_MONTH" value="{value}"<?php echo $deductions->MONTH->EditAttributes() ?>></div>
</div>
</span>
<?php } else { ?>
<span id="el_deductions_MONTH">
<span<?php echo $deductions->MONTH->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->MONTH->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_MONTH" name="x_MONTH" id="x_MONTH" value="<?php echo ew_HtmlEncode($deductions->MONTH->FormValue) ?>">
<?php } ?>
<?php echo $deductions->MONTH->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
	<tr id="r_Acc_ID">
		<td><span id="elh_deductions_Acc_ID"><?php echo $deductions->Acc_ID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->Acc_ID->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_Acc_ID">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Acc_ID->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_Acc_ID" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php
$arwrk = $deductions->Acc_ID->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($deductions->Acc_ID->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "") {
			$emptywrk = FALSE;
?>
<input type="radio" data-table="deductions" data-field="x_Acc_ID" name="x_Acc_ID" id="x_Acc_ID_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $deductions->Acc_ID->EditAttributes() ?>><?php echo $deductions->Acc_ID->DisplayValue($arwrk[$rowcntwrk]) ?>
<?php
		}
	}
	if ($emptywrk && strval($deductions->Acc_ID->CurrentValue) <> "") {
?>
<input type="radio" data-table="deductions" data-field="x_Acc_ID" name="x_Acc_ID" id="x_Acc_ID_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($deductions->Acc_ID->CurrentValue) ?>" checked<?php echo $deductions->Acc_ID->EditAttributes() ?>><?php echo $deductions->Acc_ID->CurrentValue ?>
<?php
    }
}
?>
		</div>
	</div>
	<div id="tp_x_Acc_ID" class="ewTemplate"><input type="radio" data-table="deductions" data-field="x_Acc_ID" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->Acc_ID->DisplayValueSeparator) ? json_encode($deductions->Acc_ID->DisplayValueSeparator) : $deductions->Acc_ID->DisplayValueSeparator) ?>" name="x_Acc_ID" id="x_Acc_ID" value="{value}"<?php echo $deductions->Acc_ID->EditAttributes() ?>></div>
</div>
<?php if (AllowAdd(CurrentProjectID() . "accounts")) { ?>
<button type="button" title="<?php echo ew_HtmlTitle($Language->Phrase("AddLink")) . "&nbsp;" . $deductions->Acc_ID->FldCaption() ?>" onclick="ew_AddOptDialogShow({lnk:this,el:'x_Acc_ID',url:'accountsaddopt.php'});" class="ewAddOptBtn btn btn-default btn-sm" id="aol_x_Acc_ID"><span class="glyphicon glyphicon-plus ewIcon"></span><span class="hide"><?php echo $Language->Phrase("AddLink") ?>&nbsp;<?php echo $deductions->Acc_ID->FldCaption() ?></span></button>
<?php } ?>
<?php
$sSqlWrk = "SELECT DISTINCT `PF`, `Bank_Name` AS `DispFld`, `Acc_NO` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `accounts`";
$sWhereWrk = "{filter}";
$deductions->Acc_ID->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$deductions->Acc_ID->LookupFilters += array("f0" => "`PF` = {filter_value}", "t0" => "3", "fn0" => "");
$deductions->Acc_ID->LookupFilters += array("f1" => "`PF` IN ({filter_value})", "t1" => "3", "fn1" => "");
$sSqlWrk = "";
$deductions->Lookup_Selecting($deductions->Acc_ID, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $deductions->Acc_ID->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_Acc_ID" id="s_x_Acc_ID" value="<?php echo $deductions->Acc_ID->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el_deductions_Acc_ID">
<span<?php echo $deductions->Acc_ID->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->Acc_ID->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_Acc_ID" name="x_Acc_ID" id="x_Acc_ID" value="<?php echo ew_HtmlEncode($deductions->Acc_ID->FormValue) ?>">
<?php } ?>
<?php echo $deductions->Acc_ID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
	<tr id="r_AMOUNT">
		<td><span id="elh_deductions_AMOUNT"><?php echo $deductions->AMOUNT->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->AMOUNT->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_AMOUNT">
<input type="text" data-table="deductions" data-field="x_AMOUNT" name="x_AMOUNT" id="x_AMOUNT" size="30" placeholder="<?php echo ew_HtmlEncode($deductions->AMOUNT->getPlaceHolder()) ?>" value="<?php echo $deductions->AMOUNT->EditValue ?>"<?php echo $deductions->AMOUNT->EditAttributes() ?>>
</span>
<?php } else { ?>
<span id="el_deductions_AMOUNT">
<span<?php echo $deductions->AMOUNT->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->AMOUNT->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_AMOUNT" name="x_AMOUNT" id="x_AMOUNT" value="<?php echo ew_HtmlEncode($deductions->AMOUNT->FormValue) ?>">
<?php } ?>
<?php echo $deductions->AMOUNT->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->STARTED->Visible) { // STARTED ?>
	<tr id="r_STARTED">
		<td><span id="elh_deductions_STARTED"><?php echo $deductions->STARTED->FldCaption() ?></span></td>
		<td<?php echo $deductions->STARTED->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_STARTED">
<input type="text" data-table="deductions" data-field="x_STARTED" data-format="5" name="x_STARTED" id="x_STARTED" placeholder="<?php echo ew_HtmlEncode($deductions->STARTED->getPlaceHolder()) ?>" value="<?php echo $deductions->STARTED->EditValue ?>"<?php echo $deductions->STARTED->EditAttributes() ?>>
<?php if (!$deductions->STARTED->ReadOnly && !$deductions->STARTED->Disabled && !isset($deductions->STARTED->EditAttrs["readonly"]) && !isset($deductions->STARTED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionsedit", "x_STARTED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php } else { ?>
<span id="el_deductions_STARTED">
<span<?php echo $deductions->STARTED->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->STARTED->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_STARTED" name="x_STARTED" id="x_STARTED" value="<?php echo ew_HtmlEncode($deductions->STARTED->FormValue) ?>">
<?php } ?>
<?php echo $deductions->STARTED->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->ENDED->Visible) { // ENDED ?>
	<tr id="r_ENDED">
		<td><span id="elh_deductions_ENDED"><?php echo $deductions->ENDED->FldCaption() ?></span></td>
		<td<?php echo $deductions->ENDED->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_ENDED">
<input type="text" data-table="deductions" data-field="x_ENDED" data-format="5" name="x_ENDED" id="x_ENDED" placeholder="<?php echo ew_HtmlEncode($deductions->ENDED->getPlaceHolder()) ?>" value="<?php echo $deductions->ENDED->EditValue ?>"<?php echo $deductions->ENDED->EditAttributes() ?>>
<?php if (!$deductions->ENDED->ReadOnly && !$deductions->ENDED->Disabled && !isset($deductions->ENDED->EditAttrs["readonly"]) && !isset($deductions->ENDED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionsedit", "x_ENDED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
<?php } else { ?>
<span id="el_deductions_ENDED">
<span<?php echo $deductions->ENDED->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->ENDED->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_ENDED" name="x_ENDED" id="x_ENDED" value="<?php echo ew_HtmlEncode($deductions->ENDED->FormValue) ?>">
<?php } ?>
<?php echo $deductions->ENDED->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->TYPE->Visible) { // TYPE ?>
	<tr id="r_TYPE">
		<td><span id="elh_deductions_TYPE"><?php echo $deductions->TYPE->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->TYPE->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_TYPE">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->TYPE->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_TYPE" data-repeatcolumn="5" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php
$arwrk = $deductions->TYPE->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($deductions->TYPE->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "")
			$emptywrk = FALSE;
?>
<input type="radio" data-table="deductions" data-field="x_TYPE" name="x_TYPE" id="x_TYPE_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $deductions->TYPE->EditAttributes() ?>><?php echo $deductions->TYPE->DisplayValue($arwrk[$rowcntwrk]) ?>
<?php
	}
	if ($emptywrk && strval($deductions->TYPE->CurrentValue) <> "") {
?>
<input type="radio" data-table="deductions" data-field="x_TYPE" name="x_TYPE" id="x_TYPE_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($deductions->TYPE->CurrentValue) ?>" checked<?php echo $deductions->TYPE->EditAttributes() ?>><?php echo $deductions->TYPE->CurrentValue ?>
<?php
    }
}
?>
		</div>
	</div>
	<div id="tp_x_TYPE" class="ewTemplate"><input type="radio" data-table="deductions" data-field="x_TYPE" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->TYPE->DisplayValueSeparator) ? json_encode($deductions->TYPE->DisplayValueSeparator) : $deductions->TYPE->DisplayValueSeparator) ?>" name="x_TYPE" id="x_TYPE" value="{value}"<?php echo $deductions->TYPE->EditAttributes() ?>></div>
</div>
</span>
<?php } else { ?>
<span id="el_deductions_TYPE">
<span<?php echo $deductions->TYPE->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->TYPE->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_TYPE" name="x_TYPE" id="x_TYPE" value="<?php echo ew_HtmlEncode($deductions->TYPE->FormValue) ?>">
<?php } ?>
<?php echo $deductions->TYPE->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->Batch->Visible) { // Batch ?>
	<tr id="r_Batch">
		<td><span id="elh_deductions_Batch"><?php echo $deductions->Batch->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $deductions->Batch->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_Batch">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Batch->ViewValue ?>
	</span>
	<span class="glyphicon glyphicon-remove form-control-feedback ewDropdownListClear"></span>
	<span class="form-control-feedback"><span class="caret"></span></span>
	<div id="dsl_x_Batch" data-repeatcolumn="1" class="dropdown-menu">
		<div class="ewItems" style="position: relative; overflow-x: hidden;">
<?php
$arwrk = $deductions->Batch->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($deductions->Batch->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "") {
			$emptywrk = FALSE;
?>
<input type="radio" data-table="deductions" data-field="x_Batch" name="x_Batch" id="x_Batch_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $deductions->Batch->EditAttributes() ?>><?php echo $deductions->Batch->DisplayValue($arwrk[$rowcntwrk]) ?>
<?php
		}
	}
	if ($emptywrk && strval($deductions->Batch->CurrentValue) <> "") {
?>
<input type="radio" data-table="deductions" data-field="x_Batch" name="x_Batch" id="x_Batch_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($deductions->Batch->CurrentValue) ?>" checked<?php echo $deductions->Batch->EditAttributes() ?>><?php echo $deductions->Batch->CurrentValue ?>
<?php
    }
}
?>
		</div>
	</div>
	<div id="tp_x_Batch" class="ewTemplate"><input type="radio" data-table="deductions" data-field="x_Batch" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->Batch->DisplayValueSeparator) ? json_encode($deductions->Batch->DisplayValueSeparator) : $deductions->Batch->DisplayValueSeparator) ?>" name="x_Batch" id="x_Batch" value="{value}"<?php echo $deductions->Batch->EditAttributes() ?>></div>
</div>
<?php
$sSqlWrk = "SELECT `Batch_ID`, `Batch_Number` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `batches`";
$sWhereWrk = "";
$deductions->Batch->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$deductions->Batch->LookupFilters += array("f0" => "`Batch_ID` = {filter_value}", "t0" => "3", "fn0" => "");
$sSqlWrk = "";
$deductions->Lookup_Selecting($deductions->Batch, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $deductions->Batch->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_Batch" id="s_x_Batch" value="<?php echo $deductions->Batch->LookupFilterQuery() ?>">
</span>
<?php } else { ?>
<span id="el_deductions_Batch">
<span<?php echo $deductions->Batch->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->Batch->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_Batch" name="x_Batch" id="x_Batch" value="<?php echo ew_HtmlEncode($deductions->Batch->FormValue) ?>">
<?php } ?>
<?php echo $deductions->Batch->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($deductions->NOTES->Visible) { // NOTES ?>
	<tr id="r_NOTES">
		<td><span id="elh_deductions_NOTES"><?php echo $deductions->NOTES->FldCaption() ?></span></td>
		<td<?php echo $deductions->NOTES->CellAttributes() ?>>
<?php if ($deductions->CurrentAction <> "F") { ?>
<span id="el_deductions_NOTES">
<textarea data-table="deductions" data-field="x_NOTES" name="x_NOTES" id="x_NOTES" cols="35" rows="4" placeholder="<?php echo ew_HtmlEncode($deductions->NOTES->getPlaceHolder()) ?>"<?php echo $deductions->NOTES->EditAttributes() ?>><?php echo $deductions->NOTES->EditValue ?></textarea>
</span>
<?php } else { ?>
<span id="el_deductions_NOTES">
<span<?php echo $deductions->NOTES->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $deductions->NOTES->ViewValue ?></p></span>
</span>
<input type="hidden" data-table="deductions" data-field="x_NOTES" name="x_NOTES" id="x_NOTES" value="<?php echo ew_HtmlEncode($deductions->NOTES->FormValue) ?>">
<?php } ?>
<?php echo $deductions->NOTES->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
<input type="hidden" data-table="deductions" data-field="x_Deduction_ID" name="x_Deduction_ID" id="x_Deduction_ID" value="<?php echo ew_HtmlEncode($deductions->Deduction_ID->CurrentValue) ?>">
<div class="ewDesktopButton">
<?php if ($deductions->CurrentAction <> "F") { // Confirm page ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit" onclick="this.form.a_edit.value='F';"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $deductions_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } else { ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("ConfirmBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="submit" onclick="this.form.a_edit.value='X';"><?php echo $Language->Phrase("CancelBtn") ?></button>
<?php } ?>
</div>
</div>
</form>
<script type="text/javascript">
fdeductionsedit.Init();
</script>
<?php
$deductions_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$deductions_edit->Page_Terminate();
?>
