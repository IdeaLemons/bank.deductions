<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "empinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$emp_edit = NULL; // Initialize page object first

class cemp_edit extends cemp {

	// Page ID
	var $PageID = 'edit';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'emp';

	// Page object name
	var $PageObjName = 'emp_edit';

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

		// Table object (emp)
		if (!isset($GLOBALS["emp"]) || get_class($GLOBALS["emp"]) == "cemp") {
			$GLOBALS["emp"] = &$this;
			$GLOBALS["Table"] = &$GLOBALS["emp"];
		}

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'edit', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'emp', TRUE);

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
				$this->Page_Terminate(ew_GetUrl("emplist.php"));
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
		global $EW_EXPORT, $emp;
		if ($this->CustomExport <> "" && $this->CustomExport == $this->Export && array_key_exists($this->CustomExport, $EW_EXPORT)) {
				$sContent = ob_get_contents();
			if ($gsExportFile == "") $gsExportFile = $this->TableVar;
			$class = $EW_EXPORT[$this->CustomExport];
			if (class_exists($class)) {
				$doc = new $class($emp);
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
		if (@$_GET["PF"] <> "") {
			$this->PF->setQueryStringValue($_GET["PF"]);
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
		if ($this->PF->CurrentValue == "")
			$this->Page_Terminate("emplist.php"); // Invalid key, return to list

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
					$this->Page_Terminate("emplist.php"); // No matching record, return to list
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
		$this->RowType = EW_ROWTYPE_EDIT; // Render as Edit
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
		if (!$this->Name->FldIsDetailKey) {
			$this->Name->setFormValue($objForm->GetValue("x_Name"));
		}
		if (!$this->NIC->FldIsDetailKey) {
			$this->NIC->setFormValue($objForm->GetValue("x_NIC"));
		}
		if (!$this->Password->FldIsDetailKey) {
			$this->Password->setFormValue($objForm->GetValue("x_Password"));
		}
		if (!$this->UserLevel->FldIsDetailKey) {
			$this->UserLevel->setFormValue($objForm->GetValue("x_UserLevel"));
		}
		if (!$this->Activated->FldIsDetailKey) {
			$this->Activated->setFormValue($objForm->GetValue("x_Activated"));
		}
	}

	// Restore form values
	function RestoreFormValues() {
		global $objForm;
		$this->LoadRow();
		$this->PF->CurrentValue = $this->PF->FormValue;
		$this->Name->CurrentValue = $this->Name->FormValue;
		$this->NIC->CurrentValue = $this->NIC->FormValue;
		$this->Password->CurrentValue = $this->Password->FormValue;
		$this->UserLevel->CurrentValue = $this->UserLevel->FormValue;
		$this->Activated->CurrentValue = $this->Activated->FormValue;
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
		$this->PF->setDbValue($rs->fields('PF'));
		$this->Name->setDbValue($rs->fields('Name'));
		$this->NIC->setDbValue($rs->fields('NIC'));
		$this->Password->setDbValue($rs->fields('Password'));
		$this->UserLevel->setDbValue($rs->fields('UserLevel'));
		$this->Activated->setDbValue($rs->fields('Activated'));
	}

	// Load DbValue from recordset
	function LoadDbValues(&$rs) {
		if (!$rs || !is_array($rs) && $rs->EOF) return;
		$row = is_array($rs) ? $rs : $rs->fields;
		$this->PF->DbValue = $row['PF'];
		$this->Name->DbValue = $row['Name'];
		$this->NIC->DbValue = $row['NIC'];
		$this->Password->DbValue = $row['Password'];
		$this->UserLevel->DbValue = $row['UserLevel'];
		$this->Activated->DbValue = $row['Activated'];
	}

	// Render row values based on field settings
	function RenderRow() {
		global $Security, $Language, $gsLanguage;

		// Initialize URLs
		// Call Row_Rendering event

		$this->Row_Rendering();

		// Common render codes for all row types
		// PF
		// Name
		// NIC
		// Password
		// UserLevel
		// Activated

		if ($this->RowType == EW_ROWTYPE_VIEW) { // View row

		// PF
		$this->PF->ViewValue = $this->PF->CurrentValue;
		$this->PF->ViewCustomAttributes = "";

		// Name
		$this->Name->ViewValue = $this->Name->CurrentValue;
		$this->Name->ViewCustomAttributes = "";

		// NIC
		$this->NIC->ViewValue = $this->NIC->CurrentValue;
		$this->NIC->ViewCustomAttributes = "";

		// Password
		$this->Password->ViewValue = $this->Password->CurrentValue;
		$this->Password->ViewCustomAttributes = "";

		// UserLevel
		if ($Security->CanAdmin()) { // System admin
		if (strval($this->UserLevel->CurrentValue) <> "") {
			$sFilterWrk = "`userlevelid`" . ew_SearchString("=", $this->UserLevel->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `userlevels`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->UserLevel, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->UserLevel->ViewValue = $this->UserLevel->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->UserLevel->ViewValue = $this->UserLevel->CurrentValue;
			}
		} else {
			$this->UserLevel->ViewValue = NULL;
		}
		} else {
			$this->UserLevel->ViewValue = $Language->Phrase("PasswordMask");
		}
		$this->UserLevel->ViewCustomAttributes = "";

		// Activated
		if (strval($this->Activated->CurrentValue) <> "") {
			$this->Activated->ViewValue = $this->Activated->OptionCaption($this->Activated->CurrentValue);
		} else {
			$this->Activated->ViewValue = NULL;
		}
		$this->Activated->ViewCustomAttributes = "";

			// PF
			$this->PF->LinkCustomAttributes = "";
			$this->PF->HrefValue = "";
			$this->PF->TooltipValue = "";

			// Name
			$this->Name->LinkCustomAttributes = "";
			$this->Name->HrefValue = "";
			$this->Name->TooltipValue = "";

			// NIC
			$this->NIC->LinkCustomAttributes = "";
			$this->NIC->HrefValue = "";
			$this->NIC->TooltipValue = "";

			// Password
			$this->Password->LinkCustomAttributes = "";
			$this->Password->HrefValue = "";
			$this->Password->TooltipValue = "";

			// UserLevel
			$this->UserLevel->LinkCustomAttributes = "";
			$this->UserLevel->HrefValue = "";
			$this->UserLevel->TooltipValue = "";

			// Activated
			$this->Activated->LinkCustomAttributes = "";
			$this->Activated->HrefValue = "";
			$this->Activated->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_EDIT) { // Edit row

			// PF
			$this->PF->EditAttrs["class"] = "form-control";
			$this->PF->EditCustomAttributes = "";
			$this->PF->EditValue = $this->PF->CurrentValue;
			$this->PF->ViewCustomAttributes = "";

			// Name
			$this->Name->EditAttrs["class"] = "form-control";
			$this->Name->EditCustomAttributes = "";
			$this->Name->EditValue = ew_HtmlEncode($this->Name->CurrentValue);
			$this->Name->PlaceHolder = ew_RemoveHtml($this->Name->FldCaption());

			// NIC
			$this->NIC->EditAttrs["class"] = "form-control";
			$this->NIC->EditCustomAttributes = "";
			$this->NIC->EditValue = ew_HtmlEncode($this->NIC->CurrentValue);
			$this->NIC->PlaceHolder = ew_RemoveHtml($this->NIC->FldCaption());

			// Password
			$this->Password->EditAttrs["class"] = "form-control ewPasswordStrength";
			$this->Password->EditCustomAttributes = "";
			$this->Password->EditValue = ew_HtmlEncode($this->Password->CurrentValue);
			$this->Password->PlaceHolder = ew_RemoveHtml($this->Password->FldCaption());

			// UserLevel
			$this->UserLevel->EditAttrs["class"] = "form-control";
			$this->UserLevel->EditCustomAttributes = "";
			if (!$Security->CanAdmin()) { // System admin
				$this->UserLevel->EditValue = $Language->Phrase("PasswordMask");
			} else {
			if (trim(strval($this->UserLevel->CurrentValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`userlevelid`" . ew_SearchString("=", $this->UserLevel->CurrentValue, EW_DATATYPE_NUMBER, "");
			}
			$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld`, '' AS `SelectFilterFld`, '' AS `SelectFilterFld2`, '' AS `SelectFilterFld3`, '' AS `SelectFilterFld4` FROM `userlevels`";
			$sWhereWrk = "";
			ew_AddFilter($sWhereWrk, $sFilterWrk);
			$this->Lookup_Selecting($this->UserLevel, $sWhereWrk); // Call Lookup selecting
			if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->UserLevel->EditValue = $arwrk;
			}

			// Activated
			$this->Activated->EditCustomAttributes = "";
			$this->Activated->EditValue = $this->Activated->Options(FALSE);

			// Edit refer script
			// PF

			$this->PF->HrefValue = "";

			// Name
			$this->Name->HrefValue = "";

			// NIC
			$this->NIC->HrefValue = "";

			// Password
			$this->Password->HrefValue = "";

			// UserLevel
			$this->UserLevel->HrefValue = "";

			// Activated
			$this->Activated->HrefValue = "";
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
		if (!ew_CheckInteger($this->PF->FormValue)) {
			ew_AddMessage($gsFormError, $this->PF->FldErrMsg());
		}
		if (!$this->Name->FldIsDetailKey && !is_null($this->Name->FormValue) && $this->Name->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Name->FldCaption(), $this->Name->ReqErrMsg));
		}
		if (!$this->NIC->FldIsDetailKey && !is_null($this->NIC->FormValue) && $this->NIC->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->NIC->FldCaption(), $this->NIC->ReqErrMsg));
		}
		if (!$this->UserLevel->FldIsDetailKey && !is_null($this->UserLevel->FormValue) && $this->UserLevel->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->UserLevel->FldCaption(), $this->UserLevel->ReqErrMsg));
		}
		if ($this->Activated->FormValue == "") {
			ew_AddMessage($gsFormError, str_replace("%s", $this->Activated->FldCaption(), $this->Activated->ReqErrMsg));
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
			// Name

			$this->Name->SetDbValueDef($rsnew, $this->Name->CurrentValue, "", $this->Name->ReadOnly);

			// NIC
			$this->NIC->SetDbValueDef($rsnew, $this->NIC->CurrentValue, "", $this->NIC->ReadOnly);

			// Password
			$this->Password->SetDbValueDef($rsnew, $this->Password->CurrentValue, NULL, $this->Password->ReadOnly || (EW_ENCRYPTED_PASSWORD && $rs->fields('Password') == $this->Password->CurrentValue));

			// UserLevel
			if ($Security->CanAdmin()) { // System admin
			$this->UserLevel->SetDbValueDef($rsnew, $this->UserLevel->CurrentValue, 0, $this->UserLevel->ReadOnly);
			}

			// Activated
			$this->Activated->SetDbValueDef($rsnew, $this->Activated->CurrentValue, 0, $this->Activated->ReadOnly);

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
		$rs->Close();
		return $EditRow;
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "emplist.php", "", $this->TableVar, TRUE);
		$PageId = "edit";
		$Breadcrumb->Add("edit", $PageId, $url);
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
if (!isset($emp_edit)) $emp_edit = new cemp_edit();

// Page init
$emp_edit->Page_Init();

// Page main
$emp_edit->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$emp_edit->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "edit";
var CurrentForm = fempedit = new ew_Form("fempedit", "edit");

// Validate form
fempedit.Validate = function() {
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
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $emp->PF->FldCaption(), $emp->PF->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_PF");
			if (elm && !ew_CheckInteger(elm.value))
				return this.OnError(elm, "<?php echo ew_JsEncode2($emp->PF->FldErrMsg()) ?>");
			elm = this.GetElements("x" + infix + "_Name");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $emp->Name->FldCaption(), $emp->Name->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_NIC");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $emp->NIC->FldCaption(), $emp->NIC->ReqErrMsg)) ?>");
			if ($(fobj.x_Password).hasClass("ewPasswordStrength") && !$(fobj.x_Password).data("validated"))
				return this.OnError(fobj.x_Password, ewLanguage.Phrase("PasswordTooSimple"));
			elm = this.GetElements("x" + infix + "_UserLevel");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $emp->UserLevel->FldCaption(), $emp->UserLevel->ReqErrMsg)) ?>");
			elm = this.GetElements("x" + infix + "_Activated");
			if (elm && !ew_IsHidden(elm) && !ew_HasValue(elm))
				return this.OnError(elm, "<?php echo ew_JsEncode2(str_replace("%s", $emp->Activated->FldCaption(), $emp->Activated->ReqErrMsg)) ?>");

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
fempedit.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fempedit.ValidateRequired = true;
<?php } else { ?>
fempedit.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fempedit.Lists["x_UserLevel"] = {"LinkField":"x_userlevelid","Ajax":true,"AutoFill":false,"DisplayFields":["x_userlevelname","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fempedit.Lists["x_Activated"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fempedit.Lists["x_Activated"].Options = <?php echo json_encode($emp->Activated->Options()) ?>;

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
<?php $emp_edit->ShowPageHeader(); ?>
<?php
$emp_edit->ShowMessage();
?>
<form name="fempedit" id="fempedit" class="<?php echo $emp_edit->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($emp_edit->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $emp_edit->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="emp">
<input type="hidden" name="a_edit" id="a_edit" value="U">
<!-- Fields to prevent google autofill -->
<input class="hidden" type="text" name="<?php echo ew_Encrypt(ew_Random()) ?>">
<input class="hidden" type="password" name="<?php echo ew_Encrypt(ew_Random()) ?>">
<div class="ewDesktop">
<div>
<table id="tbl_empedit" class="table table-bordered table-striped ewDesktopTable">
<?php if ($emp->PF->Visible) { // PF ?>
	<tr id="r_PF">
		<td><span id="elh_emp_PF"><?php echo $emp->PF->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $emp->PF->CellAttributes() ?>>
<span id="el_emp_PF">
<span<?php echo $emp->PF->ViewAttributes() ?>>
<p class="form-control-static"><?php echo $emp->PF->EditValue ?></p></span>
</span>
<input type="hidden" data-table="emp" data-field="x_PF" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($emp->PF->CurrentValue) ?>">
<?php echo $emp->PF->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($emp->Name->Visible) { // Name ?>
	<tr id="r_Name">
		<td><span id="elh_emp_Name"><?php echo $emp->Name->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $emp->Name->CellAttributes() ?>>
<span id="el_emp_Name">
<input type="text" data-table="emp" data-field="x_Name" name="x_Name" id="x_Name" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($emp->Name->getPlaceHolder()) ?>" value="<?php echo $emp->Name->EditValue ?>"<?php echo $emp->Name->EditAttributes() ?>>
</span>
<?php echo $emp->Name->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($emp->NIC->Visible) { // NIC ?>
	<tr id="r_NIC">
		<td><span id="elh_emp_NIC"><?php echo $emp->NIC->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $emp->NIC->CellAttributes() ?>>
<span id="el_emp_NIC">
<input type="text" data-table="emp" data-field="x_NIC" name="x_NIC" id="x_NIC" size="12" maxlength="12" placeholder="<?php echo ew_HtmlEncode($emp->NIC->getPlaceHolder()) ?>" value="<?php echo $emp->NIC->EditValue ?>"<?php echo $emp->NIC->EditAttributes() ?>>
</span>
<?php echo $emp->NIC->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($emp->Password->Visible) { // Password ?>
	<tr id="r_Password">
		<td><span id="elh_emp_Password"><?php echo $emp->Password->FldCaption() ?></span></td>
		<td<?php echo $emp->Password->CellAttributes() ?>>
<span id="el_emp_Password">
<div class="input-group" id="ig_x_Password">
<input type="text" data-password-strength="pst_x_Password" data-password-generated="pgt_x_Password" data-table="emp" data-field="x_Password" name="x_Password" id="x_Password" value="<?php echo $emp->Password->EditValue ?>" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($emp->Password->getPlaceHolder()) ?>"<?php echo $emp->Password->EditAttributes() ?>>
<span class="input-group-btn">
	<button type="button" class="btn btn-default ewPasswordGenerator" title="<?php echo ew_HtmlTitle($Language->Phrase("GeneratePassword")) ?>" data-password-field="x_Password" data-password-confirm="c_Password" data-password-strength="pst_x_Password" data-password-generated="pgt_x_Password"><?php echo $Language->Phrase("GeneratePassword") ?></button>
</span>
</div>
<span class="help-block" id="pgt_x_Password" style="display: none;"></span>
<div class="progress ewPasswordStrengthBar" id="pst_x_Password" style="display: none;">
	<div class="progress-bar" role="progressbar"></div>
</div>
</span>
<?php echo $emp->Password->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($emp->UserLevel->Visible) { // UserLevel ?>
	<tr id="r_UserLevel">
		<td><span id="elh_emp_UserLevel"><?php echo $emp->UserLevel->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $emp->UserLevel->CellAttributes() ?>>
<?php if (!$Security->IsAdmin() && $Security->IsLoggedIn()) { // Non system admin ?>
<span id="el_emp_UserLevel">
<p class="form-control-static"><?php echo $emp->UserLevel->EditValue ?></p>
</span>
<?php } else { ?>
<span id="el_emp_UserLevel">
<select data-table="emp" data-field="x_UserLevel" data-value-separator="<?php echo ew_HtmlEncode(is_array($emp->UserLevel->DisplayValueSeparator) ? json_encode($emp->UserLevel->DisplayValueSeparator) : $emp->UserLevel->DisplayValueSeparator) ?>" id="x_UserLevel" name="x_UserLevel"<?php echo $emp->UserLevel->EditAttributes() ?>>
<?php
if (is_array($emp->UserLevel->EditValue)) {
	$arwrk = $emp->UserLevel->EditValue;
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = ew_SameStr($emp->UserLevel->CurrentValue, $arwrk[$rowcntwrk][0]) ? " selected" : "";
		if ($selwrk <> "") $emptywrk = FALSE;		
?>
<option value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?>>
<?php echo $emp->UserLevel->DisplayValue($arwrk[$rowcntwrk]) ?>
</option>
<?php
	}
	if ($emptywrk && strval($emp->UserLevel->CurrentValue) <> "") {
?>
<option value="<?php echo ew_HtmlEncode($emp->UserLevel->CurrentValue) ?>" selected><?php echo $emp->UserLevel->CurrentValue ?></option>
<?php
    }
}
?>
</select>
<?php
$sSqlWrk = "SELECT `userlevelid`, `userlevelname` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `userlevels`";
$sWhereWrk = "";
$emp->UserLevel->LookupFilters = array("s" => $sSqlWrk, "d" => "");
$emp->UserLevel->LookupFilters += array("f0" => "`userlevelid` = {filter_value}", "t0" => "3", "fn0" => "");
$sSqlWrk = "";
$emp->Lookup_Selecting($emp->UserLevel, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
if ($sSqlWrk <> "") $emp->UserLevel->LookupFilters["s"] .= $sSqlWrk;
?>
<input type="hidden" name="s_x_UserLevel" id="s_x_UserLevel" value="<?php echo $emp->UserLevel->LookupFilterQuery() ?>">
</span>
<?php } ?>
<?php echo $emp->UserLevel->CustomMsg ?></td>
	</tr>
<?php } ?>
<?php if ($emp->Activated->Visible) { // Activated ?>
	<tr id="r_Activated">
		<td><span id="elh_emp_Activated"><?php echo $emp->Activated->FldCaption() ?><?php echo $Language->Phrase("FieldRequiredIndicator") ?></span></td>
		<td<?php echo $emp->Activated->CellAttributes() ?>>
<span id="el_emp_Activated">
<div id="tp_x_Activated" class="ewTemplate"><input type="radio" data-table="emp" data-field="x_Activated" data-value-separator="<?php echo ew_HtmlEncode(is_array($emp->Activated->DisplayValueSeparator) ? json_encode($emp->Activated->DisplayValueSeparator) : $emp->Activated->DisplayValueSeparator) ?>" name="x_Activated" id="x_Activated" value="{value}"<?php echo $emp->Activated->EditAttributes() ?>></div>
<div id="dsl_x_Activated" data-repeatcolumn="5" class="ewItemList"><div>
<?php
$arwrk = $emp->Activated->EditValue;
if (is_array($arwrk)) {
	$rowswrk = count($arwrk);
	$emptywrk = TRUE;
	for ($rowcntwrk = 0; $rowcntwrk < $rowswrk; $rowcntwrk++) {
		$selwrk = (strval($emp->Activated->CurrentValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
		if ($selwrk <> "")
			$emptywrk = FALSE;
?>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 1) ?>
<label class="radio-inline"><input type="radio" data-table="emp" data-field="x_Activated" name="x_Activated" id="x_Activated_<?php echo $rowcntwrk ?>" value="<?php echo ew_HtmlEncode($arwrk[$rowcntwrk][0]) ?>"<?php echo $selwrk ?><?php echo $emp->Activated->EditAttributes() ?>><?php echo $emp->Activated->DisplayValue($arwrk[$rowcntwrk]) ?></label>
<?php echo ew_RepeatColumnTable($rowswrk, $rowcntwrk, 5, 2) ?>
<?php
	}
	if ($emptywrk && strval($emp->Activated->CurrentValue) <> "") {
?>
<label class="radio-inline"><input type="radio" data-table="emp" data-field="x_Activated" name="x_Activated" id="x_Activated_<?php echo $rowswrk ?>" value="<?php echo ew_HtmlEncode($emp->Activated->CurrentValue) ?>" checked<?php echo $emp->Activated->EditAttributes() ?>><?php echo $emp->Activated->CurrentValue ?></label>
<?php
    }
}
?>
</div></div>
</span>
<?php echo $emp->Activated->CustomMsg ?></td>
	</tr>
<?php } ?>
</table>
</div>
<div class="ewDesktopButton">
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("SaveBtn") ?></button>
<button class="btn btn-default ewButton" name="btnCancel" id="btnCancel" type="button" data-href="<?php echo $emp_edit->getReturnUrl() ?>"><?php echo $Language->Phrase("CancelBtn") ?></button>
</div>
</div>
</form>
<script type="text/javascript">
fempedit.Init();
</script>
<?php
$emp_edit->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$emp_edit->Page_Terminate();
?>
