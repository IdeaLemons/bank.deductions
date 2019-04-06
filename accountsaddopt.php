<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "accountsinfo.php" ?>
<?php include_once "empinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$accounts_addopt = NULL; // Initialize page object first

class caccounts_addopt extends caccounts {

	// Page ID
	var $PageID = 'addopt';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'accounts';

	// Page object name
	var $PageObjName = 'accounts_addopt';

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

		// Table object (accounts)
		if (!isset($GLOBALS["accounts"]) || get_class($GLOBALS["accounts"]) == "caccounts") {
			$GLOBALS["accounts"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["accounts"];
		}

		// Table object (emp)
		if (!isset($GLOBALS['emp'])) $GLOBALS['emp'] = new cemp();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'addopt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'accounts', TRUE);

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
		if (!$Security->CanAdd()) {
			$Security->SaveLastUrl();
			$this->setFailureMessage($Language->Phrase("NoPermission")); // Set no permission
			if ($Security->CanList())
				$this->Page_Terminate(ew_GetUrl("accountslist.php"));
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
		global $EW_EXPORT, $accounts;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($accounts);
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

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		set_error_handler("ew_ErrorHandler");

		// Set up Breadcrumb
		//$this->SetupBreadcrumb(); // Not used
		// Process form if post back

		if ($objForm->GetValue("a_addopt") <> "") {
			$this->CurrentAction = $objForm->GetValue("a_addopt"); // Get form action
			$this->LoadFormValues(); // Load form values

			// Validate form
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->setFailureMessage($gsFormError);
			}
		} else { // Not post back
			$this->CurrentAction = "I"; // Display blank record
			$this->LoadDefaultValues(); // Load default values
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow()) { // Add successful
					$row = array();
					$row["x_Acc_ID"] = $this->Acc_ID->DbValue;
					$row["x_PF"] = $this->PF->DbValue;
					$row["x_Bank_ID"] = $this->Bank_ID->DbValue;
					$row["x_Acc_NO"] = $this->Acc_NO->DbValue;
					if (!EW_DEBUG_ENABLED && ob_get_length())
						ob_end_clean();
					echo ew_ArrayToJson(array($row));
				} else {
					$this->ShowMessage();
				}
				$this->Page_Terminate();
				exit();
		}

		// Render row
		$this->RowType = EW_ROWTYPE_ADD; // Render add type
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Get upload files
	function GetUploadFiles() {
		global $objForm, $Language;

		// Get upload data
	}

