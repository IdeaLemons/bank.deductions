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

$accounts_add = NULL; // Initialize page object first

class caccounts_add extends caccounts {

	// Page ID
	var $PageID = 'add';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'accounts';

	// Page object name
	var $PageObjName = 'accounts_add';

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
			define("EW_PAGE_ID", 'add', TRUE);

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
	var $FormClassName = "form-horizontal ewForm ewAddForm";
	var $DbMasterFilter = "";
	var $DbDetailFilter = "";
	var $StartRec;
	var $Priv = 0;
	var $OldRecordset;
	var $CopyRecord;

	// 
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsFormError;
		$this->FormClassName = "ewForm ewAddForm";
		if (ew_IsMobile())
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");

		// Process form if post back
		if (@$_POST["a_add"] <> "") {
			$this->CurrentAction = $_POST["a_add"]; // Get form action
			$this->CopyRecord = $this->LoadOldRecord(); // Load old recordset
			$this->LoadFormValues(); // Load form values
		} else { // Not post back

			// Load key values from QueryString
			$this->CopyRecord = TRUE;
			if (@$_GET["Acc_ID"] != "") {
				$this->Acc_ID->setQueryStringValue($_GET["Acc_ID"]);
				$this->setKey("Acc_ID", $this->Acc_ID->CurrentValue); // Set up key
			} else {
				$this->setKey("Acc_ID", ""); // Clear key
				$this->CopyRecord = FALSE;
			}
			if ($this->CopyRecord) {
				$this->CurrentAction = "C"; // Copy record
			} else {
				$this->CurrentAction = "I"; // Display blank record
				$this->LoadDefaultValues(); // Load default values
			}
		}

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Validate form if post back
		if (@$_POST["a_add"] <> "") {
			if (!$this->ValidateForm()) {
				$this->CurrentAction = "I"; // Form error, reset action
				$this->EventCancelled = TRUE; // Event cancelled
				$this->RestoreFormValues(); // Restore form values
				$this->setFailureMessage($gsFormError);
			}
		}

		// Perform action based on action code
		switch ($this->CurrentAction) {
			case "I": // Blank record, no action required
				break;
			case "C": // Copy an existing record
				if (!$this->LoadRow()) { // Load record based on key
					if ($this->getFailureMessage() == "") $this->setFailureMessage($Language->Phrase("NoRecord")); // No record found
					$this->Page_Terminate("accountslist.php"); // No matching record, return to list
				}
				break;
			case "A": // Add new record
				$this->SendEmail = TRUE; // Send email on add success
				if ($this->AddRow($this->OldRecordset)) { // Add successful
					if ($this->getSuccessMessage() == "")
						$this->setSuccessMessage($Language->Phrase("AddSuccess")); // Set up success message
					$sReturnUrl = $this->getReturnUrl();
					if (ew_GetPageName($sReturnUrl) == "accountsview.php")
						$sReturnUrl = $this->GetViewUrl(); // View paging, return to view page with keyurl directly
					$this->Page_Terminate($sReturnUrl); // Clean up and return
				} else {
					$this->EventCancelled = TRUE; // Event cancelled
					$this->RestoreFormValues(); // Add failed, restore form values
				}
		}

		// Render row based on row type
		$this->RowType = EW_ROWTYPE_ADD; // Render add type

