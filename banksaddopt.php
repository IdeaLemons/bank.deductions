<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "banksinfo.php" ?>
<?php include_once "empinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$banks_addopt = NULL; // Initialize page object first

class cbanks_addopt extends cbanks {

	// Page ID
	var $PageID = 'addopt';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'banks';

	// Page object name
	var $PageObjName = 'banks_addopt';

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

		// Table object (banks)
		if (!isset($GLOBALS["banks"]) || get_class($GLOBALS["banks"]) == "cbanks") {
			$GLOBALS["banks"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["banks"];
		}

		// Table object (emp)
		if (!isset($GLOBALS['emp'])) $GLOBALS['emp'] = new cemp();

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'addopt', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'banks', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("bankslist.php"));
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
		global $EW_EXPORT, $banks;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($banks);
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
					$row["x_Bank_ID"] = $this->Bank_ID->DbValue;
					$row["x_Bank_Code"] = $this->Bank_Code->DbValue;
					$row["x_Branch_Code"] = $this->Branch_Code->DbValue;
					$row["x_Name"] = $this->Name->DbValue;
					$row["x_Abbreviation"] = $this->Abbreviation->DbValue;
					$row["x_City"] = $this->City->DbValue;
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
		$this->Bank_Code->CurrentValue = NULL;
		$this->Bank_Code->OldValue = $this->Bank_Code->CurrentValue;
		$this->Branch_Code->CurrentValue = NULL;
		$this->Branch_Code->OldValue = $this->Branch_Code->CurrentValue;
		$this->Name->CurrentValue = NULL;
		$this->Name->OldValue = $this->Name->CurrentValue;
		$this->Abbreviation->CurrentValue = NULL;
		$this->Abbreviation->OldValue = $this->Abbreviation->CurrentValue;
		$this->City->CurrentValue = NULL;
		$this->City->OldValue = $this->City->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->Bank_Code->FldIsDetailKey) {
			$this->Bank_Code->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Bank_Code")));
		}
		if (!$this->Branch_Code->FldIsDetailKey) {
			$this->Branch_Code->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Branch_Code")));
		}
		if (!$this->Name->FldIsDetailKey) {
			$this->Name->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Name")));
		}
		if (!$this->Abbreviation->FldIsDetailKey) {
			$this->Abbreviation->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_Abbreviation")));
		}
		if (!$this->City->FldIsDetailKey) {
			$this->City->setFormValue(ew_ConvertFromUtf8($objForm->GetValue("x_City")));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->Bank_Code->CurrentValue = ew_ConvertToUtf8($this->Bank_Code->FormValue);
		$this->Branch_Code->CurrentValue = ew_ConvertToUtf8($this->Branch_Code->FormValue);
		$this->Name->CurrentValue = ew_ConvertToUtf8($this->Name->FormValue);
		$this->Abbreviation->CurrentValue = ew_ConvertToUtf8($this->Abbreviation->FormValue);
		$this->City->CurrentValue = ew_ConvertToUtf8($this->City->FormValue);
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
		$this->Bank_ID->setDbValue($rs->fields('Bank_ID'));
		$this->Bank_Code->setDbValue($rs->fields('Bank_Code'));
		if (array_key_exists('EV__Bank_Code', $rs->fields)) {
			$this->Bank_Code->VirtualValue = $rs->fields('EV__Bank_Code'); // Set up virtual field value
		} else {
			$this->Bank_Code->VirtualValue = ""; // Clear value
		}
		$this->Branch_Code->setDbValue($rs->fields('Branch_Code'));
		$this->Name->setDbValue($rs->fields('Name'));
		$this->Abbreviation->setDbValue($rs->fields('Abbreviation'));
		$this->City->setDbValue($rs->fields('City'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Bank_ID->DbValue = $row['Bank_ID'];
		$this->Bank_Code->DbValue = $row['Bank_Code'];
		$this->Branch_Code->DbValue = $row['Branch_Code'];
		$this->Name->DbValue = $row['Name'];
		$this->Abbreviation->DbValue = $row['Abbreviation'];
		$this->City->DbValue = $row['City'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Bank_ID
		// Bank_Code
		// Branch_Code
		// Name
		// Abbreviation
		// City

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// Bank_Code
		if ($this->Bank_Code->VirtualValue <> "") {
			$this->Bank_Code->ViewValue = $this->Bank_Code->VirtualValue;
		} else {
			$this->Bank_Code->ViewValue = $this->Bank_Code->CurrentValue;
		if (strval($this->Bank_Code->CurrentValue) <> "") {
			$sFilterWrk = "`Bank_Code`" . ew_SearchString("=", $this->Bank_Code->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT DISTINCT `Bank_Code`, `Bank_Code` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Bank_Code, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
		$sSqlWrk .= " ORDER BY `Bank_Code` ASC";
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Bank_Code->ViewValue = $this->Bank_Code->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Bank_Code->ViewValue = $this->Bank_Code->CurrentValue;
			}
		} else {
			$this->Bank_Code->ViewValue = NULL;
		}
		}
		$this->Bank_Code->ViewCustomAttributes = "";

		// Branch_Code
		$this->Branch_Code->ViewValue = $this->Branch_Code->CurrentValue;
		$this->Branch_Code->ViewCustomAttributes = "";

		// Name
		$this->Name->ViewValue = $this->Name->CurrentValue;
		$this->Name->ViewCustomAttributes = "";

		// Abbreviation
		$this->Abbreviation->ViewValue = $this->Abbreviation->CurrentValue;
		$this->Abbreviation->ViewCustomAttributes = "";

		// City
		$this->City->ViewValue = $this->City->CurrentValue;
		$this->City->ViewCustomAttributes = "";

			// Bank_Code
			$this->Bank_Code->LinkCustomAttributes = "";
			$this->Bank_Code->HrefValue = "";
			$this->Bank_Code->TooltipValue = "";

			// Branch_Code
			$this->Branch_Code->LinkCustomAttributes = "";
			$this->Branch_Code->HrefValue = "";
			$this->Branch_Code->TooltipValue = "";

			// Name
			$this->Name->LinkCustomAttributes = "";
			$this->Name->HrefValue = "";
			$this->Name->TooltipValue = "";

			// Abbreviation
			$this->Abbreviation->LinkCustomAttributes = "";
			$this->Abbreviation->HrefValue = "";
			$this->Abbreviation->TooltipValue = "";

			// City
			$this->City->LinkCustomAttributes = "";
			$this->City->HrefValue = "";
			$this->City->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_ADD) { // Add row

			// Bank_Code
			$this->Bank_Code->EditAttrs["class"] = "form-control";
			$this->Bank_Code->EditCustomAttributes = "";
			$this->Bank_Code->EditValue = ew_HtmlEncode($this->Bank_Code->CurrentValue);
			$this->Bank_Code->PlaceHolder = ew_RemoveHtml($this->Bank_Code->FldCaption());

			// Branch_Code
			$this->Branch_Code->EditAttrs["class"] = "form-control";
			$this->Branch_Code->EditCustomAttributes = "";
			$this->Branch_Code->EditValue = ew_HtmlEncode($this->Branch_Code->CurrentValue);
			$this->Branch_Code->PlaceHolder = ew_RemoveHtml($this->Branch_Code->FldCaption());

			// Name
			$this->Name->EditAttrs["class"] = "form-control";
			$this->Name->EditCustomAttributes = "";
			$this->Name->EditValue = ew_HtmlEncode($this->Name->CurrentValue);
			$this->Name->PlaceHolder = ew_RemoveHtml($this->Name->FldCaption());

			// Abbreviation
			$this->Abbreviation->EditAttrs["class"] = "form-control";
			$this->Abbreviation->EditCustomAttributes = "";
			$this->Abbreviation->EditValue = ew_HtmlEncode($this->Abbreviation->CurrentValue);
			$this->Abbreviation->PlaceHolder = ew_RemoveHtml($this->Abbreviation->FldCaption());

			// City
			$this->City->EditAttrs["class"] = "form-control";
			$this->City->EditCustomAttributes = "";
			$this->City->EditValue = ew_HtmlEncode($this->City->CurrentValue);
			$this->City->PlaceHolder = ew_RemoveHtml($this->City->FldCaption());

			// Edit refer script
			// Bank_Code

			$this->Bank_Code->HrefValue = "";

			// Branch_Code
			$this->Branch_Code->HrefValue = "";

			// Name
			$this->Name->HrefValue = "";

			// Abbreviation
			$this->Abbreviation->HrefValue = "";

			// City
			$this->City->HrefValue = "";
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
		if (!$this->Bank_Code->FldIsDetailKey && !is_null($this->Bank_Code->FormValue) && $this->Bank_Code->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Bank_Code->FldCaption(), $this->Bank_Code->ReqErrMsg));
		}
		if (!$this->Branch_Code->FldIsDetailKey && !is_null($this->Branch_Code->FormValue) && $this->Branch_Code->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Branch_Code->FldCaption(), $this->Branch_Code->ReqErrMsg));
		}
		if (!ew_CheckInteger($this->Branch_Code->FormValue)) {
			ew_AddMessage($gsFormError, $this->Branch_Code->FldErrMsg());
		}
		if (!$this->Name->FldIsDetailKey && !is_null($this->Name->FormValue) && $this->Name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Name->FldCaption(), $this->Name->ReqErrMsg));
		}
		if (!$this->City->FldIsDetailKey && !is_null($this->City->FormValue) && $this->City->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->City->FldCaption(), $this->City->ReqErrMsg));
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

		// Bank_Code
		$this->Bank_Code->SetDbValueDef($rsnew, $this->Bank_Code->CurrentValue, 0, FALSE);

		// Branch_Code
		$this->Branch_Code->SetDbValueDef($rsnew, $this->Branch_Code->CurrentValue, 0, FALSE);

		// Name
		$this->Name->SetDbValueDef($rsnew, $this->Name->CurrentValue, "", FALSE);

		// Abbreviation
		$this->Abbreviation->SetDbValueDef($rsnew, $this->Abbreviation->CurrentValue, NULL, FALSE);

		// City
		$this->City->SetDbValueDef($rsnew, $this->City->CurrentValue, "", FALSE);

		// Call Row Inserting event
		$rs = ($rsold == NULL) ? NULL : $rsold->fields;
		$bInsertRow = $this->Row_Inserting($rs, $rsnew);
		if ($bInsertRow) {
			$conn->raiseErrorFn = $GLOBALS["EW_ERROR_FN"];
			$AddRow = $this->Insert($rsnew);
			$conn->raiseErrorFn = '';
			if ($AddRow) {

				// Get insert id if necessary
				$this->Bank_ID->setDbValue($conn->Insert_ID());
				$rsnew['Bank_ID'] = $this->Bank_ID->DbValue;
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
		$Breadcrumb->Add("list", $this->TableVar, "bankslist.php", "", $this->TableVar, TRUE);
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
if (!isset($banks_addopt)) $banks_addopt = new cbanks_addopt();

// Page init
$banks_addopt->Page_Init();

// Page main
$banks_addopt->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$banks_addopt->Page_Render();
?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "addopt";
var CurrentForm = fbanksaddopt = new ew_Form("fbanksaddopt", "addopt");

// Validate form
fbanksaddopt.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Bank_Code");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $banks->Bank_Code->FldCaption(), $banks->Bank_Code->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Branch_Code");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $banks->Branch_Code->FldCaption(), $banks->Branch_Code->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Branch_Code");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($banks->Branch_Code->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $banks->Name->FldCaption(), $banks->Name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_City");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $banks->City->FldCaption(), $banks->City->ReqErrMsg)) ?>");

			// Fire Form_CustomValidate event
			if (!this.Form_CustomValidate(fobj))
				return false;
	}
	return true;
}