	// Load default values
	function LoadDefaultValues() {
		$this->PF->CurrentValue = NULL;
		$this->PF->OldValue = $this->PF->CurrentValue;
		$this->Bank_ID->CurrentValue = NULL;
		$this->Bank_ID->OldValue = $this->Bank_ID->CurrentValue;
		$this->Acc_NO->CurrentValue = NULL;
		$this->Acc_NO->OldValue = $this->Acc_NO->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->PF->FldIsDetailKey) {
			$this->PF->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_PF")));
		}
		if (!$this->Bank_ID->FldIsDetailKey) {
			$this->Bank_ID->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Bank_ID")));
		}
		if (!$this->Acc_NO->FldIsDetailKey) {
			$this->Acc_NO->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Acc_NO")));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->PF->CurrentValue = ew_ConvertToUtf8($this->PF->FormValue);
		$this->Bank_ID->CurrentValue = ew_ConvertToUtf8($this->Bank_ID->FormValue);
		$this->Acc_NO->CurrentValue = ew_ConvertToUtf8($this->Acc_NO->FormValue);
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
		$this->Acc_ID->setDbValue($rs->fields('Acc_ID'));
		$this->PF->setDbValue($rs->fields('PF'));
		if (array_key_exists('EV__PF', $rs->fields)) {
			$this->PF->VirtualValue = $rs->fields('EV__PF'); // Set up virtual field value
		} else {
			$this->PF->VirtualValue = ""; // Clear value
		}
		$this->Bank_ID->setDbValue($rs->fields('Bank_ID'));
		if (array_key_exists('EV__Bank_ID', $rs->fields)) {
			$this->Bank_ID->VirtualValue = $rs->fields('EV__Bank_ID'); // Set up virtual field value
		} else {
			$this->Bank_ID->VirtualValue = ""; // Clear value
		}
		$this->Acc_NO->setDbValue($rs->fields('Acc_NO'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Acc_ID->DbValue = $row['Acc_ID'];
		$this->PF->DbValue = $row['PF'];
		$this->Bank_ID->DbValue = $row['Bank_ID'];
		$this->Acc_NO->DbValue = $row['Acc_NO'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Acc_ID
		// PF
		// Bank_ID
		// Acc_NO

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// PF
		if ($this->PF->VirtualValue <> "") {
			$this->PF->ViewValue = $this->PF->VirtualValue;
		} else {
			$this->PF->ViewValue = $this->PF->CurrentValue;
		if (strval($this->PF->CurrentValue) <> "") {
			$sFilterWrk = "`PF`" . ew_SearchString("=", $this->PF->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld`, `NIC` AS `Disp3Fld`, '' AS `Disp4Fld` FROM `emp`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->PF, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$arwrk[3] = $rswrk->fields('Disp3Fld');
				$this->PF->ViewValue = $this->PF->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->PF->ViewValue = $this->PF->CurrentValue;
			}
		} else {
			$this->PF->ViewValue = NULL;
		}
		}
		$this->PF->ViewCustomAttributes = "";

		// Bank_ID
		if ($this->Bank_ID->VirtualValue <> "") {
			$this->Bank_ID->ViewValue = $this->Bank_ID->VirtualValue;
		} else {
			$this->Bank_ID->ViewValue = $this->Bank_ID->CurrentValue;
		if (strval($this->Bank_ID->CurrentValue) <> "") {
			$sFilterWrk = "`Bank_ID`" . ew_SearchString("=", $this->Bank_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, `City` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Bank_ID, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$arwrk[2] = $rswrk->fields('Disp2Fld');
				$this->Bank_ID->ViewValue = $this->Bank_ID->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Bank_ID->ViewValue = $this->Bank_ID->CurrentValue;
			}
		} else {
			$this->Bank_ID->ViewValue = NULL;
		}
		}
		$this->Bank_ID->ViewCustomAttributes = "";

		// Acc_NO
		$this->Acc_NO->ViewValue = $this->Acc_NO->CurrentValue;
		$this->Acc_NO->ViewCustomAttributes = "";

			// PF
			$this->PF->LinkCustomAttributes = "";
			$this->PF->HrefValue = "";
			$this->PF->TooltipValue = "";

			// Bank_ID
			$this->Bank_ID->LinkCustomAttributes = "";
			$this->Bank_ID->HrefValue = "";
			$this->Bank_ID->TooltipValue = "";

			// Acc_NO
			$this->Acc_NO->LinkCustomAttributes = "";
			$this->Acc_NO->HrefValue = "";
			$this->Acc_NO->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// PF
			$this->PF->EditAttrs["class"] = "form-control";
			$this->PF->EditCustomAttributes = "";
			$this->PF->EditValue = ew_HtmlEncode($this->PF->CurrentValue);
			$this->PF->PlaceHolder = ew_RemoveHtml($this->PF->FldCaption());

			// Bank_ID
			$this->Bank_ID->EditAttrs["class"] = "form-control";
			$this->Bank_ID->EditCustomAttributes = "";
			$this->Bank_ID->EditValue = ew_HtmlEncode($this->Bank_ID->CurrentValue);
			$this->Bank_ID->PlaceHolder = ew_RemoveHtml($this->Bank_ID->FldCaption());

			// Acc_NO
			$this->Acc_NO->EditAttrs["class"] = "form-control";
			$this->Acc_NO->EditCustomAttributes = "";
			$this->Acc_NO->EditValue = ew_HtmlEncode($this->Acc_NO->CurrentValue);
			$this->Acc_NO->PlaceHolder = ew_RemoveHtml($this->Acc_NO->FldCaption());

			// Edit refer script
			// PF

			$this->PF->HrefValue = "";

			// Bank_ID
			$this->Bank_ID->HrefValue = "";

			// Acc_NO
			$this->Acc_NO->HrefValue = "";
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
		if (!$this->Bank_ID->FldIsDetailKey && !is_null($this->Bank_ID->FormValue) && $this->Bank_ID->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Bank_ID->FldCaption(), $this->Bank_ID->ReqErrMsg));
		}
		if (!$this->Acc_NO->FldIsDetailKey && !is_null($this->Acc_NO->FormValue) && $this->Acc_NO->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Acc_NO->FldCaption(), $this->Acc_NO->ReqErrMsg));
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

	// Add record
	function AddRow($rsold = NULL) {
		global $Language, $Security;
		$conn = &$this->Connection();

		// Load db values from rsold
		if ($rsold) {
			$this->LoadDbValues($rsold);
		}
		$rsnew = array();

		// PF
		$this->PF->SetDbValueDef($rsnew, $this->PF->CurrentValue, 0, FALSE);

		// Bank_ID
		$this->Bank_ID->SetDbValueDef($rsnew, $this->Bank_ID->CurrentValue, 0, FALSE);

		// Acc_NO
		$this->Acc_NO->SetDbValueDef($rsnew, $this->Acc_NO->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->Acc_ID->setDbValue($conn->Insert_ID());
				$rsnew['Acc_ID'] = $this->Acc_ID->DbValue;
			}
		} else {
			if ($this->getSuccessMessage() <> "" || $this->getFailureMessage() <> "") {

				// Use the message, do nothing
			} elseif ($this->CancelMessage <> "") {
				$this->setFailureMessage($this->CancelMessage);
				$this->CancelMessage = "";
			} else {
				$this->setFailureMessage($Language->Phrase("InsertCancelled"));
			}
			$AddRow = FALSE;
		}
		if ($AddRow) {

			// Call Row Inserted event
			$rs = ($rsold == NULL) ? NULL : $rsold->fields;
			$this->Row_Inserted($rs, $rsnew);
		}
		return $AddRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "accountslist.php", "", $this->TableVar, TRUE);
		$PageId = "addopt";
		$Breadcrumb->Add("addopt", $PageId, $url);
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

	// Custom validate event
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
if (!isset($accounts_addopt)) $accounts_addopt = new caccounts_addopt();

// Page init
$accounts_addopt->Page_Init();

// Page main
$accounts_addopt->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$accounts_addopt->Page_Render();
?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "addopt";
var CurrentForm = faccountsaddopt = new ew_Form("faccountsaddopt", "addopt");

// Validate form
faccountsaddopt.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $accounts->PF->FldCaption(), $accounts->PF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Bank_ID");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $accounts->Bank_ID->FldCaption(), $accounts->Bank_ID->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Acc_NO");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $accounts->Acc_NO->FldCaption(), $accounts->Acc_NO->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
	return true;
}

// Form_CustomValidate event
faccountsaddopt.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faccountsaddopt.ValidateRequired = true;
<?php } else { ?>
faccountsaddopt.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
faccountsaddopt.Lists["x_PF"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_PF","x_Name","x_NIC",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
faccountsaddopt.Lists["x_Bank_ID"] = {"LinkField":"x_Bank_ID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Name","x_City","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php
$accounts_addopt->ShowMessage();
?>
<form name="faccountsaddopt" id="faccountsaddopt" class="ewForm form-horizontal" action="accountsaddopt.php" method="post">
<?php if ($accounts_addopt->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $accounts_addopt->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="accounts">
<input type="hidden" name="a_addopt" id="a_addopt" value="A">
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel"><?php echo $accounts->PF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<?php
$wrkonchange = trim(" " . @$accounts->PF->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$accounts->PF->EditAttrs["onchange"] = "";
?>
<span id="as_x_PF" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_PF" id="sv_x_PF" value="<?php echo $accounts->PF->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($accounts->PF->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($accounts->PF->getPlaceHolder()) ?>"<?php echo $accounts->PF->EditAttributes() ?>>
</span>
<input type="hidden" data-table="accounts" data-field="x_PF" data-value-separator="<?php echo ew_HtmlEncode(is_array($accounts->PF->DisplayValueSeparator) ? json_encode($accounts->PF->DisplayValueSeparator) : $accounts->PF->DisplayValueSeparator) ?>" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($accounts->PF->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld`, `NIC` AS `Disp3Fld` FROM `emp`";
$sWhereWrk = "`PF` LIKE '{query_value}%' OR CONCAT(`PF`,'" . ew_ValueSeparator(1, $Page->PF) . "',`Name`,'" . ew_ValueSeparator(2, $Page->PF) . "',`NIC`) LIKE '{query_value}%'";
$accounts->Lookup_Selecting($accounts->PF, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_PF" id="q_x_PF" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
faccountsaddopt.CreateAutoSuggest({"id":"x_PF","forceSelect":false});
</script>
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel"><?php echo $accounts->Bank_ID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<?php
$wrkonchange = trim(" " . @$accounts->Bank_ID->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$accounts->Bank_ID->EditAttrs["onchange"] = "";
?>
<span id="as_x_Bank_ID" style="white-space: nowrap; z-index: 8970">
	<input type="text" name="sv_x_Bank_ID" id="sv_x_Bank_ID" value="<?php echo $accounts->Bank_ID->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($accounts->Bank_ID->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($accounts->Bank_ID->getPlaceHolder()) ?>"<?php echo $accounts->Bank_ID->EditAttributes() ?>>
</span>
<input type="hidden" data-table="accounts" data-field="x_Bank_ID" data-value-separator="<?php echo ew_HtmlEncode(is_array($accounts->Bank_ID->DisplayValueSeparator) ? json_encode($accounts->Bank_ID->DisplayValueSeparator) : $accounts->Bank_ID->DisplayValueSeparator) ?>" name="x_Bank_ID" id="x_Bank_ID" value="<?php echo ew_HtmlEncode($accounts->Bank_ID->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, `City` AS `Disp2Fld` FROM `banks`";
$sWhereWrk = "`Name` LIKE '{query_value}%' OR CONCAT(`Name`,'" . ew_ValueSeparator(1, $Page->Bank_ID) . "',`City`) LIKE '{query_value}%'";
$accounts->Lookup_Selecting($accounts->Bank_ID, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_Bank_ID" id="q_x_Bank_ID" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
faccountsaddopt.CreateAutoSuggest({"id":"x_Bank_ID","forceSelect":false});
</script>
<?php
$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, `City` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
$sWhereWrk = "{filter}";
$accounts->Bank_ID->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$accounts->Bank_ID->LookupFilters += array("f0" => "`Bank_ID` = {filter_value}", "t0" => "3", "fn0" => "");
$sSqlWrk = "";
$accounts->Lookup_Selecting($accounts->Bank_ID, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $accounts->Bank_ID->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_Bank_ID" id="s_x_Bank_ID" value="<?php echo $accounts->Bank_ID->LookupFilterQuery() ?>">
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel" for="x_Acc_NO"><?php echo $accounts->Acc_NO->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<input type="text" data-table="accounts" data-field="x_Acc_NO" name="x_Acc_NO" id="x_Acc_NO" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($accounts->Acc_NO->getPlaceHolder()) ?>" value="<?php echo $accounts->Acc_NO->EditValue ?>"<?php echo $accounts->Acc_NO->EditAttributes() ?>>
</div>
	</div>
</form>
<script type="text/javascript">
faccountsaddopt.Init();
ew_ShowMessage();
</script>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php
$accounts_addopt->Page_Terminate();
?>