		// Render row
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
		$this->Bank_Name->CurrentValue = NULL;
		$this->Bank_Name->OldValue = $this->Bank_Name->CurrentValue;
		$this->Acc_NO->CurrentValue = NULL;
		$this->Acc_NO->OldValue = $this->Acc_NO->CurrentValue;
	}

	// Load form values
	function LoadFormValues() {

		// Load from form
		global $objForm;
		if (!$this->PF->FldIsDetailKey) {
			$this->PF->setFormValue($objForm->GetValue("x_PF"));
		}
		if (!$this->Bank_ID->FldIsDetailKey) {
			$this->Bank_ID->setFormValue($objForm->GetValue("x_Bank_ID"));
		}
		if (!$this->Bank_Name->FldIsDetailKey) {
			$this->Bank_Name->setFormValue($objForm->GetValue("x_Bank_Name"));
		}
		if (!$this->Acc_NO->FldIsDetailKey) {
			$this->Acc_NO->setFormValue($objForm->GetValue("x_Acc_NO"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadOldRecord();
		$this->PF->CurrentValue = $this->PF->FormValue;
		$this->Bank_ID->CurrentValue = $this->Bank_ID->FormValue;
		$this->Bank_Name->CurrentValue = $this->Bank_Name->FormValue;
		$this->Acc_NO->CurrentValue = $this->Acc_NO->FormValue;
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
		$this->Bank_Name->setDbValue($rs->fields('Bank_Name'));
		$this->Acc_NO->setDbValue($rs->fields('Acc_NO'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->Acc_ID->DbValue = $row['Acc_ID'];
		$this->PF->DbValue = $row['PF'];
		$this->Bank_ID->DbValue = $row['Bank_ID'];
		$this->Bank_Name->DbValue = $row['Bank_Name'];
		$this->Acc_NO->DbValue = $row['Acc_NO'];
	}

	// Load old record
	function LoadOldRecord() {

		// Load key values from Session
		$bValidKey = TRUE;
		if (strval($this->getKey("Acc_ID")) <> "")
			$this->Acc_ID->CurrentValue = $this->getKey("Acc_ID"); // Acc_ID
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
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// Acc_ID
		// PF
		// Bank_ID
		// Bank_Name
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

		// Bank_Name
		$this->Bank_Name->ViewValue = $this->Bank_Name->CurrentValue;
		$this->Bank_Name->ViewCustomAttributes = "";

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

			// Bank_Name
			$this->Bank_Name->LinkCustomAttributes = "";
			$this->Bank_Name->HrefValue = "";
			$this->Bank_Name->TooltipValue = "";

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
			if (trim(strval($this->Bank_ID->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Bank_ID`" . ew_SearchString("=", $this->Bank_ID->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, `City` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `banks`";
			$sWhereWrk = "";
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->Bank_ID, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Bank_ID->EditValue = $arwrk;

			// Bank_Name
			$this->Bank_Name->EditAttrs["class"] = "form-control";
			$this->Bank_Name->EditCustomAttributes = "";
			$this->Bank_Name->EditValue = ew_HtmlEncode($this->Bank_Name->CurrentValue);
			$this->Bank_Name->PlaceHolder = ew_RemoveHtml($this->Bank_Name->FldCaption());

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

			// Bank_Name
			$this->Bank_Name->HrefValue = "";

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
		if (!$this->Bank_Name->FldIsDetailKey && !is_null($this->Bank_Name->FormValue) && $this->Bank_Name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Bank_Name->FldCaption(), $this->Bank_Name->ReqErrMsg));
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

		// Bank_Name
		$this->Bank_Name->SetDbValueDef($rsnew, $this->Bank_Name->CurrentValue, "", FALSE);

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
		$PageId = ($this->CurrentAction == "C") ? "Copy" : "Add";
		$Breadcrumb->Add("add", $PageId, $url);
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
if (!isset($accounts_add)) $accounts_add = new caccounts_add();

// Page init
$accounts_add->Page_Init();

// Page main
$accounts_add->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$accounts_add->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "add";
var CurrentForm = faccountsadd = new ew_Form("faccountsadd", "add");

// Validate form
faccountsadd.Validate = function() {
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
			elm = this.GetElements("x" + infix + "_Bank_Name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $accounts->Bank_Name->FldCaption(), $accounts->Bank_Name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Acc_NO");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $accounts->Acc_NO->FldCaption(), $accounts->Acc_NO->ReqErrMsg)) ?>");

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
faccountsadd.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
faccountsadd.ValidateRequired = true;
<?php } else { ?>
faccountsadd.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
faccountsadd.Lists["x_PF"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_PF","x_Name","x_NIC",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
faccountsadd.Lists["x_Bank_ID"] = {"LinkField":"x_Bank_ID","Ajax":true,"AutoFill":true,"DisplayFields":["x_Name","x_City","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

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
<?php $accounts_add->ShowPageHeader(); ?>
<?php
$accounts_add->ShowMessage();
?>
<form name="faccountsadd" id="faccountsadd" class="<?php echo $accounts_add->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($accounts_add->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $accounts_add->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="accounts">
<input type="hidden" name="a_add" id="a_add" value="A">
<div class="ewDesktop">
<div>
<table id="tbl_accountsadd" class="table table-bordered table-striped ewDesktopTable">
<?php if ($accounts->PF->Visible) { // PF ?>
	<tr id="r_PF">
		<td><span id="elh_accounts_PF"><?php echo $accounts->PF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $accounts->PF->CellAttributes() ?>>
<span id="el_accounts_PF">
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
faccountsadd.CreateAutoSuggest({"id":"x_PF","forceSelect":false});
</script>
</span>
<?php echo $accounts->PF->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($accounts->Bank_ID->Visible) { // Bank_ID ?>
	<tr id="r_Bank_ID">
		<td><span id="elh_accounts_Bank_ID"><?php echo $accounts->Bank_ID->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $accounts->Bank_ID->CellAttributes() ?>>
<span id="el_accounts_Bank_ID">
<?php $accounts->Bank_ID->EditAttrs["onchange"] = "ew_AutoFill(this); " . @$accounts->Bank_ID->EditAttrs["onchange"]; ?>
<select data-table="accounts" data-field="x_Bank_ID" data-value-separator="<?php echo ew_HtmlEncode(is_array($accounts->Bank_ID->DisplayValueSeparator) ? json_encode($accounts->Bank_ID->DisplayValueSeparator) : $accounts->Bank_ID->DisplayValueSeparator) ?>" id="x_Bank_ID" name="x_Bank_ID"<?php echo $accounts->Bank_ID->EditAttributes() ?>>
<?php
if (is_array($accounts->Bank_ID->EditValue)) {
	$arwrk = $accounts->Bank_ID->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = ew_SameStr($accounts->Bank_ID->CurrentValue, $arwrk[$rowcntwrk][0]) ? " selected" : "";
		if ($selwrk <> "") $emptywrk = FALSE;		
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $accounts->Bank_ID->DisplayValue($arwrk[$rowcntwrk]) ?>
</option>
<?php
	}
	if ($emptywrk && strval($accounts->Bank_ID->CurrentValue) <> "") {
?>
<option value="<?php echo ew_HtmlEncode($accounts->Bank_ID->CurrentValue) ?>" selected><?php echo $accounts->Bank_ID->CurrentValue ?></option>
<?php
    }
}
?>
</select>
<?php if (AllowAdd(CurrentProjectID() . "banks")) { ?>
<button type="button" title="<?php echo ew_HtmlTitle($Language->Phrase("AddLink")) . "&nbsp;" . $accounts->Bank_ID->FldCaption() ?>" onclick="ew_AddOptDialogShow({lnk:this,el:'x_Bank_ID',url:'banksaddopt.php'});" class="ewAddOptBtn btn btn-default btn-sm" id="aol_x_Bank_ID"><span class="glyphicon glyphicon-plus ewIcon"></span><span class="hide"><?php echo $Language->Phrase("AddLink") ?>&nbsp;<?php echo $accounts->Bank_ID->FldCaption() ?></span></button>
<?php } ?>
<?php
$sSqlWrk = "SELECT `Bank_ID`, `Name` AS `DispFld`, `City` AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
$sWhereWrk = "";
$accounts->Bank_ID->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$accounts->Bank_ID->LookupFilters += array("f0" => "`Bank_ID` = {filter_value}", "t0" => "3", "fn0" => "");
$sSqlWrk = "";
$accounts->Lookup_Selecting($accounts->Bank_ID, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $accounts->Bank_ID->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_Bank_ID" id="s_x_Bank_ID" value="<?php echo $accounts->Bank_ID->LookupFilterQuery() ?>">
<input type="hidden" name="ln_x_Bank_ID" id="ln_x_Bank_ID" value="x_Bank_Name">
</span>
<?php echo $accounts->Bank_ID->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($accounts->Bank_Name->Visible) { // Bank_Name ?>
	<tr id="r_Bank_Name">
		<td><span id="elh_accounts_Bank_Name"><?php echo $accounts->Bank_Name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $accounts->Bank_Name->CellAttributes() ?>>
<span id="el_accounts_Bank_Name">
<input type="text" data-table="accounts" data-field="x_Bank_Name" name="x_Bank_Name" id="x_Bank_Name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($accounts->Bank_Name->getPlaceHolder()) ?>" value="<?php echo $accounts->Bank_Name->EditValue ?>"<?php echo $accounts->Bank_Name->EditAttributes() ?>>
</span>
<?php echo $accounts->Bank_Name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($accounts->Acc_NO->Visible) { // Acc_NO ?>
	<tr id="r_Acc_NO">
		<td><span id="elh_accounts_Acc_NO"><?php echo $accounts->Acc_NO->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $accounts->Acc_NO->CellAttributes() ?>>
<span id="el_accounts_Acc_NO">
<input type="text" data-table="accounts" data-field="x_Acc_NO" name="x_Acc_NO" id="x_Acc_NO" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($accounts->Acc_NO->getPlaceHolder()) ?>" value="<?php echo $accounts->Acc_NO->EditValue ?>"<?php echo $accounts->Acc_NO->EditAttributes() ?>>
</span>
<?php echo $accounts->Acc_NO->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("AddBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $accounts_add->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
</form>
<script type="text/javascript">
faccountsadd.Init();
</script>
<?php
$accounts_add->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$accounts_add->Page_Terminate();
?>