// Form_CustomValidate event
fbanksaddopt.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbanksaddopt.ValidateRequired = true;
<?php } else { ?>
fbanksaddopt.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbanksaddopt.Lists["x_Bank_Code"] = {"LinkField":"x_Bank_Code","Ajax":true,"AutoFill":false,"DisplayFields":["x_Bank_Code","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php
$banks_addopt->ShowMessage();
?>
<form name="fbanksaddopt" id="fbanksaddopt" class="ewForm form-horizontal" action="banksaddopt.php" method="post">
<?php if ($banks_addopt->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $banks_addopt->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="banks">
<input type="hidden" name="a_addopt" id="a_addopt" value="A">
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel"><?php echo $banks->Bank_Code->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<?php
$wrkonchange = trim(" " . @$banks->Bank_Code->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$banks->Bank_Code->EditAttrs["onchange"] = "";
?>
<span id="as_x_Bank_Code" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_Bank_Code" id="sv_x_Bank_Code" value="<?php echo $banks->Bank_Code->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>"<?php echo $banks->Bank_Code->EditAttributes() ?>>
</span>
<input type="hidden" data-table="banks" data-field="x_Bank_Code" data-value-separator="<?php echo ew_HtmlEncode(is_array($banks->Bank_Code->DisplayValueSeparator) ? json_encode($banks->Bank_Code->DisplayValueSeparator) : $banks->Bank_Code->DisplayValueSeparator) ?>" name="x_Bank_Code" id="x_Bank_Code" value="<?php echo ew_HtmlEncode($banks->Bank_Code->CurrentValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT DISTINCT `Bank_Code`, `Bank_Code` AS `DispFld` FROM `banks`";
$sWhereWrk = "`Bank_Code` LIKE '{query_value}%'";
$banks->Lookup_Selecting($banks->Bank_Code, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " ORDER BY `Bank_Code` ASC";
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_Bank_Code" id="q_x_Bank_Code" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
fbanksaddopt.CreateAutoSuggest({"id":"x_Bank_Code","forceSelect":false});
</script>
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel" for="x_Branch_Code"><?php echo $banks->Branch_Code->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<input type="text" data-table="banks" data-field="x_Branch_Code" name="x_Branch_Code" id="x_Branch_Code" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Branch_Code->getPlaceHolder()) ?>" value="<?php echo $banks->Branch_Code->EditValue ?>"<?php echo $banks->Branch_Code->EditAttributes() ?>>
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel" for="x_Name"><?php echo $banks->Name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<input type="text" data-table="banks" data-field="x_Name" name="x_Name" id="x_Name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($banks->Name->getPlaceHolder()) ?>" value="<?php echo $banks->Name->EditValue ?>"<?php echo $banks->Name->EditAttributes() ?>>
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel" for="x_Abbreviation"><?php echo $banks->Abbreviation->FldCaption() ?></label>
		<div class="col-sm-9">
<input type="text" data-table="banks" data-field="x_Abbreviation" name="x_Abbreviation" id="x_Abbreviation" size="8" maxlength="8" placeholder="<?php echo ew_HtmlEncode($banks->Abbreviation->getPlaceHolder()) ?>" value="<?php echo $banks->Abbreviation->EditValue ?>"<?php echo $banks->Abbreviation->EditAttributes() ?>>
</div>
	</div>
	<div class="form-group">
		<label class="col-sm-3 control-label ewLabel" for="x_City"><?php echo $banks->City->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></label>
		<div class="col-sm-9">
<input type="text" data-table="banks" data-field="x_City" name="x_City" id="x_City" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($banks->City->getPlaceHolder()) ?>" value="<?php echo $banks->City->EditValue ?>"<?php echo $banks->City->EditAttributes() ?>>
</div>
	</div>
</form>
<script type="text/javascript">
fbanksaddopt.Init();
ew_ShowMessage();
</script>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php
$banks_addopt->Page_Terminate();
?>
