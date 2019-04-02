<!-- Begin Main Menu -->
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mmi_deductions", $Language->MenuPhrase("1", "MenuText"), "deductionslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(2, "mmi_accounts", $Language->MenuPhrase("2", "MenuText"), "accountslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(3, "mmi_banks", $Language->MenuPhrase("3", "MenuText"), "bankslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(4, "mmi_emp", $Language->MenuPhrase("4", "MenuText"), "emplist.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
