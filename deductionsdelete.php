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

$deductions_delete = NULL; // Initialize page object first

class cdeductions_delete extends cdeductions {

	// Page ID
	var $PageID = 'delete';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'deductions';

	// Page object name
	var $PageObjName = 'deductions_delete';

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
    var $AuditTrailOnEdit = FALSE;
    var $AuditTrailOnDelete = TRUE;
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
			define("EW_PAGE_ID", 'delete', TRUE);

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
		if (!$Security->CanDelete()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("deductionslist.php"));
			else
				$this->Page_Terminate(ew_GetUrl("login.php"));
		}
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
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $TotalRecs = 0;
	var $RecCnt;
	var $RecKeys = array();
	var $Recordset;
	var $StartRowCnt = 1;
	var $RowCnt = 0;

	//
	// Page main
	//
	function Page_Main() {
		global $Language;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Load key parameters
		$this->RecKeys = $this->GetRecordKeys(); // Load record keys
		$sFilter = $this->GetKeyFilter();
		if ($sFilter == "")
			$this->Page_Terminate("deductionslist.php"); // Prevent SQL injection, return to list

		// Set up filter (SQL WHHERE clause) and get return SQL
		// SQL constructor in deductions class, deductionsinfo.php

		$this->CurrentFilter = $sFilter;

		// Get action
		if (@$_POST["a_delete"] <> "") {
			$this->CurrentAction = $_POST["a_delete"];
		} else {
			$this->CurrentAction = "I"; // Display record
		}
		switch ($this->CurrentAction) {
			case "D": // Delete
				$this->SendEmail = TRUE; // Send email on delete success
				if ($this->DeleteRows()) { // Delete rows
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("DeleteSuccess")); // Set up success message
					$this->Page_Terminate($this->getReturnUrl()); // Return to caller
				}
		}
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
				$rs = $conn->SelectLimit($sSql, $rowcnt, $offset, array("_hasOrderBy" => trim($this->getOrderBy()) || trim($this->getSessionOrderBy())));
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
		$this->L_Ref->setDbValue($rs->fields('L_Ref'));
		$this->YEAR->setDbValue($rs->fields('YEAR'));
		$this->MONTH->setDbValue($rs->fields('MONTH'));
		$this->Acc_ID->setDbValue($rs->fields('Acc_ID'));
		$this->AMOUNT->setDbValue($rs->fields('AMOUNT'));
		$this->STARTED->setDbValue($rs->fields('STARTED'));
		$this->ENDED->setDbValue($rs->fields('ENDED'));
		$this->TYPE->setDbValue($rs->fields('TYPE'));
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
		// NOTES

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// PF
		$this->PF->ViewValue = $this->PF->CurrentValue;
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
		$this->TYPE->CellCssStyle .= "text-align: center;";
		$this->TYPE->ViewCustomAttributes = "";

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

			// NOTES
			$this->NOTES->LinkCustomAttributes = "";
			$this->NOTES->HrefValue = "";
			$this->NOTES->TooltipValue = "";
		}

		// Call Row Rendered event
		if ($this->RowType <> EW_ROWTYPE_AGGREGATEINIT)
			$this->Row_Rendered();
	}

	//
	// Delete records based on current filter
	//
	function DeleteRows() {
		global $Language, $Security;
		if (!$Security->CanDelete()) {
			$this->setFailureMessage($Language->Phrase("NoDeletePermission")); // No delete permission
			return FALSE;
		}
		$DeleteRows = TRUE;
		$sSql = $this->SQL();
		$conn = &$this->Connection();
		$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
		$rs = $conn->Execute($sSql);
		$conn->raiseErrorFn = '';
		if ($rs === FALSE) {
			return FALSE;
		} elseif ($rs->EOF) {
			$this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
			$rs->Close();
			return FALSE;

		//} else {
		//	$this->LoadRowValues($rs); // Load row values

		}
		$rows = ($rs) ? $rs->GetRows() : array();
		$conn->BeginTrans();
		if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteBegin")); // Batch delete begin

		// Clone old rows
		$rsold = $rows;
		if ($rs)
			$rs->Close();

		// Call row deleting event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$DeleteRows = $this->Row_Deleting($row);
				if (!$DeleteRows) break;
			}
		}
		if ($DeleteRows) {
			$sKey = "";
			foreach ($rsold as $row) {
				$sThisKey = "";
				if ($sThisKey <> "") $sThisKey .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
				$sThisKey .= $row['Deduction_ID'];
				$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
				$DeleteRows = $this->Delete($row); // Delete
				$conn->raiseErrorFn = '';
				if ($DeleteRows === FALSE)
					break;
				if ($sKey <> "") $sKey .= ", ";
				$sKey .= $sThisKey;
			}
		} else {

			// Set up error message
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("DeleteCancelled"));
			}
		}
		if ($DeleteRows) {
			$conn->CommitTrans(); // Commit the changes
			if ($DeleteRows) {
				foreach ($rsold as $row)
					$this->WriteAuditTrailOnDelete($row);
			}
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteSuccess")); // Batch delete success
			$sTable = 'deductions';
			$sSubject = $sTable . " " . $Language->Phrase("RecordDeleted");
			$sAction = $Language->Phrase("ActionDeleted");
			$Email = new cEmail();
			$Email->Load(EW_EMAIL_NOTIFY_TEMPLATE);
			$Email->ReplaceSender(EW_SENDER_EMAIL); // Replace Sender
			$Email->ReplaceRecipient(EW_RECIPIENT_EMAIL); // Replace Recipient
			$Email->ReplaceSubject($sSubject); // Replace Subject
			$Email->ReplaceContent("<!--table-->", $sTable);
			$Email->ReplaceContent("<!--key-->", $sKey);
			$Email->ReplaceContent("<!--action-->", $sAction);
			$Args = array();
			$Args["rs"] = &$rsold;
			$bEmailSent = FALSE;
			if ($this->Email_Sending($Email, $Args))
				$bEmailSent = $Email->Send();
			if (!$bEmailSent)
				$this->setFailureMessage($Email->SendErrDescription);
		} else {
			$conn->RollbackTrans(); // Rollback changes
			if ($this->AuditTrailOnDelete) $this->WriteAuditTrailDummy($Language->Phrase("BatchDeleteRollback")); // Batch delete rollback
		}

		// Call Row Deleted event
		if ($DeleteRows) {
			foreach ($rsold as $row) {
				$this->Row_Deleted($row);
			}
		}
		return $DeleteRows;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "deductionslist.php", "", $this->TableVar, TRUE);
		$PageId = "delete";
		$Breadcrumb->Add("delete", $PageId, $url);
	}

	// Write Audit Trail start/end for grid update
	function WriteAuditTrailDummy($typ) {
		$table = 'deductions';
		$usr = CurrentUserName();
		ew_WriteAuditTrail("log", ew_StdCurrentDateTime(), ew_ScriptName(), $usr, $typ, $table, "", "", "", "");
	}

	// Write Audit Trail (delete page)
	function WriteAuditTrailOnDelete(&$rs) {
		global $Language;
		if (!$this->AuditTrailOnDelete) return;
		$table = 'deductions';

		// Get key value
		$key = "";
		if ($key <> "")
			$key .= $GLOBALS["EW_COMPOSITE_KEY_SEPARATOR"];
		$key .= $rs['Deduction_ID'];

		// Write Audit Trail
		$dt = ew_StdCurrentDateTime();
		$id = ew_ScriptName();
		$curUser = CurrentUserName();
		foreach (array_keys($rs) as $fldname) {
			if (array_key_exists($fldname, $this->fields) && $this->fields[$fldname]->FldDataType <> EW_DATATYPE_BLOB) { // Ignore BLOB fields
				if ($this->fields[$fldname]->FldHtmlTag == "PASSWORD") {
					$oldvalue = $Language->Phrase("PasswordMask"); // Password Field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_MEMO) {
					if (EW_AUDIT_TRAIL_TO_DATABASE)
						$oldvalue = $rs[$fldname];
					else
						$oldvalue = "[MEMO]"; // Memo field
				} elseif ($this->fields[$fldname]->FldDataType == EW_DATATYPE_XML) {
					$oldvalue = "[XML]"; // XML field
				} else {
					$oldvalue = $rs[$fldname];
				}
				ew_WriteAuditTrail("log", $dt, $id, $curUser, "D", $table, $fldname, $key, $oldvalue, "");
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
}
?>
<?php ew_Header(FALSE) ?>
<?php

// Create page object
if (!isset($deductions_delete)) $deductions_delete = new cdeductions_delete();

// Page init
$deductions_delete->Page_Init();

// Page main
$deductions_delete->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$deductions_delete->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "delete";
var CurrentForm = fdeductionsdelete = new ew_Form("fdeductionsdelete", "delete");

// Form_CustomValidate event
fdeductionsdelete.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fdeductionsdelete.ValidateRequired = true;
<?php } else { ?>
fdeductionsdelete.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fdeductionsdelete.Lists["x_YEAR"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsdelete.Lists["x_YEAR"].Options = <?php echo json_encode($deductions->YEAR->Options()) ?>;
fdeductionsdelete.Lists["x_MONTH"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsdelete.Lists["x_MONTH"].Options = <?php echo json_encode($deductions->MONTH->Options()) ?>;
fdeductionsdelete.Lists["x_Acc_ID"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_Acc_NO","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsdelete.Lists["x_TYPE"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionsdelete.Lists["x_TYPE"].Options = <?php echo json_encode($deductions->TYPE->Options()) ?>;

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php

// Load records for display
if ($deductions_delete->Recordset = $deductions_delete->LoadRecordset())
	$deductions_deleteTotalRecs = $deductions_delete->Recordset->RecordCount(); // Get record count
if ($deductions_deleteTotalRecs <= 0) { // No record found, exit
	if ($deductions_delete->Recordset)
		$deductions_delete->Recordset->Close();
	$deductions_delete->Page_Terminate("deductionslist.php"); // Return to list
}
?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php $deductions_delete->ShowPageHeader(); ?>
<?php
$deductions_delete->ShowMessage();
?>
<form name="fdeductionsdelete" id="fdeductionsdelete" class="form-inline ewForm ewDeleteForm" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($deductions_delete->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $deductions_delete->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="deductions">
<input type="hidden" name="a_delete" id="a_delete" value="D">
<?php foreach ($deductions_delete->RecKeys as $key) { ?>
<?php $keyvalue = is_array($key) ? implode($EW_COMPOSITE_KEY_SEPARATOR, $key) : $key; ?>
<input type="hidden" name="key_m[]" value="<?php echo ew_HtmlEncode($keyvalue) ?>">
<?php } ?>
<div class="ewGrid">
<div class="<?php if (ew_IsResponsiveLayout()) { echo "table-responsive "; } ?>ewGridMiddlePanel">
<table class="table ewTable">
<?php echo $deductions->TableCustomInnerHtml ?>
	<thead>
	<tr class="ewTableHeader">
<?php if ($deductions->PF->Visible) { // PF ?>
		<th><span id="elh_deductions_PF" class="deductions_PF"><?php echo $deductions->PF->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
		<th><span id="elh_deductions_L_Ref" class="deductions_L_Ref"><?php echo $deductions->L_Ref->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->YEAR->Visible) { // YEAR ?>
		<th><span id="elh_deductions_YEAR" class="deductions_YEAR"><?php echo $deductions->YEAR->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->MONTH->Visible) { // MONTH ?>
		<th><span id="elh_deductions_MONTH" class="deductions_MONTH"><?php echo $deductions->MONTH->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
		<th><span id="elh_deductions_Acc_ID" class="deductions_Acc_ID"><?php echo $deductions->Acc_ID->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
		<th><span id="elh_deductions_AMOUNT" class="deductions_AMOUNT"><?php echo $deductions->AMOUNT->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->STARTED->Visible) { // STARTED ?>
		<th><span id="elh_deductions_STARTED" class="deductions_STARTED"><?php echo $deductions->STARTED->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->ENDED->Visible) { // ENDED ?>
		<th><span id="elh_deductions_ENDED" class="deductions_ENDED"><?php echo $deductions->ENDED->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->TYPE->Visible) { // TYPE ?>
		<th><span id="elh_deductions_TYPE" class="deductions_TYPE"><?php echo $deductions->TYPE->FldCaption() ?></span></th>
<?php } ?>
<?php if ($deductions->NOTES->Visible) { // NOTES ?>
		<th><span id="elh_deductions_NOTES" class="deductions_NOTES"><?php echo $deductions->NOTES->FldCaption() ?></span></th>
<?php } ?>
	</tr>
	</thead>
	<tbody>
<?php
$deductions_delete->RecCnt = 0;
$i = 0;
while (!$deductions_delete->Recordset->EOF) {
	$deductions_delete->RecCnt++;
	$deductions_delete->RowCnt++;

	// Set row properties
	$deductions->ResetAttrs();
	$deductions->RowType = EW_ROWTYPE_VIEW; // View

	// Get the field contents
	$deductions_delete->LoadRowValues($deductions_delete->Recordset);

	// Render row
	$deductions_delete->RenderRow();
?>
	<tr<?php echo $deductions->RowAttributes() ?>>
<?php if ($deductions->PF->Visible) { // PF ?>
		<td<?php echo $deductions->PF->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_PF" class="deductions_PF">
<span<?php echo $deductions->PF->ViewAttributes() ?>>
<?php if ((!ew_EmptyStr($deductions->PF->TooltipValue)) && $deductions->PF->LinkAttributes() <> "") { ?>
<a<?php echo $deductions->PF->LinkAttributes() ?>><?php echo $deductions->PF->ListViewValue() ?></a>
<?php } else { ?>
<?php echo $deductions->PF->ListViewValue() ?>
<?php } ?>
<span id="tt_deductions_x_PF" style="display: none">
<?php echo $deductions->PF->TooltipValue ?>
</span></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
		<td<?php echo $deductions->L_Ref->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_L_Ref" class="deductions_L_Ref">
<span<?php echo $deductions->L_Ref->ViewAttributes() ?>>
<?php echo $deductions->L_Ref->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->YEAR->Visible) { // YEAR ?>
		<td<?php echo $deductions->YEAR->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_YEAR" class="deductions_YEAR">
<span<?php echo $deductions->YEAR->ViewAttributes() ?>>
<?php echo $deductions->YEAR->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->MONTH->Visible) { // MONTH ?>
		<td<?php echo $deductions->MONTH->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_MONTH" class="deductions_MONTH">
<span<?php echo $deductions->MONTH->ViewAttributes() ?>>
<?php echo $deductions->MONTH->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
		<td<?php echo $deductions->Acc_ID->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_Acc_ID" class="deductions_Acc_ID">
<span<?php echo $deductions->Acc_ID->ViewAttributes() ?>>
<?php echo $deductions->Acc_ID->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
		<td<?php echo $deductions->AMOUNT->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_AMOUNT" class="deductions_AMOUNT">
<span<?php echo $deductions->AMOUNT->ViewAttributes() ?>>
<?php echo $deductions->AMOUNT->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->STARTED->Visible) { // STARTED ?>
		<td<?php echo $deductions->STARTED->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_STARTED" class="deductions_STARTED">
<span<?php echo $deductions->STARTED->ViewAttributes() ?>>
<?php echo $deductions->STARTED->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->ENDED->Visible) { // ENDED ?>
		<td<?php echo $deductions->ENDED->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_ENDED" class="deductions_ENDED">
<span<?php echo $deductions->ENDED->ViewAttributes() ?>>
<?php echo $deductions->ENDED->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->TYPE->Visible) { // TYPE ?>
		<td<?php echo $deductions->TYPE->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_TYPE" class="deductions_TYPE">
<span<?php echo $deductions->TYPE->ViewAttributes() ?>>
<?php echo $deductions->TYPE->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
<?php if ($deductions->NOTES->Visible) { // NOTES ?>
		<td<?php echo $deductions->NOTES->CellAttributes() ?>>
<span id="el<?php echo $deductions_delete->RowCnt ?>_deductions_NOTES" class="deductions_NOTES">
<span<?php echo $deductions->NOTES->ViewAttributes() ?>>
<?php echo $deductions->NOTES->ListViewValue() ?></span>
</span>
</td>
<?php } ?>
	</tr>
<?php
	$deductions_delete->Recordset->MoveNext();
}
$deductions_delete->Recordset->Close();
?>
</tbody>
</table>
</div>
</div>
<div>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("DeleteBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $deductions_delete->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</form>
<script type="text/javascript">
fdeductionsdelete.Init();
</script>
<?php
$deductions_delete->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$deductions_delete->Page_Terminate();
?>
