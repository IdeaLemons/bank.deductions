<!-- Begin Main Menu -->
<?php $RootMenu = new cMenu(EW_MENUBAR_ID) ?>
<?php

// Generate all menu items
$RootMenu->IsRoot = TRUE;
$RootMenu->AddMenuItem(1, "mi_deductions", $Language->MenuPhrase("1", "MenuText"), "deductionslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(2, "mi_accounts", $Language->MenuPhrase("2", "MenuText"), "accountslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(3, "mi_banks", $Language->MenuPhrase("3", "MenuText"), "bankslist.php", -1, "", TRUE, FALSE);
$RootMenu->AddMenuItem(4, "mi_emp", $Language->MenuPhrase("4", "MenuText"), "emplist.php", -1, "", TRUE, FALSE);
$RootMenu->Render();
?>
<!-- End Main Menu -->
