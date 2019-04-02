<?php
if (session_id() == "") session_start(); // Initialize Session data
ob_start(); // Turn on output buffering
?>
<?php include_once "ewcfg12.php" ?>
<?php include_once ((EW_USE_ADODB) ? "adodb5/adodb.inc.php" : "ewmysql12.php") ?>
<?php include_once "phpfn12.php" ?>
<?php include_once "banksinfo.php" ?>
<?php include_once "userfn12.php" ?>
<?php

//
// Page class
//

$banks_search = NULL; // Initialize page object first

class cbanks_search extends cbanks {

	// Page ID
	var $PageID = 'search';

	// Project ID
	var $ProjectID = "{163802B9-268A-4AFB-8FD6-7A7D18262A99}";

	// Table name
	var $TableName = 'banks';

	// Page object name
	var $PageObjName = 'banks_search';

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

		// Page ID
		if (!defined("EW_PAGE_ID"))
			define("EW_PAGE_ID", 'search', TRUE);

		// Table name (for backward compatibility)
		if (!defined("EW_TABLE_NAME"))
			define("EW_TABLE_NAME", 'banks', TRUE);

		// Start timer
		if (!isset($GLOBALS["gTimer"])) $GLOBALS["gTimer"] = new cTimer();

		// Open connection
		if (!isset($conn)) $conn = ew_Connect($this->DBID);
	}

	// 
	//  Page_Init
	//
	function Page_Init() {
		global $gsExport, $gsCustomExport, $gsExportFile, $UserProfile, $Language, $Security, $objForm;

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
						$sSrchStr = "bankslist.php" . "?" . $sSrchStr;
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
		$this->BuildSearchUrl($sSrchUrl, $this->Bank_Code); // Bank_Code
		$this->BuildSearchUrl($sSrchUrl, $this->Branch_Code); // Branch_Code
		$this->BuildSearchUrl($sSrchUrl, $this->Name); // Name
		$this->BuildSearchUrl($sSrchUrl, $this->City); // City
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
		// Bank_Code

		$this->Bank_Code->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Bank_Code"));
		$this->Bank_Code->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Bank_Code");

		// Branch_Code
		$this->Branch_Code->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Branch_Code"));
		$this->Branch_Code->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Branch_Code");

		// Name
		$this->Name->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_Name"));
		$this->Name->AdvancedSearch->SearchOperator = $objForm->GetValue("z_Name");

		// City
		$this->City->AdvancedSearch->SearchValue = ew_StripSlashes($objForm->GetValue("x_City"));
		$this->City->AdvancedSearch->SearchOperator = $objForm->GetValue("z_City");
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
		if ($this->Name->VirtualValue <> "") {
			$this->Name->ViewValue = $this->Name->VirtualValue;
		} else {
		if (strval($this->Name->CurrentValue) <> "") {
			$sFilterWrk = "`Bank_Code`" . ew_SearchString("=", $this->Name->CurrentValue, EW_DATATYPE_NUMBER, "");
		$sSqlWrk = "SELECT DISTINCT `Bank_Code`, `Name` AS `DispFld`, '' AS `Disp2Fld`, '' AS `Disp3Fld`, '' AS `Disp4Fld` FROM `banks`";
		$sWhereWrk = "";
		ew_AddFilter($sWhereWrk, $sFilterWrk);
		$this->Lookup_Selecting($this->Name, $sWhereWrk); // Call Lookup selecting
		if ($sWhereWrk <> "") $sSqlWrk .= " WHERE " . $sWhereWrk;
			$rswrk = Conn()->Execute($sSqlWrk);
			if ($rswrk && !$rswrk->EOF) { // Lookup values found
				$arwrk = array();
				$arwrk[1] = $rswrk->fields('DispFld');
				$this->Name->ViewValue = $this->Name->DisplayValue($arwrk);
				$rswrk->Close();
			} else {
				$this->Name->ViewValue = $this->Name->CurrentValue;
			}
		} else {
			$this->Name->ViewValue = NULL;
		}
		}
		$this->Name->ViewCustomAttributes = "";

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

			// City
			$this->City->LinkCustomAttributes = "";
			$this->City->HrefValue = "";
			$this->City->TooltipValue = "";
		} elseif ($this->RowType == EW_ROWTYPE_SEARCH) { // Search row

			// Bank_Code
			$this->Bank_Code->EditAttrs["class"] = "form-control";
			$this->Bank_Code->EditCustomAttributes = "";
			$this->Bank_Code->EditValue = ew_HtmlEncode($this->Bank_Code->AdvancedSearch->SearchValue);
			$this->Bank_Code->PlaceHolder = ew_RemoveHtml($this->Bank_Code->FldCaption());

			// Branch_Code
			$this->Branch_Code->EditAttrs["class"] = "form-control";
			$this->Branch_Code->EditCustomAttributes = "";
			$this->Branch_Code->EditValue = ew_HtmlEncode($this->Branch_Code->AdvancedSearch->SearchValue);
			$this->Branch_Code->PlaceHolder = ew_RemoveHtml($this->Branch_Code->FldCaption());

			// Name
			$this->Name->EditAttrs["class"] = "form-control";
			$this->Name->EditCustomAttributes = "";
			$this->Name->EditValue = ew_HtmlEncode($this->Name->AdvancedSearch->SearchValue);
			$this->Name->PlaceHolder = ew_RemoveHtml($this->Name->FldCaption());

			// City
			$this->City->EditAttrs["class"] = "form-control";
			$this->City->EditCustomAttributes = "";
			$this->City->EditValue = ew_HtmlEncode($this->City->AdvancedSearch->SearchValue);
			$this->City->PlaceHolder = ew_RemoveHtml($this->City->FldCaption());
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
		if (!ew_CheckInteger($this->Branch_Code->AdvancedSearch->SearchValue)) {
			ew_AddMessage($gsSearchError, $this->Branch_Code->FldErrMsg());
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
		$this->Bank_Code->AdvancedSearch->Load();
		$this->Branch_Code->AdvancedSearch->Load();
		$this->Name->AdvancedSearch->Load();
		$this->City->AdvancedSearch->Load();
	}

	// Set up Breadcrumb
	function SetupBreadcrumb() {
		global $Breadcrumb, $Language;
		$Breadcrumb = new cBreadcrumb();
		$url = substr(ew_CurrentUrl(), strrpos(ew_CurrentUrl(), "/")+1);
		$Breadcrumb->Add("list", $this->TableVar, "bankslist.php", "", $this->TableVar, TRUE);
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
if (!isset($banks_search)) $banks_search = new cbanks_search();

// Page init
$banks_search->Page_Init();

// Page main
$banks_search->Page_Main();

// Global Page Rendering event (in userfn*.php)
Page_Rendering();

// Page Rendering event
$banks_search->Page_Render();
?>
<?php include_once "header.php" ?>
<script type="text/javascript">

// Form object
var CurrentPageID = EW_PAGE_ID = "search";
<?php if ($banks_search->IsModal) { ?>
var CurrentAdvancedSearchForm = fbankssearch = new ew_Form("fbankssearch", "search");
<?php } else { ?>
var CurrentForm = fbankssearch = new ew_Form("fbankssearch", "search");
<?php } ?>

// Form_CustomValidate event
fbankssearch.Form_CustomValidate = 
 function(fobj) { // DO NOT CHANGE THIS LINE!

 	// Your custom validation code here, return false if invalid. 
 	return true;
 }

// Use JavaScript validation or not
<?php if (EW_CLIENT_VALIDATE) { ?>
fbankssearch.ValidateRequired = true;
<?php } else { ?>
fbankssearch.ValidateRequired = false; 
<?php } ?>

// Dynamic selection lists
fbankssearch.Lists["x_Bank_Code"] = {"LinkField":"x_Bank_Code","Ajax":true,"AutoFill":false,"DisplayFields":["x_Bank_Code","","",""],"ParentFields":[],"ChildFields":["x_Name"],"FilterFields":[],"Options":[],"Template":""};
fbankssearch.Lists["x_Name"] = {"LinkField":"x_Bank_Code","Ajax":true,"AutoFill":false,"DisplayFields":["x_Name","","",""],"ParentFields":["x_Bank_Code"],"ChildFields":[],"FilterFields":["x_Bank_Code"],"Options":[],"Template":""};

// Form object for search
// Validate function for search

fbankssearch.Validate = function(fobj) {
	if (!this.ValidateRequired)
		return true; // Ignore validation
	fobj = fobj || this.Form;
	var infix = "";
	elm = this.GetElements("x" + infix + "_Branch_Code");
	if (elm && !ew_CheckInteger(elm.value))
		return this.OnError(elm, "<?php echo ew_JsEncode2($banks->Branch_Code->FldErrMsg()) ?>");

	// Fire Form_CustomValidate event
	if (!this.Form_CustomValidate(fobj))
		return false;
	return true;
}
</script>
<script type="text/javascript">

// Write your client script here, no need to add script tags.
</script>
<?php if (!$banks_search->IsModal) { ?>
<div class="ewToolbar">
<?php $Breadcrumb->Render(); ?>
<?php echo $Language->SelectionForm(); ?>
<div class="clearfix"></div>
</div>
<?php } ?>
<?php $banks_search->ShowPageHeader(); ?>
<?php
$banks_search->ShowMessage();
?>
<form name="fbankssearch" id="fbankssearch" class="<?php echo $banks_search->FormClassName ?>" action="<?php echo ew_CurrentPage() ?>" method="post">
<?php if ($banks_search->CheckToken) { ?>
<input type="hidden" name="<?php echo EW_TOKEN_NAME ?>" value="<?php echo $banks_search->Token ?>">
<?php } ?>
<input type="hidden" name="t" value="banks">
<input type="hidden" name="a_search" id="a_search" value="S">
<?php if ($banks_search->IsModal) { ?>
<input type="hidden" name="modal" value="1">
<?php } ?>
<?php if (!ew_IsMobile() && !$banks_search->IsModal) { ?>
<div class="ewDesktop">
<?php } ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
<div>
<?php } else { ?>
<div>
<table id="tbl_bankssearch" class="table table-bordered table-striped ewDesktopTable">
<?php } ?>
<?php if ($banks->Bank_Code->Visible) { // Bank_Code ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
	<div id="r_Bank_Code" class="form-group">
		<label class="<?php echo $banks_search->SearchLabelClass ?>"><span id="elh_banks_Bank_Code"><?php echo $banks->Bank_Code->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Bank_Code" id="z_Bank_Code" value="="></p>
		</label>
		<div class="<?php echo $banks_search->SearchRightColumnClass ?>"><div<?php echo $banks->Bank_Code->CellAttributes() ?>>
			<span id="el_banks_Bank_Code">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$banks->Bank_Code->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$banks->Bank_Code->EditAttrs["onchange"] = "";
?>
<span id="as_x_Bank_Code" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_Bank_Code" id="sv_x_Bank_Code" value="<?php echo $banks->Bank_Code->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>"<?php echo $banks->Bank_Code->EditAttributes() ?>>
</span>
<input type="hidden" data-table="banks" data-field="x_Bank_Code" data-value-separator="<?php echo ew_HtmlEncode(is_array($banks->Bank_Code->DisplayValueSeparator) ? json_encode($banks->Bank_Code->DisplayValueSeparator) : $banks->Bank_Code->DisplayValueSeparator) ?>" name="x_Bank_Code" id="x_Bank_Code" value="<?php echo ew_HtmlEncode($banks->Bank_Code->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
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
fbankssearch.CreateAutoSuggest({"id":"x_Bank_Code","forceSelect":false});
</script>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Bank_Code">
		<td><span id="elh_banks_Bank_Code"><?php echo $banks->Bank_Code->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Bank_Code" id="z_Bank_Code" value="="></span></td>
		<td<?php echo $banks->Bank_Code->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_banks_Bank_Code">
<?php
$wrkonchange = trim("ew_UpdateOpt.call(this); " . @$banks->Bank_Code->EditAttrs["onchange"]);
if ($wrkonchange <> "") $wrkonchange = " onchange=\"" . ew_JsEncode2($wrkonchange) . "\"";
$banks->Bank_Code->EditAttrs["onchange"] = "";
?>
<span id="as_x_Bank_Code" style="white-space: nowrap; z-index: 8980">
	<input type="text" name="sv_x_Bank_Code" id="sv_x_Bank_Code" value="<?php echo $banks->Bank_Code->EditValue ?>" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>" data-placeholder="<?php echo ew_HtmlEncode($banks->Bank_Code->getPlaceHolder()) ?>"<?php echo $banks->Bank_Code->EditAttributes() ?>>
</span>
<input type="hidden" data-table="banks" data-field="x_Bank_Code" data-value-separator="<?php echo ew_HtmlEncode(is_array($banks->Bank_Code->DisplayValueSeparator) ? json_encode($banks->Bank_Code->DisplayValueSeparator) : $banks->Bank_Code->DisplayValueSeparator) ?>" name="x_Bank_Code" id="x_Bank_Code" value="<?php echo ew_HtmlEncode($banks->Bank_Code->AdvancedSearch->SearchValue) ?>"<?php echo $wrkonchange ?>>
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
fbankssearch.CreateAutoSuggest({"id":"x_Bank_Code","forceSelect":false});
</script>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($banks->Branch_Code->Visible) { // Branch_Code ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
	<div id="r_Branch_Code" class="form-group">
		<label for="x_Branch_Code" class="<?php echo $banks_search->SearchLabelClass ?>"><span id="elh_banks_Branch_Code"><?php echo $banks->Branch_Code->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Branch_Code" id="z_Branch_Code" value="="></p>
		</label>
		<div class="<?php echo $banks_search->SearchRightColumnClass ?>"><div<?php echo $banks->Branch_Code->CellAttributes() ?>>
			<span id="el_banks_Branch_Code">
<input type="text" data-table="banks" data-field="x_Branch_Code" name="x_Branch_Code" id="x_Branch_Code" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Branch_Code->getPlaceHolder()) ?>" value="<?php echo $banks->Branch_Code->EditValue ?>"<?php echo $banks->Branch_Code->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Branch_Code">
		<td><span id="elh_banks_Branch_Code"><?php echo $banks->Branch_Code->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("=") ?><input type="hidden" name="z_Branch_Code" id="z_Branch_Code" value="="></span></td>
		<td<?php echo $banks->Branch_Code->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_banks_Branch_Code">
<input type="text" data-table="banks" data-field="x_Branch_Code" name="x_Branch_Code" id="x_Branch_Code" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Branch_Code->getPlaceHolder()) ?>" value="<?php echo $banks->Branch_Code->EditValue ?>"<?php echo $banks->Branch_Code->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($banks->Name->Visible) { // Name ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
	<div id="r_Name" class="form-group">
		<label for="x_Name" class="<?php echo $banks_search->SearchLabelClass ?>"><span id="elh_banks_Name"><?php echo $banks->Name->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Name" id="z_Name" value="LIKE"></p>
		</label>
		<div class="<?php echo $banks_search->SearchRightColumnClass ?>"><div<?php echo $banks->Name->CellAttributes() ?>>
			<span id="el_banks_Name">
<input type="text" data-table="banks" data-field="x_Name" name="x_Name" id="x_Name" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Name->getPlaceHolder()) ?>" value="<?php echo $banks->Name->EditValue ?>"<?php echo $banks->Name->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_Name">
		<td><span id="elh_banks_Name"><?php echo $banks->Name->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_Name" id="z_Name" value="LIKE"></span></td>
		<td<?php echo $banks->Name->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_banks_Name">
