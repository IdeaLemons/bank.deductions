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

$deductions_search = NULL; // Initialize page object first

class cdeductions_search extends cdeductions {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'deductions';

	// Page object name
	var $PageObjName = 'deductions_search';

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
			define("EW_PAGE_ID", 'search', TRUE);

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
		if (!$Security->CanSearch()) {
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
	var $FormClassName = "form-horizontal ewForm ewSearchForm";
	var $IsModal = FALSE;
	var $SearchLabelClass = "col-sm-3 control-label ewLabel";
	var $SearchRightColumnClass = "col-sm-9";

	//
	// Page main
	//
	function Page_Main() {
		global $objForm, $Language, $gsSearchError;
		global $gbSkipHeaderFooter;

		// Set up Breadcrumb
		$this->SetupBreadcrumb();

		// Check modal
		$this->IsModal = (@$_GET["modal"] == "1" || @$_POST["modal"] == "1");
		if ($this->IsModal)
			$gbSkipHeaderFooter = TRUE;
		$this->FormClassName = "ewForm ewSearchForm";
		if (ew_IsMobile() || $this->IsModal)
			$this->FormClassName = ew_Concat("form-horizontal", $this->FormClassName, " ");
		if ($this->IsPageRequest()) { // Validate request

			// Get action
			$this->CurrentAction = $objForm->GetValue("a_search");
			switch ($this->CurrentAction) {
				case "S": // Get search criteria

					// Build search string for advanced search, remove blank field
					$this->LoadSearchValues(); // Get search values
					if ($this->ValidateSearch()) {
						$sSrchStr = $this->BuildAdvancedSearch();
					} else {
						$sSrchStr = "";
						$this->setFailureMessage($gsSearchError);
					}
					if ($sSrchStr <> "") {
						$sSrchStr = $this->UrlParm($sSrchStr);
						$sSrchStr = "deductionslist.php" . "?" . $sSrchStr;
						if ($this->IsModal) {
							$row = array();
							$row["url"] = $sSrchStr;
							echo ew_ArrayToJson(array($row));
							$this->Page_Terminate();
							exit();
						} else {
							$this->Page_Terminate($sSrchStr); // Go to list page
						}
					}
			}
		}

		// Restore search settings from Session
		if ($gsSearchError == "")
			$this->LoadAdvancedSearch();

		// Render row for search
		$this->RowType = EW_ROWTYPE_SEARCH;
		$this->ResetAttrs();
		$this->RenderRow();
	}

	// Build advanced search
	function BuildAdvancedSearch() {
		$sSrchUrl = "";
		$this->BuildSearchUrl($sSrchUrl, $this->PF); // PF
		$this->BuildSearchUrl($sSrchUrl, $this->L_Ref); // L_Ref
		$this->BuildSearchUrl($sSrchUrl, $this->YEAR); // YEAR
		$this->BuildSearchUrl($sSrchUrl, $this->MONTH); // MONTH
		$this->BuildSearchUrl($sSrchUrl, $this->Acc_ID); // Acc_ID
		$this->BuildSearchUrl($sSrchUrl, $this->AMOUNT); // AMOUNT
		$this->BuildSearchUrl($sSrchUrl, $this->STARTED); // STARTED
		$this->BuildSearchUrl($sSrchUrl, $this->ENDED); // ENDED
		$this->BuildSearchUrl($sSrchUrl, $this->TYPE); // TYPE
		$this->BuildSearchUrl($sSrchUrl, $this->Batch); // Batch
		$this->BuildSearchUrl($sSrchUrl, $this->NOTES); // NOTES
		if ($sSrchUrl <> "") $sSrchUrl .= "&";
		$sSrchUrl .= "cmd=search";
		return $sSrchUrl;
	}

	// Build search URL
	function BuildSearchUrl(&$Url, &$Fld, $OprOnly=FALSE) {
		global $objForm;
		$sWrk = "";
		$FldParm = substr($Fld->FldVar, 2);
		$FldVal = $objForm->GetValue("x_$FldParm");
		$FldOpr = $objForm->GetValue("z_$FldParm");
		$FldCond = $objForm->GetValue("v_$FldParm");
		$FldVal2 = $objForm->GetValue("y_$FldParm");
		$FldOpr2 = $objForm->GetValue("w_$FldParm");
		$FldVal = ew_StripSlashes($FldVal);
		if (is_array($FldVal)) $FldVal = implode(",", $FldVal);
		$FldVal2 = ew_StripSlashes($FldVal2);
		if (is_array($FldVal2)) $FldVal2 = implode(",", $FldVal2);
		$FldOpr = strtoupper(trim($FldOpr));
		$lFldDataType = ($Fld->FldIsVirtual) ? EW_DATATYPE_STRING : $Fld->FldDataType;
		if ($FldOpr == "BETWEEN") {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal) && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal <> "" && $FldVal2 <> "" && $IsValidValue) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			}
		} else {
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal));
			if ($FldVal <> "" && $IsValidValue && ew_IsValidOpr($FldOpr, $lFldDataType)) {
				$sWrk = "x_" . $FldParm . "=" . urlencode($FldVal) .
					"&z_" . $FldParm . "=" . urlencode($FldOpr);
			} elseif ($FldOpr == "IS NULL" || $FldOpr == "IS NOT NULL" || ($FldOpr <> "" && $OprOnly && ew_IsValidOpr($FldOpr, $lFldDataType))) {
				$sWrk = "z_" . $FldParm . "=" . urlencode($FldOpr);
			}
			$IsValidValue = ($lFldDataType <> EW_DATATYPE_NUMBER) ||
				($lFldDataType == EW_DATATYPE_NUMBER && $this->SearchValueIsNumeric($Fld, $FldVal2));
			if ($FldVal2 <> "" && $IsValidValue && ew_IsValidOpr($FldOpr2, $lFldDataType)) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "y_" . $FldParm . "=" . urlencode($FldVal2) .
					"&w_" . $FldParm . "=" . urlencode($FldOpr2);
			} elseif ($FldOpr2 == "IS NULL" || $FldOpr2 == "IS NOT NULL" || ($FldOpr2 <> "" && $OprOnly && ew_IsValidOpr($FldOpr2, $lFldDataType))) {
				if ($sWrk <> "") $sWrk .= "&v_" . $FldParm . "=" . urlencode($FldCond) . "&";
				$sWrk .= "w_" . $FldParm . "=" . urlencode($FldOpr2);
			}
		}
		if ($sWrk <> "") {
			if ($Url <> "") $Url .= "&";
			$Url .= $sWrk;
		}
	}

	function SearchValueIsNumeric($Fld, $Value) {
		if (ew_IsFloatFormat($Fld->FldType)) $Value = ew_StrToFloat($Value);
		return is_numeric($Value);
	}

	// Load search values for validation
	function LoadSearchValues() {
		global $objForm;

		// Load search values
		// PF

		$this->PF->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_PF"));
		$this->PF->AdvancedSearch->SearchOperator = $objForm->GetValue("z_PF");

		// L_Ref
		$this->L_Ref->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_L_Ref"));
		$this->L_Ref->AdvancedSearch->SearchOperator = $objForm->GetValue("z_L_Ref");

		// YEAR
		$this->YEAR->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_YEAR"));
		$this->YEAR->AdvancedSearch->SearchOperator = $objForm->GetValue("z_YEAR");

		// MONTH
		$this->MONTH->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_MONTH"));
		$this->MONTH->AdvancedSearch->SearchOperator = $objForm->GetValue("z_MONTH");

		// Acc_ID
		$this->Acc_ID->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Acc_ID"));
		$this->Acc_ID->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Acc_ID");

		// AMOUNT
		$this->AMOUNT->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_AMOUNT"));
		$this->AMOUNT->AdvancedSearch->SearchOperator = $objForm->GetValue("z_AMOUNT");

		// STARTED
		$this->STARTED->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_STARTED"));
		$this->STARTED->AdvancedSearch->SearchOperator = $objForm->GetValue("z_STARTED");

		// ENDED
		$this->ENDED->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_ENDED"));
		$this->ENDED->AdvancedSearch->SearchOperator = $objForm->GetValue("z_ENDED");

		// TYPE
		$this->TYPE->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_TYPE"));
		$this->TYPE->AdvancedSearch->SearchOperator = $objForm->GetValue("z_TYPE");

		// Batch
		$this->Batch->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Batch"));
		$this->Batch->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Batch");

		// NOTES
		$this->NOTES->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_NOTES"));
		$this->NOTES->AdvancedSearch->SearchOperator = $objForm->GetValue("z_NOTES");
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
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// PF
			$this->PF->EditAttrs["class"] = "form-control";
			$this->PF->EditCustomAttributes = "";
			$this->PF->EditValue = ew_HtmlEncode($this->PF->AdvancedSearch->SearchValue);
			$this->PF->PlaceHolder = ew_RemoveHtml($this->PF->FldCaption());

			// L_Ref
			$this->L_Ref->EditAttrs["class"] = "form-control";
			$this->L_Ref->EditCustomAttributes = "";
			$this->L_Ref->EditValue = ew_HtmlEncode($this->L_Ref->AdvancedSearch->SearchValue);
			$this->L_Ref->PlaceHolder = ew_RemoveHtml($this->L_Ref->FldCaption());

			// YEAR
			$this->YEAR->EditCustomAttributes = "";
			$this->YEAR->EditValue = $this->YEAR->Options(TRUE);

			// MONTH
			$this->MONTH->EditCustomAttributes = "";
			$this->MONTH->EditValue = $this->MONTH->Options(TRUE);

			// Acc_ID
			$this->Acc_ID->EditCustomAttributes = "";
			if (trim(strval($this->Acc_ID->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`PF`" . ew_SearchString("=", $this->Acc_ID->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
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
				$this->Acc_ID->AdvancedSearch->ViewValue = $this->Acc_ID->DisplayValue($arwrk);
			} else {
				$this->Acc_ID->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Acc_ID->EditValue = $arwrk;

			// AMOUNT
			$this->AMOUNT->EditAttrs["class"] = "form-control";
			$this->AMOUNT->EditCustomAttributes = "";
			$this->AMOUNT->EditValue = ew_HtmlEncode($this->AMOUNT->AdvancedSearch->SearchValue);
			$this->AMOUNT->PlaceHolder = ew_RemoveHtml($this->AMOUNT->FldCaption());

			// STARTED
			$this->STARTED->EditAttrs["class"] = "form-control";
			$this->STARTED->EditCustomAttributes = "";
			$this->STARTED->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->STARTED->AdvancedSearch->SearchValue, 5), 5));
			$this->STARTED->PlaceHolder = ew_RemoveHtml($this->STARTED->FldCaption());

			// ENDED
			$this->ENDED->EditAttrs["class"] = "form-control";
			$this->ENDED->EditCustomAttributes = "";
			$this->ENDED->EditValue = ew_HtmlEncode(ew_FormatDateTime(ew_UnFormatDateTime($this->ENDED->AdvancedSearch->SearchValue, 5), 5));
			$this->ENDED->PlaceHolder = ew_RemoveHtml($this->ENDED->FldCaption());

			// TYPE
			$this->TYPE->EditCustomAttributes = "";
			$this->TYPE->EditValue = $this->TYPE->Options(FALSE);

			// Batch
			$this->Batch->EditCustomAttributes = "";
			if (trim(strval($this->Batch->AdvancedSearch->SearchValue)) == "") {
				$sFilterWrk = "0=1";
			} else {
				$sFilterWrk = "`Batch_ID`" . ew_SearchString("=", $this->Batch->AdvancedSearch->SearchValue, EW_DATATYPE_NUMBER, "");
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
				$this->Batch->AdvancedSearch->ViewValue = $this->Batch->DisplayValue($arwrk);
			} else {
				$this->Batch->AdvancedSearch->ViewValue = $Language->Phrase("PleaseSelect");
			}
			$arwrk = ($rswrk) ? $rswrk->GetRows() : array();
			if ($rswrk) $rswrk->Close();
			array_unshift($arwrk, array("", $Language->Phrase("PleaseSelect"), "", "", "", "", "", "", ""));
			$this->Batch->EditValue = $arwrk;

			// NOTES
			$this->NOTES->EditAttrs["class"] = "form-control";
			$this->NOTES->EditCustomAttributes = "";
			$this->NOTES->EditValue = ew_HtmlEncode($this->NOTES->AdvancedSearch->SearchValue);
			$this->NOTES->PlaceHolder = ew_RemoveHtml($this->NOTES->FldCaption());
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

	// Validate search
	function ValidateSearch() {
		global $gsSearchError;

		// Initialize
		$gsSearchError = "";

		// Check if validation required
		if (!EW_SERVER_VALIDATE)
			return TRUE;
		if (!ew_CheckNumber($this->AMOUNT->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->AMOUNT->FldErrMsg());
		}
		if (!ew_CheckDate($this->STARTED->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->STARTED->FldErrMsg());
		}
		if (!ew_CheckDate($this->ENDED->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->ENDED->FldErrMsg());
		}

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

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "deductionslist.php", "", $this->TableVar, TRUE);
		$PageId = "search";
		$Breadcrumb->Add("search", $PageId, $url);
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
if (!isset($deductions_search)) $deductions_search = new cdeductions_search();

// Page init
$deductions_search->Page_Init();

// Page main
$deductions_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$deductions_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($deductions_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fdeductionssearch = new ew_Form("fdeductionssearch", "search");
<?php } else { ?>
var CurrentForm = fdeductionssearch = new ew_Form("fdeductionssearch", "search");
<?php } ?>

// Form_CustomValidate event
fdeductionssearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fdeductionssearch.ValidateRequired = true;
<?php } else { ?>
fdeductionssearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fdeductionssearch.Lists["x_PF"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_PF","x_Name","",""],"ParentFields":[],"ChildFields":["x_Acc_ID"],"FilterFields":[],"Options":[],"Template":""};
fdeductionssearch.Lists["x_YEAR"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionssearch.Lists["x_YEAR"].Options = <?php echo json_encode($deductions->YEAR->Options()) ?>;
fdeductionssearch.Lists["x_MONTH"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionssearch.Lists["x_MONTH"].Options = <?php echo json_encode($deductions->MONTH->Options()) ?>;
fdeductionssearch.Lists["x_Acc_ID"] = {"LinkField":"x_PF","Ajax":true,"AutoFill":false,"DisplayFields":["x_Bank_Name","x_Acc_NO","",""],"ParentFields":["x_PF"],"ChildFields":[],"FilterFields":["x_PF"],"Options":[],"Template":""};
fdeductionssearch.Lists["x_TYPE"] = {"LinkField":"","Ajax":false,"AutoFill":false,"DisplayFields":["","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};
fdeductionssearch.Lists["x_TYPE"].Options = <?php echo json_encode($deductions->TYPE->Options()) ?>;
fdeductionssearch.Lists["x_Batch"] = {"LinkField":"x_Batch_ID","Ajax":true,"AutoFill":false,"DisplayFields":["x_Batch_Number","","",""],"ParentFields":[],"ChildFields":[],"FilterFields":[],"Options":[],"Template":""};

// Form object for search
// Validate function for search

fdeductionssearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_AMOUNT");
	if (elm && !ew_CheckNumber(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->AMOUNT->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_STARTED");
	if (elm && !ew_CheckDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->STARTED->FldErrMsg()) ?>");
	elm = this.GetElements("x" + infix + "_ENDED");
	if (elm && !ew_CheckDate(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($deductions->ENDED->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$deductions_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $deductions_search->ShowPageHeader(); ?>
<?php
$deductions_search->ShowMessage();
?>
<form name="fdeductionssearch" id="fdeductionssearch" class="<?php echo $deductions_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($deductions_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $deductions_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="deductions">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($deductions_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$deductions_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_deductionssearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($deductions->PF->Visible) { // PF ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_PF" class="form-group">
		<label class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_PF"><?php echo $deductions->PF->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PF" id="z_PF" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->PF->CellAttributes() ?>>
			<span id="el_deductions_PF">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$deductions->PF->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$deductions->PF->EditAttrs["onchange"] = "";
?>
<span id="as_x_PF" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_PF" id="sv_x_PF" value="<?php echo $deductions->PF->EditValue ?>" size="6" placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>"<?php echo $deductions->PF->EditAttributes() ?>>
</span>
<input type="hidden" data-table="deductions" data-field="x_PF" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->PF->DisplayValueSeparator) ? json_encode($deductions->PF->DisplayValueSeparator) : $deductions->PF->DisplayValueSeparator) ?>" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($deductions->PF->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld` FROM `emp`";
$sWhereWrk = "`PF` LIKE '{query_value}%' OR CONCAT(`PF`,'" . ew_ValueSeparator(1, $Page->PF) . "',`Name`) LIKE '{query_value}%'";
$deductions->Lookup_Selecting($deductions->PF, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_PF" id="q_x_PF" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
fdeductionssearch.CreateAutoSuggest({"id":"x_PF","forceSelect":false});
</script>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_PF">
		<td><span id="elh_deductions_PF"><?php echo $deductions->PF->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_PF" id="z_PF" value="="></span></td>
		<td<?php echo $deductions->PF->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_PF">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$deductions->PF->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$deductions->PF->EditAttrs["onchange"] = "";
?>
<span id="as_x_PF" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_PF" id="sv_x_PF" value="<?php echo $deductions->PF->EditValue ?>" size="6" placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($deductions->PF->getPlaceHolder()) ?>"<?php echo $deductions->PF->EditAttributes() ?>>
</span>
<input type="hidden" data-table="deductions" data-field="x_PF" data-value-separator="<?php echo ew_HtmlEncode(is_array($deductions->PF->DisplayValueSeparator) ? json_encode($deductions->PF->DisplayValueSeparator) : $deductions->PF->DisplayValueSeparator) ?>" name="x_PF" id="x_PF" value="<?php echo ew_HtmlEncode($deductions->PF->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
<?php
$sSqlWrk = "SELECT `PF`, `PF` AS `DispFld`, `Name` AS `Disp2Fld` FROM `emp`";
$sWhereWrk = "`PF` LIKE '{query_value}%' OR CONCAT(`PF`,'" . ew_ValueSeparator(1, $Page->PF) . "',`Name`) LIKE '{query_value}%'";
$deductions->Lookup_Selecting($deductions->PF, $sWhereWrk); // Call Lookup selecting
if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
$sSqlWrk .= " LIMIT " . EW_AUTO_SUGGEST_MAX_ENTRIES;
?>
<input type="hidden" name="q_x_PF" id="q_x_PF" value="s=<?php echo ew_Encrypt($sSqlWrk) ?>&d=">
<script type="text/javascript">
fdeductionssearch.CreateAutoSuggest({"id":"x_PF","forceSelect":false});
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->L_Ref->Visible) { // L_Ref ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_L_Ref" class="form-group">
		<label for="x_L_Ref" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_L_Ref"><?php echo $deductions->L_Ref->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_L_Ref" id="z_L_Ref" value="LIKE"></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->L_Ref->CellAttributes() ?>>
			<span id="el_deductions_L_Ref">
<input type="text" data-table="deductions" data-field="x_L_Ref" name="x_L_Ref" id="x_L_Ref" placeholder="<?php echo ew_HtmlEncode($deductions->L_Ref->getPlaceHolder()) ?>" value="<?php echo $deductions->L_Ref->EditValue ?>"<?php echo $deductions->L_Ref->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_L_Ref">
		<td><span id="elh_deductions_L_Ref"><?php echo $deductions->L_Ref->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_L_Ref" id="z_L_Ref" value="LIKE"></span></td>
		<td<?php echo $deductions->L_Ref->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_L_Ref">
<input type="text" data-table="deductions" data-field="x_L_Ref" name="x_L_Ref" id="x_L_Ref" placeholder="<?php echo ew_HtmlEncode($deductions->L_Ref->getPlaceHolder()) ?>" value="<?php echo $deductions->L_Ref->EditValue ?>"<?php echo $deductions->L_Ref->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->YEAR->Visible) { // YEAR ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_YEAR" class="form-group">
		<label for="x_YEAR" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_YEAR"><?php echo $deductions->YEAR->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_YEAR" id="z_YEAR" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->YEAR->CellAttributes() ?>>
			<span id="el_deductions_YEAR">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->YEAR->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->YEAR->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_YEAR">
		<td><span id="elh_deductions_YEAR"><?php echo $deductions->YEAR->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_YEAR" id="z_YEAR" value="="></span></td>
		<td<?php echo $deductions->YEAR->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_YEAR">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->YEAR->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->YEAR->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->MONTH->Visible) { // MONTH ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_MONTH" class="form-group">
		<label for="x_MONTH" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_MONTH"><?php echo $deductions->MONTH->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MONTH" id="z_MONTH" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->MONTH->CellAttributes() ?>>
			<span id="el_deductions_MONTH">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->MONTH->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->MONTH->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_MONTH">
		<td><span id="elh_deductions_MONTH"><?php echo $deductions->MONTH->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_MONTH" id="z_MONTH" value="="></span></td>
		<td<?php echo $deductions->MONTH->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_MONTH">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->MONTH->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->MONTH->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->Acc_ID->Visible) { // Acc_ID ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_Acc_ID" class="form-group">
		<label for="x_Acc_ID" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_Acc_ID"><?php echo $deductions->Acc_ID->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Acc_ID" id="z_Acc_ID" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->Acc_ID->CellAttributes() ?>>
			<span id="el_deductions_Acc_ID">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Acc_ID->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->Acc_ID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Acc_ID">
		<td><span id="elh_deductions_Acc_ID"><?php echo $deductions->Acc_ID->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Acc_ID" id="z_Acc_ID" value="="></span></td>
		<td<?php echo $deductions->Acc_ID->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_Acc_ID">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Acc_ID->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->Acc_ID->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->AMOUNT->Visible) { // AMOUNT ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_AMOUNT" class="form-group">
		<label for="x_AMOUNT" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_AMOUNT"><?php echo $deductions->AMOUNT->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AMOUNT" id="z_AMOUNT" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->AMOUNT->CellAttributes() ?>>
			<span id="el_deductions_AMOUNT">
<input type="text" data-table="deductions" data-field="x_AMOUNT" name="x_AMOUNT" id="x_AMOUNT" size="30" placeholder="<?php echo ew_HtmlEncode($deductions->AMOUNT->getPlaceHolder()) ?>" value="<?php echo $deductions->AMOUNT->EditValue ?>"<?php echo $deductions->AMOUNT->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_AMOUNT">
		<td><span id="elh_deductions_AMOUNT"><?php echo $deductions->AMOUNT->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_AMOUNT" id="z_AMOUNT" value="="></span></td>
		<td<?php echo $deductions->AMOUNT->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_AMOUNT">
<input type="text" data-table="deductions" data-field="x_AMOUNT" name="x_AMOUNT" id="x_AMOUNT" size="30" placeholder="<?php echo ew_HtmlEncode($deductions->AMOUNT->getPlaceHolder()) ?>" value="<?php echo $deductions->AMOUNT->EditValue ?>"<?php echo $deductions->AMOUNT->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->STARTED->Visible) { // STARTED ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_STARTED" class="form-group">
		<label for="x_STARTED" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_STARTED"><?php echo $deductions->STARTED->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_STARTED" id="z_STARTED" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->STARTED->CellAttributes() ?>>
			<span id="el_deductions_STARTED">
<input type="text" data-table="deductions" data-field="x_STARTED" data-format="5" name="x_STARTED" id="x_STARTED" placeholder="<?php echo ew_HtmlEncode($deductions->STARTED->getPlaceHolder()) ?>" value="<?php echo $deductions->STARTED->EditValue ?>"<?php echo $deductions->STARTED->EditAttributes() ?>>
<?php if (!$deductions->STARTED->ReadOnly && !$deductions->STARTED->Disabled && !isset($deductions->STARTED->EditAttrs["readonly"]) && !isset($deductions->STARTED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionssearch", "x_STARTED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_STARTED">
		<td><span id="elh_deductions_STARTED"><?php echo $deductions->STARTED->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_STARTED" id="z_STARTED" value="="></span></td>
		<td<?php echo $deductions->STARTED->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_STARTED">
<input type="text" data-table="deductions" data-field="x_STARTED" data-format="5" name="x_STARTED" id="x_STARTED" placeholder="<?php echo ew_HtmlEncode($deductions->STARTED->getPlaceHolder()) ?>" value="<?php echo $deductions->STARTED->EditValue ?>"<?php echo $deductions->STARTED->EditAttributes() ?>>
<?php if (!$deductions->STARTED->ReadOnly && !$deductions->STARTED->Disabled && !isset($deductions->STARTED->EditAttrs["readonly"]) && !isset($deductions->STARTED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionssearch", "x_STARTED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->ENDED->Visible) { // ENDED ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_ENDED" class="form-group">
		<label for="x_ENDED" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_ENDED"><?php echo $deductions->ENDED->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_ENDED" id="z_ENDED" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->ENDED->CellAttributes() ?>>
			<span id="el_deductions_ENDED">
<input type="text" data-table="deductions" data-field="x_ENDED" data-format="5" name="x_ENDED" id="x_ENDED" placeholder="<?php echo ew_HtmlEncode($deductions->ENDED->getPlaceHolder()) ?>" value="<?php echo $deductions->ENDED->EditValue ?>"<?php echo $deductions->ENDED->EditAttributes() ?>>
<?php if (!$deductions->ENDED->ReadOnly && !$deductions->ENDED->Disabled && !isset($deductions->ENDED->EditAttrs["readonly"]) && !isset($deductions->ENDED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionssearch", "x_ENDED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_ENDED">
		<td><span id="elh_deductions_ENDED"><?php echo $deductions->ENDED->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_ENDED" id="z_ENDED" value="="></span></td>
		<td<?php echo $deductions->ENDED->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_ENDED">
<input type="text" data-table="deductions" data-field="x_ENDED" data-format="5" name="x_ENDED" id="x_ENDED" placeholder="<?php echo ew_HtmlEncode($deductions->ENDED->getPlaceHolder()) ?>" value="<?php echo $deductions->ENDED->EditValue ?>"<?php echo $deductions->ENDED->EditAttributes() ?>>
<?php if (!$deductions->ENDED->ReadOnly && !$deductions->ENDED->Disabled && !isset($deductions->ENDED->EditAttrs["readonly"]) && !isset($deductions->ENDED->EditAttrs["disabled"])) { ?>
<script type="text/javascript">
ew_CreateCalendar("fdeductionssearch", "x_ENDED", "%Y/%m/%d");
</script>
<?php } ?>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->TYPE->Visible) { // TYPE ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_TYPE" class="form-group">
		<label class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_TYPE"><?php echo $deductions->TYPE->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TYPE" id="z_TYPE" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->TYPE->CellAttributes() ?>>
			<span id="el_deductions_TYPE">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->TYPE->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->TYPE->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_TYPE">
		<td><span id="elh_deductions_TYPE"><?php echo $deductions->TYPE->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_TYPE" id="z_TYPE" value="="></span></td>
		<td<?php echo $deductions->TYPE->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_TYPE">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->TYPE->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->TYPE->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->Batch->Visible) { // Batch ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_Batch" class="form-group">
		<label for="x_Batch" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_Batch"><?php echo $deductions->Batch->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Batch" id="z_Batch" value="="></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->Batch->CellAttributes() ?>>
			<span id="el_deductions_Batch">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Batch->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->Batch->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Batch">
		<td><span id="elh_deductions_Batch"><?php echo $deductions->Batch->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Batch" id="z_Batch" value="="></span></td>
		<td<?php echo $deductions->Batch->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_Batch">
<div class="ewDropdownList has-feedback">
	<span class="form-control dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
		<?php echo $deductions->Batch->AdvancedSearch->ViewValue ?>
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
		$selwrk = (strval($deductions->Batch->AdvancedSearch->SearchValue) == strval($arwrk[$rowcntwrk][0])) ? " checked" : "";
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
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($deductions->NOTES->Visible) { // NOTES ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
	<div id="r_NOTES" class="form-group">
		<label for="x_NOTES" class="<?php echo $deductions_search->SearchLabelClass ?>"><span id="elh_deductions_NOTES"><?php echo $deductions->NOTES->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NOTES" id="z_NOTES" value="LIKE"></p>
		</label>
		<div class="<?php echo $deductions_search->SearchRightColumnClass ?>"><div<?php echo $deductions->NOTES->CellAttributes() ?>>
			<span id="el_deductions_NOTES">
<input type="text" data-table="deductions" data-field="x_NOTES" name="x_NOTES" id="x_NOTES" maxlength="50" placeholder="<?php echo ew_HtmlEncode($deductions->NOTES->getPlaceHolder()) ?>" value="<?php echo $deductions->NOTES->EditValue ?>"<?php echo $deductions->NOTES->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_NOTES">
		<td><span id="elh_deductions_NOTES"><?php echo $deductions->NOTES->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_NOTES" id="z_NOTES" value="LIKE"></span></td>
		<td<?php echo $deductions->NOTES->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_deductions_NOTES">
<input type="text" data-table="deductions" data-field="x_NOTES" name="x_NOTES" id="x_NOTES" maxlength="50" placeholder="<?php echo ew_HtmlEncode($deductions->NOTES->getPlaceHolder()) ?>" value="<?php echo $deductions->NOTES->EditValue ?>"<?php echo $deductions->NOTES->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $deductions_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$deductions_search->IsModal) { ?>
<?php if (ew_IsMobile()) { ?>
<div class="form-group">
	<div class="col-sm-offset-3 col-sm-9">
<?php } else { ?>
<div class="ewDesktopButton">
<?php } ?>
<button class="btn btn-primary ewButton" name="btnAction" id="btnAction" type="submit"><?php echo $Language->Phrase("Search") ?></button>
<button class="btn btn-default ewButton" name="btnReset" id="btnReset" type="button" onclick="ew_ClearForm(this.form);"><?php echo $Language->Phrase("Reset") ?></button>
<?php if (ew_IsMobile()) { ?>
	</div>
</div>
<?php } else { ?>
</div>
<?php } ?>
<?php } ?>
<?php if (!ew_IsMobile() && !$deductions_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fdeductionssearch.Init();
</script>
<?php
$deductions_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$deductions_search->Page_Terminate();
?>