<input type="text" data-table="banks" data-field="x_Name" name="x_Name" id="x_Name" size="30" placeholder="<?php echo ew_HtmlEncode($banks->Name->getPlaceHolder()) ?>" value="<?php echo $banks->Name->EditValue ?>"<?php echo $banks->Name->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if ($banks->City->Visible) { // City ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
	<div id="r_City" class="form-group">
		<label for="x_City" class="<?php echo $banks_search->SearchLabelClass ?>"><span id="elh_banks_City"><?php echo $banks->City->FldCaption() ?></span>	
		<p class="form-control-static ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_City" id="z_City" value="LIKE"></p>
		</label>
		<div class="<?php echo $banks_search->SearchRightColumnClass ?>"><div<?php echo $banks->City->CellAttributes() ?>>
			<span id="el_banks_City">
<input type="text" data-table="banks" data-field="x_City" name="x_City" id="x_City" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($banks->City->getPlaceHolder()) ?>" value="<?php echo $banks->City->EditValue ?>"<?php echo $banks->City->EditAttributes() ?>>
</span>
		</div></div>
	</div>
<?php } else { ?>
	<tr id="r_City">
		<td><span id="elh_banks_City"><?php echo $banks->City->FldCaption() ?></span></td>
		<td><span class="ewSearchOperator"><?php echo $Language->Phrase("LIKE") ?><input type="hidden" name="z_City" id="z_City" value="LIKE"></span></td>
		<td<?php echo $banks->City->CellAttributes() ?>>
			<div style="white-space: nowrap;">
				<span id="el_banks_City">
<input type="text" data-table="banks" data-field="x_City" name="x_City" id="x_City" size="30" maxlength="50" placeholder="<?php echo ew_HtmlEncode($banks->City->getPlaceHolder()) ?>" value="<?php echo $banks->City->EditValue ?>"<?php echo $banks->City->EditAttributes() ?>>
</span>
			</div>
		</td>
	</tr>
<?php } ?>
<?php } ?>
<?php if (ew_IsMobile() || $banks_search->IsModal) { ?>
</div>
<?php } else { ?>
</table>
</div>
<?php } ?>
<?php if (!$banks_search->IsModal) { ?>
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
<?php if (!ew_IsMobile() && !$banks_search->IsModal) { ?>
</div>
<?php } ?>
</form>
<script type="text/javascript">
fbankssearch.Init();
</script>
<?php
$banks_search->ShowPageFooter();
if (EW_DEBUG_ENABLED)
	echo ew_DebugMsg();
?>
<script type="text/javascript">

// Write your table-specific startup script here
// document.write("page loaded");

</script>
<?php include_once "footer.php" ?>
<?php
$banks_search->Page_Terminate();
?>
